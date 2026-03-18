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
                ->description(static::todayLeadDescription($todayLeads, $newLeads))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning')
                ->chart([7, 3, 4, 5, 6, $todayLeads > 0 ? $todayLeads : 1]),

            Stat::make('Toplam Sipariş', $totalOrders)
                ->description(static::todayOrderDescription($todayOrders))
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),

            Stat::make('Lead Dönüşüm Oranı', number_format($leadConversionRate, 1, ',', '.') . '%')
                ->description("Kazanılan {$wonLeads} / Toplam {$totalLeads} talep")
                ->descriptionIcon('heroicon-m-chart-bar-square')
                ->color($leadConversionRate >= 20 ? 'success' : 'warning'),

            Stat::make('Aktif Kuryeler', $activeCouriers)
                ->description(static::courierBacklogDescription($pendingCouriers))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('Açık Destek Talepleri', $openTickets)
                ->description($openTickets > 0 ? 'Onceliklendirilecek acik talepler var' : 'Tum talepler guncel olarak kapali')
                ->descriptionIcon($openTickets > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($openTickets > 0 ? 'danger' : 'success'),
        ];
    }

    public static function todayLeadDescription(int $todayLeads, int $newLeads): string
    {
        if ($todayLeads === 0 && $newLeads === 0) {
            return 'Bugun yeni talep yok, backlog temiz.';
        }

        return "Bekleyen yeni talepler: {$newLeads}";
    }

    public static function todayOrderDescription(int $todayOrders): string
    {
        if ($todayOrders === 0) {
            return 'Bugun yeni siparis acilmadi';
        }

        return "Bugun {$todayOrders} yeni siparis";
    }

    public static function courierBacklogDescription(int $pendingCouriers): string
    {
        if ($pendingCouriers === 0) {
            return 'Bekleyen kurye basvurusu yok';
        }

        return "{$pendingCouriers} basvuru bekliyor";
    }
}
