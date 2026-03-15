<?php

namespace Modules\Checkout\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Checkout\Services\CustomerPortalAuthService;

class CustomerPortalDashboardController extends Controller
{
    private const ACTIVE_STATES = ['draft', 'pending_payment', 'paid', 'assigned', 'picked_up'];

    public function show(Request $request, CustomerPortalAuthService $authService): View|RedirectResponse
    {
        $user = $authService->currentUser($request);
        if (! $user) {
            return redirect()
                ->route('checkout.customer.login')
                ->withErrors(['phone' => 'Devam etmek icin giris yapin.']);
        }

        $search = trim((string) $request->query('search', ''));
        $selectedState = (string) $request->query('state', 'all');

        $baseQuery = Order::query()
            ->where('customer_id', $user->id);

        $totalOrdersCount = (clone $baseQuery)->count();

        $ordersQuery = (clone $baseQuery)
            ->with([
                'stateLogs' => fn ($query) => $query->orderBy('created_at'),
                'trackingEvents' => fn ($query) => $query->orderBy('created_at'),
                'orderProofs' => fn ($query) => $query->orderBy('created_at'),
            ]);

        if ($selectedState === 'active') {
            $ordersQuery->whereIn('state', self::ACTIVE_STATES);
        } elseif ($selectedState !== 'all' && in_array($selectedState, $this->availableStateFilters(), true)) {
            $ordersQuery->where('state', $selectedState);
        } else {
            $selectedState = 'all';
        }

        if ($search !== '') {
            $ordersQuery->where(function ($query) use ($search): void {
                $query
                    ->where('order_no', 'like', '%'.$search.'%')
                    ->orWhere('pickup_address', 'like', '%'.$search.'%')
                    ->orWhere('dropoff_address', 'like', '%'.$search.'%')
                    ->orWhere('pickup_name', 'like', '%'.$search.'%')
                    ->orWhere('dropoff_name', 'like', '%'.$search.'%')
                    ->orWhere('payment_method', 'like', '%'.$search.'%');
            });
        }

        $orders = $ordersQuery
            ->latest('id')
            ->limit(20)
            ->get();

        $activeOrders = (int) (clone $baseQuery)->whereIn('state', self::ACTIVE_STATES)->count();
        $completedOrders = (int) (clone $baseQuery)->where('state', 'delivered')->count();
        $filteredOrdersCount = (int) $orders->count();

        return view('checkout::customer-dashboard', [
            'customer' => $user,
            'orders' => $orders,
            'activeOrders' => $activeOrders,
            'completedOrders' => $completedOrders,
            'totalOrdersCount' => $totalOrdersCount,
            'filteredOrdersCount' => $filteredOrdersCount,
            'selectedState' => $selectedState,
            'searchTerm' => $search,
            'availableStateFilters' => $this->stateFilterLabels(),
        ]);
    }

    /**
     * @return list<string>
     */
    private function availableStateFilters(): array
    {
        return [
            'delivered',
            'pending_payment',
            'paid',
            'assigned',
            'picked_up',
            'draft',
            'failed',
            'cancelled',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function stateFilterLabels(): array
    {
        return [
            'all' => 'Tum Siparisler',
            'active' => 'Aktif',
            'pending_payment' => 'Odeme Bekleyen',
            'paid' => 'Odeme Alinan',
            'assigned' => 'Kurye Atanan',
            'picked_up' => 'Alinan',
            'delivered' => 'Teslim Edilen',
            'draft' => 'Taslak',
            'failed' => 'Basarisiz',
            'cancelled' => 'Iptal',
        ];
    }
}
