<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStateLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'from_state',
        'to_state',
        'actor_type',
        'actor_id',
        'reason',
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
}

