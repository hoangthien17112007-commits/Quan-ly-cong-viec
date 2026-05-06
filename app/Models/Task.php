<?php

namespace App\Models;

use App\Condition\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_id',
        'name',
        'ordering',
        'description',
        'status',
        'assigned_to',
        'created_by',
        'priority',
        'start_at',
        'deadline_at',
        'completed_at',
        'remind_at',
        'cover_image',
        'cover_color',
    ];

    protected $casts = [
        'status' => TaskStatus::class,
        'start_at' => 'datetime',
        'deadline_at' => 'datetime',
        'completed_at' => 'datetime',
        'remind_at' => 'datetime',
    ];

    // -------------------------------------------------------
    // RELATIONSHIPS — CẤU TRÚC CHA
    // -------------------------------------------------------

    /**
     * Task thuộc về TaskGroup nào
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(TaskGroup::class, 'group_id');
    }

    /**
     * Người được giao task (chịu trách nhiệm chính)
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Người tạo task
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // -------------------------------------------------------
    // RELATIONSHIPS — CHECKLIST (task con)
    // -------------------------------------------------------

    /**
     * Các nhóm checklist (hôm nay, ngày mai...)
     */
    public function checklists(): HasMany
    {
        return $this->hasMany(TaskChecklist::class)->orderBy('ordering');
    }

    /**
     * Tất cả checklist items qua checklists (dùng khi cần đếm tổng)
     */
    public function checklistItems(): HasManyThrough
    {
        return $this->hasManyThrough(
            TaskChecklistItem::class,
            TaskChecklist::class,
            'task_id',      // FK trên task_checklists
            'checklist_id', // FK trên task_checklist_items
        );
    }

    // -------------------------------------------------------
    // RELATIONSHIPS — LABELS
    // -------------------------------------------------------

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(
            TaskLabel::class,
            'task_label',        // tên bảng pivot
            'task_id',
            'task_label_id',
        );
    }

    // -------------------------------------------------------
    // RELATIONSHIPS — MEMBERS
    // -------------------------------------------------------

    /**
     * Các thành viên tham gia task
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_members', 'task_id', 'user_id')
            ->withTimestamps();
    }

    // -------------------------------------------------------
    // RELATIONSHIPS — COMMENTS
    // -------------------------------------------------------

    /**
     * Tất cả comment (kể cả reply) — dùng khi đếm tổng
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class)->orderBy('created_at');
    }

    /**
     * Chỉ comment gốc (không phải reply) — dùng khi render UI
     */
    public function rootComments(): HasMany
    {
        return $this->hasMany(TaskComment::class)
            ->whereNull('parent_id')
            ->orderBy('created_at');
    }

    // -------------------------------------------------------
    // RELATIONSHIPS — ATTACHMENTS
    // -------------------------------------------------------

    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class)->orderBy('created_at');
    }

    /**
     * File ảnh bìa (is_cover = true)
     */
    public function coverAttachment(): HasOne
    {
        return $this->hasOne(TaskAttachment::class)->where('is_cover', true);
    }

    // -------------------------------------------------------
    // RELATIONSHIPS — ACTIVITIES
    // -------------------------------------------------------

    public function activities(): HasMany
    {
        return $this->hasMany(TaskActivity::class)->orderByDesc('created_at');
    }

    // -------------------------------------------------------
    // ACCESSORS
    // -------------------------------------------------------

    /**
     * Phần trăm hoàn thành tổng (tất cả checklists)
     * Dùng cho progress bar ngoài card task
     */
    public function getChecklistProgressAttribute(): int
    {
        $total = $this->checklistItems()->count();
        if ($total === 0)
            return 0;

        $checked = $this->checklistItems()->where('is_checked', true)->count();
        return (int) round(($checked / $total) * 100);
    }

    /**
     * Task có quá hạn không
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->deadline_at
            && $this->deadline_at->isPast()
            && $this->completed_at === null;
    }
}
