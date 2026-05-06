<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskGroup extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'project_id',
        'title',
        'order_index',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * TaskGroup thuộc về Project nào
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * TaskGroup có nhiều Task — chỉ rõ foreign key group_id
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'group_id')->orderBy('ordering');
    }

    /**
     * Người tạo nhóm
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}