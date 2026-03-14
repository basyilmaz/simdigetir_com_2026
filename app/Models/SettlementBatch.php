<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SettlementBatch extends Model
{
    protected $fillable = [
        'batch_no',
        'status',
        'net_amount',
        'currency',
        'notes',
        'closed_at',
        'paid_at',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function walletEntries(): HasMany
    {
        return $this->hasMany(CourierWalletEntry::class);
    }
}

