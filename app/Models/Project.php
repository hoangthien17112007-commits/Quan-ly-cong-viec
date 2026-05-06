<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'created_by',
        'assigned_leader',
        'status',
        'wip_limit',
        'deadline_at',
    ];

    protected $casts = [
        'wip_limit' => 'integer',
        'deadline_at' => 'datetime',
    ];

    /**
     * Người tạo dự án
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Leader dự án
     */
    public function Leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_leader');
    }

    /**
     * Tất cả thành viên tham gia dự án
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Các nhóm task (List) trong dự án — sắp xếp theo order_index
     */
    public function taskGroups(): HasMany
    {
        return $this->hasMany(TaskGroup::class)->orderBy('order_index');
    }

    /**
     * Tất cả task trong dự án qua taskGroups — dùng để đếm
     */
    public function tasks(): HasManyThrough
    {
        return $this->hasManyThrough(Task::class, TaskGroup::class, 'project_id', 'group_id');
    }
}