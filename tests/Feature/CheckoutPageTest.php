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
    public function test_guest_can_open_checkout_entry_page(): void
    {
        $response = $this->get('/checkout');

        $response->assertOk();
        $response->assertSee('Siparise Basla');
        $response->assertSee('Ana sayfada fiyat hesapla');
        $response->assertSee('Hesap olustur');
        $response->assertSee(route('checkout.customer.register'));
        $response->assertSee('Musteri girisi');
        $response->assertSee('Siparis takip');
        $response->assertSee('KVKK');
        $response->assertSee('lang="tr"', false);
        $response->assertSee('id="theme-toggle"', false);
        $response->assertSee('id="offcanvas-sidebar"', false);
        $response->assertSee('Powered by', false);
        $response->assertDontSee('checkout-site-footer');
        $response->assertDontSee('404 yerine');
        $this->assertNoMojibake($response->getContent());
    }

    public function test_checkout_entry_page_uses_admin_managed_copy_blocks(): void
    {
        Setting::setValue('checkout.support_note', 'Destek notu test icerigi.', 'checkout');
        Setting::setValue('checkout.entry_intro', 'Admin yonetimli checkout acilis metni.', 'checkout');
        Setting::setValue('checkout.entry_help', 'Admin yardim metni.', 'checkout');

        $response = $this->get('/checkout');

        $response->assertOk();
        $response->assertSee('Destek notu test icerigi.');
        $response->assertSee('Admin yonetimli checkout acilis metni.');
        $response->assertSee('Admin yardim metni.');
    }

    public function test_checkout_entry_with_prefilled_query_redirects_to_tokenized_checkout_session(): void
    {
        $response = $this->get('/checkout?pickup=Besiktas%20Meydan&dropoff=Sisli%20Merkez&service_type=moto&service_label=Moto%20Kurye');

        $response->assertRedirect();
        $this->assertDatabaseCount('checkout_sessions', 1);
        $this->assertDatabaseCount('pricing_quotes', 1);

        $session = CheckoutSession::query()->latest('id')->first();
        $this->assertNotNull($session);
        $this->assertSame('quote', $session->current_step);
        $this->assertSame('moto', (string) data_get($session->payload, 'service_type'));
        $this->assertSame('Moto Kurye', (string) data_get($session->payload, 'service_label'));
        $this->assertSame('Besiktas Meydan', (string) data_get($session->payload, 'pickup.address'));
        $this->assertSame('Sisli Merkez', (string) data_get($session->payload, 'dropoff.address'));

        $this->get('/checkout/'.$session->token)
            ->assertOk()
            ->assertSee('Quote to order checkout')
            ->assertSee('Teklifi siparişe çevirin, ödemeyi seçin, operasyonu kilitleyin.')
            ->assertSee('Besiktas Meydan')
            ->assertSee('Sisli Merkez')
            ->assertSee('Moto Kurye')
            ->assertSee('id="theme-toggle"', false)
            ->assertSee('id="offcanvas-sidebar"', false)
            ->assertSee('Powered by', false);
    }

    public function test_siparis_shortcut_redirects_to_checkout_entry_page(): void
    {
        $response = $this->get('/siparis');

        $response->assertRedirect('/checkout');
    }

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
        $response->assertSee('Quote to order checkout');
        $response->assertSee('Teklifi siparişe çevirin, ödemeyi seçin, operasyonu kilitleyin.');
        $response->assertSee('Sisli Merkez');
        $response->assertSee('Kadikoy Moda');
        $response->assertSee('Kayıt veya giriş');
        $response->assertSee('Gönderen, alıcı ve paket detayları');
        $response->assertSee('Ödeme yöntemi');
        $response->assertSee('Gönderi Şekli');
        $response->assertSee('data-checkout-app', false);
        $response->assertSee('id="theme-toggle"', false);
        $response->assertSee('id="offcanvas-sidebar"', false);
        $response->assertSee('Powered by', false);
        $response->assertDontSee('checkout-site-footer');
        $this->assertNoMojibake($response->getContent());
    }


    public function test_checkout_page_contains_guest_auth_mode_and_no_forced_auth_gate(): void
    {
        $quote = PricingQuote::query()->create([
            'quote_no' => 'QTE-WEB-GUEST-001',
            'request_snapshot' => [],
            'resolved_rules' => [],
            'subtotal_amount' => 9000,
            'discount_amount' => 0,
            'surge_amount' => 0,
            'total_amount' => 9000,
            'currency' => 'TRY',
            'expires_at' => now()->addMinutes(15),
        ]);

        $session = CheckoutSession::query()->create([
            'token' => 'checkout-web-token-guest-001',
            'pricing_quote_id' => $quote->id,
            'status' => 'draft',
            'current_step' => 'quote',
            'payload' => [
                'service_type' => 'moto',
                'pickup' => ['address' => 'Besiktas'],
                'dropoff' => ['address' => 'Sisli'],
            ],
            'expires_at' => now()->addHour(),
        ]);

        $response = $this->get('/checkout/'.$session->token);

        $response->assertOk();
        $response->assertSee('value="guest"', false);
        $response->assertSee('current_step: \'recipient\'', false);
        $response->assertDontSee('!state.customerId && [\'recipient\', \'payment\', \'confirm\'].includes(state.currentStep) ? \'auth\'', false);
        $this->assertNoMojibake($response->getContent());
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
        $response->assertSee('Bağlı hesap');
        $response->assertSee('Ayse Yilmaz');
        $response->assertSee('05513567292');
        $response->assertSee('Göndereni hesap sahibi ile doldur');
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

    public function test_checkout_page_prefers_service_label_over_service_key_in_summary(): void
    {
        $quote = PricingQuote::query()->create([
            'quote_no' => 'QTE-WEB-003B',
            'request_snapshot' => [],
            'resolved_rules' => [],
            'subtotal_amount' => 12500,
            'discount_amount' => 0,
            'surge_amount' => 0,
            'total_amount' => 12500,
            'currency' => 'TRY',
            'expires_at' => now()->addMinutes(15),
        ]);

        $session = CheckoutSession::query()->create([
            'token' => 'checkout-web-token-003b',
            'pricing_quote_id' => $quote->id,
            'status' => 'draft',
            'current_step' => 'quote',
            'payload' => [
                'service_type' => 'moto',
                'service_label' => 'Moto Kurye',
                'pickup' => ['address' => 'Besiktas'],
                'dropoff' => ['address' => 'Maslak'],
            ],
            'expires_at' => now()->addHour(),
        ]);

        $response = $this->get('/checkout/'.$session->token);

        $response->assertOk();
        $response->assertSee('Moto Kurye');
        $response->assertDontSee('>MOTO<', false);
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
        $response->assertSee('>Kart ödemesine geç<', false);
        $response->assertSee('PAYTR');
        $response->assertSee('payment_initiate', false);
    }

    public function test_checkout_page_does_not_render_card_payment_cta_for_cash_on_delivery_order(): void
    {
        $quote = PricingQuote::query()->create([
            'quote_no' => 'QTE-WEB-005',
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
            'name' => 'Nakit Alici',
            'phone' => '05516667788',
        ]);

        $order = Order::query()->create([
            'customer_id' => $customer->id,
            'order_no' => 'ORD-WEB-CASH-001',
            'state' => 'paid',
            'payment_state' => 'cash_on_delivery',
            'payment_method' => 'cash',
            'payment_timing' => 'delivery',
            'payer_role' => 'recipient',
            'pickup_address' => 'Sisli',
            'dropoff_address' => 'Kadikoy',
            'total_amount' => 9500,
            'currency' => 'TRY',
        ]);

        $session = CheckoutSession::query()->create([
            'token' => 'checkout-web-token-005',
            'pricing_quote_id' => $quote->id,
            'customer_id' => $customer->id,
            'status' => 'completed',
            'current_step' => 'confirm',
            'payload' => [
                'pickup' => ['address' => 'Sisli'],
                'dropoff' => ['address' => 'Kadikoy'],
                'payment' => ['method' => 'cash', 'timing' => 'delivery', 'payer_role' => 'recipient'],
                'finalized_order' => [
                    'order_id' => $order->id,
                    'order_no' => 'ORD-WEB-CASH-001',
                    'next_action' => 'dispatch_ready',
                ],
            ],
            'expires_at' => now()->addHour(),
        ]);

        $response = $this->get('/checkout/'.$session->token);

        $response->assertOk();
        $response->assertSee('Siparişi takip et');
        $response->assertDontSee('>Kart ödemesine geç<', false);
    }

    private function assertNoMojibake(string $content): void
    {
        $markers = [
            ['label' => 'U+00C3', 'value' => json_decode('"\u00C3"', true)],
            ['label' => 'U+00C5', 'value' => json_decode('"\u00C5"', true)],
            ['label' => 'U+00C4', 'value' => json_decode('"\u00C4"', true)],
            ['label' => 'U+00C2', 'value' => json_decode('"\u00C2"', true)],
            ['label' => 'U+00E2U+20AC', 'value' => json_decode('"\u00E2\u20AC"', true)],
            ['label' => 'U+FFFD', 'value' => json_decode('"\uFFFD"', true)],
        ];

        foreach ($markers as $marker) {
            $this->assertStringNotContainsString(
                (string) ($marker['value'] ?? ''),
                $content,
                'Mojibake marker found in checkout response: '.($marker['label'] ?? 'unknown')
            );
        }
    }
}
