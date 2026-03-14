<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\CourierWalletEntry;
use App\Models\Order;
use App\Models\OrderAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PanelController extends Controller
{
    public function courierPanel(Request $request): View
    {
        $validated = $request->validate([
            'courier_id' => ['nullable', 'integer', 'exists:couriers,id'],
        ]);

        $courier = isset($validated['courier_id'])
            ? Courier::query()->findOrFail((int) $validated['courier_id'])
            : Courier::query()->firstOrFail();

        return $this->courierDashboard($courier);
    }

    public function courierDashboard(Courier $courier): View
    {
        $assignments = OrderAssignment::query()
            ->where('courier_id', $courier->id)
            ->with('order')
            ->latest('id')
            ->limit(20)
            ->get();

        $walletEntries = CourierWalletEntry::query()
            ->where('courier_id', $courier->id)
            ->latest('id')
            ->limit(10)
            ->get();

        return view('panel.courier-dashboard', [
            'courier' => $courier,
            'assignments' => $assignments,
            'walletEntries' => $walletEntries,
            'walletBalance' => (int) ($walletEntries->first()->balance_after ?? 0),
        ]);
    }

    public function customerDashboard(User $user): View
    {
        $orders = Order::query()
            ->where('customer_id', $user->id)
            ->latest('id')
            ->limit(20)
            ->get();

        return view('panel.customer-dashboard', [
            'customer' => $user,
            'orders' => $orders,
            'activeOrders' => (int) $orders->whereIn('state', ['draft', 'pending_payment', 'paid', 'assigned', 'picked_up'])->count(),
        ]);
    }

    public function customerPanel(Request $request): View
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $user = isset($validated['user_id'])
            ? User::query()->findOrFail((int) $validated['user_id'])
            : User::query()->firstOrFail();

        return $this->customerDashboard($user);
    }
}
