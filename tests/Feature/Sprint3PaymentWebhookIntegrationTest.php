<?php

namespace Tests\Feature;

use App\Domain\Payments\Services\PaymentSignatureService;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Tests\TestCase;

class Sprint3PaymentWebhookIntegrationTest extends TestCase
{
    public function test_webhook_signature_and_idempotency_integration(): void
    {
        $order = Order::query()->create([
            'order_no' => 'ORD-WEBHOOK-INT-001',
            'state' => 'pending_payment',
            'payment_state' => 'pending',
            'pickup_address' => 'A',
            'dropoff_address' => 'B',
            'total_amount' => 1100,
            'currency' => 'TRY',
        ]);

        PaymentTransaction::query()->create([
            'order_id' => $order->id,
            'provider' => 'mockpay',
            'provider_reference' => 'WEBHOOK-INT-REF-1',
            'amount' => 1100,
            'currency' => 'TRY',
            'status' => 'pending',
        ]);

        $payload = [
            'provider_reference' => 'WEBHOOK-INT-REF-1',
            'status' => 'succeeded',
            'amount' => 1100,
            'payload' => ['source' => 'integration-test'],
        ];

        $signature = app(PaymentSignatureService::class)->sign('mockpay', $payload);

        $first = $this->postJson('/api/v1/payments/callback/mockpay', $payload, [
            'X-Payment-Signature' => $signature,
        ]);
        $first->assertOk()->assertJsonPath('success', true);

        $second = $this->postJson('/api/v1/payments/callback/mockpay', $payload, [
            'X-Payment-Signature' => $signature,
        ]);
        $second->assertOk()->assertJsonPath('idempotent', true);
    }
}

