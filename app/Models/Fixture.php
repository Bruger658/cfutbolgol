<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fixture extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'fixture_date',
        'home_team_name',
        'home_team_badge_path',
        'away_team_name',
        'away_team_badge_path',
        'match_time',
        'weekday',
        'venue_name',
        'is_home_venue',
        'is_active',
    ];

    protected $casts = [
        'fixture_date' => 'date',
        'match_time' => 'datetime:H:i',
        'is_home_venue' => 'boolean',
        'is_active' => 'boolean',
    ];
}