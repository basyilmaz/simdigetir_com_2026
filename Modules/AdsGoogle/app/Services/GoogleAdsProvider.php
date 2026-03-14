<?php

namespace Modules\AdsGoogle\Services;

use Modules\AdsCore\Contracts\AdsProviderInterface;
use Modules\AdsCore\Models\AdCampaign;
use Modules\AdsCore\Models\AdConnection;
use Modules\AdsCore\Models\AdConversion;

class GoogleAdsProvider implements AdsProviderInterface
{
    public function platform(): string
    {
        return 'google';
    }

    public function connect(AdConnection $connection): array
    {
        return [
            'success' => true,
            'external_account_id' => $connection->external_account_id ?: 'google-'.$connection->id,
        ];
    }

    public function refreshToken(AdConnection $connection): array
    {
        $fingerprint = substr(sha1((string) $connection->id.(string) now()->timestamp), 0, 20);

        return [
            'success' => true,
            'access_token' => 'google-access-'.$fingerprint,
            'refresh_token' => 'google-refresh-'.$fingerprint,
            'token_expires_at' => now()->addDays(30)->toIso8601String(),
        ];
    }

    public function createCampaign(AdCampaign $campaign): array
    {
        return [
            'success' => true,
            'external_campaign_id' => $campaign->external_campaign_id ?: 'gads-'.$campaign->id,
            'status' => 'active',
        ];
    }

    public function updateCampaign(AdCampaign $campaign): array
    {
        return [
            'success' => true,
            'external_campaign_id' => $campaign->external_campaign_id ?: 'gads-'.$campaign->id,
            'status' => $campaign->status,
        ];
    }

    public function pauseCampaign(AdCampaign $campaign): array
    {
        return [
            'success' => true,
            'external_campaign_id' => $campaign->external_campaign_id ?: 'gads-'.$campaign->id,
            'status' => 'paused',
        ];
    }

    public function pushConversion(AdConversion $conversion): array
    {
        return [
            'success' => true,
            'status' => 'sent',
            'external_id' => $conversion->external_id ?: 'gconv-'.$conversion->id,
        ];
    }

    public function fetchInsights(array $filters = []): array
    {
        return [
            'success' => true,
            'rows' => [],
            'filters' => $filters,
        ];
    }
}
