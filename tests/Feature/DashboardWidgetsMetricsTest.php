<?php

namespace Tests\Feature;

use App\Filament\Widgets\AdsOverviewWidget;
use App\Filament\Widgets\LeadSourceWidget;
use App\Filament\Widgets\OrderTrendWidget;
use App\Filament\Widgets\StatsOverview;
use App\Models\Order;
use Modules\AdsCore\Models\AdDailyMetric;
use Modules\Leads\Models\Lead;
use Tests\TestCase;

class DashboardWidgetsMetricsTest extends TestCase
{
    public function test_stats_overview_includes_lead_conversion_card(): void
    {
        foreach (range(1, 2) as $index) {
            Lead::query()->create([
                'type' => 'contact',
                'name' => "Won Lead {$index}",
                'phone' => "05551000{$index}",
                'status' => 'won',
            ]);
        }

        foreach (range(1, 8) as $index) {
            Lead::query()->create([
                'type' => 'contact',
                'name' => "New Lead {$index}",
                'phone' => "05552000{$index}",
                'status' => 'new',
            ]);
        }

        $widget = new class extends StatsOverview
        {
            public function exposeStats(): array
            {
                return $this->getStats();
            }
        };

        $stats = collect($widget->exposeStats());
        $conversionStat = $stats->first(fn ($stat): bool => (string) $stat->getLabel() === 'Lead Dönüşüm Oranı');

        $this->assertNotNull($conversionStat);
        $this->assertSame('20,0%', (string) $conversionStat->getValue());
        $this->assertStringContainsString('Kazanılan 2 / Toplam 10 talep', (string) $conversionStat->getDescription());
    }

    public function test_lead_source_and_order_trend_widgets_use_recent_data_windows(): void
    {
        Lead::query()->insert([
            [
                'type' => 'contact',
                'name' => 'G Lead',
                'phone' => '05550000001',
                'status' => 'new',
                'source' => 'google',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'type' => 'contact',
                'name' => 'M Lead',
                'phone' => '05550000002',
                'status' => 'new',
                'source' => 'meta',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'type' => 'contact',
                'name' => 'Unknown Lead',
                'phone' => '05550000003',
                'status' => 'new',
                'source' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'contact',
                'name' => 'Old Lead',
                'phone' => '05550000004',
                'status' => 'new',
                'source' => 'google',
                'created_at' => now()->subDays(40),
                'updated_at' => now()->subDays(40),
            ],
        ]);

        Order::query()->insert([
            [
                'order_no' => 'ORD-W-001',
                'state' => 'paid',
                'payment_state' => 'succeeded',
                'total_amount' => 1000,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'order_no' => 'ORD-W-002',
                'state' => 'paid',
                'payment_state' => 'succeeded',
                'total_amount' => 1000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_no' => 'ORD-OLD-001',
                'state' => 'paid',
                'payment_state' => 'succeeded',
                'total_amount' => 1000,
                'created_at' => now()->subDays(12),
                'updated_at' => now()->subDays(12),
            ],
        ]);

        $leadSourceWidget = new class extends LeadSourceWidget
        {
            public function exposeData(): array
            {
                return $this->getData();
            }
        };

        $orderTrendWidget = new class extends OrderTrendWidget
        {
            public function exposeData(): array
            {
                return $this->getData();
            }
        };

        $leadSourceData = $leadSourceWidget->exposeData();
        $orderTrendData = $orderTrendWidget->exposeData();

        $this->assertContains('google', $leadSourceData['labels']);
        $this->assertContains('meta', $leadSourceData['labels']);
        $this->assertContains('Bilinmiyor', $leadSourceData['labels']);
        $this->assertSame(3, array_sum($leadSourceData['datasets'][0]['data']));

        $this->assertCount(7, $orderTrendData['labels']);
        $this->assertSame(2, array_sum($orderTrendData['datasets'][0]['data']));
    }

    public function test_ads_overview_widget_calculates_spend_conversion_and_cpa(): void
    {
        AdDailyMetric::query()->create([
            'metric_date' => now()->toDateString(),
            'platform' => 'google',
            'campaign_name' => 'Google Search',
            'spend' => 100,
            'leads' => 4,
            'revenue' => 300,
        ]);
        AdDailyMetric::query()->create([
            'metric_date' => now()->subDays(1)->toDateString(),
            'platform' => 'meta',
            'campaign_name' => 'Meta Leads',
            'spend' => 50,
            'leads' => 1,
            'revenue' => 100,
        ]);

        $widget = new class extends AdsOverviewWidget
        {
            public function exposeStats(): array
            {
                return $this->getStats();
            }
        };

        $stats = collect($widget->exposeStats())->mapWithKeys(
            fn ($stat): array => [(string) $stat->getLabel() => $stat]
        );

        $this->assertSame('150,00 ₺', (string) $stats['Toplam Harcama']->getValue());
        $this->assertSame('5', (string) $stats['Toplam Dönüşüm']->getValue());
        $this->assertSame('30,00 ₺', (string) $stats['Ortalama CPA']->getValue());
        $this->assertStringContainsString('ROAS: 2,67', (string) $stats['Ortalama CPA']->getDescription());
    }
}
