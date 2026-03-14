<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AdsCore\Models\AdCampaign;
use Modules\AdsCore\Models\AdConnection;
use Modules\AdsCore\Models\AdConversion;
use Modules\AdsCore\Models\AdEvent;
use Modules\AdsCore\Services\CampaignLifecycleService;
use Modules\AdsGoogle\Services\GoogleOAuthService;
use Tests\TestCase;

class AdsSprint2GoogleIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_campaign_publish_and_pause_flow(): void
    {
        $connection = AdConnection::query()->create([
            'platform' => 'google',
            'name' => 'Google Main',
            'status' => 'draft',
        ]);

        $campaign = AdCampaign::query()->create([
            'ad_connection_id' => $connection->id,
            'platform' => 'google',
            'name' => 'Search Campaign',
            'status' => 'draft',
        ]);

        /** @var CampaignLifecycleService $lifecycle */
        $lifecycle = app(CampaignLifecycleService::class);
        $publish = $lifecycle->publishCampaign($campaign);
        $pause = $lifecycle->pauseCampaign($campaign->fresh());

        $this->assertTrue((bool) ($publish['success'] ?? false));
        $this->assertTrue((bool) ($pause['success'] ?? false));
        $this->assertDatabaseHas('ad_campaigns', [
            'id' => $campaign->id,
            'status' => 'paused',
        ]);
        $this->assertDatabaseHas('ad_sync_logs', [
            'platform' => 'google',
            'action' => 'publish_campaign',
            'status' => 'success',
        ]);
        $this->assertDatabaseHas('ad_sync_logs', [
            'platform' => 'google',
            'action' => 'pause_campaign',
            'status' => 'success',
        ]);
    }

    public function test_google_conversion_push_is_idempotent_by_external_id(): void
    {
        $event = AdEvent::query()->create([
            'event_name' => 'lead_submitted',
            'source' => 'google',
        ]);

        $first = AdConversion::query()->create([
            'ad_event_id' => $event->id,
            'platform' => 'google',
            'event_name' => 'lead_submitted',
            'status' => 'pending',
            'external_id' => 'g-ext-123',
        ]);

        $second = AdConversion::query()->create([
            'ad_event_id' => $event->id,
            'platform' => 'google',
            'event_name' => 'lead_submitted',
            'status' => 'pending',
            'external_id' => 'g-ext-123',
        ]);

        /** @var CampaignLifecycleService $lifecycle */
        $lifecycle = app(CampaignLifecycleService::class);
        $lifecycle->pushConversion($first);
        $response = $lifecycle->pushConversion($second->fresh());

        $this->assertSame('skipped', $response['status']);
        $this->assertDatabaseHas('ad_conversions', [
            'id' => $first->id,
            'status' => 'sent',
        ]);
        $this->assertDatabaseHas('ad_conversions', [
            'id' => $second->id,
            'status' => 'skipped',
        ]);
    }

    public function test_google_oauth_connection_flow_sets_tokens(): void
    {
        $connection = AdConnection::query()->create([
            'platform' => 'google',
            'name' => 'OAuth Connection',
            'status' => 'draft',
        ]);

        /** @var GoogleOAuthService $oauth */
        $oauth = app(GoogleOAuthService::class);
        $begin = $oauth->beginConnection($connection, 'https://simdigetir.com/oauth/callback');
        $complete = $oauth->completeConnection($connection->fresh(), $begin['state'], 'auth-code-123');

        $this->assertTrue((bool) ($complete['success'] ?? false));
        $this->assertArrayHasKey('auth_url', $begin);
        $this->assertDatabaseHas('ad_connections', [
            'id' => $connection->id,
            'status' => 'connected',
        ]);

        $saved = $connection->fresh();
        $this->assertNotEmpty($saved?->access_token);
        $this->assertNotEmpty($saved?->refresh_token);
    }
}
