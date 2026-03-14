<?php

namespace Tests\Feature;

use Modules\Landing\Database\Seeders\LandingDatabaseSeeder;
use Modules\Landing\Models\LandingPage;
use Modules\Landing\Models\LandingPageSection;
use Modules\Landing\Models\LandingSectionItem;
use Tests\TestCase;

class LandingSeederTest extends TestCase
{
    public function test_landing_database_seeder_creates_home_page_and_sections()
    {
        $this->seed(LandingDatabaseSeeder::class);

        $page = LandingPage::query()->where('slug', 'home')->first();
        $this->assertNotNull($page);

        $sectionKeys = LandingPageSection::query()
            ->where('page_id', $page->id)
            ->pluck('key')
            ->all();

        $this->assertContains('hero', $sectionKeys);
        $this->assertContains('services', $sectionKeys);
        $this->assertContains('faq', $sectionKeys);
        $this->assertContains('corporate_cta', $sectionKeys);
        $this->assertContains('courier_cta', $sectionKeys);
    }

    public function test_landing_database_seeder_creates_items_for_dynamic_sections()
    {
        $this->seed(LandingDatabaseSeeder::class);

        $this->assertGreaterThanOrEqual(3, LandingSectionItem::query()
            ->whereHas('section', fn ($query) => $query->where('key', 'services'))
            ->count());

        $this->assertGreaterThanOrEqual(2, LandingSectionItem::query()
            ->whereHas('section', fn ($query) => $query->where('key', 'faq'))
            ->count());

        $this->assertGreaterThanOrEqual(4, LandingSectionItem::query()
            ->whereHas('section', fn ($query) => $query->where('key', 'corporate_cta'))
            ->count());

        $this->assertGreaterThanOrEqual(4, LandingSectionItem::query()
            ->whereHas('section', fn ($query) => $query->where('key', 'courier_cta'))
            ->count());
    }
}
