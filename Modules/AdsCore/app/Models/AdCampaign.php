<?php

namespace Modules\AdsCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdCampaign extends Model
{
    protected $fillable = [
        'ad_connection_id',
        'platform',
        'name',
        'objective',
        'status',
        'external_campaign_id',
        'daily_budget',
        'currency',
        'targeting',
        'meta',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'daily_budget' => 'decimal:2',
        'targeting' => 'array',
        'meta' => 'array',
    ];

    public function connection(): BelongsTo
    {
        return $this->belongsTo(AdConnection::class, 'ad_connection_id');
    }

    public function conversions(): HasMany
    {
        return $this->hasMany(AdConversion::class);
    }

    public function adsets(): HasMany
    {
        return $this->hasMany(AdAdset::class, 'ad_campaign_id');
    }

    public function ads(): HasMany
    {
        return $this->hasMany(AdAd::class, 'ad_campaign_id');
    }
}
