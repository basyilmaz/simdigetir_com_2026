<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Orders\Enums\OrderState;
use App\Domain\Orders\Services\OrderStateTransitionService;
use App\Domain\Finance\Services\SettlementService;
use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\CourierWalletEntry;
use App\Models\PaymentReconciliation;
use App\Models\PaymentRefund;
use App\Models\PaymentTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function runSettlement(Request $request, SettlementService $settlementService): JsonResponse
    {
        $validated = $request->validate([
            'courier_id' => ['nullable', 'integer', 'exists:couriers,id'],
            'commission_rate_bps' => ['nullable', 'integer', 'min:0', 'max:5000'],
        ]);

        $batch = $settlementService->run(
            courierId: isset($validated['courier_id']) ? (int) $validated['courier_id'] : null,
            commissionRateBps: (int) ($validated['commission_rate_bps'] ?? 1000)
        );

        return response()->json([
            'success' => true,
            'data' => $batch,
        ], 201);
    }

    public function courierWallet(Courier $courier): JsonResponse
    {
        $entries = CourierWalletEntry::query()
            ->where('courier_id', $courier->id)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'courier_id' => $courier->id,
                'entry_count' => $entries->count(),
                'balance' => (int) ($entries->first()->balance_after ?? 0),
                'entries' => $entries,
            ],
        ]);
    }

    public function reconcilePayment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'payment_transaction_id' => ['required', 'integer', 'exists:payment_transactions,id'],
            'provider_status' => ['required', 'string', 'max:40'],
            'notes' => ['nullable', 'string'],
        ]);

        $reconciliation = DB::transaction(function () use ($validated) {
            $transaction = PaymentTransaction::query()
                ->with('order')
                ->lockForUpdate()
                ->findOrFail((int) $validated['payment_transaction_id']);

            $internal = strtolower((string) $transaction->status);
            $provider = $this->normalizeProviderStatus((string) $validated['provider_status']);
            $isMatch = $internal === $provider;

            $reconciliation = PaymentReconciliation::query()->create([
                'payment_transaction_id' => $transaction->id,
                'provider_status' => $provider,
                'internal_status' => $internal,
                'is_match' => $isMatch,
                'notes' => $validated['notes'] ?? null,
                'reconciled_at' => now(),
            ]);

            $callbackPayload = (array) ($transaction->callback_payload ?? []);
            $transaction->status = $provider;
            $transaction->callback_payload = array_replace($callbackPayload, [
                'reconciled_at' => now()->toISOString(),
                'provider_status' => $provider,
                'notes' => $validated['notes'] ?? null,
            ]);
            if (in_array($provider, ['succeeded', 'failed'], true)) {
                $transaction->processed_at = now();
            }
            $transaction->save();

            $order = $transaction->order;
            if ($order && $order->state === OrderState::PendingPayment->value) {
                $transitionService = app(OrderStateTransitionService::class);

                if ($provider === 'succeeded') {
                    $transitionService->transition(
                        order: $order,
                        toState: OrderState::Paid,
                        actorType: 'finance',
                        actorId: null,
                        reason: 'payment_reconciled',
                        metadata: ['payment_transaction_id' => $transaction->id]
                    );
                } elseif ($provider === 'failed') {
                    $transitionService->transition(
                        order: $order,
                        toState: OrderState::Failed,
                        actorType: 'finance',
                        actorId: null,
                        reason: 'payment_reconcile_failed',
                        metadata: ['payment_transaction_id' => $transaction->id]
                    );
                }
            }

            return $reconciliation;
        });

        return response()->json([
            'success' => true,
            'data' => $reconciliation,
        ], 201);
    }

    public function refund(Request $request, PaymentTransaction $transaction): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'integer', 'min:1'],
            'reason' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'array'],
        ]);

        if ($transaction->status !== 'succeeded') {
            return response()->json([
                'success' => false,
                'message' => 'Sadece succeeded islemler iade edilebilir.',
            ], 422);
        }

        $refundedTotal = (int) PaymentRefund::query()
            ->where('payment_transaction_id', $transaction->id)
            ->sum('amount');

        $amount = (int) $validated['amount'];
        if ($refundedTotal + $amount > (int) $transaction->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Iade tutari islem tutarini asamaz.',
            ], 422);
        }

        $refund = PaymentRefund::query()->create([
            'payment_transaction_id' => $transaction->id,
            'order_id' => $transaction->order_id,
            'amount' => $amount,
            'currency' => (string) $transaction->currency,
            'status' => 'succeeded',
            'provider_reference' => 'RFN'.now()->format('YmdHis').$transaction->id,
            'reason' => $validated['reason'] ?? null,
            'metadata' => $validated['metadata'] ?? [],
            'processed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $refund,
        ], 201);
    }

    private function normalizeProviderStatus(string $providerStatus): string
    {
        return match (strtolower(trim($providerStatus))) {
            'paid', 'completed', 'success', 'successful', 'succeeded' => 'succeeded',
            'waiting', 'processing', 'pending' => 'pending',
            'declined', 'cancelled', 'canceled', 'error', 'failed' => 'failed',
            default => strtolower(trim($providerStatus)),
        };
    }
}
