<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderProof;
use App\Models\OrderStateLog;
use App\Models\OrderTrackingEvent;
use Tests\TestCase;

class PublicOrderTrackingTest extends TestCase
{
    public function test_guest_can_lookup_order_tracking_via_api(): void
    {
        $order = Order::query()->create([
            'order_no' => 'ORD-TRACK-001',
            'state' => 'picked_up',
            'payment_state' => 'cash_on_delivery',
            'payment_method' => 'cash',
            'pickup_name' => 'Gonderen',
            'pickup_phone' => '05550000061',
            'pickup_address' => 'Sisli Merkez',
            'dropoff_name' => 'Alici',
            'dropoff_phone' => '05550000062',
            'dropoff_address' => 'Kadikoy Moda',
            'total_amount' => 14500,
            'currency' => 'TRY',
        ]);

        OrderStateLog::query()->create([
            'order_id' => $order->id,
            'from_state' => null,
            'to_state' => 'paid',
            'actor_type' => 'checkout',
            'actor_id' => 1,
            'reason' => 'checkout_finalized',
            'metadata' => [],
            'created_at' => now()->subMinutes(20),
        ]);

        OrderStateLog::query()->create([
            'order_id' => $order->id,
            'from_state' => 'paid',
            'to_state' => 'picked_up',
            'actor_type' => 'courier',
            'actor_id' => 1,
            'reason' => 'courier_pickup_confirmed',
            'metadata' => [],
            'created_at' => now()->subMinutes(10),
        ]);

        OrderTrackingEvent::query()->create([
            'order_id' => $order->id,
            'event_type' => 'eta_update',
            'eta_seconds' => 900,
            'note' => 'Kurye yolda',
            'created_at' => now()->subMinutes(5),
        ]);

        OrderProof::query()->create([
            'order_id' => $order->id,
            'stage' => 'pickup',
            'proof_type' => 'photo',
            'file_url' => 'https://cdn.simdigetir.test/pickup.jpg',
            'created_at' => now()->subMinutes(9),
        ]);

        $response = $this->getJson('/api/v1/order-tracking?order_no=ORD-TRACK-001&phone=05550000061');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.order.order_no', 'ORD-TRACK-001')
            ->assertJsonPath('data.order.state', 'picked_up')
            ->assertJsonPath('data.order.total_amount_formatted', '145,00 TRY')
            ->assertJsonPath('data.tracking_events.0.note', 'Kurye yolda')
            ->assertJsonPath('data.proofs.0.stage', 'pickup');
    }

    public function test_tracking_page_renders_lookup_result_for_matching_phone(): void
    {
        $order = Order::query()->create([
            'order_no' => 'ORD-TRACK-002',
            'state' => 'delivered',
            'payment_state' => 'succeeded',
            'payment_method' => 'bank_transfer',
            'pickup_name' => 'Gonderen',
            'pickup_phone' => '05550000071',
            'pickup_address' => 'Levent',
            'dropoff_name' => 'Alici',
            'dropoff_phone' => '05550000072',
            'dropoff_address' => 'Maslak',
            'total_amount' => 9900,
            'currency' => 'TRY',
        ]);

        OrderStateLog::query()->create([
            'order_id' => $order->id,
            'from_state' => 'picked_up',
            'to_state' => 'delivered',
            'actor_type' => 'courier',
            'actor_id' => 1,
            'reason' => 'courier_delivery_completed',
            'metadata' => [],
            'created_at' => now()->subMinutes(3),
        ]);

        $response = $this->get('/siparis-takip?order_no=ORD-TRACK-002&phone=05550000072');

        $response->assertOk();
        $response->assertSee('Siparis Takip');
        $response->assertSee('ORD-TRACK-002');
        $response->assertSee('delivered');
        $response->assertSee('Maslak');
    }
}
