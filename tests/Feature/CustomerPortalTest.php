<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderProof;
use App\Models\OrderStateLog;
use App\Models\OrderTrackingEvent;
use App\Models\User;
use Modules\Settings\Models\Setting;
use Tests\TestCase;

class CustomerPortalTest extends TestCase
{
    public function test_guest_is_redirected_to_customer_login_when_opening_dashboard(): void
    {
        $response = $this->get('/hesabim');

        $response->assertRedirect(route('checkout.customer.login'));
    }

    public function test_customer_can_login_with_phone_and_view_own_orders(): void
    {
        $customer = User::factory()->create([
            'name' => 'Portal Customer',
            'phone' => '905551112233',
            'password' => 'secret123',
        ]);

        $otherCustomer = User::factory()->create();

        Order::query()->create([
            'customer_id' => $customer->id,
            'order_no' => 'ORD-PORTAL-001',
            'state' => 'paid',
            'payment_state' => 'cash_on_delivery',
            'payment_method' => 'cash',
            'payment_timing' => 'delivery',
            'pickup_address' => 'Sisli',
            'dropoff_address' => 'Kadikoy',
            'total_amount' => 8200,
            'currency' => 'TRY',
        ]);

        Order::query()->create([
            'customer_id' => $otherCustomer->id,
            'order_no' => 'ORD-PORTAL-OTHER',
            'state' => 'delivered',
            'payment_state' => 'succeeded',
            'payment_method' => 'bank_transfer',
            'payment_timing' => 'prepaid',
            'pickup_address' => 'Levent',
            'dropoff_address' => 'Maslak',
            'total_amount' => 9200,
            'currency' => 'TRY',
        ]);

        $portalOrder = Order::query()->where('order_no', 'ORD-PORTAL-001')->firstOrFail();

        OrderStateLog::query()->create([
            'order_id' => $portalOrder->id,
            'from_state' => null,
            'to_state' => 'paid',
            'actor_type' => 'checkout',
            'actor_id' => $customer->id,
            'reason' => 'checkout_finalized',
            'metadata' => [],
            'created_at' => now()->subMinutes(15),
        ]);

        OrderTrackingEvent::query()->create([
            'order_id' => $portalOrder->id,
            'event_type' => 'eta_update',
            'eta_seconds' => 600,
            'note' => 'Kurye cikis yapti',
            'created_at' => now()->subMinutes(5),
        ]);

        OrderProof::query()->create([
            'order_id' => $portalOrder->id,
            'stage' => 'pickup',
            'proof_type' => 'photo',
            'file_url' => 'https://cdn.simdigetir.test/portal-proof.jpg',
            'created_at' => now()->subMinutes(4),
        ]);

        $response = $this->followingRedirects()->post('/hesabim/giris', [
            'phone' => '0555 111 22 33',
            'password' => 'secret123',
        ]);

        $response->assertOk();
        $response->assertSee('Portal Customer');
        $response->assertSee('ORD-PORTAL-001');
        $response->assertDontSee('ORD-PORTAL-OTHER');
        $response->assertSee('/hesabim/siparisler/ORD-PORTAL-001');
        $response->assertSee('/siparis-takip?order_no=ORD-PORTAL-001');
        $response->assertSee('Detaylari Goster');
        $response->assertSee('checkout_finalized');
        $response->assertSee('Kurye cikis yapti');
        $response->assertSee('portal-proof.jpg');
    }

    public function test_customer_can_open_own_order_detail_page(): void
    {
        Setting::setValue('checkout.bank_transfer_title', 'Musteri Havale Notu', 'checkout');
        Setting::setValue('checkout.bank_transfer_body', 'Havale odemeniz onaylanana kadar siparis beklemede kalir.', 'checkout');
        Setting::setValue('checkout.bank_transfer_bank_name', 'Akbank', 'checkout');
        Setting::setValue('checkout.bank_transfer_reference_note', 'Aciklamaya ORD-PORTAL-DETAIL-001 yazin.', 'checkout');

        $customer = User::factory()->create([
            'name' => 'Portal Detail Customer',
            'phone' => '905551119977',
            'password' => 'secret123',
        ]);

        $order = Order::query()->create([
            'customer_id' => $customer->id,
            'order_no' => 'ORD-PORTAL-DETAIL-001',
            'state' => 'pending_payment',
            'payment_state' => 'awaiting_reconcile',
            'payment_method' => 'bank_transfer',
            'payment_timing' => 'prepaid',
            'payer_role' => 'sender',
            'pickup_name' => 'Gonderen Kisi',
            'pickup_phone' => '05550000001',
            'pickup_address' => 'Sisli Merkez',
            'dropoff_name' => 'Alici Kisi',
            'dropoff_phone' => '05550000002',
            'dropoff_address' => 'Kadikoy Merkez',
            'distance_meters' => 6400,
            'duration_seconds' => 1500,
            'total_amount' => 12000,
            'currency' => 'TRY',
            'vehicle_type' => 'moto',
            'notes' => ['delivery_notes' => 'Resepsiyona birakma.'],
            'checkout_snapshot' => [
                'service_type' => 'moto',
                'same_person' => false,
            ],
            'price_breakdown' => ['source' => 'quote'],
        ]);

        $order->packages()->create([
            'package_type' => 'electronics',
            'quantity' => 1,
            'weight_grams' => 1200,
            'declared_value_amount' => 25000,
            'description' => 'Tablet kutusu',
        ]);

        $order->paymentTransactions()->create([
            'provider' => 'bank_transfer',
            'provider_reference' => 'BNK-PORTAL-001',
            'amount' => 12000,
            'currency' => 'TRY',
            'status' => 'pending',
        ]);

        OrderStateLog::query()->create([
            'order_id' => $order->id,
            'from_state' => null,
            'to_state' => 'pending_payment',
            'actor_type' => 'checkout',
            'actor_id' => $customer->id,
            'reason' => 'checkout_finalized',
            'metadata' => [],
            'created_at' => now()->subMinutes(10),
        ]);

        OrderTrackingEvent::query()->create([
            'order_id' => $order->id,
            'event_type' => 'pickup_eta',
            'eta_seconds' => 900,
            'note' => 'Kurye atama bekliyor',
            'created_at' => now()->subMinutes(5),
        ]);

        OrderProof::query()->create([
            'order_id' => $order->id,
            'stage' => 'pickup',
            'proof_type' => 'photo',
            'file_url' => 'https://cdn.simdigetir.test/order-detail-proof.jpg',
            'created_at' => now()->subMinutes(4),
        ]);

        $this->post('/hesabim/giris', [
            'phone' => '0555 111 99 77',
            'password' => 'secret123',
        ]);

        $response = $this->get('/hesabim/siparisler/ORD-PORTAL-DETAIL-001');

        $response->assertOk();
        $response->assertSee('ORD-PORTAL-DETAIL-001');
        $response->assertSee('Tablet kutusu');
        $response->assertSee('BNK-PORTAL-001');
        $response->assertSee('Kurye atama bekliyor');
        $response->assertSee('order-detail-proof.jpg');
        $response->assertSee('Resepsiyona birakma.');
        $response->assertSee('Musteri Havale Notu');
        $response->assertSee('Akbank');
        $response->assertSee('Aciklamaya ORD-PORTAL-DETAIL-001 yazin.');
    }

