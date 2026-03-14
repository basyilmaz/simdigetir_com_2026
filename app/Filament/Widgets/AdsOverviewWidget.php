<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Schema;
use Modules\AdsCore\Models\AdDailyMetric;

class AdsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = '30s';

    protected ?string $heading = 'Reklam Performans Özeti (7 Gün)';

    protected function getStats(): array
    {
        if (! Schema::hasTable('ad_daily_metrics')) {
            return $this->buildStats(0.0, 0, 0.0, 0.0);
        }

        $totals = AdDailyMetric::query()
            ->whereDate('metric_date', '>=', now()->subDays(6)->toDateString())
            ->selectRaw('COALESCE(SUM(spend), 0) as spend')
            ->selectRaw('COALESCE(SUM(leads), 0) as leads')
            ->selectRaw('COALESCE(SUM(revenue), 0) as revenue')
            ->first();

        $spend = (float) ($totals?->spend ?? 0);
        $leads = (int) ($totals?->leads ?? 0);
        $revenue = (float) ($totals?->revenue ?? 0);
        $cpa = $leads > 0 ? round($spend / $leads, 2) : 0.0;

        return $this->buildStats($spend, $leads, $cpa, $revenue);
    }

    /**
     * @return array<Stat>
     */
    protected function buildStats(float $spend, int $leads, float $cpa, float $revenue): array
    {
        $roas = $spend > 0 ? round($revenue / $spend, 2) : 0.0;

        return [
            Stat::make('Toplam Harcama', number_format($spend, 2, ',', '.') . ' ₺')
                ->description('Son 7 gün toplam reklam maliyeti')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),

            Stat::make('Toplam Dönüşüm', $leads)
                ->description('AdDailyMetric lead toplamı')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('success'),

            Stat::make('Ortalama CPA', number_format($cpa, 2, ',', '.') . ' ₺')
                ->description('ROAS: ' . number_format($roas, 2, ',', '.'))
                ->descriptionIcon('heroicon-m-scale')
                ->color($cpa > 0 ? 'info' : 'gray'),
        ];
    }
}
