<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Orders\Enums\OrderState;
use App\Domain\Orders\Exceptions\InvalidOrderTransitionException;
use App\Domain\Orders\Services\OrderStateTransitionService;
use App\Domain\Payments\Services\PaymentGatewayManager;
use App\Domain\Payments\Services\PaymentSignatureService;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\PricingQuote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Throwable;

class PaymentController extends Controller
{
    public function initiate(
        Request $request,
        OrderStateTransitionService $transitionService,
        PaymentGatewayManager $gatewayManager
    ): JsonResponse
    {
        $validated = $request->validate([
            'provider' => ['nullable', 'string', 'max:40'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'pricing_quote_id' => ['nullable', 'integer', 'exists:pricing_quotes,id'],
            'amount' => ['nullable', 'integer', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'request_payload' => ['nullable', 'array'],
        ]);

        if (empty($validated['order_id']) && empty($validated['pricing_quote_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'order_id veya pricing_quote_id zorunludur.',
            ], 422);
        }

        $provider = (string) ($validated['provider'] ?? config('payments.default_provider', 'mockpay'));
        $order = ! empty($validated['order_id']) ? Order::query()->find($validated['order_id']) : null;
        $quote = ! empty($validated['pricing_quote_id']) ? PricingQuote::query()->find($validated['pricing_quote_id']) : null;

        $amount = $order
            ? (int) $order->total_amount
            : ($quote ? (int) $quote->total_amount : (int) ($validated['amount'] ?? 0));
        $currency = strtoupper((string) ($order?->currency ?? $quote?->currency ?? ($validated['currency'] ?? 'TRY')));

        try {
            $gatewayResult = $gatewayManager->resolve($provider)->initiate($amount, $currency, [
                'order_id' => $order?->id,
                'order_no' => $order?->order_no,
                'pricing_quote_id' => $quote?->id,
                'callback_url' => url('/api/v1/payments/callback/'.$provider),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Odeme saglayici baslatma hatasi.',
                'error' => $e->getMessage(),
            ], 422);
        }

        $transaction = DB::transaction(function () use ($provider, $order, $quote, $amount, $currency, $validated, $transitionService, $gatewayResult) {
            $tx = PaymentTransaction::query()->create([
                'order_id' => $order?->id,
                'pricing_quote_id' => $quote?->id,
                'provider' => $provider,
                'provider_reference' => (string) $gatewayResult['provider_reference'],
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'pending',
                'request_payload' => array_merge(
                    (array) ($validated['request_payload'] ?? []),
                    (array) ($gatewayResult['request_payload'] ?? [])
                ),
            ]);

            if ($order && $order->state === OrderState::Draft->value) {
                try {
                    $transitionService->transition(
                        order: $order,
                        toState: OrderState::PendingPayment,
                        actorType: 'api',
                        actorId: null,
                        reason: 'payment_initiated',
                        metadata: ['transaction_id' => $tx->id]
                    );
                } catch (InvalidOrderTransitionException) {
                    // no-op: do not fail payment initiation due to state race
                }
            }

            return $tx;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'transaction_id' => $transaction->id,
                'provider' => $transaction->provider,
                'provider_reference' => $transaction->provider_reference,
                'status' => $transaction->status,
                'amount' => (int) $transaction->amount,
                'currency' => $transaction->currency,
                'payment_url' => (string) $gatewayResult['payment_url'],
            ],
        ], 201);
    }

    public function callback(
        string $provider,
        Request $request,
        PaymentSignatureService $signatureService,
        OrderStateTransitionService $transitionService
    ): JsonResponse {
        $normalizedPayload = $this->normalizeProviderCallbackPayload($provider, (array) $request->all());
        if ($normalizedPayload === null) {
            return response()->json([
                'success' => false,
                'message' => 'Gecersiz callback payload.',
            ], 422);
        }

        $validator = Validator::make($normalizedPayload, [
            'provider_reference' => ['required', 'string', 'max:120'],
            'status' => ['required', 'string', 'max:40'],
            'amount' => ['nullable', 'integer', 'min:0'],
            'payload' => ['nullable', 'array'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Gecersiz callback payload.',
                'errors' => $validator->errors(),
            ], 422);
        }
        /** @var array{provider_reference:string,status:string,amount?:int,payload?:array<string,mixed>} $validated */
        $validated = $validator->validated();

        $signature = (string) ($request->header('X-Payment-Signature') ?: $request->header('X-Iyzico-Signature') ?: '');
        if (
            ! $signatureService->verify($provider, $validated, $signature)
            && ! $signatureService->verify($provider, (array) $request->all(), $signature)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Imza dogrulamasi basarisiz.',
            ], 422);
        }

        $transaction = PaymentTransaction::query()
            ->where('provider', $provider)
            ->where('provider_reference', $validated['provider_reference'])
            ->first();

