<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\PricingQuote;
use Tests\TestCase;

class Sprint3SmokeAdminVisibilityTest extends TestCase
{
    public function test_landing_like_payload_creates_quote_order_and_visible_records(): void
    {
        $quote = $this->postJson('/api/v1/quotes', [
            'base_amount' => 1500,
            'zone' => 'B',
            'hour' => 10,
            'currency' => 'TRY',
            'pickup' => ['address' => 'Maslak, Istanbul', 'name' => 'Firma A'],
            'dropoff' => ['address' => 'Kadikoy, Istanbul', 'name' => 'Musteri B'],
            'packages' => [['package_type' => 'document', 'quantity' => 1]],
        ]);
        $quote->assertStatus(201);
        $quoteId = (int) $quote->json('data.id');

        $order = $this->postJson('/api/v1/orders', [
            'pricing_quote_id' => $quoteId,
            'pickup' => ['address' => 'Maslak, Istanbul', 'name' => 'Firma A'],
            'dropoff' => ['address' => 'Kadikoy, Istanbul', 'name' => 'Musteri B'],
            'packages' => [['package_type' => 'document', 'quantity' => 1]],
        ]);
        $order->assertStatus(201);
        $orderId = (int) $order->json('data.id');

        $payment = $this->postJson('/api/v1/payments/initiate', [
            'provider' => 'mockpay',
            'order_id' => $orderId,
        ]);
        $payment->assertStatus(201);

        $this->assertNotNull(PricingQuote::query()->find($quoteId));
        $this->assertNotNull(Order::query()->find($orderId));
        $this->assertTrue(PaymentTransaction::query()->where('order_id', $orderId)->exists());
    }
}

