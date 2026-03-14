<?php

namespace Tests\Feature;

use App\Domain\Orders\Enums\OrderState;
use App\Models\Order;
use App\Models\OrderStateLog;
use App\Models\User;
use Tests\TestCase;

class Sprint3OrderLifecycleApiTest extends TestCase
{
    public function test_can_create_order_and_read_timeline(): void
    {
        $create = $this->postJson('/api/v1/orders', [
            'pickup' => [
                'address' => 'Pickup Address 1',
                'name' => 'Ali',
                'phone' => '05320000001',
            ],
            'dropoff' => [
                'address' => 'Dropoff Address 1',
                'name' => 'Veli',
                'phone' => '05320000002',
            ],
            'packages' => [
                ['package_type' => 'envelope', 'quantity' => 1],
            ],
            'subtotal_amount' => 1000,
            'discount_amount' => 100,
            'surge_amount' => 50,
            'total_amount' => 950,
            'currency' => 'TRY',
        ]);

        $create->assertStatus(201)->assertJsonPath('success', true);
        $orderId = (int) $create->json('data.id');

        $timeline = $this->getJson('/api/v1/orders/'.$orderId.'/timeline');
        $timeline->assertOk()->assertJsonPath('success', true);
        $this->assertSame(OrderState::Draft->value, $timeline->json('data.current_state'));
        $this->assertNotEmpty($timeline->json('data.timeline'));
    }

    public function test_transition_guard_blocks_invalid_transition(): void
    {
        $order = Order::query()->create([
            'order_no' => 'ORDTEST0001',
            'state' => OrderState::Draft->value,
            'payment_state' => 'pending',
            'pickup_address' => 'A',
            'dropoff_address' => 'B',
            'total_amount' => 1000,
            'currency' => 'TRY',
        ]);

        $response = $this->postJson('/api/v1/orders/'.$order->id.'/transition', [
            'to_state' => OrderState::Delivered->value,
            'reason' => 'invalid direct jump',
        ]);

        $response->assertStatus(422)->assertJsonPath('success', false);
    }

    public function test_transition_is_idempotent_for_same_target_state(): void
    {
        $order = Order::query()->create([
            'order_no' => 'ORDTEST0002',
            'state' => OrderState::Draft->value,
            'payment_state' => 'pending',
            'pickup_address' => 'A',
            'dropoff_address' => 'B',
            'total_amount' => 1000,
            'currency' => 'TRY',
        ]);

        OrderStateLog::query()->create([
            'order_id' => $order->id,
            'from_state' => null,
            'to_state' => OrderState::Draft->value,
            'actor_type' => 'system',
            'reason' => 'created',
            'created_at' => now(),
        ]);

        $this->postJson('/api/v1/orders/'.$order->id.'/transition', [
            'to_state' => OrderState::PendingPayment->value,
        ])->assertOk();

        $firstCount = OrderStateLog::query()->where('order_id', $order->id)->count();

        $this->postJson('/api/v1/orders/'.$order->id.'/transition', [
            'to_state' => OrderState::PendingPayment->value,
        ])->assertOk();

        $secondCount = OrderStateLog::query()->where('order_id', $order->id)->count();

        $this->assertSame($firstCount, $secondCount);
    }

    public function test_can_list_orders_with_filters_and_pagination(): void
    {
        $customerA = User::query()->create([
            'name' => 'Customer A',
            'email' => 'customer.a@example.com',
            'password' => bcrypt('password'),
        ]);
        $customerB = User::query()->create([
            'name' => 'Customer B',
            'email' => 'customer.b@example.com',
            'password' => bcrypt('password'),
        ]);

        Order::query()->create([
            'customer_id' => $customerA->id,
            'order_no' => 'ORD-A-001',
            'state' => OrderState::Draft->value,
            'payment_state' => 'pending',
            'pickup_name' => 'Ali',
            'pickup_address' => 'A',
            'dropoff_address' => 'B',
            'total_amount' => 1000,
            'currency' => 'TRY',
        ]);
        Order::query()->create([
            'customer_id' => $customerA->id,
            'order_no' => 'ORD-A-002',
            'state' => OrderState::PendingPayment->value,
            'payment_state' => 'pending',
            'pickup_name' => 'Veli',
            'pickup_address' => 'A',
            'dropoff_address' => 'B',
            'total_amount' => 2000,
            'currency' => 'TRY',
        ]);
        Order::query()->create([
            'customer_id' => $customerB->id,
            'order_no' => 'ORD-B-001',
            'state' => OrderState::Paid->value,
            'payment_state' => 'succeeded',
            'pickup_name' => 'Ayse',
            'pickup_address' => 'A',
            'dropoff_address' => 'B',
            'total_amount' => 3000,
            'currency' => 'TRY',
        ]);

        $response = $this->getJson('/api/v1/orders?customer_id='.$customerA->id.'&state=draft&per_page=1&q=ORD-A');
        $response->assertOk()->assertJsonPath('success', true);
        $response->assertJsonPath('meta.per_page', 1);
        $response->assertJsonPath('meta.total', 1);
        $this->assertSame('ORD-A-001', $response->json('data.0.order_no'));
    }

    public function test_can_get_order_detail(): void
    {
        $order = Order::query()->create([
            'order_no' => 'ORD-DETAIL-001',
            'state' => OrderState::Draft->value,
            'payment_state' => 'pending',
            'pickup_address' => 'A',
            'dropoff_address' => 'B',
            'total_amount' => 1000,
            'currency' => 'TRY',
        ]);

        $response = $this->getJson('/api/v1/orders/'.$order->id);
        $response->assertOk()->assertJsonPath('success', true);
        $this->assertSame('ORD-DETAIL-001', $response->json('data.order_no'));
    }
}
