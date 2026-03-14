<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OrderTrendWidget extends ChartWidget
{
    protected static ?int $sort = 0;

    protected static ?string $heading = 'Haftalık Sipariş Trendi';

    protected static ?string $pollingInterval = '30s';

    protected int | string | array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $startDate = now()->startOfDay()->subDays(6);

        $dailyOrders = Order::query()
            ->whereDate('created_at', '>=', $startDate->toDateString())
            ->selectRaw('DATE(created_at) as order_date, COUNT(*) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total', 'order_date');

        $labels = [];
        $values = [];

        foreach (range(0, 6) as $offset) {
            $date = $startDate->copy()->addDays($offset);
            $dateKey = $date->toDateString();

            $labels[] = $date->format('d.m');
            $values[] = (int) ($dailyOrders[$dateKey] ?? 0);
        }

        return [
            'datasets' => [[
                'label' => 'Sipariş',
                'data' => $values,
                'borderColor' => '#0ea5e9',
                'backgroundColor' => 'rgba(14, 165, 233, 0.18)',
                'tension' => 0.35,
                'fill' => true,
            ]],
            'labels' => $labels,
        ];
    }
}
