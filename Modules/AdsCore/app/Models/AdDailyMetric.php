<?php

namespace Modules\AdsCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdDailyMetric extends Model
{
    protected $fillable = [
        'metric_date',
        'platform',
        'ad_campaign_id',
        'campaign_name',
        'spend',
        'impressions',
        'clicks',
        'leads',
        'revenue',
        'roas',
        'meta',
    ];

    protected $casts = [
        'metric_date' => 'date',
        'spend' => 'decimal:2',
        'revenue' => 'decimal:2',
        'roas' => 'decimal:4',
        'meta' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(AdCampaign::class, 'ad_campaign_id');
    }
}
