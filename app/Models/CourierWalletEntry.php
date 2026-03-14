<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourierWalletEntry extends Model
{
    protected $fillable = [
        'courier_id',
        'order_id',
        'order_assignment_id',
        'settlement_batch_id',
        'entry_type',
        'amount',
        'balance_after',
        'currency',
        'metadata',
        'entry_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'entry_at' => 'datetime',
    ];

    public function courier(): BelongsTo
    {
        return $this->belongsTo(Courier::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(OrderAssignment::class, 'order_assignment_id');
    }

    public function settlementBatch(): BelongsTo
    {
        return $this->belongsTo(SettlementBatch::class);
    }
}

