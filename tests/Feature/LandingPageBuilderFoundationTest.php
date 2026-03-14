<?php

namespace Tests\Feature;

use Modules\Landing\Models\LandingPage;
use Modules\Landing\Models\LandingPageSection;
use Modules\Landing\Models\LandingSectionItem;
use Modules\Landing\Services\LandingPageService;
use Tests\TestCase;

class LandingPageBuilderFoundationTest extends TestCase
{
    public function test_page_builder_relations_work()
    {
        $page = LandingPage::create([
            'slug' => 'home',
            'title' => 'Home',
            'meta' => ['title' => 'Home SEO'],
            'is_active' => true,
        ]);

        $section = LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'hero',
            'type' => 'hero',
            'title' => 'Hero Section',
            'payload' => ['headline' => 'Fast delivery'],
            'sort_order' => 1,
            'is_active' => true,
        ]);

        LandingSectionItem::create([
            'section_id' => $section->id,
            'item_key' => 'cta_primary',
            'payload' => ['label' => 'Teklif Al'],
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->assertCount(1, $page->sections);
        $this->assertCount(1, $section->items);
        $this->assertSame('hero', $page->sections->first()->key);
    }

    public function test_service_returns_only_active_sections_and_items_in_order()
    {
        $page = LandingPage::create([
            'slug' => 'home',
            'title' => 'Home',
            'is_active' => true,
        ]);

        $hero = LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'hero',
            'type' => 'hero',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $inactiveSection = LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'deprecated',
            'type' => 'legacy',
            'sort_order' => 1,
            'is_active' => false,
        ]);

        LandingSectionItem::create([
            'section_id' => $hero->id,
            'item_key' => 'secondary',
            'payload' => ['label' => 'Secondary'],
            'sort_order' => 2,
            'is_active' => true,
        ]);

        LandingSectionItem::create([
            'section_id' => $hero->id,
            'item_key' => 'primary',
            'payload' => ['label' => 'Primary'],
            'sort_order' => 1,
            'is_active' => true,
        ]);

        LandingSectionItem::create([
            'section_id' => $hero->id,
            'item_key' => 'inactive-item',
            'payload' => ['label' => 'Hidden'],
            'sort_order' => 0,
            'is_active' => false,
        ]);

        // Explicitly keep the variable used to ensure builder setup is complete.
        $this->assertNotNull($inactiveSection);

        $service = new LandingPageService;
        $published = $service->getPublishedPage('home');

        $this->assertNotNull($published);
        $this->assertCount(1, $published->sections);
        $this->assertSame('hero', $published->sections->first()->key);
        $this->assertCount(2, $published->sections->first()->items);
        $this->assertSame(
            ['primary', 'secondary'],
            $published->sections->first()->items->pluck('item_key')->values()->all()
        );
    }
}
