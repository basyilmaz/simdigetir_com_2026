<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderPackage extends Model
{
    protected $fillable = [
        'order_id',
        'package_type',
        'quantity',
        'weight_grams',
        'length_cm',
        'width_cm',
        'height_cm',
        'declared_value_amount',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

