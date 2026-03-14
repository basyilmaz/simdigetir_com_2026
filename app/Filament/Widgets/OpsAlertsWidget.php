<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\SupportTicket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Leads\Models\Lead;
use Modules\Settings\Models\Setting;

class OpsAlertsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

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

        return $user->hasAnyPermission(['support.manage', 'orders.manage', 'reports.view']);
    }

    protected function getStats(): array
    {
        $leadThreshold = $this->threshold('ops.sla_lead_new_minutes', 15);
        $ticketThreshold = $this->threshold('ops.sla_ticket_open_minutes', 30);
        $orderThreshold = $this->threshold('ops.sla_order_pending_payment_minutes', 20);

        $staleLeads = Lead::query()
            ->where('status', 'new')
            ->where('created_at', '<=', now()->subMinutes($leadThreshold))
            ->count();

        $staleTickets = SupportTicket::query()
            ->whereIn('status', ['open', 'pending'])
            ->where('created_at', '<=', now()->subMinutes($ticketThreshold))
            ->count();

        $staleOrders = Order::query()
            ->where('state', 'pending_payment')
            ->where('created_at', '<=', now()->subMinutes($orderThreshold))
            ->count();

        return [
            Stat::make('SLA Aşan Yeni Talep', $staleLeads)
                ->description("{$leadThreshold} dakikadan uzun süredir yeni kalan lead")
                ->color($staleLeads > 0 ? 'danger' : 'success')
                ->descriptionIcon($staleLeads > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle'),
            Stat::make('SLA Aşan Açık Ticket', $staleTickets)
                ->description("{$ticketThreshold} dakikadan uzun süredir açık/pending ticket")
                ->color($staleTickets > 0 ? 'danger' : 'success')
                ->descriptionIcon($staleTickets > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle'),
            Stat::make('SLA Aşan Ödeme Bekleyen Sipariş', $staleOrders)
                ->description("{$orderThreshold} dakikadan uzun süredir pending_payment sipariş")
                ->color($staleOrders > 0 ? 'warning' : 'success')
                ->descriptionIcon($staleOrders > 0 ? 'heroicon-m-clock' : 'heroicon-m-check-circle'),
        ];
    }

    private function threshold(string $key, int $default): int
    {
        $value = (int) Setting::getValue($key, $default);

        return $value > 0 ? $value : $default;
    }
}