        if (! $transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Islem bulunamadi.',
            ], 404);
        }

        $status = $this->normalizeStatus((string) $validated['status']);
        if ($status === null) {
            return response()->json([
                'success' => false,
                'message' => 'Desteklenmeyen callback status degeri.',
            ], 422);
        }

        if ($transaction->processed_at !== null && $transaction->status === $status) {
            return response()->json([
                'success' => true,
                'idempotent' => true,
                'data' => [
                    'transaction_id' => $transaction->id,
                    'status' => $transaction->status,
                ],
            ]);
        }

        $transaction->status = $status;
        $transaction->callback_payload = $validated['payload'] ?? [];
        $transaction->processed_at = now();
        $transaction->save();

        $order = $transaction->order;
        if ($order) {
            try {
                if ($status === 'succeeded' && $order->state === OrderState::PendingPayment->value) {
                    $transitionService->transition(
                        order: $order,
                        toState: OrderState::Paid,
                        actorType: 'webhook',
                        actorId: null,
                        reason: 'payment_callback_succeeded',
                        metadata: ['transaction_id' => $transaction->id]
                    );
                } elseif ($status === 'failed' && $order->state === OrderState::PendingPayment->value) {
                    $transitionService->transition(
                        order: $order,
                        toState: OrderState::Failed,
                        actorType: 'webhook',
                        actorId: null,
                        reason: 'payment_callback_failed',
                        metadata: ['transaction_id' => $transaction->id]
                    );
                }
            } catch (InvalidOrderTransitionException) {
                // ignore inconsistent callback order transitions, transaction already stored
            } catch (Throwable) {
                // ignore non-critical state sync errors
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'transaction_id' => $transaction->id,
                'status' => $transaction->status,
            ],
        ]);
    }

    public function retry(
        Request $request,
        Order $order,
        OrderStateTransitionService $transitionService
    ): JsonResponse {
        $validated = $request->validate([
            'provider' => ['nullable', 'string', 'max:40'],
            'request_payload' => ['nullable', 'array'],
        ]);

        if (! in_array($order->state, [OrderState::Failed->value, OrderState::Cancelled->value], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Retry sadece failed veya cancelled siparislerde desteklenir.',
            ], 422);
        }

        $provider = (string) ($validated['provider'] ?? config('payments.default_provider', 'mockpay'));

        $transaction = DB::transaction(function () use ($order, $provider, $validated, $transitionService) {
            $updatedOrder = $transitionService->transition(
                order: $order,
                toState: OrderState::PendingPayment,
                actorType: 'api',
                actorId: null,
                reason: 'payment_retry',
                metadata: ['source' => 'payment_retry_endpoint']
            );

            return PaymentTransaction::query()->create([
                'order_id' => $updatedOrder->id,
                'provider' => $provider,
                'provider_reference' => $this->nextProviderReference(),
                'amount' => (int) $updatedOrder->total_amount,
                'currency' => (string) $updatedOrder->currency,
                'status' => 'pending',
                'request_payload' => $validated['request_payload'] ?? [],
            ]);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'transaction_id' => $transaction->id,
                'provider_reference' => $transaction->provider_reference,
                'status' => $transaction->status,
            ],
        ], 201);
    }

    private function normalizeStatus(string $raw): ?string
    {
        $normalized = strtolower(trim($raw));
        return match ($normalized) {
            'success', 'succeeded', 'paid' => 'succeeded',
            'failed', 'fail', 'error' => 'failed',
            'cancelled', 'canceled' => 'cancelled',
            'pending' => 'pending',
            default => null,
        };
    }

    private function nextProviderReference(): string
    {
        return 'PAY'.now()->format('YmdHis').Str::upper(Str::random(6));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{provider_reference:string,status:string,amount:int|null,payload:array<string,mixed>}|null
     */
    private function normalizeProviderCallbackPayload(string $provider, array $payload): ?array
    {
        $providerKey = strtolower(trim($provider));

        if ($providerKey === 'iyzico') {
            $reference = (string) ($payload['provider_reference'] ?? $payload['conversationId'] ?? $payload['paymentId'] ?? '');
            $status = (string) ($payload['status'] ?? $payload['paymentStatus'] ?? $payload['result'] ?? '');
            if ($reference === '' || $status === '') {
                return null;
            }

            return [
                'provider_reference' => $reference,
                'status' => $status,
                'amount' => $this->normalizeAmountToMinor($payload['amount'] ?? $payload['paidPrice'] ?? null),
                'payload' => (array) ($payload['payload'] ?? $payload),
            ];
        }

        $reference = (string) ($payload['provider_reference'] ?? '');
        $status = (string) ($payload['status'] ?? '');
        if ($reference === '' || $status === '') {
            return null;
        }

        return [
            'provider_reference' => $reference,
            'status' => $status,
            'amount' => $this->normalizeAmountToMinor($payload['amount'] ?? null),
            'payload' => (array) ($payload['payload'] ?? $payload),
        ];
    }

    private function normalizeAmountToMinor(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        if (! is_string($value) && ! is_float($value)) {
            return null;
        }

        $raw = is_float($value) ? (string) $value : trim((string) $value);
        if ($raw === '') {
            return null;
        }

        if (str_contains($raw, ',') || str_contains($raw, '.')) {
            $normalized = str_replace(',', '.', $raw);
            if (! is_numeric($normalized)) {
                return null;
            }

            return (int) round(((float) $normalized) * 100);
        }

        return is_numeric($raw) ? (int) $raw : null;
    }
}
