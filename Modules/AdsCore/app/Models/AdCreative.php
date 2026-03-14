<?php

namespace Modules\AdsCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdCreative extends Model
{
    protected $fillable = [
        'ad_ad_id',
        'platform',
        'name',
        'status',
        'external_creative_id',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function ad(): BelongsTo
    {
        return $this->belongsTo(AdAd::class, 'ad_ad_id');
    }
}
