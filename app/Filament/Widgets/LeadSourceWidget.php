<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Modules\Leads\Models\Lead;

class LeadSourceWidget extends ChartWidget
{
    protected static ?int $sort = -1;

    protected static ?string $heading = 'Talep Kaynağı Analizi (30 Gün)';

    protected static ?string $pollingInterval = '30s';

    protected int | string | array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $rows = Lead::query()
            ->whereDate('created_at', '>=', now()->subDays(30)->toDateString())
            ->selectRaw('TRIM(source) as source_raw, COUNT(*) as total')
            ->groupByRaw('TRIM(source)')
            ->orderByDesc('total')
            ->limit(6)
            ->get()
            ->map(fn ($row) => [
                'label' => (isset($row->source_raw) && $row->source_raw !== '') ? $row->source_raw : 'Bilinmiyor',
                'total' => (int) $row->total,
            ]);


        if ($rows->isEmpty()) {
            return [
                'datasets' => [[
                    'label' => 'Talep Adedi',
                    'data' => [1],
                    'backgroundColor' => ['#94a3b8'],
                ]],
                'labels' => ['Veri Yok'],
            ];
        }

        return [
            'datasets' => [[
                'label' => 'Talep Adedi',
                'data' => $rows->pluck('total')->all(),
                'backgroundColor' => ['#f97316', '#0ea5e9', '#22c55e', '#f59e0b', '#6366f1', '#ef4444'],
            ]],
            'labels' => $rows->pluck('label')->all(),
        ];
    }
}
