<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSettings;
use App\Filament\Widgets\ActivityStreamWidget;
use App\Filament\Widgets\FunnelOverviewWidget;
use App\Filament\Widgets\OpsAlertsWidget;
use App\Filament\Widgets\SeoHealthWidget;
use App\Filament\Widgets\SourceQualityWidget;
use App\Models\AdminAuditLog;
use App\Models\Courier;
use App\Models\FormSubmission;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\SupportTicket;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Modules\AdsCore\Models\AdCampaign;
use Modules\Landing\Models\LandingPage;
use Modules\Leads\Models\Lead;
use Tests\TestCase;

class AdminRoleAccessMatrixTest extends TestCase
{
    public function test_role_matrix_for_core_admin_models(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $matrix = [
            'admin' => [
                'lead' => true,
                'order' => true,
                'courier' => true,
                'support_ticket' => true,
                'payment' => true,
                'form_submission' => true,
                'audit_log' => true,
                'landing_page' => true,
                'ad_campaign' => true,
            ],
            'operations' => [
                'lead' => true,
                'order' => true,
                'courier' => true,
                'support_ticket' => true,
                'payment' => false,
                'form_submission' => false,
                'audit_log' => true,
                'landing_page' => false,
                'ad_campaign' => true,
            ],
            'support' => [
                'lead' => true,
                'order' => true,
                'courier' => false,
                'support_ticket' => true,
                'payment' => false,
                'form_submission' => false,
                'audit_log' => false,
                'landing_page' => false,
                'ad_campaign' => true,
            ],
            'finance' => [
                'lead' => true,
                'order' => false,
                'courier' => false,
                'support_ticket' => false,
                'payment' => true,
                'form_submission' => false,
                'audit_log' => true,
                'landing_page' => false,
                'ad_campaign' => true,
            ],
        ];

        foreach ($matrix as $role => $expected) {
            $user = User::factory()->create();
            $user->assignRole($role);
            $this->actingAs($user);

            $this->assertSame($expected['lead'], $user->can('viewAny', Lead::class), "Lead viewAny mismatch for {$role}");
            $this->assertSame($expected['order'], $user->can('viewAny', Order::class), "Order viewAny mismatch for {$role}");
            $this->assertSame($expected['courier'], $user->can('viewAny', Courier::class), "Courier viewAny mismatch for {$role}");
            $this->assertSame($expected['support_ticket'], $user->can('viewAny', SupportTicket::class), "SupportTicket viewAny mismatch for {$role}");
            $this->assertSame($expected['payment'], $user->can('viewAny', PaymentTransaction::class), "Payment viewAny mismatch for {$role}");
            $this->assertSame($expected['form_submission'], $user->can('viewAny', FormSubmission::class), "FormSubmission viewAny mismatch for {$role}");
            $this->assertSame($expected['audit_log'], $user->can('viewAny', AdminAuditLog::class), "AdminAuditLog viewAny mismatch for {$role}");
            $this->assertSame($expected['landing_page'], $user->can('viewAny', LandingPage::class), "LandingPage viewAny mismatch for {$role}");
            $this->assertSame($expected['ad_campaign'], $user->can('viewAny', AdCampaign::class), "AdCampaign viewAny mismatch for {$role}");
        }
    }

    public function test_role_matrix_for_dashboard_widget_visibility(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $matrix = [
            'admin' => [
                'settings' => true,
                'activity' => true,
                'ops' => true,
                'funnel' => true,
                'source' => true,
                'seo' => true,
            ],
            'operations' => [
                'settings' => false,
                'activity' => true,
                'ops' => true,
                'funnel' => true,
                'source' => true,
                'seo' => true,
            ],
            'support' => [
                'settings' => false,
                'activity' => false,
                'ops' => true,
                'funnel' => false,
                'source' => false,
                'seo' => false,
            ],
            'finance' => [
                'settings' => false,
                'activity' => true,
                'ops' => true,
                'funnel' => true,
                'source' => true,
                'seo' => true,
            ],
        ];

        foreach ($matrix as $role => $expected) {
            $user = User::factory()->create();
            $user->assignRole($role);
            $this->actingAs($user);

            $this->assertSame($expected['settings'], ManageSettings::canAccess(), "ManageSettings access mismatch for {$role}");
            $this->assertSame($expected['activity'], ActivityStreamWidget::canView(), "ActivityStreamWidget mismatch for {$role}");
            $this->assertSame($expected['ops'], OpsAlertsWidget::canView(), "OpsAlertsWidget mismatch for {$role}");
            $this->assertSame($expected['funnel'], FunnelOverviewWidget::canView(), "FunnelOverviewWidget mismatch for {$role}");
            $this->assertSame($expected['source'], SourceQualityWidget::canView(), "SourceQualityWidget mismatch for {$role}");
            $this->assertSame($expected['seo'], SeoHealthWidget::canView(), "SeoHealthWidget mismatch for {$role}");
        }
    }
}
