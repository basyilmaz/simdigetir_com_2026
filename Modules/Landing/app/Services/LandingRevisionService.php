<?php

namespace Modules\Landing\Services;

use Illuminate\Support\Facades\DB;
use Modules\Landing\Models\LandingPageSection;
use Modules\Landing\Models\LandingSectionItem;
use Modules\Landing\Models\LandingSectionRevision;

class LandingRevisionService
{
    public function snapshotSection(LandingPageSection $section, string $note, ?int $changedBy = null): LandingSectionRevision
    {
        $sectionSnapshot = $section->fresh();
        if ($sectionSnapshot === null) {
            return LandingSectionRevision::query()->create([
                'page_id' => $section->page_id,
                'section_id' => $section->id,
                'changed_by' => $changedBy,
                'snapshot' => ['section' => [], 'items' => []],
                'note' => $note,
            ]);
        }

        $sectionSnapshot->setRelation(
            'items',
            $sectionSnapshot->items()->orderBy('sort_order')->get()
        );

        $snapshot = [
            'section' => [
                'id' => $sectionSnapshot->id,
                'page_id' => $sectionSnapshot->page_id,
                'key' => $sectionSnapshot->key,
                'type' => $sectionSnapshot->type,
                'title' => $sectionSnapshot->title,
                'payload' => $sectionSnapshot->payload,
                'sort_order' => $sectionSnapshot->sort_order,
                'is_active' => $sectionSnapshot->is_active,
            ],
            'items' => $sectionSnapshot->items
                ->sortBy('sort_order')
                ->map(fn (LandingSectionItem $item) => [
                    'item_key' => $item->item_key,
                    'payload' => $item->payload,
                    'sort_order' => $item->sort_order,
                    'is_active' => $item->is_active,
                ])
                ->values()
                ->all(),
        ];

        return LandingSectionRevision::query()->create([
            'page_id' => $section->page_id,
            'section_id' => $section->id,
            'changed_by' => $changedBy,
            'snapshot' => $snapshot,
            'note' => $note,
        ]);
    }

    public function restoreRevision(LandingSectionRevision $revision, ?int $changedBy = null): ?LandingPageSection
    {
        $restoredSection = null;

        DB::transaction(function () use ($revision, $changedBy): void {
            $sectionData = $revision->snapshot['section'] ?? null;
            if (! is_array($sectionData) || empty($revision->section_id)) {
                return;
            }

            /** @var LandingPageSection|null $section */
            $section = LandingPageSection::query()->find($revision->section_id);
            if (! $section) {
                return;
            }

            // Keep an audit trail of the state right before restore is applied.
            $this->snapshotSection($section, 'before_restore_from_revision:'.$revision->id, $changedBy);

            $section->update([
                'page_id' => $sectionData['page_id'] ?? $section->page_id,
                'key' => $sectionData['key'] ?? $section->key,
                'type' => $sectionData['type'] ?? $section->type,
                'title' => $sectionData['title'] ?? null,
                'payload' => $sectionData['payload'] ?? [],
                'sort_order' => $sectionData['sort_order'] ?? 0,
                'is_active' => (bool) ($sectionData['is_active'] ?? true),
            ]);

            $items = $revision->snapshot['items'] ?? [];

            LandingSectionItem::query()->where('section_id', $section->id)->delete();
            foreach ($items as $item) {
                LandingSectionItem::query()->create([
                    'section_id' => $section->id,
                    'item_key' => $item['item_key'] ?? null,
                    'payload' => $item['payload'] ?? [],
                    'sort_order' => $item['sort_order'] ?? 0,
                    'is_active' => (bool) ($item['is_active'] ?? true),
                ]);
            }

            // Record the final restored state as its own revision event.
            $section->refresh();
            $this->snapshotSection($section, 'restored_from_revision:'.$revision->id, $changedBy);
        });

        if ($revision->section_id) {
            $restoredSection = LandingPageSection::query()->find($revision->section_id);
        }

        return $restoredSection;
    }
}
