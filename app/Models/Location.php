<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
        'phone',
    ];

    /**
     * Quan hệ: 1 Location có nhiều Book
     */
    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
