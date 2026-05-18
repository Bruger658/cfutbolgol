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
        'paid_months',
        'is_up_to_date',
    ];

    protected $casts = [
        'paid_months' => 'array',
        'is_up_to_date' => 'boolean',
    ];


 public function getMissingMonthsAttribute(): array
    {
        $paidMonths = collect($this->paid_months ?? [])
            ->map(fn ($month) => (int) $month)
            ->filter(fn (int $month) => $month >= 1 && $month <= 12)
            ->unique()
            ->values()
            ->all();

        $expectedMonths = range(1, 12);

        return array_values(array_diff($expectedMonths, $paidMonths));
    }
}