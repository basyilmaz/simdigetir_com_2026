<?php

namespace Modules\AdsCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdConversion extends Model
{
    protected $fillable = [
        'ad_campaign_id',
        'ad_event_id',
        'platform',
        'event_name',
        'status',
        'value',
        'currency',
        'external_id',
        'response_payload',
        'pushed_at',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'response_payload' => 'array',
        'pushed_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(AdCampaign::class, 'ad_campaign_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(AdEvent::class, 'ad_event_id');
    }
}
