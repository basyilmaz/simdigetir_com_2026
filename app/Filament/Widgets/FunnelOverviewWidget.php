<?php

namespace App\Filament\Widgets;

use App\Models\FormSubmission;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Leads\Models\Lead;

class FunnelOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected static ?string $pollingInterval = '30s';

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

    protected function getStats(): array
    {
        $windowStart = now()->subDays(7);

        $submissions = FormSubmission::query()
            ->where('created_at', '>=', $windowStart)
            ->count();

        $leads = Lead::query()
            ->where('created_at', '>=', $windowStart)
            ->count();

        $wonLeads = Lead::query()
            ->where('created_at', '>=', $windowStart)
            ->where('status', 'won')
            ->count();

        $orders = Order::query()
            ->where('created_at', '>=', $windowStart)
            ->count();

        $leadRate = $submissions > 0 ? round(($leads / $submissions) * 100, 1) : 0.0;
        $winRate = $leads > 0 ? round(($wonLeads / $leads) * 100, 1) : 0.0;
        $leadToOrder = $leads > 0 ? round(($orders / $leads) * 100, 1) : 0.0;

        return [
            Stat::make('Form Gönderimi (7 Gün)', $submissions)
                ->description('Funnel girişi'),
            Stat::make('Lead (7 Gün)', $leads)
                ->description("Lead Rate: {$leadRate}%")
                ->color($leadRate >= 20 ? 'success' : 'warning'),
            Stat::make('Kazanılan Lead (7 Gün)', $wonLeads)
                ->description("Win Rate: {$winRate}%")
                ->color($winRate >= 15 ? 'success' : 'warning'),
            Stat::make('Sipariş (7 Gün)', $orders)
                ->description("Lead→Order: {$leadToOrder}%")
                ->color($leadToOrder >= 10 ? 'success' : 'warning'),
        ];
    }
}
