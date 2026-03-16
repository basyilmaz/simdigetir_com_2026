<?php

namespace Modules\Checkout\Http\Controllers;

use App\Domain\Pricing\Services\PricingQuoteResolver;
use App\Domain\Pricing\Services\PricingServiceCatalog;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PricingQuote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Checkout\Models\CheckoutSession;
use Modules\Checkout\Services\CheckoutContentResolver;
use Modules\Checkout\Services\CheckoutSessionService;
use App\Domain\Payments\Services\PaymentProviderRegistry;

class CheckoutPageController extends Controller
{
    public function index(
        Request $request,
        CheckoutSessionService $checkoutSessionService,
        PricingQuoteResolver $pricingQuoteResolver,
        PricingServiceCatalog $pricingServiceCatalog
    ): View|RedirectResponse
    {
        $pickupAddress = trim((string) $request->query('pickup', ''));
        $dropoffAddress = trim((string) $request->query('dropoff', ''));
        $serviceType = trim((string) $request->query('service_type', 'moto'));
        $serviceLabel = trim((string) $request->query('service_label', ''));

        if ($pickupAddress !== '' && $dropoffAddress !== '') {
            $fallbackServiceOptions = $this->fallbackQuoteServiceOptions();
            $serviceBaseAmount = $pricingServiceCatalog->resolveBaseAmountForService($serviceType, $fallbackServiceOptions) ?? 25000;
            $resolvedServiceLabel = $pricingServiceCatalog->resolveLabelForService($serviceType, $fallbackServiceOptions)
                ?? ($serviceLabel !== '' ? $serviceLabel : $serviceType);
            $quoteContext = [
                'base_amount' => $serviceBaseAmount,
                'currency' => 'TRY',
                'service_type' => $serviceType,
                'pickup_address' => $pickupAddress,
                'dropoff_address' => $dropoffAddress,
                'channel' => 'landing_checkout_fallback',
            ];

            $resolved = $pricingQuoteResolver->resolveFromDatabase($quoteContext);
            $quoteNo = 'QTE'.now()->format('YmdHis').Str::upper(Str::random(5));

            $pricingQuote = PricingQuote::query()->create([
                'quote_no' => $quoteNo,
                'request_snapshot' => [
                    'context' => $quoteContext,
                    'pickup' => ['address' => $pickupAddress],
                    'dropoff' => ['address' => $dropoffAddress],
                ],
                'resolved_rules' => $resolved['applied_rules'] ?? [],
                'subtotal_amount' => (int) ($resolved['subtotal_amount'] ?? 0),
                'discount_amount' => (int) ($resolved['discount_amount'] ?? 0),
                'surge_amount' => (int) ($resolved['surge_amount'] ?? 0),
                'total_amount' => (int) ($resolved['total_amount'] ?? 0),
                'currency' => (string) ($resolved['currency'] ?? 'TRY'),
                'expires_at' => now()->addMinutes(15),
            ]);

            $checkoutSession = $checkoutSessionService->create([
                'pricing_quote_id' => $pricingQuote->id,
                'current_step' => 'quote',
                'payload' => [
                    'service_type' => $serviceType !== '' ? $serviceType : 'moto',
                    'service_label' => $resolvedServiceLabel,
                    'pickup' => ['address' => $pickupAddress],
                    'dropoff' => ['address' => $dropoffAddress],
                    'quote_preview' => [
                        'quote_no' => $pricingQuote->quote_no,
                        'total_amount' => (int) $pricingQuote->total_amount,
                        'currency' => (string) $pricingQuote->currency,
                    ],
                ],
            ]);

            return redirect()->route('checkout.show', ['checkoutSession' => $checkoutSession->token]);
        }

        return view('checkout::index');
    }

    /**
     * @return array<int, array<string, int|string|bool>>
     */
    private function fallbackQuoteServiceOptions(): array
    {
        return [
            [
                'value' => 'moto',
                'label' => 'Moto Kurye',
                'base_amount' => 25000,
                'fallback_minutes' => 45,
                'is_default' => true,
            ],
            [
                'value' => 'urgent',
                'label' => 'Acil Kurye',
                'base_amount' => 35000,
                'fallback_minutes' => 35,
                'is_default' => false,
            ],
            [
                'value' => 'van',
                'label' => 'Aracli Kurye',
                'base_amount' => 45000,
                'fallback_minutes' => 70,
                'is_default' => false,
            ],
        ];
    }

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
