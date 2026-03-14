<?php

namespace Modules\Landing\Observers;

use Modules\Landing\Models\LandingPageSection;
use Modules\Landing\Services\LandingMediaVariantService;
use Modules\Landing\Services\LandingRevisionService;

class LandingPageSectionObserver
{
    private bool $syncingImagePayload = false;

    public function created(LandingPageSection $section): void
    {
        $this->syncHeroImagePayload($section);
        $this->snapshot($section, 'section_created');
    }

    public function updated(LandingPageSection $section): void
    {
        $this->syncHeroImagePayload($section);
        $this->snapshot($section, 'section_updated');
    }

    public function deleting(LandingPageSection $section): void
    {
        $this->snapshot($section, 'section_deleting');
    }

    private function snapshot(LandingPageSection $section, string $note): void
    {
        app(LandingRevisionService::class)->snapshotSection(
            $section,
            $note,
            auth()->id()
        );
    }

    private function syncHeroImagePayload(LandingPageSection $section): void
    {
        if ($this->syncingImagePayload || $section->key !== 'hero') {
            return;
        }

        $payload = is_array($section->payload) ? $section->payload : [];
        $imagePath = trim((string) ($payload['hero_slide2_image_url'] ?? ''));
        if ($imagePath === '' || str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://') || str_starts_with($imagePath, '/')) {
            return;
        }

        $generated = app(LandingMediaVariantService::class)->generateForPublicPath($imagePath);
        if (empty($generated['srcset'])) {
            return;
        }

        $this->syncingImagePayload = true;
        try {
            $payload['hero_slide2_image_srcset'] = $generated['srcset'];
            $payload['hero_slide2_image_variants'] = $generated['variants'];
            if (empty($payload['hero_slide2_image_sizes'])) {
                $payload['hero_slide2_image_sizes'] = '(max-width: 768px) 100vw, 50vw';
            }

            $section->updateQuietly(['payload' => $payload]);
        } finally {
            $this->syncingImagePayload = false;
        }
    }
}
