<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberFeePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'months',
        'base_amount',
        'surcharge_amount',
        'total_amount',
        'status',
        'payment_provider',
        'provider_reference',
        'provider_payment_id',
        'checkout_url',
        'paid_at',
    ];

    protected $casts = [
        'months' => 'array',
        'base_amount' => 'decimal:2',
        'surcharge_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}