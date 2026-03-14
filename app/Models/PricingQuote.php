<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PricingQuote extends Model
{
    protected $fillable = [
        'quote_no',
        'customer_id',
        'request_snapshot',
        'resolved_rules',
        'subtotal_amount',
        'discount_amount',
        'surge_amount',
        'total_amount',
        'currency',
        'expires_at',
    ];

    protected $casts = [
        'request_snapshot' => 'array',
        'resolved_rules' => 'array',
        'expires_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }
}

