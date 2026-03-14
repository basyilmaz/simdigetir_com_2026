<?php

namespace Tests\Feature;

use App\Filament\Resources\AdminAuditLogResource;
use App\Filament\Widgets\FunnelOverviewWidget;
use App\Filament\Widgets\OpsAlertsWidget;
use App\Filament\Widgets\SeoHealthWidget;
use App\Filament\Widgets\SourceQualityWidget;
use App\Models\FormDefinition;
use App\Models\FormSubmission;
use App\Models\Order;
use App\Models\SupportTicket;
use Modules\Landing\Models\LandingPage;
use Modules\Leads\Models\Lead;
use Tests\TestCase;

class AdminOpsAndMarketingWidgetsTest extends TestCase
{
    public function test_admin_audit_log_resource_exposes_view_page(): void
    {
        $this->assertArrayHasKey('view', AdminAuditLogResource::getPages());
    }

    public function test_ops_alerts_widget_flags_sla_breaches(): void
    {
        Lead::query()->insert([
            'type' => 'contact',
            'name' => 'Stale Lead',
            'phone' => '05550000111',
            'status' => 'new',
            'created_at' => now()->subMinutes(16),
            'updated_at' => now()->subMinutes(16),
        ]);

        SupportTicket::query()->insert([
            'ticket_no' => 'TKT-SLA-1',
            'status' => 'open',
            'priority' => 'normal',
            'subject' => 'SLA Ticket',
            'description' => 'SLA breach',
            'created_at' => now()->subMinutes(31),
            'updated_at' => now()->subMinutes(31),
        ]);

        Order::query()->insert([
            'order_no' => 'ORD-SLA-1',
            'state' => 'pending_payment',
            'payment_state' => 'pending',
            'total_amount' => 1000,
            'currency' => 'TRY',
            'created_at' => now()->subMinutes(21),
            'updated_at' => now()->subMinutes(21),
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
    }

    public function test_funnel_overview_widget_calculates_7_day_funnel_metrics(): void
    {
        $form = FormDefinition::query()->create([
            'key' => 'contact-form',
            'title' => 'İletişim Formu',
        ]);

        foreach (range(1, 10) as $index) {
            FormSubmission::query()->create([
                'form_definition_id' => $form->id,
                'payload' => ['name' => "Form {$index}"],
                'status' => 'received',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]);
        }

        foreach (range(1, 4) as $index) {
            Lead::query()->create([
                'type' => 'contact',
                'name' => "Lead {$index}",
                'phone' => "05559990{$index}",
                'status' => $index <= 2 ? 'won' : 'new',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ]);
        }

        foreach (range(1, 3) as $index) {
            Order::query()->create([
                'order_no' => "ORD-FUNNEL-{$index}",
                'state' => 'paid',
                'payment_state' => 'succeeded',
                'total_amount' => 2000,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ]);
        }

        $widget = new class extends FunnelOverviewWidget
        {
            public function exposeStats(): array
            {
                return $this->getStats();
            }
        };

        $stats = collect($widget->exposeStats())->mapWithKeys(
            fn ($stat): array => [(string) $stat->getLabel() => $stat]
        );

        $this->assertSame('10', (string) $stats['Form Gönderimi (7 Gün)']->getValue());
        $this->assertSame('4', (string) $stats['Lead (7 Gün)']->getValue());
        $this->assertSame('2', (string) $stats['Kazanılan Lead (7 Gün)']->getValue());
        $this->assertSame('3', (string) $stats['Sipariş (7 Gün)']->getValue());
    }

    public function test_source_quality_and_seo_health_widgets_compute_expected_metrics(): void
    {
        Lead::query()->insert([
            [
                'type' => 'contact',
                'name' => 'Google Won',
                'phone' => '05557770001',
                'status' => 'won',
                'source' => 'google',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'type' => 'contact',
                'name' => 'Google Lost',
                'phone' => '05557770002',
                'status' => 'lost',
                'source' => 'google',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'type' => 'contact',
                'name' => 'Meta Won',
                'phone' => '05557770003',
                'status' => 'won',
                'source' => 'meta',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'type' => 'contact',
                'name' => 'Unknown Won',
                'phone' => '05557770004',
                'status' => 'won',
                'source' => null,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'type' => 'contact',
                'name' => 'Unknown Lost',
                'phone' => '05557770005',
                'status' => 'lost',
                'source' => '   ',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
        ]);

        LandingPage::query()->create([
            'slug' => 'seo-complete',
            'meta' => ['meta_title' => 'Title', 'meta_description' => 'Desc'],
            'is_active' => true,
        ]);
        LandingPage::query()->create([
            'slug' => 'seo-partial',
            'meta' => ['meta_title' => 'Title Only'],
            'is_active' => true,
        ]);
        LandingPage::query()->create([
            'slug' => 'seo-missing',
            'meta' => [],
            'is_active' => true,
        ]);

        $sourceWidget = new class extends SourceQualityWidget
        {
            public function exposeData(): array
            {
                return $this->getData();
            }
        };

        $seoWidget = new class extends SeoHealthWidget
        {
            public function exposeStats(): array
            {
                return $this->getStats();
            }
        };

        $sourceData = $sourceWidget->exposeData();
        $seoStats = collect($seoWidget->exposeStats())->mapWithKeys(
            fn ($stat): array => [(string) $stat->getLabel() => $stat]
        );

        $this->assertContains('google', $sourceData['labels']);
        $this->assertContains('meta', $sourceData['labels']);
        $this->assertContains('Bilinmiyor', $sourceData['labels']);
        $this->assertContains(50.0, $sourceData['datasets'][0]['data']);
        $this->assertContains(100.0, $sourceData['datasets'][0]['data']);

        $unknownIndex = array_search('Bilinmiyor', $sourceData['labels'], true);
        $this->assertNotFalse($unknownIndex);
        $this->assertSame(50.0, $sourceData['datasets'][0]['data'][$unknownIndex]);

        $this->assertSame('1', (string) $seoStats['SEO Tam Sayfa']->getValue());
        $this->assertSame('1', (string) $seoStats['SEO Kısmi Sayfa']->getValue());
        $this->assertSame('1', (string) $seoStats['SEO Eksik Sayfa']->getValue());
    }
}
