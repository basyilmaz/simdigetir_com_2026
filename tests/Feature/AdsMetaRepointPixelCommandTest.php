<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AdsCore\Models\AdCampaign;
use Modules\AdsCore\Models\AdConnection;
use Modules\Settings\Models\Setting;
use Tests\TestCase;

class AdsMetaRepointPixelCommandTest extends TestCase
{
    use RefreshDatabase;

    private const OLD = '1111111111';

    private const NEW = '2222222222';

    private const OTHER = '3333333333';

    private function seedRecords(): array
    {
        $match = AdConnection::query()->create([
            'platform' => 'meta', 'name' => 'Match', 'status' => 'connected',
            'meta' => ['pixel_id' => self::OLD, 'capi_enabled' => true],
        ]);
        $other = AdConnection::query()->create([
            'platform' => 'meta', 'name' => 'Other', 'status' => 'connected',
            'meta' => ['pixel_id' => self::OTHER],
        ]);
        $campaign = AdCampaign::query()->create([
            'ad_connection_id' => $match->id, 'platform' => 'meta', 'name' => 'Campaign', 'status' => 'active',
            'meta' => ['pixel_id' => self::OLD, 'note' => 'keep'],
        ]);
        Setting::setValue('marketing.meta_pixel_id', self::OLD, 'marketing');

        return [$match, $other, $campaign];
    }

    public function test_dry_run_reports_counts_without_writing(): void
    {
        [$match, , $campaign] = $this->seedRecords();

        $this->artisan('ads:meta-repoint-pixel', ['old' => self::OLD, 'new' => self::NEW, '--dry-run' => true])
            ->expectsOutputToContain('[dry-run] affected AdConnection: 1')
            ->expectsOutputToContain('[dry-run] affected Campaign: 1')
            ->expectsOutputToContain('[dry-run] affected Setting (marketing.meta_pixel_id): 1')
            ->assertSuccessful();

        $this->assertSame(self::OLD, $match->fresh()->meta['pixel_id']);
        $this->assertSame(self::OLD, $campaign->fresh()->meta['pixel_id']);
        $this->assertSame(self::OLD, (string) Setting::getValue('marketing.meta_pixel_id'));
    }

    public function test_repoints_only_matching_records_and_keeps_other_meta(): void
    {
        [$match, $other, $campaign] = $this->seedRecords();

        $this->artisan('ads:meta-repoint-pixel', ['old' => self::OLD, 'new' => self::NEW])->assertSuccessful();

        $this->assertSame(self::NEW, $match->fresh()->meta['pixel_id']);
        $this->assertTrue($match->fresh()->meta['capi_enabled']);
        $this->assertSame(self::OTHER, $other->fresh()->meta['pixel_id']);
        $this->assertSame(self::NEW, $campaign->fresh()->meta['pixel_id']);
        $this->assertSame('keep', $campaign->fresh()->meta['note']);
        $this->assertSame(self::NEW, (string) Setting::getValue('marketing.meta_pixel_id'));
    }

    public function test_second_run_is_idempotent(): void
    {
        $this->seedRecords();
        $this->artisan('ads:meta-repoint-pixel', ['old' => self::OLD, 'new' => self::NEW])->assertSuccessful();

        $this->artisan('ads:meta-repoint-pixel', ['old' => self::OLD, 'new' => self::NEW])
            ->expectsOutputToContain('updated AdConnection: 0')
            ->expectsOutputToContain('updated Campaign: 0')
            ->expectsOutputToContain('updated Setting (marketing.meta_pixel_id): 0')
            ->assertSuccessful();
    }

    public function test_rejects_invalid_ids(): void
    {
        $this->artisan('ads:meta-repoint-pixel', ['old' => 'abc', 'new' => self::NEW])->assertExitCode(2);
        $this->artisan('ads:meta-repoint-pixel', ['old' => self::OLD, 'new' => self::OLD])->assertExitCode(2);
    }

    public function test_home_renders_domain_verification_tag_server_side(): void
    {
        $this->get('/')->assertStatus(200)
            ->assertSee('<meta name="facebook-domain-verification" content="pob8lhq6fp8jh6dykxwekd13mnxth4" />', false);
    }
}
