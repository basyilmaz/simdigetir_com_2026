<?php

namespace Modules\Checkout\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;
use Modules\Checkout\Models\CheckoutSession;
use Modules\Checkout\Services\CheckoutContentResolver;
use App\Domain\Payments\Services\PaymentProviderRegistry;

class CheckoutPageController extends Controller
{
    public function show(
        CheckoutSession $checkoutSession,
        CheckoutContentResolver $contentResolver,
        PaymentProviderRegistry $paymentProviderRegistry
    ): View
    {
        $checkoutSession->load(['pricingQuote', 'customer']);

        $payload = (array) ($checkoutSession->payload ?? []);
        $pricingQuote = $checkoutSession->pricingQuote;
        $finalizedOrder = null;
        $finalizedOrderId = $payload['finalized_order']['order_id'] ?? null;

        if (is_numeric($finalizedOrderId)) {
            $finalizedOrder = Order::query()->find((int) $finalizedOrderId);
        }

        return view('checkout::show', [
            'checkoutSession' => $checkoutSession,
            'pageState' => [
                'session' => [
                    'id' => $checkoutSession->id,
                    'token' => $checkoutSession->token,
                    'customer_id' => $checkoutSession->customer_id,
                    'status' => $checkoutSession->status,
                    'current_step' => $checkoutSession->current_step,
                    'payload' => $payload,
                    'expires_at' => optional($checkoutSession->expires_at)->toISOString(),
                ],
                'quote' => $pricingQuote ? [
                    'id' => $pricingQuote->id,
                    'quote_no' => $pricingQuote->quote_no,
                    'subtotal_amount' => (int) $pricingQuote->subtotal_amount,
                    'discount_amount' => (int) $pricingQuote->discount_amount,
                    'surge_amount' => (int) $pricingQuote->surge_amount,
                    'total_amount' => (int) $pricingQuote->total_amount,
                    'currency' => (string) $pricingQuote->currency,
                    'expires_at' => optional($pricingQuote->expires_at)->toISOString(),
                    'distance_meters' => $pricingQuote->request_snapshot['context']['distance_meters'] ?? null,
                    'duration_seconds' => $pricingQuote->request_snapshot['context']['duration_seconds'] ?? null,
                ] : null,
                'payment' => [
                    'default_provider' => $paymentProviderRegistry->defaultProvider(),
                    'provider_label' => $paymentProviderRegistry->defaultProviderLabel(),
                    'card_ready' => $paymentProviderRegistry->isCardCheckoutReady(),
                    'bank_transfer' => $contentResolver->bankTransferInstructions(),
                ],
                'customer' => $checkoutSession->customer ? [
                    'id' => $checkoutSession->customer->id,
                    'name' => $checkoutSession->customer->name,
                    'email' => $checkoutSession->customer->email,
                    'phone' => $checkoutSession->customer->phone,
                ] : (isset($payload['customer']) && is_array($payload['customer']) ? [
                    'id' => $payload['customer']['id'] ?? null,
                    'name' => $payload['customer']['name'] ?? null,
                    'email' => $payload['customer']['email'] ?? null,
                    'phone' => $payload['customer']['phone'] ?? null,
                ] : null),
                'finalized_order' => $finalizedOrder ? [
                    'id' => $finalizedOrder->id,
                    'order_no' => $finalizedOrder->order_no,
                    'state' => $finalizedOrder->state,
                    'payment_state' => $finalizedOrder->payment_state,
                    'payment_method' => $finalizedOrder->payment_method,
                    'payment_timing' => $finalizedOrder->payment_timing,
                    'total_amount' => (int) $finalizedOrder->total_amount,
                    'currency' => (string) $finalizedOrder->currency,
                ] : null,
                'endpoints' => [
                    'update' => route('api.v1.checkout-sessions.update', ['checkoutSession' => $checkoutSession->token]),
                    'finalize' => route('api.v1.checkout-sessions.finalize', ['checkoutSession' => $checkoutSession->token]),
                    'payment_initiate' => route('api.v1.checkout-sessions.payments.initiate', ['checkoutSession' => $checkoutSession->token]),
                    'register' => route('api.v1.auth.register'),
                    'login' => route('api.v1.auth.login'),
                ],
            ],
        ]);
    }
}
