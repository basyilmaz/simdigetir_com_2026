<?php

namespace Tests\Feature;

use Modules\Landing\Models\LandingPage;
use Modules\Landing\Models\LandingPageSection;
use Modules\Landing\Models\LandingSectionItem;
use Tests\TestCase;

class LandingStandardPagesDynamicContentTest extends TestCase
{
    public function test_about_page_uses_db_backed_meta_and_hero_content()
    {
        $page = LandingPage::create([
            'slug' => 'about',
            'title' => 'About',
            'is_active' => true,
            'meta' => [
                'meta_title' => 'Dynamic About Meta Title',
                'meta_description' => 'Dynamic About Description',
                'meta_keywords' => 'dynamic-about-keywords',
            ],
        ]);

        LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'hero',
            'type' => 'hero',
            'is_active' => true,
            'sort_order' => 1,
            'payload' => [
                'hero_badge_text' => 'Dynamic About Badge',
                'hero_title_html' => "Dynamic <span class='gradient-text'>About</span> Title",
                'hero_description_text' => 'Dynamic about hero description.',
            ],
        ]);

        $response = $this->get('/hakkimizda');

        $response->assertStatus(200);
        $response->assertSee('Dynamic About Meta Title');
        $response->assertSee('Dynamic About Description');
        $response->assertSee('Dynamic About Badge');
        $response->assertSee('Dynamic');
        $response->assertSee('About');
        $response->assertSee('Dynamic about hero description.');
    }

    public function test_contact_page_uses_db_backed_hero_content()
    {
        $page = LandingPage::create([
            'slug' => 'contact',
            'title' => 'Contact',
            'is_active' => true,
        ]);

        LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'hero',
            'type' => 'hero',
            'is_active' => true,
            'sort_order' => 1,
            'payload' => [
                'hero_badge_text' => 'Dynamic Contact Badge',
                'hero_title_html' => "<span class='gradient-text'>Dynamic Contact</span> Title",
                'hero_description_text' => 'Dynamic contact hero description.',
            ],
        ]);

        $response = $this->get('/iletisim');

        $response->assertStatus(200);
        $response->assertSee('Dynamic Contact Badge');
        $response->assertSee('Dynamic Contact');
        $response->assertSee('Dynamic contact hero description.');
    }

    public function test_contact_page_renders_db_backed_contact_channels()
    {
        $page = LandingPage::create([
            'slug' => 'contact',
            'title' => 'Contact',
            'is_active' => true,
        ]);

        $section = LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'contact_channels',
            'type' => 'contact_channels',
            'is_active' => true,
            'sort_order' => 2,
            'payload' => [],
        ]);

        LandingSectionItem::create([
            'section_id' => $section->id,
            'item_key' => 'test_channel',
            'sort_order' => 1,
            'is_active' => true,
            'payload' => [
                'title' => 'Destek Hattı',
                'value' => '+90 555 000 00 00',
                'hint' => 'Test Kanalı',
                'icon_class' => 'fa-phone',
                'link' => 'tel:+905550000000',
                'card_class' => '',
                'icon_wrapper_class' => '',
                'target_blank' => false,
            ],
        ]);

        $response = $this->get('/iletisim');

        $response->assertStatus(200);
        $response->assertSee('Destek Hattı');
        $response->assertSee('0551 356 72 92');
        $response->assertSee('"telephone":"+905513567292"', false);
        $response->assertSee('Test Kanalı');
    }

    public function test_faq_page_renders_db_backed_faq_items()
    {
        $page = LandingPage::create([
            'slug' => 'faq',
            'title' => 'FAQ',
            'is_active' => true,
        ]);

        $section = LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'faq_items',
            'type' => 'faq',
            'is_active' => true,
            'sort_order' => 2,
            'payload' => [],
        ]);

        LandingSectionItem::create([
            'section_id' => $section->id,
            'item_key' => 'test_faq',
            'sort_order' => 1,
            'is_active' => true,
            'payload' => [
                'icon' => '❓',
                'question' => 'Test soru nedir?',
                'answer_text' => 'Bu bir test cevabıdır.',
            ],
        ]);

        $response = $this->get('/sss');

        $response->assertStatus(200);
        $response->assertSee('Test soru nedir?');
        $response->assertSee('"name":"Test soru nedir?"', false);
        $response->assertSee('Bu bir test cevabıdır.');
    }
    public function test_about_page_uses_meta_structured_data_override_when_provided()
    {
        LandingPage::create([
            'slug' => 'about',
            'title' => 'About',
            'is_active' => true,
            'meta' => [
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'Organization',
                    'name' => 'Custom About Org',
                ],
            ],
        ]);

        $response = $this->get('/hakkimizda');

        $response->assertStatus(200);
        $response->assertSee('"name":"Custom About Org"', false);
    }

    public function test_services_page_uses_meta_service_schema_items()
    {
        LandingPage::create([
            'slug' => 'services',
            'title' => 'Services',
            'is_active' => true,
            'meta' => [
                'service_schema_items' => [
                    [
                        'name' => 'Custom Service',
                        'description' => 'Custom service description',
                        'serviceType' => 'Custom Type',
                        'url' => url('/hizmetler').'#custom-service',
                    ],
                ],
            ],
        ]);

        $response = $this->get('/hizmetler');

        $response->assertStatus(200);
        $response->assertSee('"name":"Custom Service"', false);
        $response->assertSee('"serviceType":"Custom Type"', false);
    }

    public function test_corporate_page_uses_meta_structured_data_override_when_provided()
    {
        LandingPage::create([
            'slug' => 'corporate',
            'title' => 'Corporate',
            'is_active' => true,
            'meta' => [
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'Service',
                    'name' => 'Custom Corporate Service',
                ],
            ],
        ]);

        $response = $this->get('/kurumsal');

        $response->assertStatus(200);
        $response->assertSee('"name":"Custom Corporate Service"', false);
    }
}
