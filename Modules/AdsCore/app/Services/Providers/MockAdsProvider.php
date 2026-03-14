<?php

namespace Modules\AdsCore\Services\Providers;

use Modules\AdsCore\Contracts\AdsProviderInterface;
use Modules\AdsCore\Models\AdCampaign;
use Modules\AdsCore\Models\AdConnection;
use Modules\AdsCore\Models\AdConversion;

class MockAdsProvider implements AdsProviderInterface
{
    public function platform(): string
    {
        return 'mock';
    }

    public function connect(AdConnection $connection): array
    {
        return [
            'success' => true,
            'external_account_id' => $connection->external_account_id ?: 'mock-account-'.now()->timestamp,
        ];
    }

    public function refreshToken(AdConnection $connection): array
    {
        return [
            'success' => true,
            'token_expires_at' => now()->addDays(30)->toIso8601String(),
        ];
    }

    public function createCampaign(AdCampaign $campaign): array
    {
        return [
            'success' => true,
            'external_campaign_id' => $campaign->external_campaign_id ?: 'mock-campaign-'.$campaign->id,
            'status' => 'active',
        ];
    }

    public function updateCampaign(AdCampaign $campaign): array
    {
        return [
            'success' => true,
            'external_campaign_id' => $campaign->external_campaign_id ?: 'mock-campaign-'.$campaign->id,
            'status' => $campaign->status,
        ];
    }

    public function pauseCampaign(AdCampaign $campaign): array
    {
        return [
            'success' => true,
            'external_campaign_id' => $campaign->external_campaign_id ?: 'mock-campaign-'.$campaign->id,
            'status' => 'paused',
        ];
    }

    public function pushConversion(AdConversion $conversion): array
    {
        return [
            'success' => true,
            'status' => 'sent',
            'external_id' => $conversion->external_id ?: 'mock-conversion-'.$conversion->id,
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
