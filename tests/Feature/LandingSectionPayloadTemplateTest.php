<?php

namespace Tests\Feature;

use Illuminate\Validation\ValidationException;
use Modules\Landing\Filament\Resources\LandingPageSectionResource;
use Modules\Landing\Filament\Resources\LandingSectionItemResource;
use Modules\Landing\Models\LandingPage;
use Modules\Landing\Models\LandingPageSection;
use Tests\TestCase;

class LandingSectionPayloadTemplateTest extends TestCase
{
    public function test_section_payload_builder_merges_json_and_template_fields(): void
    {
        $data = [
            'key' => 'hero',
            'payload' => [
                'hero_badge_text' => 'Old Badge',
            ],
            'payload_json' => '{"extra_key":"extra_value"}',
            'tpl_hero_badge_text' => 'New Badge',
            'tpl_hero_title_html' => '<b>Title</b>',
            'tpl_hero_description_text' => 'Hero desc',
        ];

        $normalized = LandingPageSectionResource::buildPayloadFromFormData($data);

        $this->assertArrayNotHasKey('payload_json', $normalized);
        $this->assertSame('extra_value', $normalized['payload']['extra_key']);
        $this->assertSame('New Badge', $normalized['payload']['hero_badge_text']);
        $this->assertSame('<b>Title</b>', $normalized['payload']['hero_title_html']);
        $this->assertSame('Hero desc', $normalized['payload']['hero_description_text']);
    }

    public function test_section_payload_builder_throws_validation_exception_for_invalid_json(): void
    {
        $this->expectException(ValidationException::class);

        LandingPageSectionResource::buildPayloadFromFormData([
            'key' => 'hero',
            'payload' => [],
            'payload_json' => '{invalid-json',
        ]);
    }

    public function test_item_payload_builder_supports_faq_items_template(): void
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
            'sort_order' => 1,
            'payload' => [],
        ]);

        $normalized = LandingSectionItemResource::buildPayloadFromFormData([
            'section_id' => $section->id,
            'payload' => [],
            'payload_json' => '{"extra":"value"}',
            'tpl_icon_text' => '❓',
            'tpl_question' => 'Soru?',
            'tpl_answer_text' => 'Cevap.',
        ]);

        $this->assertSame('value', $normalized['payload']['extra']);
        $this->assertSame('❓', $normalized['payload']['icon']);
        $this->assertSame('Soru?', $normalized['payload']['question']);
        $this->assertSame('Cevap.', $normalized['payload']['answer_text']);
    }

    public function test_item_payload_builder_supports_contact_channels_template(): void
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
            'sort_order' => 1,
            'payload' => [],
        ]);

        $normalized = LandingSectionItemResource::buildPayloadFromFormData([
            'section_id' => $section->id,
            'payload' => [],
            'tpl_title' => 'Telefon',
            'tpl_value' => '+90 500 000 00 00',
            'tpl_hint' => '7/24',
            'tpl_icon_class' => 'fa-phone',
            'tpl_link' => 'tel:+905000000000',
            'tpl_target_blank' => false,
        ]);

        $this->assertSame('Telefon', $normalized['payload']['title']);
        $this->assertSame('+90 500 000 00 00', $normalized['payload']['value']);
        $this->assertSame('fa-phone', $normalized['payload']['icon_class']);
        $this->assertSame('tel:+905000000000', $normalized['payload']['link']);
        $this->assertFalse($normalized['payload']['target_blank']);
    }
}

