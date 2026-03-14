<?php

namespace Modules\Landing\Observers;

use Modules\Landing\Models\LandingPageSection;
use Modules\Landing\Models\LandingSectionItem;
use Modules\Landing\Services\LandingMediaVariantService;
use Modules\Landing\Services\LandingRevisionService;

class LandingSectionItemObserver
{
    private bool $syncingImagePayload = false;

    public function created(LandingSectionItem $item): void
    {
        $this->syncImagePayload($item);
        $this->snapshotBySectionId($item->section_id, 'item_created');
    }

    public function updated(LandingSectionItem $item): void
    {
        $this->syncImagePayload($item);
        $this->snapshotBySectionId($item->section_id, 'item_updated');
    }

    public function deleted(LandingSectionItem $item): void
    {
        $this->snapshotBySectionId($item->section_id, 'item_deleted');
    }

    private function snapshotBySectionId(?int $sectionId, string $note): void
    {
        if (! $sectionId) {
            return;
        }

        $section = LandingPageSection::query()->find($sectionId);
        if (! $section) {
            return;
        }

        app(LandingRevisionService::class)->snapshotSection(
            $section,
            $note,
            auth()->id()
        );
    }

    private function syncImagePayload(LandingSectionItem $item): void
    {
        if ($this->syncingImagePayload) {
            return;
        }

        $payload = is_array($item->payload) ? $item->payload : [];
        $imagePath = trim((string) ($payload['image_url'] ?? ''));
        if ($imagePath === '' || str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://') || str_starts_with($imagePath, '/')) {
            return;
        }

        $generated = app(LandingMediaVariantService::class)->generateForPublicPath($imagePath);
        if (empty($generated['srcset'])) {
            return;
        }

        $this->syncingImagePayload = true;
        try {
            $payload['image_srcset'] = $generated['srcset'];
            $payload['image_variants'] = $generated['variants'];
            if (empty($payload['image_sizes'])) {
                $payload['image_sizes'] = '(max-width: 768px) 100vw, 33vw';
            }

            $item->updateQuietly(['payload' => $payload]);
        } finally {
            $this->syncingImagePayload = false;
        }
    }
}
