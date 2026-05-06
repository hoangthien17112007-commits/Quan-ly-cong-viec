<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskActivity extends Model
{
    public $timestamps = false; // chỉ có created_at, không cần updated_at

    protected $fillable = [
        'task_id',
        'user_id',
        'action',
        'old_value',
        'new_value',
        'created_at',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
        'created_at' => 'datetime',
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

    // --- HELPERS ---

    /**
     * Tạo activity log nhanh
     * TaskActivity::log($task, 'status_changed', ['status' => 'todo'], ['status' => 'done']);
     */
    public static function log(Task $task, string $action, mixed $old = null, mixed $new = null): static
    {
        return static::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'old_value' => $old,
            'new_value' => $new,
        ]);
    }
}