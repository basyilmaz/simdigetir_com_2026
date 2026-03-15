<?php

namespace Tests\Feature;

use App\Models\NotificationDispatch;
use App\Models\NotificationTemplate;
use App\Models\PricingQuote;
use App\Models\User;
use Modules\Settings\Models\Setting;
use Tests\TestCase;

class CheckoutSessionApiTest extends TestCase
{
    public function test_guest_can_create_checkout_session(): void
    {
        $quote = PricingQuote::query()->create([
            'quote_no' => 'QTE-CHECKOUT-001',
            'request_snapshot' => [],
            'resolved_rules' => [],
            'subtotal_amount' => 10000,
            'discount_amount' => 0,
            'surge_amount' => 0,
            'total_amount' => 10000,
            'currency' => 'TRY',
            'expires_at' => now()->addMinutes(15),
        ]);

        $response = $this->postJson('/api/v1/checkout-sessions', [
            'pricing_quote_id' => $quote->id,
            'current_step' => 'quote',
            'payload' => [
                'pickup' => ['address' => 'Sisli'],
                'dropoff' => ['address' => 'Kadikoy'],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.pricing_quote_id', $quote->id)
            ->assertJsonPath('data.current_step', 'quote');

        $this->assertNotEmpty($response->json('data.token'));
        $this->assertDatabaseHas('checkout_sessions', [
            'pricing_quote_id' => $quote->id,
            'current_step' => 'quote',
            'status' => 'draft',
        ]);
    }

    public function test_guest_can_update_checkout_session_payload(): void
    {
        $createResponse = $this->postJson('/api/v1/checkout-sessions', [
            'payload' => [
                'pickup' => ['address' => 'Besiktas'],
            ],
        ]);

        $token = (string) $createResponse->json('data.token');
        $user = User::factory()->create();

        $response = $this->patchJson('/api/v1/checkout-sessions/'.$token, [
            'customer_id' => $user->id,
            'current_step' => 'auth',
            'status' => 'authenticated',
            'payload' => [
                'recipient' => ['name' => 'Alici'],
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.customer_id', $user->id)
            ->assertJsonPath('data.current_step', 'auth')
            ->assertJsonPath('data.status', 'authenticated')
            ->assertJsonPath('data.payload.pickup.address', 'Besiktas')
            ->assertJsonPath('data.payload.recipient.name', 'Alici');
    }

    public function test_guest_can_fetch_checkout_session_by_token(): void
    {
        $createResponse = $this->postJson('/api/v1/checkout-sessions', [
            'payload' => [
                'service_type' => 'moto',
            ],
        ]);

        $token = (string) $createResponse->json('data.token');

        $response = $this->getJson('/api/v1/checkout-sessions/'.$token);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.token', $token)
            ->assertJsonPath('data.payload.service_type', 'moto');
    }

    public function test_authenticated_customer_can_finalize_card_checkout_session(): void
    {
        $quote = PricingQuote::query()->create([
            'quote_no' => 'QTE-CHECKOUT-CARD-001',
            'request_snapshot' => ['context' => ['distance_meters' => 5400, 'duration_seconds' => 1200]],
            'resolved_rules' => [],
            'subtotal_amount' => 12500,
            'discount_amount' => 0,
            'surge_amount' => 0,
            'total_amount' => 12500,
            'currency' => 'TRY',
            'expires_at' => now()->addMinutes(15),
        ]);
        $user = User::factory()->create();

        $createResponse = $this->postJson('/api/v1/checkout-sessions', [
            'pricing_quote_id' => $quote->id,
            'current_step' => 'payment',
            'payload' => [
                'service_type' => 'moto',
                'pickup' => ['address' => 'Sisli Merkez', 'name' => 'Gonderen', 'phone' => '05550000001'],
                'dropoff' => ['address' => 'Kadikoy Merkez', 'name' => 'Alici', 'phone' => '05550000002'],
                'payment' => ['method' => 'card', 'timing' => 'prepaid', 'payer_role' => 'sender'],
                'packages' => [
                    ['package_type' => 'document', 'quantity' => 1, 'description' => 'Evrak'],
                ],
            ],
        ]);

        $token = (string) $createResponse->json('data.token');

        $response = $this->postJson('/api/v1/checkout-sessions/'.$token.'/finalize', [
            'customer_id' => $user->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.order.state', 'draft')
            ->assertJsonPath('data.order.payment_state', 'pending')
            ->assertJsonPath('data.order.payment_method', 'card')
            ->assertJsonPath('data.order.payment_timing', 'prepaid')
            ->assertJsonPath('data.order.payer_role', 'sender')
            ->assertJsonPath('data.next_action', 'initiate_card_payment')
            ->assertJsonPath('data.payment_transaction_id', null)
            ->assertJsonPath('data.checkout_session.status', 'completed');

        $orderId = (int) $response->json('data.order.id');

        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'customer_id' => $user->id,
            'state' => 'draft',
            'payment_state' => 'pending',
            'payment_method' => 'card',
            'payment_timing' => 'prepaid',
            'payer_role' => 'sender',
            'total_amount' => 12500,
            'currency' => 'TRY',
        ]);
        $this->assertDatabaseHas('order_packages', [
            'order_id' => $orderId,
            'package_type' => 'document',
            'quantity' => 1,
        ]);
    }

    public function test_completed_card_checkout_session_can_initiate_paytr_payment(): void
    {
        config()->set('payments.default_provider', 'paytr');
        config()->set('payments.providers.paytr.merchant_id', 'merchant-id');
        config()->set('payments.providers.paytr.merchant_key', 'merchant-key');
        config()->set('payments.providers.paytr.merchant_salt', 'merchant-salt');
        config()->set('payments.providers.paytr.base_url', 'https://www.paytr.com');
        config()->set('payments.providers.paytr.sandbox', true);

        $quote = PricingQuote::query()->create([
            'quote_no' => 'QTE-CHECKOUT-CARD-INIT-001',
            'request_snapshot' => [],
            'resolved_rules' => [],
            'subtotal_amount' => 15000,
            'discount_amount' => 0,
            'surge_amount' => 0,
            'total_amount' => 15000,
            'currency' => 'TRY',
            'expires_at' => now()->addMinutes(15),
        ]);
        $user = User::factory()->create();

        $createResponse = $this->postJson('/api/v1/checkout-sessions', [
            'pricing_quote_id' => $quote->id,
            'payload' => [
                'pickup' => ['address' => 'Levent Buyukdere', 'name' => 'Gonderen', 'phone' => '05550000101'],
                'dropoff' => ['address' => 'Acibadem', 'name' => 'Alici', 'phone' => '05550000102'],
                'payment' => ['method' => 'card', 'timing' => 'prepaid', 'payer_role' => 'sender'],
            ],
        ]);

        $token = (string) $createResponse->json('data.token');

        $finalizeResponse = $this->postJson('/api/v1/checkout-sessions/'.$token.'/finalize', [
            'customer_id' => $user->id,
        ]);

        $finalizeResponse->assertStatus(201)
            ->assertJsonPath('data.next_action', 'initiate_card_payment');

        $paymentResponse = $this->postJson('/api/v1/checkout-sessions/'.$token.'/payments/initiate');

        $paymentResponse->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.provider', 'paytr');

        $this->assertStringContainsString(
            'paytr.com/odeme/guvenli/',
            (string) $paymentResponse->json('data.payment_url')
        );

        $orderId = (int) $finalizeResponse->json('data.order.id');

        $this->assertDatabaseHas('payment_transactions', [
            'order_id' => $orderId,
            'provider' => 'paytr',
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'state' => 'pending_payment',
            'payment_state' => 'pending',
            'payment_method' => 'card',
        ]);
    }

    public function test_checkout_card_payment_initiation_rejects_bank_transfer_sessions(): void
    {
        config()->set('payments.default_provider', 'paytr');
        config()->set('payments.providers.paytr.merchant_id', 'merchant-id');
        config()->set('payments.providers.paytr.merchant_key', 'merchant-key');
        config()->set('payments.providers.paytr.merchant_salt', 'merchant-salt');

        $quote = PricingQuote::query()->create([
            'quote_no' => 'QTE-CHECKOUT-CARD-INIT-INVALID-001',
            'request_snapshot' => [],
            'resolved_rules' => [],
            'subtotal_amount' => 10000,
            'discount_amount' => 0,
            'surge_amount' => 0,
            'total_amount' => 10000,
            'currency' => 'TRY',
            'expires_at' => now()->addMinutes(15),
        ]);
        $user = User::factory()->create();

        $createResponse = $this->postJson('/api/v1/checkout-sessions', [
            'pricing_quote_id' => $quote->id,
            'payload' => [
                'pickup' => ['address' => 'Bomonti'],
                'dropoff' => ['address' => 'Moda'],
                'payment' => ['method' => 'bank_transfer', 'timing' => 'prepaid'],
            ],
        ]);

        $token = (string) $createResponse->json('data.token');

        $this->postJson('/api/v1/checkout-sessions/'.$token.'/finalize', [
            'customer_id' => $user->id,
        ])->assertStatus(201);

        $this->postJson('/api/v1/checkout-sessions/'.$token.'/payments/initiate')
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Bu checkout oturumu kart odemesi icin uygun degil.');
    }

    public function test_authenticated_customer_can_finalize_bank_transfer_checkout_session(): void
    {
        Setting::setValue('checkout.bank_transfer_title', 'Havale Bilgisi', 'checkout');
        Setting::setValue('checkout.bank_transfer_bank_name', 'Yapi Kredi', 'checkout');
        Setting::setValue('checkout.bank_transfer_account_holder', 'SimdiGetir A.S.', 'checkout');
        Setting::setValue('checkout.bank_transfer_iban', 'TR22 2222 2222 2222 2222 2222 22', 'checkout');
        Setting::setValue('checkout.bank_transfer_reference_note', 'Aciklamaya siparis numarasini yazin.', 'checkout');

        NotificationTemplate::query()->create([
            'event_key' => 'orders.payment_pending_bank_transfer',
            'channel' => 'sms',
            'subject' => null,
            'body' => 'Siparisiniz alindi. No: {order_no}. Havale odemesi bekleniyor. Tutar: {total_amount}. Takip: {track_url}',
            'is_active' => true,
            'variables' => ['order_no', 'total_amount', 'track_url'],
        ]);

        $quote = PricingQuote::query()->create([
            'quote_no' => 'QTE-CHECKOUT-BANK-001',
            'request_snapshot' => [],
            'resolved_rules' => [],
            'subtotal_amount' => 9800,
            'discount_amount' => 0,
            'surge_amount' => 200,
            'total_amount' => 10000,
            'currency' => 'TRY',
            'expires_at' => now()->addMinutes(15),
        ]);
        $user = User::factory()->create();

        $createResponse = $this->postJson('/api/v1/checkout-sessions', [
            'pricing_quote_id' => $quote->id,
            'payload' => [
                'pickup' => ['address' => 'Atasehir', 'name' => 'Gonderen', 'phone' => '05550000031'],
                'dropoff' => ['address' => 'Bostanci', 'name' => 'Alici', 'phone' => '05550000032'],
                'payment' => ['method' => 'bank_transfer', 'timing' => 'prepaid'],
            ],
        ]);

        $token = (string) $createResponse->json('data.token');

        $response = $this->postJson('/api/v1/checkout-sessions/'.$token.'/finalize', [
            'customer_id' => $user->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.order.state', 'pending_payment')
            ->assertJsonPath('data.order.payment_state', 'awaiting_reconcile')
            ->assertJsonPath('data.order.payment_method', 'bank_transfer')
            ->assertJsonPath('data.order.payment_timing', 'prepaid')
            ->assertJsonPath('data.order.payer_role', 'sender')
            ->assertJsonPath('data.next_action', 'await_bank_transfer_reconcile');

        $transactionId = (int) $response->json('data.payment_transaction_id');
        $orderId = (int) $response->json('data.order.id');

        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'state' => 'pending_payment',
            'payment_state' => 'awaiting_reconcile',
            'payment_method' => 'bank_transfer',
            'payment_timing' => 'prepaid',
        ]);
        $this->assertDatabaseHas('payment_transactions', [
            'id' => $transactionId,
            'order_id' => $orderId,
            'provider' => 'bank_transfer',
            'status' => 'pending',
            'amount' => 10000,
        ]);
        $this->assertDatabaseHas('notification_dispatches', [
            'event_key' => 'orders.order_created',
            'channel' => 'sms',
            'target' => '05550000031',
            'status' => 'sent',
        ]);
        $this->assertDatabaseHas('notification_dispatches', [
            'event_key' => 'orders.payment_pending_bank_transfer',
            'channel' => 'sms',
            'target' => '05550000031',
            'status' => 'sent',
        ]);
        $this->assertDatabaseHas('notification_templates', [
            'event_key' => 'orders.order_created',
            'channel' => 'sms',
            'is_active' => 1,
        ]);
        $this->assertDatabaseHas('notification_templates', [
            'event_key' => 'orders.payment_pending_bank_transfer',
            'channel' => 'sms',
            'is_active' => 1,
        ]);

        $bankTransferTemplate = NotificationTemplate::query()
            ->where('event_key', 'orders.payment_pending_bank_transfer')
            ->where('channel', 'sms')
            ->first();
        $this->assertNotNull($bankTransferTemplate);
        $this->assertStringContainsString('{bank_transfer_instruction}', (string) $bankTransferTemplate?->body);

        $orderCreatedDispatch = NotificationDispatch::query()
            ->where('event_key', 'orders.order_created')
            ->where('target', '05550000031')
            ->latest('id')
            ->first();
        $this->assertNotNull($orderCreatedDispatch);
        $this->assertStringContainsString('/siparis-takip?order_no=', (string) data_get($orderCreatedDispatch?->payload, 'body'));
        $this->assertStringContainsString('phone=05550000031', (string) data_get($orderCreatedDispatch?->payload, 'body'));

        $bankTransferDispatch = NotificationDispatch::query()
            ->where('event_key', 'orders.payment_pending_bank_transfer')
            ->where('target', '05550000031')
            ->latest('id')
            ->first();
        $this->assertNotNull($bankTransferDispatch);
        $this->assertStringContainsString('/siparis-takip?order_no=', (string) data_get($bankTransferDispatch?->payload, 'body'));
        $this->assertStringContainsString('phone=05550000031', (string) data_get($bankTransferDispatch?->payload, 'body'));
        $this->assertStringContainsString('Yapi Kredi', (string) data_get($bankTransferDispatch?->payload, 'body'));
        $this->assertStringContainsString('TR22 2222 2222 2222 2222 2222 22', (string) data_get($bankTransferDispatch?->payload, 'body'));
        $this->assertStringContainsString('Aciklamaya siparis numarasini yazin.', (string) data_get($bankTransferDispatch?->payload, 'body'));
    }

    public function test_authenticated_customer_can_finalize_cash_on_delivery_checkout_session(): void
    {
        $quote = PricingQuote::query()->create([
            'quote_no' => 'QTE-CHECKOUT-CASH-001',
            'request_snapshot' => [],
            'resolved_rules' => [],
            'subtotal_amount' => 7000,
            'discount_amount' => 0,
            'surge_amount' => 0,
            'total_amount' => 7000,
            'currency' => 'TRY',
            'expires_at' => now()->addMinutes(15),
        ]);
        $user = User::factory()->create();

        $createResponse = $this->postJson('/api/v1/checkout-sessions', [
            'pricing_quote_id' => $quote->id,
            'payload' => [
                'pickup' => ['address' => 'Levent'],
                'dropoff' => ['address' => 'Maslak'],
                'payment' => ['method' => 'cash', 'timing' => 'delivery'],
            ],
        ]);

        $token = (string) $createResponse->json('data.token');

        $response = $this->postJson('/api/v1/checkout-sessions/'.$token.'/finalize', [
            'customer_id' => $user->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.order.state', 'paid')
            ->assertJsonPath('data.order.payment_state', 'cash_on_delivery')
            ->assertJsonPath('data.order.payment_method', 'cash')
            ->assertJsonPath('data.order.payment_timing', 'delivery')
            ->assertJsonPath('data.order.payer_role', 'recipient')
            ->assertJsonPath('data.next_action', 'dispatch_ready')
            ->assertJsonPath('data.payment_transaction_id', null);

        $this->assertDatabaseHas('orders', [
            'id' => (int) $response->json('data.order.id'),
            'state' => 'paid',
            'payment_state' => 'cash_on_delivery',
            'payment_method' => 'cash',
            'payment_timing' => 'delivery',
            'payer_role' => 'recipient',
        ]);
    }

    public function test_finalize_rejects_invalid_payment_combination(): void
    {
        $quote = PricingQuote::query()->create([
            'quote_no' => 'QTE-CHECKOUT-INVALID-001',
            'request_snapshot' => [],
            'resolved_rules' => [],
            'subtotal_amount' => 5000,
            'discount_amount' => 0,
            'surge_amount' => 0,
            'total_amount' => 5000,
            'currency' => 'TRY',
            'expires_at' => now()->addMinutes(15),
        ]);
        $user = User::factory()->create();

        $createResponse = $this->postJson('/api/v1/checkout-sessions', [
            'pricing_quote_id' => $quote->id,
            'payload' => [
                'pickup' => ['address' => 'Umraniye'],
                'dropoff' => ['address' => 'Mecidiyekoy'],
                'payment' => ['method' => 'cash', 'timing' => 'prepaid'],
            ],
        ]);

        $token = (string) $createResponse->json('data.token');

        $response = $this->postJson('/api/v1/checkout-sessions/'.$token.'/finalize', [
            'customer_id' => $user->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Gecersiz odeme kombinasyonu.');
    }
}
