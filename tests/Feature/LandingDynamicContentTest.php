<?php

namespace Tests\Feature;

use Modules\Landing\Models\LandingPage;
use Modules\Landing\Models\LandingPageSection;
use Modules\Landing\Models\LandingSectionItem;
use Tests\TestCase;

class LandingDynamicContentTest extends TestCase
{
    public function test_home_renders_hero_quote_widget_when_enabled(): void
    {
        config()->set('landing.quote_widget_enabled', true);
        config()->set('services_integrations.maps.google_maps_api_key', '');

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('data-quote-widget', false);
        $response->assertSee('data-quote-autocomplete-provider="manual"', false);
        $response->assertSee('Fiyat Hesapla');
        $response->assertSee('data-quote-start-checkout', false);
        $response->assertSee('data-quote-start-checkout-fallback', false);
        $response->assertSee('Devam Et');
    }

    public function test_home_exposes_google_places_autocomplete_when_maps_key_present(): void
    {
        config()->set('landing.quote_widget_enabled', true);
        config()->set('services_integrations.maps.google_maps_api_key', 'test-maps-api-key');

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('data-quote-autocomplete-provider="google_places"', false);
        $response->assertSee('data-google-maps-api-key="test-maps-api-key"', false);
        $response->assertSee('loadGoogleMapsPlaces');
        $response->assertSee('quote_autocomplete_fallback');
    }

    public function test_home_hides_hero_quote_widget_when_disabled(): void
    {
        config()->set('landing.quote_widget_enabled', false);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('data-quote-widget', false);
    }

    public function test_home_uses_db_backed_quote_widget_content_when_available(): void
    {
        $page = LandingPage::create([
            'slug' => 'home',
            'title' => 'Home',
            'is_active' => true,
        ]);

        LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'hero',
            'type' => 'hero',
            'is_active' => true,
            'sort_order' => 1,
            'payload' => [
                'quote_widget_title_text' => 'Hizli Teklif Al',
                'quote_widget_subtitle_text' => 'Panelden yonetilen teklif aciklamasi.',
                'quote_widget_pickup_placeholder_text' => 'Orn: Besiktas',
                'quote_widget_dropoff_placeholder_text' => 'Orn: Atasehir',
                'quote_widget_submit_label_text' => 'Teklif Hesapla',
                'quote_widget_whatsapp_label_text' => 'WhatsApp Destegi',
                'quote_widget_call_label_text' => 'Telefon ile Ara',
                'quote_widget_service_options' => [
                    [
                        'value' => 'vip',
                        'label' => 'VIP Kurye',
                        'base_amount' => 99000,
                        'fallback_minutes' => 25,
                    ],
                ],
            ],
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Hizli Teklif Al');
        $response->assertSee('Panelden yonetilen teklif aciklamasi.');
        $response->assertSee('Orn: Besiktas');
        $response->assertSee('Orn: Atasehir');
        $response->assertSee('Teklif Hesapla');
        $response->assertSee('WhatsApp Destegi');
        $response->assertSee('Telefon ile Ara');
        $response->assertSee('VIP Kurye');
    }

    public function test_home_hides_quote_widget_when_db_payload_disables_it(): void
    {
        config()->set('landing.quote_widget_enabled', true);

        $page = LandingPage::create([
            'slug' => 'home',
            'title' => 'Home',
            'is_active' => true,
        ]);

        LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'hero',
            'type' => 'hero',
            'is_active' => true,
            'sort_order' => 1,
            'payload' => [
                'quote_widget_enabled' => false,
            ],
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('data-quote-widget', false);
    }

    public function test_home_contains_quote_widget_slider_pause_hooks(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('landing:hero-quote-engage');
        $response->assertSee('landing:hero-quote-release');
        $response->assertSee('quoteWidgetInteractionLocks');
    }

    public function test_home_contains_quote_widget_slider_lock_hooks(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('lockHeroQuoteVisible');
        $response->assertSee('loop: !lockHeroQuoteVisible');
        $response->assertSee('hero-slider-section.hero-slider-locked .swiper-pagination');
        $response->assertSee('allowTouchMove: !lockHeroQuoteVisible');
    }

