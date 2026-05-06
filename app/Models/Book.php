<?php

namespace App\Models;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $fillable = [
        'image',
        'name',
        'type',
        'author',
        'published_year',
        'location_id',
    ];
    protected $casts = [
        'type' => 'array',
    ];

    public function getImageUrlAttribute($value)
    {
        return $this->image ? asset('storage/' . $this->image) : asset('storage/books/default.png');
    }

    /**
     * Quan hệ: 1 Book thuộc về 1 Location
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
