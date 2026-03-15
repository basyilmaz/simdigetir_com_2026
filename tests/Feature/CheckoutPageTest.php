<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PricingQuote;
use App\Models\User;
use Modules\Checkout\Models\CheckoutSession;
use Modules\Settings\Models\Setting;
use Tests\TestCase;

class CheckoutPageTest extends TestCase
{
    public function test_guest_can_open_checkout_page_for_tokenized_session(): void
    {
        $quote = PricingQuote::query()->create([
            'quote_no' => 'QTE-WEB-001',
            'request_snapshot' => [
                'context' => [
                    'distance_meters' => 4200,
                    'duration_seconds' => 900,
                ],
            ],
            'resolved_rules' => [],
            'subtotal_amount' => 8000,
            'discount_amount' => 0,
            'surge_amount' => 500,
            'total_amount' => 8500,
            'currency' => 'TRY',
            'expires_at' => now()->addMinutes(15),
        ]);

        $session = CheckoutSession::query()->create([
            'token' => 'checkout-web-token-001',
            'pricing_quote_id' => $quote->id,
            'status' => 'draft',
            'current_step' => 'quote',
            'payload' => [
                'service_type' => 'moto',
                'pickup' => ['address' => 'Sisli Merkez'],
                'dropoff' => ['address' => 'Kadikoy Moda'],
            ],
            'expires_at' => now()->addHour(),
        ]);

        $response = $this->get('/checkout/'.$session->token);

        $response->assertOk();
        $response->assertSee('SimdiGetir Checkout');
        $response->assertSee('Sisli Merkez');
        $response->assertSee('Kadikoy Moda');
        $response->assertSee('Kayit veya giris');
        $response->assertSee('Gonderen, alici ve paket detaylari');
        $response->assertSee('Odeme yontemi');
        $response->assertSee('Gonderi Sekli');
        $response->assertSee('data-checkout-app', false);
    }

    public function test_checkout_page_shows_bound_customer_summary_when_session_has_customer(): void
    {
        $quote = PricingQuote::query()->create([
            'quote_no' => 'QTE-WEB-002',
            'request_snapshot' => [],
            'resolved_rules' => [],
            'subtotal_amount' => 9500,
            'discount_amount' => 0,
            'surge_amount' => 0,
            'total_amount' => 9500,
            'currency' => 'TRY',
            'expires_at' => now()->addMinutes(15),
        ]);

        $customer = User::factory()->create([
            'name' => 'Ayse Yilmaz',
            'phone' => '05513567292',
            'email' => 'ayse@example.com',
        ]);

        $session = CheckoutSession::query()->create([
            'token' => 'checkout-web-token-002',
            'pricing_quote_id' => $quote->id,
            'customer_id' => $customer->id,
            'status' => 'authenticated',
            'current_step' => 'recipient',
            'payload' => [
                'service_type' => 'moto',
                'pickup' => ['address' => 'Levent Buyukdere', 'name' => 'Ayse Yilmaz'],
                'dropoff' => ['address' => 'Etiler Nispetiye'],
                'packages' => [
                    ['package_type' => 'electronics', 'quantity' => 1, 'description' => 'Telefon kutusu'],
                ],
            ],
            'expires_at' => now()->addHour(),
        ]);

        $response = $this->get('/checkout/'.$session->token);

        $response->assertOk();
        $response->assertSee('Bagli hesap');
        $response->assertSee('Ayse Yilmaz');
        $response->assertSee('05513567292');
        $response->assertSee('Gondereni hesap sahibi ile doldur');
        $response->assertSee('Telefon kutusu');
    }

