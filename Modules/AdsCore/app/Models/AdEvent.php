<?php

namespace Modules\AdsCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdEvent extends Model
{
    protected $fillable = [
        'event_name',
        'source',
        'medium',
        'campaign',
        'lead_id',
        'order_id',
        'payment_id',
        'value',
        'currency',
        'external_id',
        'gclid',
        'fbclid',
        'payload',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'payload' => 'array',
    ];

    public function conversions(): HasMany
    {
        return $this->hasMany(AdConversion::class);
    }
}
