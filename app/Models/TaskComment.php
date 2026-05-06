<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskComment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'task_id',
        'user_id',
        'parent_id',
        'content',
        'edited_at',
    ];

    protected $casts = [
        'edited_at' => 'datetime',
    ];

    // --- RELATIONSHIPS ---

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Comment cha (null nếu là comment gốc)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(TaskComment::class, 'parent_id');
    }

    /**
     * Các reply của comment này
     */
    public function replies(): HasMany
    {
        return $this->hasMany(TaskComment::class, 'parent_id')
            ->orderBy('created_at');
    }

    // --- ACCESSORS ---

    public function getIsEditedAttribute(): bool
    {
        return $this->edited_at !== null;
    }
}