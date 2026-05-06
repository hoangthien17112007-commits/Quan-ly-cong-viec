<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class TaskAttachment extends Model
{
    protected $fillable = [
        'task_id',
        'uploaded_by',
        'name',
        'path',
        'disk',
        'mime_type',
        'size_bytes',
        'is_cover',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
        'size_bytes' => 'integer',
    ];

    // --- RELATIONSHIPS ---

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // --- ACCESSORS ---

    /**
     * URL đầy đủ để hiển thị/download
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Kích thước file dạng người đọc được: "2.4 MB"
     */
    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size_bytes;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 1) . ' ' . $units[$i];
    }

    /**
     * Có phải file ảnh không (để preview)
     */
    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }
}