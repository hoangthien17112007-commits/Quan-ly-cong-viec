<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Ai cũng có thể xem danh sách dự án họ tham gia.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Chỉ thành viên trong dự án mới được xem chi tiết.
     */
    public function view(User $user, Project $project): bool
    {
        return $project->users()->where('user_id', $user->id)->exists()
            || $project->user_id === $user->id;
    }

    /**
     * Ai đăng nhập cũng có thể tạo dự án mới.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Chỉ Owner (người tạo) mới được sửa dự án.
     */
    public function update(User $user, Project $project): bool
    {
        return $project->user_id === $user->id;
    }

    /**
     * Chỉ Owner mới được xóa dự án.
     */
    public function delete(User $user, Project $project): bool
    {
        return $project->user_id === $user->id;
    }
}
