<?php

namespace App\Domain\Payments\Services;

use App\Domain\Orders\Enums\OrderState;
use App\Domain\Orders\Exceptions\InvalidOrderTransitionException;
use App\Domain\Orders\Services\OrderStateTransitionService;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\PricingQuote;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PaymentInitiationService
{
    public function __construct(
        private readonly PaymentGatewayManager $gatewayManager,
        private readonly OrderStateTransitionService $transitionService
    ) {
    }

    /**
     * @param  array<string, mixed>  $requestPayload
     * @return array{transaction:PaymentTransaction,payment_url:string}
     */
    public function initiate(
        string $provider,
        ?Order $order = null,
        ?PricingQuote $quote = null,
        ?int $amount = null,
        ?string $currency = null,
        array $requestPayload = []
    ): array {
        if (! $order && ! $quote) {
            throw new RuntimeException('order_id veya pricing_quote_id zorunludur.');
        }

        $provider = strtolower(trim($provider));
        $resolvedAmount = $order
            ? (int) $order->total_amount
            : ($quote ? (int) $quote->total_amount : (int) ($amount ?? 0));
        $resolvedCurrency = strtoupper((string) ($order?->currency ?? $quote?->currency ?? ($currency ?? 'TRY')));

        $reusableTransaction = $this->resolveReusableTransaction(
            provider: $provider,
            order: $order,
            quote: $quote,
            amount: $resolvedAmount,
            currency: $resolvedCurrency
        );

        if ($reusableTransaction) {
            return [
                'transaction' => $reusableTransaction,
                'payment_url' => (string) data_get($reusableTransaction->request_payload, 'payment_url', ''),
            ];
        }

        $gatewayResult = $this->gatewayManager->resolve($provider)->initiate($resolvedAmount, $resolvedCurrency, [
            'order_id' => $order?->id,
            'order_no' => $order?->order_no,
            'pricing_quote_id' => $quote?->id,
            'callback_url' => url('/api/v1/payments/callback/'.$provider),
        ]);

        $transaction = DB::transaction(function () use (
            $provider,
            $order,
            $quote,
            $resolvedAmount,
            $resolvedCurrency,
            $requestPayload,
            $gatewayResult
        ) {
            $transaction = PaymentTransaction::query()->create([
                'order_id' => $order?->id,
                'pricing_quote_id' => $quote?->id,
                'provider' => $provider,
                'provider_reference' => (string) $gatewayResult['provider_reference'],
                'amount' => $resolvedAmount,
                'currency' => $resolvedCurrency,
                'status' => 'pending',
                'request_payload' => array_merge(
                    $requestPayload,
                    (array) ($gatewayResult['request_payload'] ?? []),
                    ['payment_url' => (string) $gatewayResult['payment_url']]
                ),
            ]);

            if ($order && $order->state === OrderState::Draft->value) {
                try {
                    $this->transitionService->transition(
                        order: $order,
                        toState: OrderState::PendingPayment,
                        actorType: 'api',
                        actorId: null,
                        reason: 'payment_initiated',
                        metadata: ['transaction_id' => $transaction->id]
                    );
                } catch (InvalidOrderTransitionException) {
                    // no-op: do not fail payment initiation due to state race
                }
            }

            return $transaction;
        });

        return [
            'transaction' => $transaction,
            'payment_url' => (string) $gatewayResult['payment_url'],
        ];
    }

    private function resolveReusableTransaction(
        string $provider,
        ?Order $order,
        ?PricingQuote $quote,
        int $amount,
        string $currency
    ): ?PaymentTransaction {
        $query = PaymentTransaction::query()
            ->where('provider', $provider)
            ->where('status', 'pending')
            ->whereNull('processed_at')
            ->where('amount', $amount)
            ->where('currency', $currency)
            ->where('created_at', '>=', now()->subMinutes(30));

        if ($order) {
            $query->where('order_id', $order->id);
        } elseif ($quote) {
            $query->where('pricing_quote_id', $quote->id);
        } else {
            return null;
        }

        $transaction = $query->latest('id')->first();
        if (! $transaction) {
            return null;
        }

        $paymentUrl = (string) data_get($transaction->request_payload, 'payment_url', '');

        return $paymentUrl !== '' ? $transaction : null;
    }
}
