<?php

namespace Modules\AdsCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdAd extends Model
{
    protected $table = 'ad_ads';

    protected $fillable = [
        'ad_campaign_id',
        'ad_adset_id',
        'platform',
        'name',
        'status',
        'external_ad_id',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(AdCampaign::class, 'ad_campaign_id');
    }

    public function adset(): BelongsTo
    {
        return $this->belongsTo(AdAdset::class, 'ad_adset_id');
    }

    public function creatives(): HasMany
    {
        return $this->hasMany(AdCreative::class, 'ad_ad_id');
    }
}
