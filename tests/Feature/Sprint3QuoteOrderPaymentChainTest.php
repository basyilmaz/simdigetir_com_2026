<?php

namespace Tests\Feature;

use App\Domain\Payments\Services\PaymentSignatureService;
use App\Models\Order;
use App\Models\PricingRule;
use Tests\TestCase;

class Sprint3QuoteOrderPaymentChainTest extends TestCase
{
    public function test_quote_to_order_to_payment_callback_chain(): void
    {
        PricingRule::query()->create([
            'name' => 'Zone A Surge',
            'rule_type' => 'zone',
            'priority' => 10,
            'conditions' => ['zone' => 'A'],
            'effect' => ['type' => 'add_surge', 'amount' => 200],
            'is_active' => true,
        ]);

        $quoteResponse = $this->postJson('/api/v1/quotes', [
            'base_amount' => 1000,
            'zone' => 'A',
            'hour' => 14,
            'currency' => 'TRY',
            'pickup' => ['address' => 'P1'],
            'dropoff' => ['address' => 'D1'],
        ]);
        $quoteResponse->assertStatus(201)->assertJsonPath('success', true);

        $quoteId = (int) $quoteResponse->json('data.id');
        $quoteTotal = (int) $quoteResponse->json('data.total_amount');
        $this->assertGreaterThan(0, $quoteTotal);

        $orderResponse = $this->postJson('/api/v1/orders', [
            'pricing_quote_id' => $quoteId,
            'pickup' => ['address' => 'Pickup X'],
            'dropoff' => ['address' => 'Dropoff X'],
            'packages' => [['package_type' => 'envelope', 'quantity' => 1]],
        ]);
        $orderResponse->assertStatus(201)->assertJsonPath('success', true);

        $orderId = (int) $orderResponse->json('data.id');
        $order = Order::query()->findOrFail($orderId);
        $this->assertSame($quoteTotal, (int) $order->total_amount);

        $initiate = $this->postJson('/api/v1/payments/initiate', [
            'provider' => 'mockpay',
            'order_id' => $orderId,
        ]);
        $initiate->assertStatus(201)->assertJsonPath('success', true);
        $providerReference = (string) $initiate->json('data.provider_reference');

        $payload = [
            'provider_reference' => $providerReference,
            'status' => 'succeeded',
            'amount' => $quoteTotal,
            'payload' => ['event' => 'paid'],
        ];
        $signature = app(PaymentSignatureService::class)->sign('mockpay', $payload);

        $callback = $this->postJson('/api/v1/payments/callback/mockpay', $payload, [
            'X-Payment-Signature' => $signature,
        ]);
        $callback->assertOk()->assertJsonPath('success', true);

        $order->refresh();
        $this->assertSame('paid', $order->state);

        $timeline = $this->getJson('/api/v1/orders/'.$orderId.'/timeline');
        $timeline->assertOk()->assertJsonPath('success', true);
        $states = collect($timeline->json('data.timeline'))->pluck('to_state')->all();
        $this->assertContains('draft', $states);
        $this->assertContains('pending_payment', $states);
        $this->assertContains('paid', $states);
    }
}

