<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Order;
use App\Models\OrderAssignment;
use App\Models\PaymentTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class KpiController extends Controller
{
    public function overview(): JsonResponse
    {
        $data = Cache::remember('kpi:overview:v1', now()->addSeconds(60), function (): array {
            $totalOrders = (int) Order::query()->count();
            $deliveredOrders = (int) Order::query()->where('state', 'delivered')->count();
            $successRate = $totalOrders > 0
                ? round(($deliveredOrders / $totalOrders) * 100, 2)
                : 0.0;

            $paidTransactions = (int) PaymentTransaction::query()->where('status', 'succeeded')->count();
            $allTransactions = (int) PaymentTransaction::query()->count();
            $paymentSuccessRate = $allTransactions > 0
                ? round(($paidTransactions / $allTransactions) * 100, 2)
                : 0.0;

            $avgAssignSeconds = (int) round((float) OrderAssignment::query()
                ->whereNotNull('assigned_at')
                ->whereNotNull('accepted_at')
                ->get(['assigned_at', 'accepted_at'])
                ->map(function (OrderAssignment $assignment): int {
                    return Carbon::parse((string) $assignment->assigned_at)
                        ->diffInSeconds(Carbon::parse((string) $assignment->accepted_at));
                })
                ->avg());

            $activeCouriers = (int) Courier::query()
                ->where('status', 'approved')
                ->whereHas('availability', fn ($q) => $q->where('is_online', true))
                ->count();

            $grossRevenue = (int) Order::query()
                ->whereIn('state', ['delivered', 'closed'])
                ->sum('total_amount');

            return [
                'orders_total' => $totalOrders,
                'orders_delivered' => $deliveredOrders,
                'delivery_success_rate_pct' => $successRate,
                'payments_success_rate_pct' => $paymentSuccessRate,
                'avg_assignment_accept_seconds' => max(0, $avgAssignSeconds),
                'active_couriers' => $activeCouriers,
                'gross_revenue' => $grossRevenue,
                'currency' => 'TRY',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => ['cached_seconds' => 60],
        ]);
    }
}
