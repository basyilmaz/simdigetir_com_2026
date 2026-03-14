<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdsPlatformFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_ads_core_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('ad_connections'));
        $this->assertTrue(Schema::hasTable('ad_campaigns'));
        $this->assertTrue(Schema::hasTable('ad_events'));
        $this->assertTrue(Schema::hasTable('ad_conversions'));
        $this->assertTrue(Schema::hasTable('ad_sync_logs'));
    }

    public function test_lead_submission_creates_ad_event_and_conversion(): void
    {
        $response = $this->postJson('/api/leads', [
            'type' => 'contact',
            'name' => 'Ads Test',
            'phone' => '05555555555',
            'email' => 'ads-test@example.com',
            'utm_source' => 'google',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'search_brand',
            'page_url' => 'https://simdigetir.com',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('ad_events', [
            'event_name' => 'lead_submitted',
            'source' => 'google',
            'medium' => 'cpc',
            'campaign' => 'search_brand',
        ]);

        $this->assertDatabaseHas('ad_conversions', [
            'platform' => 'google',
            'event_name' => 'lead_submitted',
            'status' => 'pending',
        ]);
    }
}
