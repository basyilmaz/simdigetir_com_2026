<?php

namespace Modules\Checkout\Http\Controllers;

use App\Domain\Orders\Enums\OrderState;
use App\Domain\Payments\Services\PaymentInitiationService;
use App\Domain\Payments\Services\PaymentProviderRegistry;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Checkout\Models\CheckoutSession;
use Throwable;

class CheckoutPaymentController extends Controller
{
    public function initiate(
        Request $request,
        CheckoutSession $checkoutSession,
        PaymentInitiationService $paymentInitiationService,
        PaymentProviderRegistry $paymentProviderRegistry
    ): JsonResponse {
        $validated = $request->validate([
            'provider' => ['nullable', 'string', 'max:40'],
        ]);

        $order = $this->resolveFinalizedOrder($checkoutSession);
        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Finalize edilmis checkout siparisi bulunamadi.',
            ], 422);
        }

        if ((int) $order->customer_id !== (int) $checkoutSession->customer_id) {
            return response()->json([
                'success' => false,
                'message' => 'Checkout oturumu ile siparis musteri eslesmiyor.',
            ], 422);
        }

        if ((string) $order->payment_method !== 'card' || (string) $order->payment_timing !== 'prepaid') {
            return response()->json([
                'success' => false,
                'message' => 'Bu checkout oturumu kart odemesi icin uygun degil.',
            ], 422);
        }

        if (! in_array((string) $order->state, [OrderState::Draft->value, OrderState::PendingPayment->value], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Siparisin mevcut durumu kart odemesi baslatmaya uygun degil.',
            ], 422);
        }

        $provider = strtolower(trim((string) ($validated['provider'] ?? config('payments.default_provider', 'mockpay'))));

        if (! $paymentProviderRegistry->supportsCardCheckout($provider)) {
            return response()->json([
                'success' => false,
                'message' => 'Gercek kart odeme saglayicisi aktif degil.',
            ], 422);
        }

        if (! $paymentProviderRegistry->isCardCheckoutReady($provider)) {
            return response()->json([
                'success' => false,
                'message' => $paymentProviderRegistry->providerLabel($provider).' konfigrasyonu eksik.',
            ], 422);
        }

        try {
            $result = $paymentInitiationService->initiate(
                provider: $provider,
                order: $order,
                requestPayload: [
                    'checkout_session_id' => $checkoutSession->id,
                    'source' => 'checkout_session_payment_initiate',
                ]
            );
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kart odeme baslatilamadi.',
                'error' => $e->getMessage(),
            ], 422);
        }

        $order->refresh();

        return response()->json([
            'success' => true,
            'data' => [
                'transaction_id' => $result['transaction']->id,
                'provider' => $result['transaction']->provider,
                'provider_reference' => $result['transaction']->provider_reference,
                'status' => $result['transaction']->status,
                'payment_url' => $result['payment_url'],
                'order' => [
                    'id' => $order->id,
                    'order_no' => $order->order_no,
                    'state' => $order->state,
                    'payment_state' => $order->payment_state,
                    'payment_method' => $order->payment_method,
                    'payment_timing' => $order->payment_timing,
                    'total_amount' => (int) $order->total_amount,
                    'currency' => (string) $order->currency,
                ],
            ],
        ], 201);
    }

    private function resolveFinalizedOrder(CheckoutSession $checkoutSession): ?Order
    {
        $payload = (array) ($checkoutSession->payload ?? []);
        $orderId = $payload['finalized_order']['order_id'] ?? null;

        return is_numeric($orderId) ? Order::query()->find((int) $orderId) : null;
    }
}
