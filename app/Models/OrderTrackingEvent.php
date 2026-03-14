<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTrackingEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'courier_id',
        'event_type',
        'lat',
        'lng',
        'eta_seconds',
        'note',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'eta_seconds' => 'integer',
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