    public function test_customer_dashboard_supports_state_filter_and_search(): void
    {
        $customer = User::factory()->create([
            'name' => 'Portal Filter Customer',
            'phone' => '905551117755',
            'password' => 'secret123',
        ]);

        Order::query()->create([
            'customer_id' => $customer->id,
            'order_no' => 'ORD-PORTAL-ACTIVE-001',
            'state' => 'assigned',
            'payment_state' => 'paid',
            'payment_method' => 'card',
            'payment_timing' => 'prepaid',
            'pickup_address' => 'Sisli Bomonti',
            'dropoff_address' => 'Kadikoy Moda',
            'total_amount' => 10100,
            'currency' => 'TRY',
        ]);

        Order::query()->create([
            'customer_id' => $customer->id,
            'order_no' => 'ORD-PORTAL-DELIVERED-001',
            'state' => 'delivered',
            'payment_state' => 'succeeded',
            'payment_method' => 'bank_transfer',
            'payment_timing' => 'prepaid',
            'pickup_address' => 'Levent',
            'dropoff_address' => 'Maslak',
            'total_amount' => 9800,
            'currency' => 'TRY',
        ]);

        Order::query()->create([
            'customer_id' => $customer->id,
            'order_no' => 'ORD-PORTAL-FAILED-001',
            'state' => 'failed',
            'payment_state' => 'failed',
            'payment_method' => 'cash',
            'payment_timing' => 'delivery',
            'pickup_address' => 'Uskudar',
            'dropoff_address' => 'Besiktas',
            'total_amount' => 7600,
            'currency' => 'TRY',
        ]);

        $this->post('/hesabim/giris', [
            'phone' => '0555 111 77 55',
            'password' => 'secret123',
        ]);

        $response = $this->get('/hesabim?state=active&search=Bomonti');

        $response->assertOk();
        $response->assertSee('ORD-PORTAL-ACTIVE-001');
        $response->assertDontSee('ORD-PORTAL-DELIVERED-001');
        $response->assertDontSee('ORD-PORTAL-FAILED-001');
        $response->assertSee('<strong>1</strong> sonuc', false);
        $response->assertSee('/hesabim?state=active&amp;search=Bomonti', false);
        $response->assertSee('Temizle');
    }

    public function test_customer_cannot_open_another_customers_order_detail_page(): void
    {
        $customer = User::factory()->create([
            'phone' => '905551110011',
            'password' => 'secret123',
        ]);

        $otherCustomer = User::factory()->create();

        Order::query()->create([
            'customer_id' => $otherCustomer->id,
            'order_no' => 'ORD-PORTAL-FORBIDDEN-001',
            'state' => 'paid',
            'payment_state' => 'cash_on_delivery',
            'payment_method' => 'cash',
            'payment_timing' => 'delivery',
            'pickup_address' => 'Levent',
            'dropoff_address' => 'Maslak',
            'total_amount' => 5000,
            'currency' => 'TRY',
        ]);

        $this->post('/hesabim/giris', [
            'phone' => '0555 111 00 11',
            'password' => 'secret123',
        ]);

        $response = $this->get('/hesabim/siparisler/ORD-PORTAL-FORBIDDEN-001');

        $response->assertNotFound();
    }

    public function test_customer_login_rejects_wrong_password(): void
    {
        User::factory()->create([
            'phone' => '905551119988',
            'password' => 'secret123',
        ]);

        $response = $this->from('/hesabim/giris')->post('/hesabim/giris', [
            'phone' => '0555 111 99 88',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/hesabim/giris');
        $response->assertSessionHasErrors('phone');
    }
}
