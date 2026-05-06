<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use App\Models\TaskGroup;
use App\Models\User;
use App\Condition\TaskStatus;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Component;

class ActionsTask extends Component
{
    // State
    public bool $isEdit = false;
    public ?int $taskId = null;
    public ?int $groupId = null;
    public $taskTitle;
    public $taskDescription;
    public $newComment;

    // Fields
    public string $name = '';
    public string $description = '';
    public string $priority = 'low';
    public string $status = 'todo';
    public ?int $assignedTo = null;
    public ?string $startAt = null;
    public ?string $deadlineAt = null;
    public $taskIsDone = false;

    // -------------------------------------------------------------------------
    // Mở slideover tạo task mới
    // -------------------------------------------------------------------------
    #[On('quick-add-task')]
    public function quickAddTask(int $groupId, string $name): void
    {
        $name = trim($name);

        if (empty($name))
            return;

        Task::create([
            'group_id' => $groupId,
            'name' => $name,
            'status' => TaskStatus::TODO,
            'priority' => 'low',
            'created_by' => auth()->id(),
            'ordering' => Task::where('group_id', $groupId)->max('ordering') + 1,
        ]);

        $this->dispatch('reloadData');
    }
    #[On('addTask')]
    public function openCreate(int $groupId): void
    {
        $this->resetData();
        $this->groupId = $groupId;
        $this->isEdit = false;
        // dd($this->all());
        Flux::modal('task-detail-modal')->show();
    }

    public function createTask(): void
    {
        $this->validate();

        Task::create([
            'group_id' => $this->groupId,
            'name' => $this->name,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => $this->status,
            'assigned_to' => $this->assignedTo,
            'start_at' => $this->startAt,
            'deadline_at' => $this->deadlineAt,
            'created_by' => auth()->id(),
            'ordering' => Task::where('group_id', $this->groupId)->max('ordering') + 1,
        ]);

        Flux::modal('task-slideover')->close();
        Flux::toast(text: 'Đã tạo task mới.', variant: 'success');
        $this->dispatch('reloadData');
    }

    // -------------------------------------------------------------------------
    // Mở slideover chỉnh sửa task
    // -------------------------------------------------------------------------

    #[On('editTask')]
    public function openEdit(int $id): void
    {
        $this->resetData();

        $task = Task::findOrFail($id);

        $this->isEdit = true;
        $this->taskId = $task->id;
        $this->groupId = $task->group_id;
        $this->name = $task->name;
        $this->description = $task->description ?? '';
        $this->priority = $task->priority;
        $this->status = $task->status->value;
        $this->assignedTo = $task->assigned_to;
        $this->startAt = $task->start_at?->format('Y-m-d\TH:i');
        $this->deadlineAt = $task->deadline_at?->format('Y-m-d\TH:i');

        Flux::modal('task-slideover')->show();
    }

    public function updateTask(): void
    {
        $this->validate();

        $task = Task::findOrFail($this->taskId);

        $task->update([
            'name' => $this->name,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => $this->status,
            'assigned_to' => $this->assignedTo,
            'start_at' => $this->startAt,
            'deadline_at' => $this->deadlineAt,
        ]);

        Flux::modal('task-slideover')->close();
        Flux::toast(text: 'Đã cập nhật task.', variant: 'success');
        $this->dispatch('reloadData');
    }

    // -------------------------------------------------------------------------
    // Xóa task
    // -------------------------------------------------------------------------

    public function deleteTask(): void
    {
        Task::findOrFail($this->taskId)->delete();

        Flux::modal('task-slideover')->close();
        Flux::toast(text: 'Đã xóa task.', variant: 'success');
        $this->dispatch('reloadData');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function resetData(): void
    {
        $this->reset([
            'taskId',
            'groupId',
            'isEdit',
            'name',
            'description',
            'priority',
            'status',
            'assignedTo',
            'startAt',
            'deadlineAt',
        ]);
        $this->status = 'todo';
        $this->priority = 'low';
        $this->resetErrorBag();
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:todo,done',
            'assignedTo' => 'nullable|exists:users,id',
            'startAt' => 'nullable|date',
            'deadlineAt' => 'nullable|date|after_or_equal:startAt',
            'groupId' => 'required|exists:task_groups,id',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Tên task là bắt buộc.',
            'name.max' => 'Tên task không quá 255 ký tự.',
            'deadlineAt.after_or_equal' => 'Deadline phải sau hoặc bằng ngày bắt đầu.',
            'groupId.required' => 'Không xác định được nhóm task.',
        ];
    }

    public function render()
    {
        $group = $this->groupId ? TaskGroup::find($this->groupId) : null;

        return view('livewire.tasks.actions-task', [
            'users' => User::orderBy('name')->get(),
            'group' => $group,
            'statuses' => TaskStatus::cases(),
        ]);
    }
}