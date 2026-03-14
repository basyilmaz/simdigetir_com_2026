<?php

namespace Tests\Feature;

use App\Domain\Orders\Enums\OrderState;
use App\Domain\Payments\Services\PaymentSignatureService;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Tests\TestCase;

class Sprint3PaymentFlowApiTest extends TestCase
{
    public function test_payment_initiation_moves_order_to_pending_payment(): void
    {
        $order = Order::query()->create([
            'order_no' => 'ORD-PAY-001',
            'state' => OrderState::Draft->value,
            'payment_state' => 'pending',
            'pickup_address' => 'A',
            'dropoff_address' => 'B',
            'total_amount' => 1750,
            'currency' => 'TRY',
        ]);

        $response = $this->postJson('/api/v1/payments/initiate', [
            'provider' => 'mockpay',
            'order_id' => $order->id,
        ]);

        $response->assertStatus(201)->assertJsonPath('success', true);
        $this->assertDatabaseHas('payment_transactions', [
            'order_id' => $order->id,
            'provider' => 'mockpay',
            'status' => 'pending',
            'amount' => 1750,
        ]);

        $order->refresh();
        $this->assertSame(OrderState::PendingPayment->value, $order->state);
    }

    public function test_payment_callback_is_verified_and_idempotent(): void
    {
        $order = Order::query()->create([
            'order_no' => 'ORD-PAY-002',
            'state' => OrderState::PendingPayment->value,
            'payment_state' => 'pending',
            'pickup_address' => 'A',
            'dropoff_address' => 'B',
            'total_amount' => 2000,
            'currency' => 'TRY',
        ]);

        $tx = PaymentTransaction::query()->create([
            'order_id' => $order->id,
            'provider' => 'mockpay',
            'provider_reference' => 'PAYREF-001',
            'amount' => 2000,
            'currency' => 'TRY',
            'status' => 'pending',
        ]);

        $payload = [
            'provider_reference' => 'PAYREF-001',
            'status' => 'succeeded',
            'amount' => 2000,
            'payload' => ['raw' => 'ok'],
        ];

        /** @var PaymentSignatureService $signer */
        $signer = app(PaymentSignatureService::class);
        $signature = $signer->sign('mockpay', $payload);

        $first = $this->postJson('/api/v1/payments/callback/mockpay', $payload, [
            'X-Payment-Signature' => $signature,
        ]);
        $first->assertOk()->assertJsonPath('success', true);

        $tx->refresh();
        $this->assertSame('succeeded', $tx->status);
        $this->assertNotNull($tx->processed_at);

        $order->refresh();
        $this->assertSame(OrderState::Paid->value, $order->state);

        $second = $this->postJson('/api/v1/payments/callback/mockpay', $payload, [
            'X-Payment-Signature' => $signature,
        ]);
        $second->assertOk()->assertJsonPath('idempotent', true);
    }

    public function test_payment_callback_rejects_invalid_signature(): void
    {
        PaymentTransaction::query()->create([
            'provider' => 'mockpay',
            'provider_reference' => 'PAYREF-INVALID',
            'amount' => 999,
            'currency' => 'TRY',
            'status' => 'pending',
        ]);

        $response = $this->postJson('/api/v1/payments/callback/mockpay', [
            'provider_reference' => 'PAYREF-INVALID',
            'status' => 'failed',
            'amount' => 999,
        ], [
            'X-Payment-Signature' => 'bad-signature',
        ]);

        $response->assertStatus(422)->assertJsonPath('success', false);
    }

    public function test_failed_callback_moves_order_to_failed_and_allows_retry(): void
    {
        $order = Order::query()->create([
            'order_no' => 'ORD-PAY-003',
            'state' => OrderState::PendingPayment->value,
            'payment_state' => 'pending',
            'pickup_address' => 'A',
            'dropoff_address' => 'B',
            'total_amount' => 2500,
            'currency' => 'TRY',
        ]);

        PaymentTransaction::query()->create([
            'order_id' => $order->id,
            'provider' => 'mockpay',
            'provider_reference' => 'PAYREF-FAIL-001',
            'amount' => 2500,
            'currency' => 'TRY',
            'status' => 'pending',
        ]);

        $payload = [
            'provider_reference' => 'PAYREF-FAIL-001',
            'status' => 'failed',
            'amount' => 2500,
            'payload' => ['raw' => 'failed'],
        ];

        /** @var PaymentSignatureService $signer */
        $signer = app(PaymentSignatureService::class);
        $signature = $signer->sign('mockpay', $payload);

        $callback = $this->postJson('/api/v1/payments/callback/mockpay', $payload, [
            'X-Payment-Signature' => $signature,
        ]);
        $callback->assertOk()->assertJsonPath('success', true);

        $order->refresh();
        $this->assertSame(OrderState::Failed->value, $order->state);
        $this->assertSame('failed', $order->payment_state);

        $retry = $this->postJson('/api/v1/payments/'.$order->id.'/retry', [
            'provider' => 'mockpay',
        ]);
        $retry->assertStatus(201)->assertJsonPath('success', true);

        $order->refresh();
        $this->assertSame(OrderState::PendingPayment->value, $order->state);
        $this->assertDatabaseHas('payment_transactions', [
            'order_id' => $order->id,
            'provider' => 'mockpay',
            'status' => 'pending',
        ]);
    }

    public function test_retry_is_rejected_for_non_failed_orders(): void
    {
        $order = Order::query()->create([
            'order_no' => 'ORD-PAY-004',
            'state' => OrderState::PendingPayment->value,
            'payment_state' => 'pending',
            'pickup_address' => 'A',
            'dropoff_address' => 'B',
            'total_amount' => 1250,
            'currency' => 'TRY',
        ]);

        $response = $this->postJson('/api/v1/payments/'.$order->id.'/retry', [
            'provider' => 'mockpay',
        ]);

        $response->assertStatus(422)->assertJsonPath('success', false);
    }

    public function test_iyzico_style_callback_payload_is_normalized_and_processed(): void
    {
        config()->set('payments.providers.iyzico.secret', 'iyzico-callback-secret');

        $order = Order::query()->create([
            'order_no' => 'ORD-PAY-005',
            'state' => OrderState::PendingPayment->value,
            'payment_state' => 'pending',
            'pickup_address' => 'A',
            'dropoff_address' => 'B',
            'total_amount' => 2000,
            'currency' => 'TRY',
        ]);

        $tx = PaymentTransaction::query()->create([
            'order_id' => $order->id,
            'provider' => 'iyzico',
            'provider_reference' => 'IYZ-REF-001',
            'amount' => 2000,
            'currency' => 'TRY',
            'status' => 'pending',
        ]);

        $rawPayload = [
            'conversationId' => 'IYZ-REF-001',
            'paymentStatus' => 'success',
            'paidPrice' => '20.00',
            'paymentId' => 'PAYMENT-123',
            'result' => 'success',
        ];
        $normalizedPayload = [
            'provider_reference' => 'IYZ-REF-001',
            'status' => 'success',
            'amount' => 2000,
        ];

        /** @var PaymentSignatureService $signer */
        $signer = app(PaymentSignatureService::class);
        $signature = $signer->sign('iyzico', $normalizedPayload);

        $response = $this->postJson('/api/v1/payments/callback/iyzico', $rawPayload, [
            'X-Payment-Signature' => $signature,
        ]);

        $response->assertOk()->assertJsonPath('success', true);

        $tx->refresh();
        $this->assertSame('succeeded', $tx->status);
        $this->assertNotNull($tx->processed_at);

        $order->refresh();
        $this->assertSame(OrderState::Paid->value, $order->state);
    }
}
