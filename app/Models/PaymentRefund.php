<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentRefund extends Model
{
    protected $fillable = [
        'payment_transaction_id',
        'order_id',
        'amount',
        'currency',
        'status',
        'provider_reference',
        'reason',
        'metadata',
        'processed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'processed_at' => 'datetime',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class, 'payment_transaction_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

