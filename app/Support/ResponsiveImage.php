<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class ResponsiveImage
{
    public static function resolveUrl(?string $url): ?string
    {
        $value = trim((string) $url);
        if ($value === '') {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://') || str_starts_with($value, '/')) {
            return $value;
        }

        return Storage::disk('public')->url($value);
    }

    /**
     * Build a conservative srcset using query width params.
     * Compatible with image CDNs, and harmless for static file URLs.
     */
    public static function buildSrcset(?string $url, array $widths = [480, 768, 1200]): ?string
    {
        $baseUrl = static::resolveUrl($url);
        if ($baseUrl === null || $baseUrl === '') {
            return null;
        }

        $parts = [];
        foreach ($widths as $width) {
            $w = (int) $width;
            if ($w <= 0) {
                continue;
            }

            $sep = str_contains($baseUrl, '?') ? '&' : '?';
            $parts[] = "{$baseUrl}{$sep}w={$w} {$w}w";
        }

        return empty($parts) ? null : implode(', ', $parts);
    }

    public static function normalizeSizes(?string $sizes, string $fallback = '(max-width: 768px) 100vw, 33vw'): string
    {
        $value = trim((string) $sizes);
        return $value !== '' ? $value : $fallback;
    }
}
