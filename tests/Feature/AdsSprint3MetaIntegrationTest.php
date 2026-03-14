<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Modules\AdsCore\Models\AdCampaign;
use Modules\AdsCore\Models\AdConnection;
use Modules\AdsCore\Models\AdConversion;
use Modules\AdsCore\Models\AdEvent;
use Modules\AdsCore\Services\CampaignLifecycleService;
use Tests\TestCase;

class AdsSprint3MetaIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_meta_publish_creates_adset_ad_and_creative_records(): void
    {
        $connection = AdConnection::query()->create([
            'platform' => 'meta',
            'name' => 'Meta Main',
            'status' => 'connected',
        ]);

        $campaign = AdCampaign::query()->create([
            'ad_connection_id' => $connection->id,
            'platform' => 'meta',
            'name' => 'Meta Conversion Campaign',
            'status' => 'draft',
        ]);

        /** @var CampaignLifecycleService $lifecycle */
        $lifecycle = app(CampaignLifecycleService::class);
        $response = $lifecycle->publishCampaign($campaign);

        $this->assertTrue((bool) ($response['success'] ?? false));
        $this->assertDatabaseHas('ad_campaigns', [
            'id' => $campaign->id,
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('ad_adsets', [
            'ad_campaign_id' => $campaign->id,
            'platform' => 'meta',
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('ad_ads', [
            'ad_campaign_id' => $campaign->id,
            'platform' => 'meta',
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('ad_creatives', [
            'platform' => 'meta',
            'status' => 'active',
        ]);
    }

    public function test_meta_conversion_push_writes_capi_sync_log(): void
    {
        $event = AdEvent::query()->create([
            'event_name' => 'lead_submitted',
            'source' => 'facebook',
        ]);

        $conversion = AdConversion::query()->create([
            'ad_event_id' => $event->id,
            'platform' => 'meta',
            'event_name' => 'lead_submitted',
            'status' => 'pending',
            'external_id' => 'meta-ext-001',
        ]);

        /** @var CampaignLifecycleService $lifecycle */
        $lifecycle = app(CampaignLifecycleService::class);
        $response = $lifecycle->pushConversion($conversion);

        $this->assertTrue((bool) ($response['success'] ?? false));
        $this->assertArrayHasKey('capi_event_id', $response);
        $this->assertDatabaseHas('ad_sync_logs', [
            'platform' => 'meta',
            'action' => 'push_conversion_capi',
            'status' => 'success',
            'target_id' => (string) $conversion->id,
        ]);
    }

    public function test_meta_conversion_push_uses_graph_api_when_credentials_are_configured(): void
    {
        config()->set('adscore.meta.enabled', true);
        config()->set('adscore.meta.pixel_id', '1657531168735846');
        config()->set('adscore.meta.access_token', 'test-access-token');
        config()->set('adscore.meta.graph_base_url', 'https://graph.facebook.com');
        config()->set('adscore.meta.graph_version', 'v22.0');

        Http::fake([
            'https://graph.facebook.com/v22.0/1657531168735846/events' => Http::response([
                'events_received' => 1,
                'fbtrace_id' => 'trace-123',
            ], 200),
        ]);

        $connection = AdConnection::query()->create([
            'platform' => 'meta',
            'name' => 'Meta Main',
            'status' => 'connected',
        ]);

        $campaign = AdCampaign::query()->create([
            'ad_connection_id' => $connection->id,
            'platform' => 'meta',
            'name' => 'Meta Conversion Campaign',
            'status' => 'active',
        ]);

        $event = AdEvent::query()->create([
            'event_name' => 'lead_submitted',
            'source' => 'meta',
            'payload' => [
                'lead_type' => 'contact',
                'page_url' => 'https://simdigetir.com/iletisim',
            ],
        ]);

        $conversion = AdConversion::query()->create([
            'ad_campaign_id' => $campaign->id,
            'ad_event_id' => $event->id,
            'platform' => 'meta',
            'event_name' => 'lead_submitted',
            'status' => 'pending',
            'external_id' => 'meta-ext-graph-001',
            'currency' => 'TRY',
        ]);

        /** @var CampaignLifecycleService $lifecycle */
        $lifecycle = app(CampaignLifecycleService::class);
        $response = $lifecycle->pushConversion($conversion);

        $this->assertTrue((bool) ($response['success'] ?? false));
        $this->assertSame('direct', $response['mode'] ?? null);
        $this->assertSame(1, (int) ($response['events_received'] ?? 0));

        Http::assertSent(function ($request): bool {
            $urlMatches = $request->url() === 'https://graph.facebook.com/v22.0/1657531168735846/events';
            $hasData = is_string($request['data'] ?? null) && str_contains((string) $request['data'], '"event_name":"Lead"');
            $hasToken = ($request['access_token'] ?? null) === 'test-access-token';

            return $urlMatches && $hasData && $hasToken;
        });
    }

    public function test_meta_conversion_push_uses_connection_meta_runtime_settings(): void
    {
        config()->set('adscore.meta.enabled', false);
        config()->set('adscore.meta.pixel_id', '');
        config()->set('adscore.meta.access_token', '');
        config()->set('adscore.meta.graph_base_url', 'https://graph.facebook.com');
        config()->set('adscore.meta.graph_version', 'v22.0');

        Http::fake([
            'https://graph.facebook.com/v22.0/1657531168735846/events' => Http::response([
                'events_received' => 1,
                'fbtrace_id' => 'trace-connection-123',
            ], 200),
        ]);

        $connection = AdConnection::query()->create([
            'platform' => 'meta',
            'name' => 'Meta Main',
            'status' => 'connected',
            'access_token' => 'connection-token-123',
            'meta' => [
                'capi_enabled' => true,
                'pixel_id' => '1657531168735846',
                'test_event_code' => 'TEST-EVENT-1',
            ],
        ]);

        $campaign = AdCampaign::query()->create([
            'ad_connection_id' => $connection->id,
            'platform' => 'meta',
            'name' => 'Meta Conversion Campaign',
            'status' => 'active',
        ]);

        $event = AdEvent::query()->create([
            'event_name' => 'lead_submitted',
            'source' => 'meta',
            'payload' => [
                'lead_type' => 'contact',
                'page_url' => 'https://simdigetir.com/iletisim',
            ],
        ]);

        $conversion = AdConversion::query()->create([
            'ad_campaign_id' => $campaign->id,
            'ad_event_id' => $event->id,
            'platform' => 'meta',
            'event_name' => 'lead_submitted',
            'status' => 'pending',
            'external_id' => 'meta-ext-graph-connection-001',
            'currency' => 'TRY',
        ]);

        /** @var CampaignLifecycleService $lifecycle */
        $lifecycle = app(CampaignLifecycleService::class);
        $response = $lifecycle->pushConversion($conversion);

        $this->assertTrue((bool) ($response['success'] ?? false));
        $this->assertSame('direct', $response['mode'] ?? null);
        $this->assertSame(1, (int) ($response['events_received'] ?? 0));

        Http::assertSent(function ($request): bool {
            $urlMatches = $request->url() === 'https://graph.facebook.com/v22.0/1657531168735846/events';
            $hasToken = ($request['access_token'] ?? null) === 'connection-token-123';
            $hasTestEventCode = ($request['test_event_code'] ?? null) === 'TEST-EVENT-1';

            return $urlMatches && $hasToken && $hasTestEventCode;
        });
    }
}
