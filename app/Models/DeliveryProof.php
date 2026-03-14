<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryProof extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'courier_id',
        'proof_type',
        'proof_value',
        'file_url',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(Courier::class);
    }
}

