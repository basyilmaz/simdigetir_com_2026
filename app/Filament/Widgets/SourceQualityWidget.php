<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Modules\Leads\Models\Lead;

class SourceQualityWidget extends ChartWidget
{
    protected static ?int $sort = 5;

    protected static ?string $heading = 'Kaynak Bazlı Lead Kalitesi (30 Gün)';

    protected static ?string $pollingInterval = '30s';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->hasRole('super-admin')) {
            return true;
        }

        return $user->hasAnyPermission(['reports.view', 'ads.report']);
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $rows = Lead::query()
            ->whereDate('created_at', '>=', now()->subDays(30)->toDateString())
            ->select('source')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'won' THEN 1 ELSE 0 END) as won_total")
            ->groupBy('source')
            ->get()
            ->map(fn ($row) => [
                'label' => filled(trim((string) $row->source))
                    ? trim((string) $row->source)
                    : 'Bilinmiyor',
                'total' => (int) $row->total,
                'won_total' => (int) $row->won_total,
            ])
            ->groupBy('label')
            ->map(function ($group, $label) {
                $total = (int) $group->sum('total');
                $wonTotal = (int) $group->sum('won_total');

                return [
                    'label' => (string) $label,
                    'total' => $total,
                    'won_total' => $wonTotal,
                    'win_rate' => $total > 0
                        ? round(($wonTotal / $total) * 100, 1)
                        : 0.0,
                ];
            })
            ->sortByDesc('total')
            ->take(6)
            ->values()
            ->map(fn ($row) => [
                'label' => $row['label'],
                'win_rate' => $row['win_rate'],
            ]);

        if ($rows->isEmpty()) {
            return [
                'datasets' => [[
                    'label' => 'Won Rate %',
                    'data' => [0],
                    'backgroundColor' => ['#94a3b8'],
                ]],
                'labels' => ['Veri Yok'],
            ];
        }

        return [
            'datasets' => [[
                'label' => 'Won Rate %',
                'data' => $rows->pluck('win_rate')->all(),
                'backgroundColor' => ['#0ea5e9', '#22c55e', '#f97316', '#f59e0b', '#6366f1', '#ef4444'],
            ]],
            'labels' => $rows->pluck('label')->all(),
        ];
    }
}
