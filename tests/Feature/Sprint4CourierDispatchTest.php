<?php

namespace Tests\Feature;

use App\Models\Courier;
use App\Models\CourierAvailability;
use App\Models\NotificationDispatch;
use App\Models\Order;
use App\Models\OrderAssignment;
use App\Models\OrderProof;
use App\Models\OrderTrackingEvent;
use Tests\TestCase;

class Sprint4CourierDispatchTest extends TestCase
{
    public function test_approved_courier_can_receive_and_complete_order(): void
    {
        $courier = Courier::query()->create([
            'full_name' => 'Courier One',
            'phone' => '05320000001',
            'status' => 'approved',
        ]);
        CourierAvailability::query()->create([
            'courier_id' => $courier->id,
            'is_online' => true,
            'zone' => 'A',
            'last_seen_at' => now(),
        ]);

        $order = Order::query()->create([
            'order_no' => 'ORD-S4-001',
            'state' => 'paid',
            'payment_state' => 'succeeded',
            'pickup_phone' => '05550000041',
            'dropoff_phone' => '05550000042',
            'pickup_address' => 'P',
            'dropoff_address' => 'D',
            'total_amount' => 1200,
            'currency' => 'TRY',
        ]);

        $auto = $this->postJson('/api/v1/dispatch/orders/'.$order->id.'/auto-assign');
        $auto->assertStatus(201)->assertJsonPath('success', true);

        $tasks = $this->getJson('/api/v1/couriers/'.$courier->id.'/tasks');
        $tasks->assertOk()->assertJsonPath('success', true);
        $this->assertNotEmpty($tasks->json('data'));

        $this->postJson('/api/v1/couriers/'.$courier->id.'/orders/'.$order->id.'/accept')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->postJson('/api/v1/couriers/'.$courier->id.'/orders/'.$order->id.'/pickup', [
            'proof_type' => 'photo',
            'file_url' => 'https://cdn.simdigetir.test/pickup-proof.jpg',
        ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $tracking = $this->postJson('/api/v1/orders/'.$order->id.'/tracking-events', [
            'courier_id' => $courier->id,
            'event_type' => 'eta_update',
            'eta_seconds' => 900,
            'lat' => 41.02,
            'lng' => 29.01,
            'note' => 'Kurye yolda',
        ]);
        $tracking->assertStatus(201)->assertJsonPath('success', true);

        $deliver = $this->postJson('/api/v1/couriers/'.$courier->id.'/orders/'.$order->id.'/deliver', [
            'proof_type' => 'otp',
            'proof_value' => '123456',
        ]);
        $deliver->assertOk()->assertJsonPath('success', true);

        $order->refresh();
        $this->assertSame('delivered', $order->state);
        $this->assertDatabaseHas('order_assignments', [
            'order_id' => $order->id,
            'courier_id' => $courier->id,
            'status' => 'completed',
        ]);
        $this->assertDatabaseHas('order_proofs', [
            'order_id' => $order->id,
            'courier_id' => $courier->id,
            'stage' => 'pickup',
            'proof_type' => 'photo',
            'file_url' => 'https://cdn.simdigetir.test/pickup-proof.jpg',
        ]);
        $this->assertDatabaseHas('order_proofs', [
            'order_id' => $order->id,
            'courier_id' => $courier->id,
            'stage' => 'delivery',
            'proof_type' => 'otp',
        ]);
        $this->assertDatabaseHas('notification_dispatches', [
            'event_key' => 'orders.pickup_completed',
            'channel' => 'sms',
            'target' => '05550000041',
            'status' => 'sent',
        ]);
        $this->assertDatabaseHas('notification_dispatches', [
            'event_key' => 'orders.pickup_completed',
            'channel' => 'sms',
            'target' => '05550000042',
            'status' => 'sent',
        ]);
        $this->assertDatabaseHas('notification_dispatches', [
            'event_key' => 'orders.delivery_completed',
            'channel' => 'sms',
            'target' => '05550000041',
            'status' => 'sent',
        ]);
        $this->assertDatabaseHas('notification_dispatches', [
            'event_key' => 'orders.delivery_completed',
            'channel' => 'sms',
            'target' => '05550000042',
            'status' => 'sent',
        ]);

        $pickupDispatch = NotificationDispatch::query()
            ->where('event_key', 'orders.pickup_completed')
            ->where('target', '05550000041')
            ->latest('id')
            ->first();
        $this->assertNotNull($pickupDispatch);
        $this->assertStringContainsString('/siparis-takip?order_no=ORD-S4-001', (string) data_get($pickupDispatch?->payload, 'body'));
        $this->assertStringContainsString('phone=05550000041', (string) data_get($pickupDispatch?->payload, 'body'));

        $deliveryDispatch = NotificationDispatch::query()
            ->where('event_key', 'orders.delivery_completed')
            ->where('target', '05550000042')
            ->latest('id')
            ->first();
        $this->assertNotNull($deliveryDispatch);
        $this->assertStringContainsString('/siparis-takip?order_no=ORD-S4-001', (string) data_get($deliveryDispatch?->payload, 'body'));
        $this->assertStringContainsString('phone=05550000042', (string) data_get($deliveryDispatch?->payload, 'body'));

        $this->assertTrue(OrderTrackingEvent::query()->where('order_id', $order->id)->exists());
    }

    public function test_dispatch_can_auto_assign_and_reassign_with_sla_timeout(): void
    {
        $courierA = Courier::query()->create([
            'full_name' => 'Courier A',
            'phone' => '05320000011',
            'status' => 'approved',
        ]);
        $courierB = Courier::query()->create([
            'full_name' => 'Courier B',
            'phone' => '05320000012',
            'status' => 'approved',
        ]);

        CourierAvailability::query()->create([
            'courier_id' => $courierA->id,
            'is_online' => true,
            'zone' => 'A',
            'last_seen_at' => now(),
        ]);
        CourierAvailability::query()->create([
            'courier_id' => $courierB->id,
            'is_online' => true,
            'zone' => 'A',
            'last_seen_at' => now(),
        ]);

        $order = Order::query()->create([
            'order_no' => 'ORD-S4-002',
            'state' => 'paid',
            'payment_state' => 'succeeded',
            'pickup_address' => 'P',
            'dropoff_address' => 'D',
            'total_amount' => 1500,
            'currency' => 'TRY',
        ]);

        $this->postJson('/api/v1/dispatch/orders/'.$order->id.'/auto-assign')
            ->assertStatus(201)
            ->assertJsonPath('success', true);

        $firstAssignment = OrderAssignment::query()
            ->where('order_id', $order->id)
            ->where('status', 'pending')
            ->firstOrFail();
        $firstCourierId = $firstAssignment->courier_id;
        $firstAssignment->assigned_at = now()->subMinutes(20);
        $firstAssignment->save();

        $reassign = $this->postJson('/api/v1/dispatch/reassign-overdue', [
            'sla_minutes' => 10,
        ]);
        $reassign->assertOk()->assertJsonPath('success', true);
        $this->assertSame(1, (int) $reassign->json('data.reassigned_count'));

        $newAssignment = OrderAssignment::query()
            ->where('order_id', $order->id)
            ->where('status', 'pending')
            ->latest('id')
            ->firstOrFail();
        $this->assertNotSame($firstCourierId, $newAssignment->courier_id);
    }

    public function test_delivery_completion_requires_proof_method(): void
    {
        $courier = Courier::query()->create([
            'full_name' => 'Courier Proof',
            'phone' => '05320000021',
            'status' => 'approved',
        ]);
        CourierAvailability::query()->create([
            'courier_id' => $courier->id,
            'is_online' => true,
            'zone' => 'A',
            'last_seen_at' => now(),
        ]);

        $order = Order::query()->create([
            'order_no' => 'ORD-S4-003',
            'state' => 'picked_up',
            'payment_state' => 'succeeded',
            'pickup_address' => 'P',
            'dropoff_address' => 'D',
            'total_amount' => 1700,
            'currency' => 'TRY',
        ]);

        OrderAssignment::query()->create([
            'order_id' => $order->id,
            'courier_id' => $courier->id,
            'status' => 'accepted',
            'assignment_type' => 'manual',
            'assigned_at' => now(),
            'accepted_at' => now(),
        ]);

        $deliver = $this->postJson('/api/v1/couriers/'.$courier->id.'/orders/'.$order->id.'/deliver', [
            'proof_type' => 'otp',
            // proof_value intentionally missing
        ]);

        $deliver->assertStatus(422)->assertJsonPath('success', false);
        $this->assertFalse(OrderProof::query()->where('order_id', $order->id)->exists());
    }
}
