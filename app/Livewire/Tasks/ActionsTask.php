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
    // === State cho Modal Chi Tiết ===
    public bool $isEdit = false;
    public ?int $taskId = null;
    public ?int $groupId = null;
    public $taskTitle;       // Bind với input tiêu đề trong modal chi tiết
    public $taskDescription; // Bind với editor mô tả trong modal chi tiết
    public $taskIsDone = false; // Bind với checkbox trạng thái
    public $newComment;

    // === Fields cho Logic cũ (Slideover/Create) ===
    public string $name = '';
    public string $description = '';
    public string $priority = 'low';
    public string $status = 'todo';
    public ?int $assignedTo = null;
    public ?string $startAt = null;
    public ?string $deadlineAt = null;

    // -------------------------------------------------------------------------
    // Logic Thêm nhanh Task (Từ Kanban)
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

    // -------------------------------------------------------------------------
    // Logic Mở Modal Tạo Mới
    // -------------------------------------------------------------------------
    #[On('addTask')]
    public function openCreate(int $groupId): void
    {
        $this->resetData();
        $this->groupId = $groupId;
        $this->isEdit = false;
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

        Flux::modal('task-detail-modal')->close();
        Flux::toast(text: 'Đã tạo task mới.', variant: 'success');
        $this->dispatch('reloadData');
    }

    // -------------------------------------------------------------------------
    // Logic Mở Modal Chi Tiết (Khi click vào Task trên Kanban)
    // -------------------------------------------------------------------------
    #[On('editTask')]
    public function openEdit(int $id): void
    {
        $this->resetData();

        $task = Task::findOrFail($id);

        $this->isEdit = true;
        $this->taskId = $task->id;
        $this->groupId = $task->group_id;

        // Đổ dữ liệu vào các biến modal chi tiết
        $this->taskTitle = $task->name;
        $this->taskDescription = $task->description ?? '';
        $this->taskIsDone = ($task->status === TaskStatus::DONE);

        // Đổ dữ liệu vào các biến cũ để các hàm update cũ không lỗi
        $this->name = $task->name;
        $this->description = $task->description ?? '';
        $this->priority = $task->priority;
        $this->status = $task->status->value;
        $this->assignedTo = $task->assigned_to;
        $this->startAt = $task->start_at?->format('Y-m-d\TH:i');
        $this->deadlineAt = $task->deadline_at?->format('Y-m-d\TH:i');

        Flux::modal('task-detail-modal')->show(); // Mở modal theo ID bạn đặt
    }

    // Tự động lưu tiêu đề khi blur
    public function updatedTaskTitle($value)
    {
        if ($this->taskId) {
            Task::where('id', $this->taskId)->update(['name' => $value]);
            $this->dispatch('reloadData');
        }
    }

    // Tự động lưu trạng thái checkbox
    public function toggleTaskDone()
    {
        if (!$this->taskId)
            return;

        $task = Task::findOrFail($this->taskId);
        $newStatus = ($task->status === TaskStatus::DONE) ? TaskStatus::TODO : TaskStatus::DONE;

        $task->update(['status' => $newStatus]);
        $this->taskIsDone = ($newStatus === TaskStatus::DONE);

        $this->dispatch('reloadData');
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

        Flux::modal('task-detail-modal')->close();
        Flux::toast(text: 'Đã cập nhật task.', variant: 'success');
        $this->dispatch('reloadData');
    }

    public function deleteTask(): void
    {
        Task::findOrFail($this->taskId)->delete();
        Flux::modal('task-detail-modal')->close();
        Flux::toast(text: 'Đã xóa task.', variant: 'success');
        $this->dispatch('reloadData');
    }

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
            'taskTitle',
            'taskDescription',
            'taskIsDone',
            'newComment'
        ]);
        $this->status = 'todo';
        $this->priority = 'low';
        $this->resetErrorBag();
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required',
            'groupId' => 'required',
        ];
    }

    public function render()
    {
        $currentTask = $this->taskId ? Task::with('activities.user')->find($this->taskId) : null;
        $group = $this->groupId ? TaskGroup::find($this->groupId) : null;

        return view('livewire.tasks.actions-task', [
            'users' => User::orderBy('name')->get(),
            'group' => $group,
            'statuses' => TaskStatus::cases(),
            'currentTask' => $currentTask, // Truyền task hiện tại vào view
        ]);
    }
}