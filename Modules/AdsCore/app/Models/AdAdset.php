<?php

namespace Modules\AdsCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdAdset extends Model
{
    protected $fillable = [
        'ad_campaign_id',
        'platform',
        'name',
        'status',
        'external_adset_id',
        'targeting',
        'meta',
    ];

    protected $casts = [
        'targeting' => 'array',
        'meta' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(AdCampaign::class, 'ad_campaign_id');
    }

    public function ads(): HasMany
    {
        return $this->hasMany(AdAd::class, 'ad_adset_id');
    }
}
