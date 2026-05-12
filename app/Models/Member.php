<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'category',
        'document_number',
        'address',
        'city',
        'phone',
        'responsible_adult_phone',
        'is_up_to_date',
    ];

    protected $casts = [
        'is_up_to_date' => 'boolean',
    ];
}