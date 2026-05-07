<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskGroup;
use App\Condition\TaskStatus;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class ProjectBoard extends Component
{
    public Project $project;

    #[On('group-created')]
    public function onGroupCreated(): void
    {
        $this->refreshData();
    }


    #[On('group-deleted')]
    public function onGroupDeleted(): void
    {
        $this->refreshData();
    }

    #[On('group-renamed')]
    public function onGroupRenamed(): void
    {
        $this->refreshData();
    }

    #[On('group-moved')]
    public function onGroupMoved(): void
    {
        $this->refreshData();
    }

    #[On('reloadData')]
    public function refreshData(): void
    {
        $this->project->refresh();
    }

    public function toggleTask(int $taskId): void
    {
        $task = Task::findOrFail($taskId);
        $isDone = $task->status === TaskStatus::DONE;

        $task->update([
            'status' => $isDone ? TaskStatus::TODO->value : TaskStatus::DONE->value,
            'completed_at' => $isDone ? null : now(),
        ]);
    }

    /**
     * Kéo thả cột: cập nhật order_index cho TaskGroup.
     * Livewire 4 wire:sort gọi handler với ($item, $position).
     */
    public function updateGroupOrder(string|int $item, string|int $position): void
    {
        $groupId = (int) $item;

        $belongsToProject = TaskGroup::where('project_id', $this->project->id)
            ->whereKey($groupId)
            ->exists();

        if (!$belongsToProject) {
            return;
        }

        DB::transaction(function () use ($groupId, $position) {
            $groups = TaskGroup::where('project_id', $this->project->id)
                ->orderBy('order_index')
                ->orderBy('id')
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->all();

            $oldIndex = array_search($groupId, $groups, true);

            if ($oldIndex === false) {
                return;
            }

            array_splice($groups, $oldIndex, 1);

            $newIndex = max(0, min((int) $position, count($groups)));
            array_splice($groups, $newIndex, 0, [$groupId]);

            foreach ($groups as $index => $id) {
                TaskGroup::whereKey($id)->update(['order_index' => $index]);
            }
        });

        $this->skipRender();

        Flux::toast(text: 'Đã sắp xếp lại thứ tự nhóm.', variant: 'success');
    }

    /**
     * Kéo thả task giữa các cột / trong cùng cột.
     * Livewire 4 wire:sort:group gọi handler với ($item, $position, $groupId).
     */
    public function updateTaskOrder(string|int $item, string|int $position, string|int $groupId): void
    {
        $task = Task::findOrFail((int) $item);
        $newGroupId = (int) $groupId;
        $newPosition = (int) $position;

        $targetGroup = TaskGroup::where('project_id', $this->project->id)
            ->whereKey($newGroupId)
            ->firstOrFail();

        DB::transaction(function () use ($task, $targetGroup, $newPosition) {
            $oldGroupId = (int) $task->group_id;
            $newGroupId = (int) $targetGroup->id;

            $tasksInTargetGroup = Task::where('group_id', $newGroupId)
                ->where('id', '!=', $task->id)
                ->orderBy('ordering')
                ->orderBy('id')
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->all();

            $newIndex = max(0, min($newPosition, count($tasksInTargetGroup)));
            array_splice($tasksInTargetGroup, $newIndex, 0, [$task->id]);

            foreach ($tasksInTargetGroup as $index => $taskId) {
                Task::whereKey($taskId)->update([
                    'group_id' => $newGroupId,
                    'ordering' => $index,
                ]);
            }

            if ($oldGroupId !== $newGroupId) {
                Task::where('group_id', $oldGroupId)
                    ->orderBy('ordering')
                    ->orderBy('id')
                    ->pluck('id')
                    ->each(fn($taskId, $index) => Task::whereKey($taskId)->update(['ordering' => $index]));
            }
        });

        $this->skipRender();
        $this->dispatch('reloadData');

        Flux::toast(text: 'Đã lưu vị trí mới.', variant: 'success');
    }

    public function render()
    {
        return view('livewire.projects.project-board', [
            'project' => Project::where('slug', $this->project->slug)
                ->with([
                    'creator',
                    'Leader',
                    'taskGroups' => fn($q) => $q->orderBy('order_index')->with([
                        'tasks' => fn($q) => $q->orderBy('ordering')->with('assignee'),
                    ]),
                ])
                ->firstOrFail(),
        ]);
    }
}
