<?php

namespace App\Filament\Widgets;

use App\Models\Courier;
use App\Models\Order;
use App\Models\SupportTicket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Leads\Models\Lead;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = -3;

    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $todayLeads = Lead::whereDate('created_at', today())->count();
        $newLeads = Lead::where('status', 'new')->count();
        $totalLeads = Lead::count();
        $wonLeads = Lead::where('status', 'won')->count();
        $leadConversionRate = $totalLeads > 0 ? round(($wonLeads / $totalLeads) * 100, 1) : 0.0;
        $totalOrders = Order::count();
        $todayOrders = Order::whereDate('created_at', today())->count();
        $activeCouriers = Courier::where('status', 'approved')->count();
        $pendingCouriers = Courier::where('status', 'pending')->count();
        $openTickets = SupportTicket::whereIn('status', ['open', 'pending'])->count();

        return [
            Stat::make('Bugün Gelen Talepler', $todayLeads)
                ->description("Bekleyen yeni talepler: {$newLeads}")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning')
                ->chart([7, 3, 4, 5, 6, $todayLeads > 0 ? $todayLeads : 1]),

            Stat::make('Toplam Sipariş', $totalOrders)
                ->description("Bugün {$todayOrders} yeni sipariş")
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),

            Stat::make('Lead Dönüşüm Oranı', number_format($leadConversionRate, 1, ',', '.') . '%')
                ->description("Kazanılan {$wonLeads} / Toplam {$totalLeads} talep")
                ->descriptionIcon('heroicon-m-chart-bar-square')
                ->color($leadConversionRate >= 20 ? 'success' : 'warning'),

            Stat::make('Aktif Kuryeler', $activeCouriers)
                ->description("{$pendingCouriers} başvuru bekliyor")
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('Açık Destek Talepleri', $openTickets)
                ->description($openTickets > 0 ? 'İlgilenilmesi gereken talepler var' : 'Tüm talepler çözüldü')
                ->descriptionIcon($openTickets > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($openTickets > 0 ? 'danger' : 'success'),
        ];
    }
}
