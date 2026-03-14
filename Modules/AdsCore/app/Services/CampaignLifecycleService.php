<?php

namespace Modules\AdsCore\Services;

use Modules\AdsCore\Models\AdCampaign;
use Modules\AdsCore\Models\AdAd;
use Modules\AdsCore\Models\AdAdset;
use Modules\AdsCore\Models\AdCreative;
use Modules\AdsCore\Models\AdConversion;
use Modules\AdsCore\Models\AdSyncLog;

class CampaignLifecycleService
{
    public function __construct(private readonly AdsProviderManager $providers)
    {
    }

    public function publishCampaign(AdCampaign $campaign): array
    {
        $provider = $this->providers->forPlatform($campaign->platform);
        $response = $provider->createCampaign($campaign);

        $campaign->forceFill([
            'external_campaign_id' => $response['external_campaign_id'] ?? $campaign->external_campaign_id,
            'status' => $response['status'] ?? ($response['success'] ?? false ? 'active' : 'failed'),
        ])->save();

        if ($campaign->platform === 'meta' && ($response['success'] ?? false)) {
            $adset = AdAdset::query()->firstOrCreate(
                [
                    'ad_campaign_id' => $campaign->id,
                    'external_adset_id' => $response['external_adset_id'] ?? null,
                ],
                [
                    'platform' => 'meta',
                    'name' => $campaign->name.' - Adset',
                    'status' => 'active',
                    'targeting' => $campaign->targeting ?? [],
                ]
            );

            $ad = AdAd::query()->firstOrCreate(
                [
                    'ad_campaign_id' => $campaign->id,
                    'external_ad_id' => $response['external_ad_id'] ?? null,
                ],
                [
                    'ad_adset_id' => $adset->id,
                    'platform' => 'meta',
                    'name' => $campaign->name.' - Ad',
                    'status' => 'active',
                ]
            );

            AdCreative::query()->firstOrCreate(
                [
                    'ad_ad_id' => $ad->id,
                    'external_creative_id' => $response['external_creative_id'] ?? null,
                ],
                [
                    'platform' => 'meta',
                    'name' => $campaign->name.' - Creative',
                    'status' => 'active',
                    'payload' => [],
                ]
            );
        }

        $this->logSync('publish_campaign', $campaign->platform, $campaign->id, $response['success'] ?? false, [
            'campaign' => $campaign->only(['id', 'platform', 'name', 'status']),
        ], $response);

        return $response;
    }

    public function pauseCampaign(AdCampaign $campaign): array
    {
        $provider = $this->providers->forPlatform($campaign->platform);
        $response = $provider->pauseCampaign($campaign);

        $campaign->forceFill([
            'status' => $response['status'] ?? 'paused',
        ])->save();

        $this->logSync('pause_campaign', $campaign->platform, $campaign->id, $response['success'] ?? false, [
            'campaign' => $campaign->only(['id', 'platform', 'name', 'status']),
        ], $response);

        return $response;
    }

    public function pushConversion(AdConversion $conversion): array
    {
        $existingSent = AdConversion::query()
            ->where('platform', $conversion->platform)
            ->where('event_name', $conversion->event_name)
            ->where('external_id', $conversion->external_id)
            ->where('status', 'sent')
            ->where('id', '!=', $conversion->id)
            ->exists();

        if ($conversion->external_id !== null && $conversion->external_id !== '' && $existingSent) {
            $conversion->forceFill([
                'status' => 'skipped',
                'response_payload' => ['idempotent' => true, 'reason' => 'external_id_already_sent'],
            ])->save();

            $this->logSync('push_conversion', $conversion->platform, $conversion->id, true, [
                'conversion' => $conversion->only(['id', 'platform', 'event_name', 'external_id']),
            ], ['idempotent' => true, 'status' => 'skipped']);

            return ['success' => true, 'idempotent' => true, 'status' => 'skipped'];
        }

        $provider = $this->providers->forPlatform($conversion->platform);
        $response = $provider->pushConversion($conversion);

        $success = (bool) ($response['success'] ?? false);
        $conversion->forceFill([
            'status' => $success ? 'sent' : 'failed',
            'pushed_at' => $success ? now() : null,
            'response_payload' => $response,
        ])->save();

        $action = $conversion->platform === 'meta' ? 'push_conversion_capi' : 'push_conversion';
        $this->logSync($action, $conversion->platform, $conversion->id, $success, [
            'conversion' => $conversion->only(['id', 'platform', 'event_name', 'external_id']),
        ], $response);

        return $response;
    }

    public function pushConversionById(int $conversionId): array
    {
        $conversion = AdConversion::query()->findOrFail($conversionId);

        return $this->pushConversion($conversion);
    }

    private function logSync(string $action, string $platform, int $targetId, bool $success, array $request, array $response): void
    {
        AdSyncLog::query()->create([
            'platform' => $platform,
            'action' => $action,
            'status' => $success ? 'success' : 'failed',
            'target_type' => 'conversion_or_campaign',
            'target_id' => (string) $targetId,
            'request_payload' => $request,
            'response_payload' => $response,
            'attempt_count' => 1,
            'processed_at' => now(),
        ]);
    }
}
