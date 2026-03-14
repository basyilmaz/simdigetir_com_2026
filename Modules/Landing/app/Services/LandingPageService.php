<?php

namespace Modules\Landing\Services;

use Modules\Landing\Models\LandingPage;

class LandingPageService
{
    public function getPublishedPage(string $slug): ?LandingPage
    {
        return LandingPage::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'sections' => function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                },
                'sections.items' => function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                },
            ])
            ->first();
    }
}
