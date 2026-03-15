<?php

namespace Modules\Checkout\Services;

use App\Domain\Notifications\Services\OrderLifecycleNotificationService;
use App\Domain\Orders\Enums\OrderState;
use App\Models\Order;
use App\Models\OrderPackage;
use App\Models\OrderStateLog;
use App\Models\PaymentTransaction;
use App\Models\PricingQuote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Checkout\Models\CheckoutSession;
use RuntimeException;

class CheckoutFinalizeService
{
    /**
     * @param  array<string, mixed>  $overridePayload
     * @return array{order:Order,next_action:string,payment_transaction:?PaymentTransaction,session:CheckoutSession}
     */
    public function finalize(
        CheckoutSession $checkoutSession,
        array $overridePayload = [],
        ?int $customerId = null
    ): array {
        if ($checkoutSession->expires_at && $checkoutSession->expires_at->isPast()) {
            throw new RuntimeException('Checkout oturumu suresi dolmus.');
        }

        $payload = array_replace_recursive((array) ($checkoutSession->payload ?? []), $overridePayload);
        $resolvedCustomerId = $customerId ?? $checkoutSession->customer_id;

        if (! $resolvedCustomerId) {
            throw new RuntimeException('Musteri kimligi zorunludur.');
        }

        $pickup = (array) ($payload['pickup'] ?? []);
        $dropoff = (array) ($payload['dropoff'] ?? []);
        if (trim((string) ($pickup['address'] ?? '')) === '' || trim((string) ($dropoff['address'] ?? '')) === '') {
            throw new RuntimeException('Alinis ve teslimat adresi zorunludur.');
        }

        $payment = (array) ($payload['payment'] ?? []);
        $paymentMethod = (string) ($payment['method'] ?? '');
        $paymentTiming = (string) ($payment['timing'] ?? '');
        $payerRole = (string) ($payment['payer_role'] ?? '');

        if (! $this->isAllowedPaymentCombination($paymentMethod, $paymentTiming)) {
            throw new RuntimeException('Gecersiz odeme kombinasyonu.');
        }

        if ($payerRole === '') {
            $payerRole = $paymentMethod === 'cash' ? 'recipient' : 'sender';
        }

        $quote = $checkoutSession->pricingQuote ?: PricingQuote::query()->find($checkoutSession->pricing_quote_id);
        if (! $quote) {
            throw new RuntimeException('Pricing quote zorunludur.');
        }

        $result = DB::transaction(function () use (
            $checkoutSession,
            $payload,
            $resolvedCustomerId,
            $pickup,
            $dropoff,
            $paymentMethod,
            $paymentTiming,
            $payerRole,
            $quote
        ) {
            [$initialState, $paymentState, $nextAction] = $this->resolvePaymentState($paymentMethod);

            $order = Order::query()->create([
                'customer_id' => $resolvedCustomerId,
                'order_no' => $this->nextOrderNo(),
                'state' => $initialState,
                'payment_state' => $paymentState,
                'payment_method' => $paymentMethod,
                'payment_timing' => $paymentTiming,
                'payer_role' => $payerRole,
                'pickup_name' => $pickup['name'] ?? null,
                'pickup_phone' => $pickup['phone'] ?? null,
                'pickup_address' => $pickup['address'],
                'pickup_lat' => $pickup['lat'] ?? null,
                'pickup_lng' => $pickup['lng'] ?? null,
                'dropoff_name' => $dropoff['name'] ?? null,
                'dropoff_phone' => $dropoff['phone'] ?? null,
                'dropoff_address' => $dropoff['address'],
                'dropoff_lat' => $dropoff['lat'] ?? null,
                'dropoff_lng' => $dropoff['lng'] ?? null,
                'scheduled_at' => $payload['scheduled_at'] ?? null,
                'distance_meters' => $payload['distance_meters'] ?? ($quote->request_snapshot['context']['distance_meters'] ?? null),
                'duration_seconds' => $payload['duration_seconds'] ?? ($quote->request_snapshot['context']['duration_seconds'] ?? null),
                'vehicle_type' => $payload['vehicle_type'] ?? $payload['service_type'] ?? null,
                'notes' => (array) ($payload['notes'] ?? []),
                'subtotal_amount' => (int) $quote->subtotal_amount,
                'discount_amount' => (int) $quote->discount_amount,
                'surge_amount' => (int) $quote->surge_amount,
                'total_amount' => (int) $quote->total_amount,
                'currency' => (string) $quote->currency,
                'price_breakdown' => ['source' => 'quote', 'quote_no' => $quote->quote_no],
                'checkout_snapshot' => $payload,
            ]);

            foreach ((array) ($payload['packages'] ?? []) as $item) {
                OrderPackage::query()->create([
                    'order_id' => $order->id,
                    'package_type' => $item['package_type'] ?? null,
                    'quantity' => (int) ($item['quantity'] ?? 1),
                    'weight_grams' => $item['weight_grams'] ?? null,
                    'length_cm' => $item['length_cm'] ?? null,
                    'width_cm' => $item['width_cm'] ?? null,
                    'height_cm' => $item['height_cm'] ?? null,
                    'declared_value_amount' => $item['declared_value_amount'] ?? null,
                    'description' => $item['description'] ?? null,
                    'metadata' => $item['metadata'] ?? null,
                ]);
            }

            OrderStateLog::query()->create([
                'order_id' => $order->id,
                'from_state' => null,
                'to_state' => $order->state,
                'actor_type' => 'checkout',
                'actor_id' => $resolvedCustomerId,
                'reason' => 'checkout_finalized',
                'metadata' => [
                    'checkout_session_id' => $checkoutSession->id,
                    'payment_method' => $paymentMethod,
                    'payment_timing' => $paymentTiming,
                ],
                'created_at' => now(),
            ]);

            $paymentTransaction = null;
            if ($paymentMethod === 'bank_transfer') {
                $paymentTransaction = PaymentTransaction::query()->create([
                    'order_id' => $order->id,
                    'provider' => 'bank_transfer',
                    'provider_reference' => 'BNK'.now()->format('YmdHis').Str::upper(Str::random(6)),
                    'amount' => (int) $order->total_amount,
                    'currency' => (string) $order->currency,
                    'status' => 'pending',
                    'request_payload' => [
                        'payment_method' => $paymentMethod,
                        'payment_timing' => $paymentTiming,
                        'checkout_session_id' => $checkoutSession->id,
                    ],
                ]);
            }

            $checkoutSession->customer_id = $resolvedCustomerId;
            $checkoutSession->status = 'completed';
            $checkoutSession->current_step = 'confirm';
            $checkoutSession->payload = array_replace_recursive($payload, [
                'finalized_order' => [
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                    'next_action' => $nextAction,
                ],
            ]);
            $checkoutSession->save();

            return [
                'order' => $order,
                'next_action' => $nextAction,
                'payment_transaction' => $paymentTransaction,
                'session' => $checkoutSession->refresh(),
            ];
        });

        /** @var OrderLifecycleNotificationService $notificationService */
        $notificationService = app(OrderLifecycleNotificationService::class);
        $notificationService->dispatchOrderCreated($result['order']);

        if ($paymentMethod === 'bank_transfer') {
            $notificationService->dispatchBankTransferPending($result['order']);
        }

        return $result;
    }

    private function isAllowedPaymentCombination(string $method, string $timing): bool
    {
        $normalizedMethod = strtolower(trim($method));
        $normalizedTiming = strtolower(trim($timing));

        return in_array($normalizedMethod.'|'.$normalizedTiming, [
            'card|prepaid',
            'bank_transfer|prepaid',
            'cash|delivery',
        ], true);
    }

    /**
     * @return array{0:string,1:string,2:string}
     */
    private function resolvePaymentState(string $paymentMethod): array
    {
        return match (strtolower(trim($paymentMethod))) {
            'card' => [OrderState::Draft->value, 'pending', 'initiate_card_payment'],
            'bank_transfer' => [OrderState::PendingPayment->value, 'awaiting_reconcile', 'await_bank_transfer_reconcile'],
            'cash' => [OrderState::Paid->value, 'cash_on_delivery', 'dispatch_ready'],
            default => throw new RuntimeException('Desteklenmeyen odeme yontemi.'),
        };
    }

    private function nextOrderNo(): string
    {
        return 'ORD'.now()->format('YmdHis').Str::upper(Str::random(5));
    }
}
