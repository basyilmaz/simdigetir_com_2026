<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourierAvailability extends Model
{
    protected $fillable = [
        'courier_id',
        'is_online',
        'zone',
        'lat',
        'lng',
        'active_load',
        'last_seen_at',
    ];

    protected $casts = [
        'is_online' => 'boolean',
        'lat' => 'float',
        'lng' => 'float',
        'active_load' => 'integer',
        'last_seen_at' => 'datetime',
    ];

    public function courier(): BelongsTo
    {
        return $this->belongsTo(Courier::class);
    }
}