    public function test_home_contains_deterministic_quote_continue_path_hooks(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('resolveDeterministicContinueUrl');
        $response->assertSee('prepareCheckoutSessionSilently');
        $response->assertSee('preparedCheckoutToken');
        $response->assertSee('data-default-label="Siparise Gec"', false);
    }

    public function test_home_contains_cta_funnel_instrumentation_payload_contract(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('window.buildCtaPayload = buildCtaPayload;', false);
        $response->assertSee('cta_channel', false);
        $response->assertSee('cta_context', false);
        $response->assertSee('cta_label', false);
        $response->assertSee('cta_href', false);
        $response->assertSee("trackEvent('cta_click', payload);", false);
        $response->assertSee("trackEvent('quote_start_checkout_click', payload);", false);
        $response->assertSee("trackEvent('quote_cta_whatsapp_click', payload);", false);
        $response->assertSee("trackEvent('quote_cta_call_click', payload);", false);
    }

    public function test_home_and_standard_pages_use_db_backed_header_b2b_cta_when_enabled(): void
    {
        $page = LandingPage::create([
            'slug' => 'home',
            'title' => 'Home',
            'is_active' => true,
        ]);

        LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'hero',
            'type' => 'hero',
            'is_active' => true,
            'sort_order' => 1,
            'payload' => [
                'header_b2b_cta_enabled' => true,
                'header_b2b_cta_label_text' => 'Kurumsal Portal',
                'header_b2b_cta_href' => '/kurumsal/giris',
                'header_b2b_cta_target' => '_blank',
            ],
        ]);

        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Kurumsal Portal');
        $homeResponse->assertSee('/kurumsal/giris');
        $homeResponse->assertSee('fa-building-lock');
        $homeResponse->assertSee('target="_blank"', false);

        $servicesResponse = $this->get('/hizmetler');
        $servicesResponse->assertStatus(200);
        $servicesResponse->assertSee('Kurumsal Portal');
        $servicesResponse->assertSee('/kurumsal/giris');
        $servicesResponse->assertSee('target="_blank"', false);
    }

    public function test_home_uses_db_backed_hero_content_when_available()
    {
        $page = LandingPage::create([
            'slug' => 'home',
            'title' => 'Home',
            'is_active' => true,
        ]);

        LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'hero',
            'type' => 'hero',
            'is_active' => true,
            'sort_order' => 1,
            'payload' => [
                'hero_badge_text' => 'Dynamic Badge',
                'hero_title_html' => "Dynamic <span class='gradient-text'>Hero</span> Title",
                'hero_description_text' => 'Dynamic hero description text.',
            ],
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Dynamic Badge');
        $response->assertSee('Dynamic hero description text.');
        $response->assertSee('Dynamic');
        $response->assertSee('Hero');
    }

    public function test_home_uses_services_faq_and_cta_content_when_available()
    {
        $page = LandingPage::create([
            'slug' => 'home',
            'title' => 'Home',
            'is_active' => true,
        ]);

        LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'services',
            'type' => 'services',
            'is_active' => true,
            'sort_order' => 2,
            'payload' => [
                'services_badge_text' => 'Dynamic Services Badge',
                'services_title_html' => "Dynamic <span class='gradient-text'>Services</span>",
                'services_subtitle_text' => 'Dynamic services subtitle text.',
            ],
        ]);

        LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'faq',
            'type' => 'faq',
            'is_active' => true,
            'sort_order' => 3,
            'payload' => [
                'faq_card_title_text' => 'Dynamic FAQ Title',
                'faq_card_description_text' => 'Dynamic FAQ description text.',
            ],
        ]);

        LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'courier_cta',
            'type' => 'cta',
            'is_active' => true,
            'sort_order' => 4,
            'payload' => [
                'courier_cta_card_title_text' => 'Dynamic Courier CTA',
                'courier_cta_card_description_text' => 'Dynamic CTA description.',
                'courier_cta_side_title_html' => "<span class='gradient-text'>Dynamic</span> Courier Side",
            ],
        ]);

        LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'corporate_cta',
            'type' => 'cta',
            'is_active' => true,
            'sort_order' => 5,
            'payload' => [
                'corporate_cta_form_title_text' => 'Dynamic Corporate Form',
                'corporate_cta_form_subtitle_text' => 'Dynamic corporate subtitle.',
            ],
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Dynamic Services Badge');
        $response->assertSee('Dynamic services subtitle text.');
        $response->assertSee('Dynamic FAQ Title');
        $response->assertSee('Dynamic FAQ description text.');
        $response->assertSee('Dynamic Courier CTA');
        $response->assertSee('Dynamic CTA description.');
        $response->assertSee('Dynamic Corporate Form');
        $response->assertSee('Dynamic corporate subtitle.');
    }

    public function test_home_uses_service_items_from_database_when_available()
    {
        $page = LandingPage::create([
            'slug' => 'home',
            'title' => 'Home',
            'is_active' => true,
        ]);

        $services = LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'services',
            'type' => 'services',
            'is_active' => true,
            'sort_order' => 1,
            'payload' => [],
        ]);

        LandingSectionItem::create([
            'section_id' => $services->id,
            'item_key' => 'custom_service',
            'sort_order' => 1,
            'is_active' => true,
            'payload' => [
                'number' => '09',
                'icon_text' => '*',
                'title' => 'Dinamik Servis',
                'description' => 'Panelden gelen servis aciklamasi.',
                'features' => ['Ozellik A', 'Ozellik B'],
                'button_icon' => 'fa-phone',
                'button_label' => 'Dinamik Ara',
                'button_href' => 'tel:+900000000000',
            ],
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Dinamik Servis');
        $response->assertSee('Panelden gelen servis aciklamasi.');
        $response->assertSee('Ozellik A');
        $response->assertSee('Dinamik Ara');
        $response->assertSee('tel:+900000000000');
    }

    public function test_home_uses_faq_items_from_database_when_available()
    {
        $page = LandingPage::create([
            'slug' => 'home',
            'title' => 'Home',
            'is_active' => true,
        ]);

        $faq = LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'faq',
            'type' => 'faq',
            'is_active' => true,
            'sort_order' => 1,
            'payload' => [],
        ]);

        LandingSectionItem::create([
            'section_id' => $faq->id,
            'item_key' => 'faq_teaser',
            'sort_order' => 1,
            'is_active' => true,
            'payload' => [
                'title' => 'Dinamik SSS Karti',
                'description' => 'Dinamik SSS aciklamasi.',
                'link' => '/sss',
                'link_label' => 'Dinamik Sorulara Bak',
                'date_label' => '1 Mart 2026',
                'icon_class' => 'fa-circle-question',
            ],
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Dinamik SSS Karti');
        $response->assertSee('Dinamik SSS aciklamasi.');
        $response->assertSee('Dinamik Sorulara Bak');
        $response->assertSee('1 Mart 2026');
    }

    public function test_home_uses_courier_and_corporate_cta_items_from_database_when_available()
    {
        $page = LandingPage::create([
            'slug' => 'home',
            'title' => 'Home',
            'is_active' => true,
        ]);

        $corporateCta = LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'corporate_cta',
            'type' => 'cta',
            'is_active' => true,
            'sort_order' => 1,
            'payload' => [],
        ]);

        LandingSectionItem::create([
            'section_id' => $corporateCta->id,
            'item_key' => 'corp_benefit_1',
            'sort_order' => 1,
            'is_active' => true,
            'payload' => [
                'icon_class' => 'fa-shield-halved',
                'text' => 'Dinamik Kurumsal Avantaj',
            ],
        ]);

        $courierCta = LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'courier_cta',
            'type' => 'cta',
            'is_active' => true,
            'sort_order' => 2,
            'payload' => [],
        ]);

        LandingSectionItem::create([
            'section_id' => $courierCta->id,
            'item_key' => 'courier_feature_1',
            'sort_order' => 1,
            'is_active' => true,
            'payload' => [
                'icon_class' => 'fa-map-location-dot',
                'title' => 'Dinamik Kurye Ozelligi',
                'subtitle' => 'Dinamik kurye alt aciklamasi.',
            ],
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Dinamik Kurumsal Avantaj');
        $response->assertSee('Dinamik Kurye Ozelligi');
        $response->assertSee('Dinamik kurye alt aciklamasi.');
    }
    public function test_home_hides_inactive_managed_sections()
    {
        $page = LandingPage::create([
            'slug' => 'home',
            'title' => 'Home',
            'is_active' => true,
        ]);

        LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'hero',
            'type' => 'hero',
            'is_active' => true,
            'sort_order' => 1,
            'payload' => [
                'hero_badge_text' => 'Sadece Hero Acik',
            ],
        ]);

        LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'services',
            'type' => 'services',
            'is_active' => false,
            'sort_order' => 2,
            'payload' => [
                'services_badge_text' => 'Gizli Servis Rozeti',
            ],
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Sadece Hero Acik');
        $response->assertDontSee('Gizli Servis Rozeti');
    }

    public function test_home_uses_feature_process_stat_and_testimonial_items_from_database_when_available()
    {
        $page = LandingPage::create([
            'slug' => 'home',
            'title' => 'Home',
            'is_active' => true,
        ]);

        $features = LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'features',
            'type' => 'features',
            'is_active' => true,
            'sort_order' => 1,
            'payload' => [],
        ]);
        LandingSectionItem::create([
            'section_id' => $features->id,
            'item_key' => 'feature_1',
            'sort_order' => 1,
            'is_active' => true,
            'payload' => ['icon' => '*', 'title' => 'Dinamik Ozellik', 'description' => 'Dinamik ozellik aciklamasi'],
        ]);

        $process = LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'process',
            'type' => 'process',
            'is_active' => true,
            'sort_order' => 2,
            'payload' => [],
        ]);
        LandingSectionItem::create([
            'section_id' => $process->id,
            'item_key' => 'process_1',
            'sort_order' => 1,
            'is_active' => true,
            'payload' => ['number' => '11', 'title' => 'Dinamik Surec', 'description' => 'Dinamik surec aciklamasi'],
        ]);

        $stats = LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'stats',
            'type' => 'stats',
            'is_active' => true,
            'sort_order' => 3,
            'payload' => [],
        ]);
        LandingSectionItem::create([
            'section_id' => $stats->id,
            'item_key' => 'stat_1',
            'sort_order' => 1,
            'is_active' => true,
            'payload' => ['count' => 77, 'suffix' => '%', 'label' => 'Dinamik Istatistik'],
        ]);

        $testimonials = LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'testimonials',
            'type' => 'testimonials',
            'is_active' => true,
            'sort_order' => 4,
            'payload' => [],
        ]);
        LandingSectionItem::create([
            'section_id' => $testimonials->id,
            'item_key' => 'testimonial_1',
            'sort_order' => 1,
            'is_active' => true,
            'payload' => [
                'avatar_text' => 'DT',
                'avatar_style' => 'background:#111;color:#fff;',
                'stars' => 5,
                'text' => 'Dinamik referans metni',
                'author_name' => 'Dinamik Kisi',
                'author_role' => 'Dinamik Rol',
            ],
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Dinamik Ozellik');
        $response->assertSee('Dinamik Surec');
        $response->assertSee('Dinamik Istatistik');
        $response->assertSee('Dinamik referans metni');
        $response->assertSee('Dinamik Kisi');
    }

    public function test_home_uses_db_backed_hero_slide_image_when_available()
    {
        $page = LandingPage::create([
            'slug' => 'home',
            'title' => 'Home',
            'is_active' => true,
        ]);

        LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'hero',
            'type' => 'hero',
            'is_active' => true,
            'sort_order' => 1,
            'payload' => [
                'hero_slide2_image_url' => 'landing/hero/custom-hero.webp',
                'hero_slide2_image_alt' => 'Custom Hero Visual',
            ],
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('landing/hero/custom-hero.webp');
        $response->assertSee('Custom Hero Visual');
    }

    public function test_home_uses_db_backed_testimonial_avatar_image_when_available()
    {
        $page = LandingPage::create([
            'slug' => 'home',
            'title' => 'Home',
            'is_active' => true,
        ]);

        $testimonials = LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'testimonials',
            'type' => 'testimonials',
            'is_active' => true,
            'sort_order' => 1,
            'payload' => [],
        ]);

        LandingSectionItem::create([
            'section_id' => $testimonials->id,
            'item_key' => 'testimonial_media',
            'sort_order' => 1,
            'is_active' => true,
            'payload' => [
                'avatar_image_url' => 'landing/testimonials/avatar-1.webp',
                'avatar_image_alt' => 'Avatar One',
                'text' => 'Media testimonial',
                'author_name' => 'Avatar User',
            ],
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('landing/testimonials/avatar-1.webp');
        $response->assertSee('Avatar One');
        $response->assertSee('Media testimonial');
    }
}
