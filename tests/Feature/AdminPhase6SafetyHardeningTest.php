<?php

namespace Tests\Feature;

use App\Filament\Resources\AdminAuditLogResource;
use App\Models\AdminAuditLog;
use App\Models\Courier;
use App\Models\SupportTicket;
use App\Models\User;
use App\Support\BulkActionRateLimiter;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\RateLimiter;
use Modules\Leads\Models\Lead;
use Tests\TestCase;

class AdminPhase6SafetyHardeningTest extends TestCase
{
    public function test_soft_deleted_models_are_hidden_by_default_query(): void
    {
        $lead = Lead::query()->create([
            'type' => 'contact',
            'name' => 'Lead Test',
            'phone' => '05550000000',
            'status' => 'new',
        ]);

        $courier = Courier::query()->create([
            'full_name' => 'Kurye Test',
            'phone' => '05551112233',
            'status' => 'pending',
        ]);

        $ticket = SupportTicket::query()->create([
            'ticket_no' => 'TST-0001',
            'status' => 'open',
            'priority' => 'normal',
            'subject' => 'Test Konu',
            'description' => 'Test Açıklama',
        ]);

        $lead->delete();
        $courier->delete();
        $ticket->delete();

        $this->assertSame(0, Lead::query()->whereKey($lead->id)->count());
        $this->assertSame(1, Lead::withTrashed()->whereKey($lead->id)->count());

        $this->assertSame(0, Courier::query()->whereKey($courier->id)->count());
        $this->assertSame(1, Courier::withTrashed()->whereKey($courier->id)->count());

        $this->assertSame(0, SupportTicket::query()->whereKey($ticket->id)->count());
        $this->assertSame(1, SupportTicket::withTrashed()->whereKey($ticket->id)->count());
    }

    public function test_bulk_action_rate_limiter_blocks_after_threshold(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $key = 'filament:bulk-action:test-phase6-rate-limit:'.$user->id;
        RateLimiter::clear($key);

        $this->assertTrue(BulkActionRateLimiter::allow('test-phase6-rate-limit', 1, 60));
        $this->assertFalse(BulkActionRateLimiter::allow('test-phase6-rate-limit', 1, 60));
    }

    public function test_admin_audit_log_access_is_bound_to_reports_permission(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $support = User::factory()->create();
        $support->assignRole('support');

        $this->assertTrue($admin->can('viewAny', AdminAuditLog::class));
        $this->assertFalse($support->can('viewAny', AdminAuditLog::class));
        $this->assertArrayHasKey('index', AdminAuditLogResource::getPages());
    }
}
