<?php

namespace Tests\Feature;

use Modules\Settings\Models\Setting;
use Tests\TestCase;

/**
 * KVKK onayı (Consent Mode v2) + ölçüm düzeltmeleri — Dalga 1 (2026-09).
 *
 * Canlı denetim (vault/clients/simdigetir/denetim/2026-09-site-denetimi.md §10) şunu buldu:
 * GA4, Google Ads ve Meta Pixel onaydan ÖNCE çalışıyordu; bant "devam ederek kabul" diyordu,
 * Reddet yoktu. Ayrıca form gönderimi GA4'e hiç ulaşmıyor (generate_lead dinleyicisi
 * tetiklenmeyen bir olayı bekliyordu) ve kurye başvuruları Meta'ya "Lead" gidiyordu.
 *
 * Bu testler yeni davranışı ve dönüşüm korumasını (tel/WhatsApp/Ads) birlikte kilitler.
 */
class ConsentAndTrackingTest extends TestCase
{
    public function test_consent_default_is_denied_and_runs_before_google_tag(): void
    {
        $html = $this->get('/')->assertStatus(200)->getContent();

        $consent = strpos($html, "gtag('consent', 'default'");
        $gtagJs = strpos($html, 'googletagmanager.com/gtag/js?id=AW-17989545006');

        $this->assertNotFalse($consent, 'Consent Mode v2 varsayılanı yok');
        $this->assertNotFalse($gtagJs, 'Google etiketi yok');
        $this->assertLessThan($gtagJs, $consent, 'Onay varsayılanı etiketten ÖNCE gelmeli');

        foreach (['ad_storage', 'analytics_storage', 'ad_user_data', 'ad_personalization'] as $alan) {
            $this->assertMatchesRegularExpression("/'{$alan}': 'denied'/", $html, "{$alan} varsayılan reddedilmiş değil");
        }
    }

    public function test_banner_has_accept_reject_and_preferences_without_implied_consent(): void
    {
        $response = $this->get('/')->assertStatus(200);

        $response->assertSee('id="cookie-accept"', false);
        $response->assertSee('id="cookie-reject"', false);
        $response->assertSee('id="cookie-prefs-toggle"', false);
        $response->assertSee('id="cookie-pref-analitik"', false);
        $response->assertSee('id="cookie-pref-pazarlama"', false);
        $response->assertSee('id="cookie-settings-link"', false);
        $response->assertSee('/cerez-politikasi', false);
        $response->assertDontSee('Siteyi kullanmaya devam ederek', false);
    }

    public function test_meta_pixel_waits_for_marketing_consent_and_noscript_pixel_is_removed(): void
    {
        $html = $this->get('/')->assertStatus(200)->getContent();

        $revoke = strpos($html, "fbq('consent', (window.sgOnay && window.sgOnay.pazarlama) ? 'grant' : 'revoke');");
        $init = strpos($html, "fbq('init', '1657531168735846');");
        $this->assertNotFalse($revoke);
        $this->assertNotFalse($init);
        $this->assertLessThan($init, $revoke, 'Pixel onayı init öncesi ayarlanmalı');
        $this->assertStringNotContainsString('facebook.com/tr?id=', $html, 'noscript piksel onaysız veri gönderir');
    }

    public function test_conversion_protection_is_unchanged(): void
    {
        $html = $this->get('/')->assertStatus(200)->getContent();

        $this->assertGreaterThanOrEqual(3, substr_count($html, 'trackConversion('), 'trackConversion 1 tanım + 2 çağrı');
        $this->assertStringContainsString("'transport_type': 'beacon'", $html);
        $this->assertStringContainsString("gtag('config', 'G-XYCY1D28EF');", $html);
        $this->assertStringContainsString("gtag('config', 'AW-17989545006');", $html);
        $this->assertStringContainsString("gtag('event', 'click_phone'", $html);
        $this->assertStringContainsString("gtag('event', 'click_whatsapp'", $html);
    }

