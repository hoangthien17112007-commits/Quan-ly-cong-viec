<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Thành viên dự án mới được xem task.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Chỉ thành viên trong cùng dự án mới xem được task.
     */
    public function view(User $user, Task $task): bool
    {
        return $task->project->users()->where('user_id', $user->id)->exists()
            || $task->project->user_id === $user->id;
    }

    /**
     * Thành viên trong dự án có thể tạo task.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Owner dự án hoặc người được giao task mới được sửa.
     */
    public function update(User $user, Task $task): bool
    {
        return $task->project->user_id === $user->id
            || $task->user_id === $user->id;
    }

    /**
     * Chỉ Owner dự án mới được xóa task.
     */
    public function delete(User $user, Task $task): bool
    {
        return $task->project->user_id === $user->id;
    }
}
