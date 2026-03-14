<?php

namespace Tests\Feature;

use App\Models\Order;
use Tests\TestCase;

class Sprint3PerformanceBaselineTest extends TestCase
{
    public function test_quote_and_order_list_have_reasonable_p95_baseline(): void
    {
        // Baseline data for order list endpoint.
        for ($i = 0; $i < 30; $i++) {
            Order::query()->create([
                'order_no' => 'ORD-P95-'.$i,
                'state' => 'draft',
                'payment_state' => 'pending',
                'pickup_address' => 'A',
                'dropoff_address' => 'B',
                'total_amount' => 1000 + $i,
                'currency' => 'TRY',
            ]);
        }

        $quoteTimings = [];
        for ($i = 0; $i < 12; $i++) {
            $start = microtime(true);
            $this->postJson('/api/v1/quotes', [
                'base_amount' => 1000,
                'zone' => 'A',
                'hour' => 12,
                'currency' => 'TRY',
            ])->assertStatus(201);
            $quoteTimings[] = (microtime(true) - $start) * 1000;
        }

        $orderListTimings = [];
        for ($i = 0; $i < 12; $i++) {
            $start = microtime(true);
            $this->getJson('/api/v1/orders?per_page=20')->assertOk();
            $orderListTimings[] = (microtime(true) - $start) * 1000;
        }

        $quoteP95 = $this->p95($quoteTimings);
        $orderListP95 = $this->p95($orderListTimings);

        // CI/local variance için toleranslı üst sınır.
        $this->assertLessThan(3000, $quoteP95, 'Quote endpoint p95 too high: '.$quoteP95.' ms');
        $this->assertLessThan(3000, $orderListP95, 'Order list endpoint p95 too high: '.$orderListP95.' ms');
    }

    /**
     * @param  array<int, float>  $samples
     */
    private function p95(array $samples): float
    {
        sort($samples);
        $index = (int) ceil(count($samples) * 0.95) - 1;
        $index = max(0, min($index, count($samples) - 1));

        return (float) $samples[$index];
    }
}

