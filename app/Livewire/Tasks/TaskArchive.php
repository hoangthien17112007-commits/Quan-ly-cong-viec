<?php

namespace App\Livewire\Tasks;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskGroup;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Component;

class TaskArchive extends Component
{
    public Project $project;

    public string $search = '';
    public string $activeTab = 'groups';

    #[On('open-archive')]
    public function openArchive(): void
    {
        $this->search = '';
        $this->activeTab = 'groups';
        Flux::modal('task-archive')->show();
    }


    public function restoreGroup(int $groupId): void
    {
        $group = TaskGroup::onlyTrashed()->findOrFail($groupId);

        // Kiểm tra WIP limit của project
        $currentCount = TaskGroup::where('project_id', $group->project_id)->count();
        $project = Project::findOrFail($group->project_id);

        if ($project->wip_limit !== null && $currentCount >= $project->wip_limit) {
            Flux::toast(
                heading: 'Giới hạn WIP',
                text: "Dự án \"{$project->name}\" đã đầy slot, không thể khôi phục.",
                variant: 'danger'
            );
            return;
        }

        $group->restore();

        Flux::toast(
            heading: 'Đã khôi phục',
            text: "Nhóm \"{$group->title}\" đã được khôi phục về dự án \"{$project->name}\".",
            variant: 'success'
        );

        $this->dispatch('reloadData');
    }

    public function forceDeleteGroup(int $groupId): void
    {
        $group = TaskGroup::onlyTrashed()->findOrFail($groupId);
        $group->forceDelete();

        Flux::toast(heading: 'Đã xóa', text: 'Nhóm đã bị xóa vĩnh viễn.', variant: 'success');
    }


    public function restoreTask(int $taskId): void
    {
        $task = Task::onlyTrashed()->findOrFail($taskId);
        $group = TaskGroup::withTrashed()->find($task->group_id);

        // Kiểm tra group còn tồn tại không
        if (!$group || $group->trashed()) {
            Flux::toast(
                heading: 'Không thể khôi phục',
                text: 'Nhóm chứa task này đã bị xóa. Hãy khôi phục nhóm trước.',
                variant: 'danger'
            );
            return;
        }

        $task->restore();

        Flux::toast(
            heading: 'Đã khôi phục',
            text: "Task \"{$task->name}\" đã được khôi phục về nhóm \"{$group->title}\".",
            variant: 'success'
        );

        $this->dispatch('reloadData');
    }

    public function forceDeleteTask(int $taskId): void
    {
        $task = Task::onlyTrashed()->findOrFail($taskId);
        $task->forceDelete();

        Flux::toast(heading: 'Đã xóa', text: 'Task đã bị xóa vĩnh viễn.', variant: 'success');
    }

    public function render()
    {
        // TaskGroups đã lưu trữ trong project này
        $archivedGroups = TaskGroup::onlyTrashed()
            ->where('project_id', $this->project->id)
            ->when($this->search, fn($q) => $q->where('title', 'like', '%' . $this->search . '%'))
            ->latest('deleted_at')
            ->get();

        // Tasks đã lưu trữ trong project này (qua group)
        $groupIds = TaskGroup::withTrashed()
            ->where('project_id', $this->project->id)
            ->pluck('id');

        $archivedTasks = Task::onlyTrashed()
            ->whereIn('group_id', $groupIds)
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->with(['group' => fn($q) => $q->withTrashed()])
            ->latest('deleted_at')
            ->get();

        return view('livewire.tasks.task-archive', [
            'archivedGroups' => $archivedGroups,
            'archivedTasks' => $archivedTasks,
        ]);
    }
}