    public function test_checkout_page_uses_admin_managed_bank_transfer_instruction_content(): void
    {
        Setting::setValue('checkout.bank_transfer_title', 'Kurumsal Havale Bilgisi', 'checkout');
        Setting::setValue('checkout.bank_transfer_body', 'Lutfen odemeyi ayni gun icinde tamamlayin.', 'checkout');
        Setting::setValue('checkout.bank_transfer_bank_name', 'Ziraat Bankasi', 'checkout');
        Setting::setValue('checkout.bank_transfer_account_holder', 'SimdiGetir Teknoloji', 'checkout');
        Setting::setValue('checkout.bank_transfer_iban', 'TR11 1111 1111 1111 1111 1111 11', 'checkout');
        Setting::setValue('checkout.bank_transfer_reference_note', 'Aciklamaya siparis numarasini yazin.', 'checkout');

        $quote = PricingQuote::query()->create([
            'quote_no' => 'QTE-WEB-003',
            'request_snapshot' => [],
            'resolved_rules' => [],
            'subtotal_amount' => 8000,
            'discount_amount' => 0,
            'surge_amount' => 0,
            'total_amount' => 8000,
            'currency' => 'TRY',
            'expires_at' => now()->addMinutes(15),
        ]);

        $session = CheckoutSession::query()->create([
            'token' => 'checkout-web-token-003',
            'pricing_quote_id' => $quote->id,
            'status' => 'ready',
            'current_step' => 'payment',
            'payload' => [
                'service_type' => 'moto',
                'pickup' => ['address' => 'Bomonti'],
                'dropoff' => ['address' => 'Acibadem'],
                'payment' => ['method' => 'bank_transfer', 'timing' => 'prepaid', 'payer_role' => 'sender'],
            ],
            'expires_at' => now()->addHour(),
        ]);

        $response = $this->get('/checkout/'.$session->token);

        $response->assertOk();
        $response->assertSee('Kurumsal Havale Bilgisi');
        $response->assertSee('Lutfen odemeyi ayni gun icinde tamamlayin.');
        $response->assertSee('Ziraat Bankasi');
        $response->assertSee('TR11 1111 1111 1111 1111 1111 11');
        $response->assertSee('Aciklamaya siparis numarasini yazin.');
    }

    public function test_checkout_page_shows_card_payment_cta_when_provider_is_ready_and_order_is_pending_payment(): void
    {
        config()->set('payments.default_provider', 'paytr');
        config()->set('payments.providers.paytr.merchant_id', 'merchant-id');
        config()->set('payments.providers.paytr.merchant_key', 'merchant-key');
        config()->set('payments.providers.paytr.merchant_salt', 'merchant-salt');

        $quote = PricingQuote::query()->create([
            'quote_no' => 'QTE-WEB-004',
            'request_snapshot' => [],
            'resolved_rules' => [],
            'subtotal_amount' => 11200,
            'discount_amount' => 0,
            'surge_amount' => 0,
            'total_amount' => 11200,
            'currency' => 'TRY',
            'expires_at' => now()->addMinutes(15),
        ]);

        $customer = User::factory()->create([
            'name' => 'Fatma Kaya',
            'phone' => '05514443322',
        ]);
        $order = Order::query()->create([
            'customer_id' => $customer->id,
            'order_no' => 'ORD-WEB-CARD-001',
            'state' => 'pending_payment',
            'payment_state' => 'pending',
            'payment_method' => 'card',
            'payment_timing' => 'prepaid',
            'payer_role' => 'sender',
            'pickup_address' => 'Sisli',
            'dropoff_address' => 'Atasehir',
            'total_amount' => 11200,
            'currency' => 'TRY',
        ]);

        $session = CheckoutSession::query()->create([
            'token' => 'checkout-web-token-004',
            'pricing_quote_id' => $quote->id,
            'customer_id' => $customer->id,
            'status' => 'completed',
            'current_step' => 'confirm',
            'payload' => [
                'pickup' => ['address' => 'Sisli'],
                'dropoff' => ['address' => 'Atasehir'],
                'payment' => ['method' => 'card', 'timing' => 'prepaid', 'payer_role' => 'sender'],
                'finalized_order' => [
                    'order_id' => $order->id,
                    'order_no' => 'ORD-WEB-CARD-001',
                    'next_action' => 'initiate_card_payment',
                ],
            ],
            'expires_at' => now()->addHour(),
        ]);

        $response = $this->get('/checkout/'.$session->token);

        $response->assertOk();
        $response->assertSee('Kart odemesine gec');
        $response->assertSee('PAYTR');
        $response->assertSee('payment_initiate', false);
    }
}
