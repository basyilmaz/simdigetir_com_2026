<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'order_id',
        'pricing_quote_id',
        'provider',
        'provider_reference',
        'amount',
        'currency',
        'status',
        'request_payload',
        'callback_payload',
        'processed_at',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'callback_payload' => 'array',
        'processed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function pricingQuote(): BelongsTo
    {
        return $this->belongsTo(PricingQuote::class);
    }

    public function reconciliations(): HasMany
    {
        return $this->hasMany(PaymentReconciliation::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(PaymentRefund::class);
    }
}
