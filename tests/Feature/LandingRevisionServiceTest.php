<?php

namespace Tests\Feature;

use Modules\Landing\Models\LandingPage;
use Modules\Landing\Models\LandingPageSection;
use Modules\Landing\Models\LandingSectionItem;
use Modules\Landing\Models\LandingSectionRevision;
use Modules\Landing\Services\LandingRevisionService;
use Tests\TestCase;

class LandingRevisionServiceTest extends TestCase
{
    public function test_snapshot_and_restore_section_state()
    {
        $page = LandingPage::create([
            'slug' => 'home',
            'title' => 'Home',
            'is_active' => true,
        ]);

        $section = LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'services',
            'type' => 'services',
            'title' => 'Old Title',
            'payload' => ['services_badge_text' => 'Old Badge'],
            'sort_order' => 1,
            'is_active' => true,
        ]);

        LandingSectionItem::create([
            'section_id' => $section->id,
            'item_key' => 'old_item',
            'payload' => ['title' => 'Old Item'],
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $service = app(LandingRevisionService::class);
        $revision = $service->snapshotSection($section, 'test_snapshot');

        $section->update([
            'title' => 'New Title',
            'payload' => ['services_badge_text' => 'New Badge'],
            'is_active' => false,
        ]);

        LandingSectionItem::query()->where('section_id', $section->id)->delete();
        LandingSectionItem::create([
            'section_id' => $section->id,
            'item_key' => 'new_item',
            'payload' => ['title' => 'New Item'],
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $service->restoreRevision($revision->fresh());

        $section->refresh();
        $items = LandingSectionItem::query()
            ->where('section_id', $section->id)
            ->orderBy('sort_order')
            ->get();

        $this->assertSame('Old Title', $section->title);
        $this->assertSame('Old Badge', $section->payload['services_badge_text']);
        $this->assertTrue($section->is_active);
        $this->assertCount(1, $items);
        $this->assertSame('old_item', $items->first()->item_key);
        $this->assertSame('Old Item', $items->first()->payload['title']);
    }

    public function test_restore_creates_audit_revisions_for_before_and_after_state()
    {
        $page = LandingPage::create([
            'slug' => 'home',
            'title' => 'Home',
            'is_active' => true,
        ]);

        $section = LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'faq',
            'type' => 'faq',
            'title' => 'Initial',
            'payload' => ['faq_card_title_text' => 'Initial'],
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $service = app(LandingRevisionService::class);
        $targetRevision = $service->snapshotSection($section, 'target_snapshot');

        $section->update([
            'title' => 'Mutated',
            'payload' => ['faq_card_title_text' => 'Mutated'],
        ]);

        $beforeCount = LandingSectionRevision::query()->where('section_id', $section->id)->count();
        $service->restoreRevision($targetRevision->fresh());
        $afterCount = LandingSectionRevision::query()->where('section_id', $section->id)->count();

        $this->assertGreaterThanOrEqual($beforeCount + 2, $afterCount);
        $this->assertNotNull(
            LandingSectionRevision::query()
                ->where('section_id', $section->id)
                ->where('note', 'before_restore_from_revision:'.$targetRevision->id)
                ->first()
        );
        $this->assertNotNull(
            LandingSectionRevision::query()
                ->where('section_id', $section->id)
                ->where('note', 'restored_from_revision:'.$targetRevision->id)
                ->first()
        );
    }
}
