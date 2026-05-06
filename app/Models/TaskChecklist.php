<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskChecklist extends Model
{
    protected $fillable = [
        'task_id',
        'name',
        'ordering',
    ];

    // --- RELATIONSHIPS ---

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TaskChecklistItem::class, 'checklist_id')
            ->orderBy('ordering');
    }

    // --- ACCESSORS ---

    /**
     * Phần trăm hoàn thành checklist (dùng cho progress bar)
     */
    public function getProgressAttribute(): int
    {
        $total = $this->items()->count();
        if ($total === 0)
            return 0;

        $checked = $this->items()->where('is_checked', true)->count();
        return (int) round(($checked / $total) * 100);
    }
}