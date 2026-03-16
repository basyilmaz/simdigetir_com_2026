<?php

namespace Modules\Checkout\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Checkout\Services\CheckoutContentResolver;
use Modules\Checkout\Services\CustomerPortalAuthService;

class CustomerPortalOrderController extends Controller
{
    public function show(
        Request $request,
        string $orderNo,
        CustomerPortalAuthService $authService,
        CheckoutContentResolver $contentResolver
    ): View|RedirectResponse {
        $user = $authService->currentUser($request);
        if (! $user) {
            return redirect()
                ->route('checkout.customer.login')
                ->withErrors(['phone' => 'Devam etmek icin giris yapin.']);
        }

        $order = Order::query()
            ->where('customer_id', $user->id)
            ->where('order_no', $orderNo)
            ->with([
                'packages',
                'paymentTransactions' => fn ($query) => $query->latest('id'),
                'stateLogs' => fn ($query) => $query->orderBy('created_at'),
                'trackingEvents' => fn ($query) => $query->orderBy('created_at'),
                'orderProofs' => fn ($query) => $query->orderBy('created_at'),
            ])
            ->firstOrFail();

        return view('checkout::customer-order-detail', [
            'customer' => $user,
            'order' => $order,
            'bankTransfer' => $contentResolver->bankTransferInstructions(),
        ]);
    }

    public function receipt(Request $request, string $orderNo, CustomerPortalAuthService $authService): View|RedirectResponse
    {
        $user = $authService->currentUser($request);
        if (! $user) {
            return redirect()
                ->route('checkout.customer.login')
                ->withErrors(['phone' => 'Devam etmek için giriş yapın.']);
        }

        $order = Order::query()
            ->where('customer_id', $user->id)
            ->where('order_no', $orderNo)
            ->with([
                'paymentTransactions' => fn ($query) => $query->latest('id'),
            ])
            ->firstOrFail();

        return view('checkout::customer-order-receipt', [
            'customer' => $user,
            'order' => $order,
        ]);
    }
}
