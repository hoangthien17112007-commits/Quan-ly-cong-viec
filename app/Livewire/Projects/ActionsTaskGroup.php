<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Models\TaskGroup;
use App\Services\TaskGroupService;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Component;

class ActionsTaskGroup extends Component
{
    public int $projectId;
    public string $newGroupTitle = '';
    public bool $showNewGroupForm = false;

    // Di chuyển group sang project khác
    public ?int $movingGroupId = null;
    public ?int $targetProjectId = null;
    public ?int $targetPosition = null;
    public array $availableProjects = [];
    public array $targetPositions = []; // Danh sách vị trí của project đích

    // -------------------------------------------------------------------------
    // Tạo group
    // -------------------------------------------------------------------------

    #[On('show-new-group-form')]
    public function showNewGroupFormHandler(): void
    {
        $this->showNewGroupForm = true;
    }

    public function addGroup(TaskGroupService $service): void
    {
        $this->validate([
            'newGroupTitle' => 'required|string|max:255',
        ]);

        $project = Project::findOrFail($this->projectId);
        $result = $service->createGroup($project, $this->newGroupTitle);

        if (!$result['success']) {
            Flux::toast(heading: 'Giới hạn WIP', text: $result['message'], variant: 'danger');
            return;
        }

        $this->newGroupTitle = '';
        $this->showNewGroupForm = false;

        Flux::toast(text: $result['message'], variant: 'success');
        $this->dispatch('group-created');
    }

    // -------------------------------------------------------------------------
    // Xóa group
    // -------------------------------------------------------------------------

    #[On('delete-group')]
    public function deleteGroup(int $groupId, TaskGroupService $service): void
    {
        $result = $service->deleteGroup($groupId);
        Flux::toast(text: $result['message'], variant: 'success');
        $this->dispatch('group-deleted');
    }

    // -------------------------------------------------------------------------
    // Đổi tên inline — Alpine gửi groupId + newTitle trực tiếp
    // -------------------------------------------------------------------------

    #[On('save-rename')]
    public function saveRename(int $groupId, string $newTitle, TaskGroupService $service): void
    {
        if (trim($newTitle) === '')
            return;

        $service->renameGroup($groupId, trim($newTitle));
        $this->dispatch('group-renamed');
    }

    // -------------------------------------------------------------------------
    // Di chuyển group sang project khác
    // -------------------------------------------------------------------------

    #[On('start-move-group')]
    public function openMoveModal(int $groupId, TaskGroupService $service): void
    {
        $this->movingGroupId = $groupId;
        $this->targetProjectId = null;
        $this->availableProjects = $service->getAvailableProjects($groupId);

        Flux::modal('move-group')->show();
    }

    public function updatedTargetProjectId(): void
    {
        if (!$this->targetProjectId) {
            $this->targetPositions = [];
            $this->targetPosition = null;
            return;
        }

        $count = TaskGroup::where('project_id', $this->targetProjectId)->count();

        // Tạo danh sách vị trí 1 → count+1
        $this->targetPositions = range(1, $count + 1);

        // Mặc định chọn cuối cùng
        $this->targetPosition = $count + 1;
    }

    public function moveGroup(TaskGroupService $service): void
    {
        $this->validate([
            'targetProjectId' => 'required|integer|exists:projects,id',
        ]);

        $result = $service->moveGroupToProject($this->movingGroupId, $this->targetProjectId, $this->targetPosition);

        if (!$result['success']) {
            Flux::toast(heading: 'Không thể chuyển', text: $result['message'], variant: 'danger');
            return;
        }

        $this->movingGroupId = null;
        $this->targetProjectId = null;
        $this->availableProjects = [];

        Flux::modal('move-group')->close();
        Flux::toast(text: $result['message'], variant: 'success');
        $this->dispatch('group-moved');
    }

    public function closeMoveModal(): void
    {
        $this->movingGroupId = null;
        $this->targetProjectId = null;
        $this->targetPosition = null;
        $this->availableProjects = [];
        $this->targetPositions = [];
        Flux::modal('move-group')->close();
    }

    // -------------------------------------------------------------------------
    // Lưu trữ group
    // -------------------------------------------------------------------------

    #[On('archive-group')]
    public function archiveGroup(int $groupId, TaskGroupService $service): void
    {
        $service->deleteGroup($groupId);
        Flux::toast(heading: 'Đã lưu trữ', text: 'Danh sách đã được đưa vào kho lưu trữ.', variant: 'success');
        $this->dispatch('group-deleted');
    }

    // -------------------------------------------------------------------------
    // Validation messages
    // -------------------------------------------------------------------------

    protected function messages(): array
    {
        return [
            'newGroupTitle.required' => 'Tên nhóm là bắt buộc.',
            'newGroupTitle.max' => 'Tên nhóm không quá 255 ký tự.',
            'targetProjectId.required' => 'Vui lòng chọn dự án đích.',
        ];
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render()
    {
        return view('livewire.projects.actions-task-group');
    }
}