    public function test_lead_forms_send_generate_lead_but_courier_applications_do_not_become_leads(): void
    {
        $html = $this->get('/')->assertStatus(200)->getContent();

        $this->assertStringContainsString("gtag('event', 'generate_lead'", $html);
        $this->assertStringContainsString("if (leadType === 'courier_application')", $html);
        $this->assertStringContainsString("gtag('event', 'courier_application_submit'", $html);
        // Hiç tetiklenmeyen eski dinleyici kaldırıldı
        $this->assertStringNotContainsString("window.addEventListener('lead_submit'", $html);
    }

    public function test_clarity_loads_only_through_consent_function_when_configured(): void
    {
        // Varsayılan proje (simdigetir.com, yoe20jr1jz) — betik düz <script src> olarak değil, yalnız onay fonksiyonuyla
        $html = $this->get('/')->assertStatus(200)->getContent();
        $this->assertStringContainsString('"clarity", "script", "yoe20jr1jz"', $html);
        $this->assertDoesNotMatchRegularExpression('/<script[^>]*clarity.ms/', $html);

        // Admin ayarı varsayılanı ezer; boş bırakılırsa Clarity hiç yüklenmez
        Setting::setValue('marketing.clarity_id', '', 'marketing');
        $this->get('/')->assertStatus(200)->assertDontSee('clarity.ms/tag', false);

        Setting::setValue('marketing.clarity_id', 'abc123xyz', 'marketing');
        $html = $this->get('/')->assertStatus(200)->getContent();

        $this->assertStringContainsString('window.sgClarityYukle = function', $html);
        $this->assertStringContainsString('"clarity", "script", "abc123xyz"', $html);
        $this->assertStringContainsString("if (window.sgOnay && window.sgOnay.analitik) { window.sgClarityYukle(); }", $html);
    }

    public function test_basic_mode_defers_google_tag_until_consent(): void
    {
        Setting::setValue('consent.mode', 'basic', 'marketing');
        $html = $this->get('/')->assertStatus(200)->getContent();

        $this->assertStringNotContainsString('<script async src="https://www.googletagmanager.com/gtag/js', $html);
        $this->assertStringContainsString('window.sgGtagYukle = function', $html);
        $this->assertStringContainsString("gtag('consent', 'default'", $html);
    }

    public function test_emergency_switch_restores_previous_behaviour(): void
    {
        Setting::setValue('consent.v2_enabled', '0', 'marketing');
        $html = $this->get('/')->assertStatus(200)->getContent();

        $this->assertStringNotContainsString("gtag('consent', 'default'", $html);
        $this->assertStringNotContainsString('id="cookie-reject"', $html);
        $this->assertStringContainsString('Siteyi kullanmaya devam ederek', $html);
        // dönüşüm zinciri kapalı modda da aynen çalışır
        $this->assertGreaterThanOrEqual(3, substr_count($html, 'trackConversion('));
    }

    public function test_consent_choice_expires_after_twelve_months(): void
    {
        $this->get('/')->assertStatus(200)
            ->assertSee('Date.now() - t > 365 * 24 * 60 * 60 * 1000', false);
    }

    public function test_kvkk_page_names_data_controller_address_and_contact(): void
    {
        $this->get('/kvkk')->assertStatus(200)
            ->assertSee('Ceyhun Aslan — SimdiGetir Kurye Hizmetleri', false)
            ->assertSee('Yeşilce Mahallesi Aytekin Sokak No:5/2 Kağıthane / İstanbul', false)
            ->assertSee('simdigetir34@gmail.com', false)
            ->assertSee('Son Güncelleme: 27.09.2026', false)
            ->assertSee('href="/cerez-politikasi"', false);
    }

    public function test_district_and_neighborhood_pages_emit_view_district(): void
    {
        $this->get('/kurye/sisli')->assertStatus(200)
            ->assertSee("gtag('event', 'view_district'", false)
            ->assertSee("'district_slug': \"sisli\"", false);

        $this->get('/kurye/sisli/mecidiyekoy')->assertStatus(200)
            ->assertSee("gtag('event', 'view_district'", false)
            ->assertSee("'neighborhood':", false);
    }
}
