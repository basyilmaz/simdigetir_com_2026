<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class DeliveryProof extends OrderProof
{
    protected static function booted(): void
    {
        static::addGlobalScope('delivery_stage', function (Builder $builder): void {
            $builder->where('stage', 'delivery');
        });

        static::creating(function (DeliveryProof $proof): void {
            $proof->stage = 'delivery';
        });
    }
}
