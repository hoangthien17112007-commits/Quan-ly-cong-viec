<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskChecklistItem extends Model
{
    protected $fillable = [
        'checklist_id',
        'name',
        'is_checked',
        'ordering',
        'assigned_to',
        'remind_at',
        'completed_at',
    ];

    protected $casts = [
        'is_checked' => 'boolean',
        'remind_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // --- RELATIONSHIPS ---

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(TaskChecklist::class, 'checklist_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}