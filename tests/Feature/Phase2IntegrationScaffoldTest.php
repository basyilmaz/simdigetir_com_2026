<?php

namespace Tests\Feature;

use App\Models\NotificationTemplate;
use App\Models\Order;
use Tests\TestCase;

class Phase2IntegrationScaffoldTest extends TestCase
{
    public function test_paytr_sandbox_payment_initiation_works_with_gateway_abstraction(): void
    {
        config()->set('payments.providers.paytr.merchant_id', 'merchant-id');
        config()->set('payments.providers.paytr.merchant_key', 'merchant-key');
        config()->set('payments.providers.paytr.merchant_salt', 'merchant-salt');
        config()->set('payments.providers.paytr.base_url', 'https://www.paytr.com');
        config()->set('payments.providers.paytr.sandbox', true);

        $order = Order::query()->create([
            'order_no' => 'ORD-IYZ-001',
            'state' => 'draft',
            'payment_state' => 'pending',
            'pickup_address' => 'P',
            'dropoff_address' => 'D',
            'total_amount' => 2100,
            'currency' => 'TRY',
        ]);

        $response = $this->postJson('/api/v1/payments/initiate', [
            'provider' => 'paytr',
            'order_id' => $order->id,
        ]);

        $response->assertStatus(201)->assertJsonPath('success', true);
        $this->assertStringContainsString('paytr.com/odeme/guvenli/', (string) $response->json('data.payment_url'));
        $this->assertDatabaseHas('payment_transactions', [
            'order_id' => $order->id,
            'provider' => 'paytr',
            'status' => 'pending',
        ]);
    }

    public function test_notification_orchestrator_uses_sms_gateway_provider(): void
    {
        config()->set('services_integrations.sms.default', 'netgsm');
        config()->set('services_integrations.sms.providers.netgsm.sandbox', true);

        NotificationTemplate::query()->create([
            'event_key' => 'order_assigned',
            'channel' => 'sms',
            'subject' => null,
            'body' => 'Siparisiniz atandi: {order_no}',
            'is_active' => true,
            'variables' => ['order_no'],
        ]);

        $response = $this->postJson('/api/v1/notifications/dispatch', [
            'event_key' => 'order_assigned',
            'targets' => [
                ['channel' => 'sms', 'target' => '05320000123'],
            ],
            'context' => ['order_no' => 'ORD-IYZ-001'],
        ]);

        $response->assertStatus(201)->assertJsonPath('success', true)->assertJsonPath('data.count', 1);
        $this->assertDatabaseHas('notification_dispatches', [
            'event_key' => 'order_assigned',
            'channel' => 'sms',
            'status' => 'sent',
        ]);
    }

    public function test_quote_includes_distance_estimation_from_coordinates(): void
    {
        config()->set('services_integrations.maps.google_maps_api_key', '');

        $response = $this->postJson('/api/v1/quotes', [
            'base_amount' => 1000,
            'zone' => 'A',
            'hour' => 14,
            'currency' => 'TRY',
            'pickup' => [
                'lat' => 41.015137,
                'lng' => 28.979530,
            ],
            'dropoff' => [
                'lat' => 41.041173,
                'lng' => 29.009117,
            ],
        ]);

        $response->assertStatus(201)->assertJsonPath('success', true);
        $this->assertGreaterThan(0, (int) $response->json('data.distance_meters'));
        $this->assertGreaterThan(0, (int) $response->json('data.duration_seconds'));
        $this->assertSame('haversine_fallback', (string) $response->json('data.distance_source'));
    }
}
