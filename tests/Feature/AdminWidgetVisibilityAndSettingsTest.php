<?php

namespace Tests\Feature;

use App\Filament\Widgets\ActivityStreamWidget;
use App\Filament\Widgets\FunnelOverviewWidget;
use App\Filament\Widgets\OpsAlertsWidget;
use App\Filament\Widgets\SeoHealthWidget;
use App\Filament\Widgets\SourceQualityWidget;
use App\Models\Order;
use App\Models\SupportTicket;
use App\Models\User;
use Modules\Leads\Models\Lead;
use Modules\Settings\Models\Setting;
use Tests\TestCase;

class AdminWidgetVisibilityAndSettingsTest extends TestCase
{
    public function test_widget_visibility_respects_role_permissions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $this->assertTrue(ActivityStreamWidget::canView());
        $this->assertTrue(OpsAlertsWidget::canView());
        $this->assertTrue(FunnelOverviewWidget::canView());
        $this->assertTrue(SourceQualityWidget::canView());
        $this->assertTrue(SeoHealthWidget::canView());

        $support = User::factory()->create();
        $support->assignRole('support');
        $this->actingAs($support);

        $this->assertFalse(ActivityStreamWidget::canView());
        $this->assertTrue(OpsAlertsWidget::canView());
        $this->assertFalse(FunnelOverviewWidget::canView());
        $this->assertFalse(SourceQualityWidget::canView());
        $this->assertFalse(SeoHealthWidget::canView());
    }

    public function test_ops_alerts_widget_reads_sla_thresholds_from_settings(): void
    {
        Setting::setValue('ops.sla_lead_new_minutes', 5, 'operations');
        Setting::setValue('ops.sla_ticket_open_minutes', 10, 'operations');
        Setting::setValue('ops.sla_order_pending_payment_minutes', 7, 'operations');

        Lead::query()->insert([
            [
                'type' => 'contact',
                'name' => 'Lead Stale',
                'phone' => '05558880001',
                'status' => 'new',
                'created_at' => now()->subMinutes(6),
                'updated_at' => now()->subMinutes(6),
            ],
            [
                'type' => 'contact',
                'name' => 'Lead Fresh',
                'phone' => '05558880002',
                'status' => 'new',
                'created_at' => now()->subMinutes(4),
                'updated_at' => now()->subMinutes(4),
            ],
        ]);

        SupportTicket::query()->insert([
            [
                'ticket_no' => 'TKT-THR-1',
                'status' => 'open',
                'priority' => 'normal',
                'subject' => 'Stale Ticket',
                'description' => 'stale',
                'created_at' => now()->subMinutes(11),
                'updated_at' => now()->subMinutes(11),
            ],
            [
                'ticket_no' => 'TKT-THR-2',
                'status' => 'pending',
                'priority' => 'normal',
                'subject' => 'Fresh Ticket',
                'description' => 'fresh',
                'created_at' => now()->subMinutes(8),
                'updated_at' => now()->subMinutes(8),
            ],
        ]);

        Order::query()->insert([
            [
                'order_no' => 'ORD-THR-1',
                'state' => 'pending_payment',
                'payment_state' => 'pending',
                'currency' => 'TRY',
                'total_amount' => 1000,
                'created_at' => now()->subMinutes(8),
                'updated_at' => now()->subMinutes(8),
            ],
            [
                'order_no' => 'ORD-THR-2',
                'state' => 'pending_payment',
                'payment_state' => 'pending',
                'currency' => 'TRY',
                'total_amount' => 1000,
                'created_at' => now()->subMinutes(5),
                'updated_at' => now()->subMinutes(5),
            ],
        ]);

        $widget = new class extends OpsAlertsWidget
        {
            public function exposeStats(): array
            {
                return $this->getStats();
            }
        };

        $stats = collect($widget->exposeStats())->mapWithKeys(
            fn ($stat): array => [(string) $stat->getLabel() => $stat]
        );

        $this->assertSame('1', (string) $stats['SLA Aşan Yeni Talep']->getValue());
        $this->assertSame('1', (string) $stats['SLA Aşan Açık Ticket']->getValue());
        $this->assertSame('1', (string) $stats['SLA Aşan Ödeme Bekleyen Sipariş']->getValue());
        $this->assertStringContainsString('5 dakikadan', (string) $stats['SLA Aşan Yeni Talep']->getDescription());
        $this->assertStringContainsString('10 dakikadan', (string) $stats['SLA Aşan Açık Ticket']->getDescription());
        $this->assertStringContainsString('7 dakikadan', (string) $stats['SLA Aşan Ödeme Bekleyen Sipariş']->getDescription());
    }
}
