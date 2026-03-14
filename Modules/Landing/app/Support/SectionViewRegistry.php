<?php

namespace Modules\Landing\Support;

class SectionViewRegistry
{
    public static function home(): array
    {
        return [
            'features' => static::viewForType('features'),
            'process' => static::viewForType('process'),
            'stats' => static::viewForType('stats'),
            'testimonials' => static::viewForType('testimonials'),
            'main_cta' => static::viewForType('main_cta'),
        ];
    }

    public static function viewForType(string $type): string
    {
        return match ($type) {
            'features' => 'landing.sections.features',
            'process' => 'landing.sections.process',
            'stats' => 'landing.sections.stats',
            'testimonials' => 'landing.sections.testimonials',
            'main_cta' => 'landing.sections.main-cta',
            default => 'landing.sections.features',
        };
    }
}
