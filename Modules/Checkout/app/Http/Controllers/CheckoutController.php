<?php

namespace Modules\Checkout\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Checkout\Models\CheckoutSession;
use Modules\Checkout\Services\CheckoutFinalizeService;
use Modules\Checkout\Services\CheckoutSessionService;
use RuntimeException;

class CheckoutController extends Controller
{
    public function store(Request $request, CheckoutSessionService $service): JsonResponse
    {
        $validated = $request->validate([
            'pricing_quote_id' => ['nullable', 'integer', 'exists:pricing_quotes,id'],
            'current_step' => ['nullable', 'string', 'in:quote,auth,recipient,payment,confirm'],
            'payload' => ['nullable', 'array'],
        ]);

        $checkoutSession = $service->create($validated);

        return response()->json([
            'success' => true,
            'data' => $this->transform($checkoutSession),
        ], 201);
    }

    public function show(CheckoutSession $checkoutSession): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->transform($checkoutSession),
        ]);
    }

    public function update(
        Request $request,
        CheckoutSession $checkoutSession,
        CheckoutSessionService $service
    ): JsonResponse {
        $validated = $request->validate([
            'pricing_quote_id' => ['nullable', 'integer', 'exists:pricing_quotes,id'],
            'customer_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['nullable', 'string', 'in:draft,authenticated,ready,expired,completed'],
            'current_step' => ['nullable', 'string', 'in:quote,auth,recipient,payment,confirm'],
            'payload' => ['nullable', 'array'],
        ]);

        $checkoutSession = $service->update($checkoutSession, $validated);

        return response()->json([
            'success' => true,
            'data' => $this->transform($checkoutSession),
        ]);
    }

    public function finalize(
        Request $request,
        CheckoutSession $checkoutSession,
        CheckoutSessionService $sessionService,
        CheckoutFinalizeService $finalizeService
    ): JsonResponse {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer', 'exists:users,id'],
            'payload' => ['nullable', 'array'],
        ]);

        if (! empty($validated['payload'])) {
            $checkoutSession = $sessionService->update($checkoutSession, [
                'payload' => (array) $validated['payload'],
            ]);
        }

        try {
            $result = $finalizeService->finalize(
                checkoutSession: $checkoutSession,
                customerId: isset($validated['customer_id']) ? (int) $validated['customer_id'] : null
            );
        } catch (RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order' => [
                    'id' => $result['order']->id,
                    'order_no' => $result['order']->order_no,
                    'state' => $result['order']->state,
                    'payment_state' => $result['order']->payment_state,
                    'payment_method' => $result['order']->payment_method,
                    'payment_timing' => $result['order']->payment_timing,
                    'payer_role' => $result['order']->payer_role,
                    'total_amount' => $result['order']->total_amount,
                    'currency' => $result['order']->currency,
                ],
                'next_action' => $result['next_action'],
                'payment_transaction_id' => $result['payment_transaction']?->id,
                'checkout_session' => $this->transform($result['session']),
            ],
        ], 201);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(CheckoutSession $checkoutSession): array
    {
        return [
            'id' => $checkoutSession->id,
            'token' => $checkoutSession->token,
            'customer_id' => $checkoutSession->customer_id,
            'pricing_quote_id' => $checkoutSession->pricing_quote_id,
            'status' => $checkoutSession->status,
            'current_step' => $checkoutSession->current_step,
            'payload' => $checkoutSession->payload ?? [],
            'expires_at' => optional($checkoutSession->expires_at)->toISOString(),
            'created_at' => optional($checkoutSession->created_at)->toISOString(),
            'updated_at' => optional($checkoutSession->updated_at)->toISOString(),
        ];
    }
}
