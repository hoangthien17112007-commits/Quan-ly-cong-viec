<?php

namespace App\Services;

use App\Models\Project;
use App\Models\TaskGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * TaskGroupService — Xử lý toàn bộ logic nghiệp vụ cho TaskGroup.
 *
 * Component Livewire KHÔNG chứa logic tính toán.
 * Chúng chỉ gọi Service này và xử lý kết quả.
 */
class TaskGroupService
{
    // -------------------------------------------------------------------------
    // WIP Limit
    // -------------------------------------------------------------------------

    /**
     * Lấy trạng thái WIP hiện tại của một project.
     *
     * @return array{current: int, limit: int|null, is_full: bool}
     */
    public function getWipStatus(Project $project): array
    {
        $currentCount = $project->taskGroups()->count();
        $limit = $project->wip_limit;

        return [
            'current' => $currentCount,
            'limit' => $limit,
            'is_full' => $limit !== null && $currentCount >= $limit,
        ];
    }

    /**
     * Kiểm tra project có thể thêm group mới không.
     *
     * Trả về true nếu:
     * - Project không có WIP limit (null), HOẶC
     * - Số group hiện tại < WIP limit
     */
    public function canAddGroup(Project $project): bool
    {
        return !$this->getWipStatus($project)['is_full'];
    }

    // -------------------------------------------------------------------------
    // CRUD
    // -------------------------------------------------------------------------

    /**
     * Tạo TaskGroup mới trong project.
     *
     * - Tự động gán order_index = max(order_index) + 1
     * - Kiểm tra WIP limit trước khi tạo
     *
     * @return array{success: bool, message: string, group: TaskGroup|null}
     */
    public function createGroup(Project $project, string $title): array
    {
        // Kiểm tra WIP limit
        if (!$this->canAddGroup($project)) {
            return [
                'success' => false,
                'message' => "Dự án này chỉ được tối đa {$project->wip_limit} nhóm task.",
                'group' => null,
            ];
        }

        // Tính order_index tiếp theo — sử dụng subquery thay vì load toàn bộ records
        $nextOrder = $project->taskGroups()->max('order_index') + 1;

        $group = TaskGroup::create([
            'project_id' => $project->id,
            'title' => $title,
            'order_index' => $nextOrder,
            'created_by' => Auth::id(),
        ]);

        return [
            'success' => true,
            'message' => 'Tạo nhóm thành công.',
            'group' => $group,
        ];
    }

    /**
     * Xóa TaskGroup (cascade xóa tasks bên trong nhờ DB constraint).
     *
     * @return array{success: bool, message: string}
     */
    public function deleteGroup(int $groupId): array
    {
        $group = TaskGroup::findOrFail($groupId);
        $group->delete();

        return [
            'success' => true,
            'message' => 'Xóa nhóm thành công.',
        ];
    }

    /**
     * Đổi tên TaskGroup.
     *
     * @return array{success: bool, message: string}
     */
    public function renameGroup(int $groupId, string $newTitle): array
    {
        $group = TaskGroup::findOrFail($groupId);
        $group->update(['title' => $newTitle]);

        return [
            'success' => true,
            'message' => 'Đổi tên nhóm thành công.',
        ];
    }

    // -------------------------------------------------------------------------
    // Move Group
    // -------------------------------------------------------------------------

    /**
     * Lấy danh sách project khả dụng để di chuyển group tới.
     *
     * Loại trừ project hiện tại của group.
     * Mỗi project kèm theo trạng thái WIP: can_accept, wip_current, wip_limit.
     *
     * Tối ưu: Sử dụng withCount thay vì N+1 query.
     *
     * @return array<int, array{id: int, name: string, wip_current: int, wip_limit: int|null, can_accept: bool}>
     */
    public function getAvailableProjects(int $groupId): array
    {
        $group = TaskGroup::findOrFail($groupId);

        // Lấy tất cả project (trừ project hiện tại) kèm số lượng group
        // → 1 query duy nhất, tránh N+1
        $projects = Project::where('id', '!=', $group->project_id)
            ->withCount('taskGroups')
            ->orderBy('name')
            ->get();

        return $projects->map(function (Project $project) {
            $limit = $project->wip_limit;
            $current = $project->task_groups_count;

            return [
                'id' => $project->id,
                'name' => $project->name,
                'wip_current' => $current,
                'wip_limit' => $limit,
                'can_accept' => $limit === null || $current < $limit,
            ];
        })->toArray();
    }

    /**
     * Di chuyển TaskGroup sang project khác (kèm toàn bộ tasks).
     *
     * Logic:
     * 1. Kiểm tra project đích có chấp nhận thêm group không (WIP limit)
     * 2. Nếu OK → cập nhật project_id + recalculate order_index
     * 3. Sử dụng DB::transaction để đảm bảo tính toàn vẹn
     *
     * @return array{success: bool, message: string}
     */
    public function moveGroupToProject(int $groupId, int $targetProjectId, ?int $targetPosition = null): array
    {
        $group = TaskGroup::findOrFail($groupId);
        $targetProject = Project::findOrFail($targetProjectId);

        if ($group->project_id === $targetProject->id) {
            return ['success' => false, 'message' => 'Nhóm đã thuộc dự án này.'];
        }

        if (!$this->canAddGroup($targetProject)) {
            $status = $this->getWipStatus($targetProject);
            return [
                'success' => false,
                'message' => "Dự án \"{$targetProject->name}\" đã đầy ({$status['current']}/{$status['limit']} nhóm).",
            ];
        }

        DB::transaction(function () use ($group, $targetProject, $targetPosition) {
            $maxOrder = $targetProject->taskGroups()->max('order_index') ?? 0;
            $newOrder = $targetPosition ?? ($maxOrder + 1);

            // Đẩy các group từ vị trí targetPosition trở đi lên 1
            if ($targetPosition !== null) {
                TaskGroup::where('project_id', $targetProject->id)
                    ->where('order_index', '>=', $targetPosition)
                    ->increment('order_index');
            }

            $group->update([
                'project_id' => $targetProject->id,
                'order_index' => $newOrder,
            ]);
        });

        return [
            'success' => true,
            'message' => "Đã chuyển nhóm \"{$group->title}\" sang dự án \"{$targetProject->name}\".",
        ];
    }
}