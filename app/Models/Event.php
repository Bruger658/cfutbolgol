<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'starts_at',
        'description',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];
}