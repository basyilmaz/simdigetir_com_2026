<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AdsCore\Models\AdConnection;
use Modules\AdsCore\Models\AdSyncLog;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdsSprint5GovernanceHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_ads_permissions_are_seeded_and_assigned_to_roles(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $support = User::factory()->create();

        $admin->assignRole(Role::findByName('admin', 'web'));
        $support->assignRole(Role::findByName('support', 'web'));

        $this->assertTrue($admin->hasPermissionTo('ads.view'));
        $this->assertTrue($admin->hasPermissionTo('ads.publish'));
        $this->assertFalse($support->hasPermissionTo('ads.publish'));
        $this->assertTrue($support->hasPermissionTo('ads.view'));
    }

    public function test_ads_health_check_reports_expiring_tokens_and_failed_syncs(): void
    {
        AdConnection::query()->create([
            'platform' => 'google',
            'name' => 'Risky Connection',
            'status' => 'connected',
            'token_expires_at' => now()->addHours(6),
        ]);

        AdSyncLog::query()->create([
            'platform' => 'meta',
            'action' => 'push_conversion_capi',
            'status' => 'failed',
            'target_type' => 'conversion',
            'target_id' => '22',
            'error_message' => 'timeout',
            'attempt_count' => 3,
            'processed_at' => now(),
        ]);

        $this->artisan('ads:health-check --hours=24')
            ->expectsOutputToContain('expiring_tokens=1')
            ->expectsOutputToContain('failed_syncs=1')
            ->expectsOutputToContain('ads_health=degraded')
            ->assertFailed();
    }
}
