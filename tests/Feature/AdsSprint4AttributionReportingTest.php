<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AdsCore\Models\AdCampaign;
use Modules\AdsCore\Models\AdConnection;
use Modules\AdsCore\Models\AdConversion;
use Modules\AdsCore\Models\AdDailyMetric;
use Modules\Reporting\Services\AdsReportingService;
use Tests\TestCase;

class AdsSprint4AttributionReportingTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_endpoint_uses_attribution_resolver_defaults(): void
    {
        $response = $this->withHeaders([
            'referer' => 'https://www.google.com/search?q=simdigetir',
        ])->postJson('/api/leads', [
            'type' => 'contact',
            'name' => 'Attribution Test',
            'phone' => '05555555555',
            'gclid' => 'gclid-123',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('leads', [
            'name' => 'Attribution Test',
            'source' => 'google',
            'medium' => 'cpc',
        ]);
    }

    public function test_lead_with_fbclid_defaults_to_meta_platform_conversion(): void
    {
        $response = $this->postJson('/api/leads', [
            'type' => 'contact',
            'name' => 'Meta Attribution Test',
            'phone' => '05555555555',
            'fbclid' => 'fbclid-123',
            'page_url' => 'https://simdigetir.com',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('ad_events', [
            'event_name' => 'lead_submitted',
            'source' => null,
            'medium' => 'cpc',
            'fbclid' => 'fbclid-123',
        ]);

        $this->assertDatabaseHas('ad_conversions', [
            'platform' => 'meta',
            'event_name' => 'lead_submitted',
            'status' => 'pending',
        ]);
    }

    public function test_reporting_dashboard_filters_by_date_platform_and_campaign(): void
    {
        $googleConnection = AdConnection::query()->create([
            'platform' => 'google',
            'name' => 'Google',
            'status' => 'connected',
        ]);
        $metaConnection = AdConnection::query()->create([
            'platform' => 'meta',
            'name' => 'Meta',
            'status' => 'connected',
        ]);

        $googleCampaign = AdCampaign::query()->create([
            'ad_connection_id' => $googleConnection->id,
            'platform' => 'google',
            'name' => 'Google Search',
            'daily_budget' => 100,
            'status' => 'active',
        ]);
        $metaCampaign = AdCampaign::query()->create([
            'ad_connection_id' => $metaConnection->id,
            'platform' => 'meta',
            'name' => 'Meta Leads',
            'daily_budget' => 120,
            'status' => 'active',
        ]);

        AdConversion::query()->create([
            'ad_campaign_id' => $googleCampaign->id,
            'platform' => 'google',
            'event_name' => 'lead_submitted',
            'status' => 'sent',
            'value' => 300,
            'created_at' => now()->startOfDay(),
            'updated_at' => now()->startOfDay(),
        ]);
        AdConversion::query()->create([
            'ad_campaign_id' => $metaCampaign->id,
            'platform' => 'meta',
            'event_name' => 'lead_submitted',
            'status' => 'sent',
            'value' => 240,
            'created_at' => now()->startOfDay(),
            'updated_at' => now()->startOfDay(),
        ]);

        /** @var AdsReportingService $reporting */
        $reporting = app(AdsReportingService::class);
        $reporting->aggregateDailyMetrics(now()->toDateString());

        $googleOnly = $reporting->performanceDashboard([
            'from' => now()->toDateString(),
            'to' => now()->toDateString(),
            'platform' => 'google',
            'campaign_id' => $googleCampaign->id,
        ]);

        $this->assertSame(1, count($googleOnly['rows']));
        $this->assertSame('google', $googleOnly['rows'][0]['platform']);
        $this->assertSame($googleCampaign->id, $googleOnly['rows'][0]['campaign_id']);
        $this->assertSame(3.0, $googleOnly['totals']['roas']);
        $this->assertDatabaseHas('ad_daily_metrics', [
            'platform' => 'google',
            'ad_campaign_id' => $googleCampaign->id,
        ]);
        $this->assertDatabaseHas('ad_daily_metrics', [
            'platform' => 'meta',
            'ad_campaign_id' => $metaCampaign->id,
        ]);
    }

    public function test_meta_connection_can_enable_auto_push_without_env_override(): void
    {
        config()->set('adscore.conversion.auto_push', false);
        config()->set('adscore.conversion.auto_push_mode', 'sync');
        config()->set('adscore.conversion.auto_push_platforms', ['meta']);

        AdConnection::query()->create([
            'platform' => 'meta',
            'name' => 'Meta Runtime',
            'status' => 'connected',
            'meta' => [
                'auto_push' => true,
                'auto_push_mode' => 'sync',
                'auto_push_platforms' => ['meta'],
            ],
        ]);

        $response = $this->postJson('/api/leads', [
            'type' => 'contact',
            'name' => 'Meta Auto Push Runtime Test',
            'phone' => '05555555555',
            'fbclid' => 'fbclid-auto-push-1',
            'page_url' => 'https://simdigetir.com',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('ad_conversions', [
            'platform' => 'meta',
            'event_name' => 'lead_submitted',
            'status' => 'sent',
        ]);
    }
}
