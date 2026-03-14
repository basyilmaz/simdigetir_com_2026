<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReconciliation extends Model
{
    protected $fillable = [
        'payment_transaction_id',
        'provider_status',
        'internal_status',
        'is_match',
        'notes',
        'reconciled_at',
    ];

    protected $casts = [
        'is_match' => 'boolean',
        'reconciled_at' => 'datetime',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class, 'payment_transaction_id');
    }
}

