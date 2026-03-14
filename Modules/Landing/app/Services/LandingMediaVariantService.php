<?php

namespace Modules\Landing\Services;

use Illuminate\Support\Facades\Storage;

class LandingMediaVariantService
{
    /**
     * @return array{srcset: string|null, variants: array<int, array<string, mixed>>}
     */
    public function generateForPublicPath(string $relativePath, array $widths = [480, 768, 1200]): array
    {
        if ($relativePath === '' || str_starts_with($relativePath, 'http://') || str_starts_with($relativePath, 'https://') || ! extension_loaded('gd')) {
            return ['srcset' => null, 'variants' => []];
        }

        $fullPath = storage_path('app/public/'.$relativePath);
        if (! is_file($fullPath)) {
            return ['srcset' => null, 'variants' => []];
        }

        [$source, $sourceWidth, $sourceHeight] = $this->loadImage($fullPath);
        if (! $source || $sourceWidth < 2 || $sourceHeight < 2) {
            return ['srcset' => null, 'variants' => []];
        }

        $baseName = pathinfo($relativePath, PATHINFO_FILENAME);
        $variants = [];

        foreach ($widths as $rawWidth) {
            $targetWidth = (int) $rawWidth;
            if ($targetWidth <= 0 || $targetWidth > $sourceWidth) {
                continue;
            }

            $targetHeight = (int) round(($sourceHeight / $sourceWidth) * $targetWidth);
            $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
            if (! $canvas) {
                continue;
            }

            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

            $variantRelative = 'landing/variants/'.$baseName.'-'.$targetWidth.'.webp';
            $variantFullPath = storage_path('app/public/'.$variantRelative);
            $variantDir = dirname($variantFullPath);
            if (! is_dir($variantDir)) {
                @mkdir($variantDir, 0775, true);
            }

            if (function_exists('imagewebp') && imagewebp($canvas, $variantFullPath, 80)) {
                $variants[] = [
                    'path' => $variantRelative,
                    'url' => Storage::disk('public')->url($variantRelative),
                    'width' => $targetWidth,
                    'height' => $targetHeight,
                ];
            }

            imagedestroy($canvas);
        }

        imagedestroy($source);

        $srcset = null;
        if (! empty($variants)) {
            $srcset = collect($variants)
                ->map(fn ($variant) => $variant['url'].' '.$variant['width'].'w')
                ->implode(', ');
        }

        return [
            'srcset' => $srcset,
            'variants' => $variants,
        ];
    }

    /**
     * @return array{0: resource|\GdImage|null, 1: int, 2: int}
     */
    private function loadImage(string $path): array
    {
        $info = @getimagesize($path);
        if (! is_array($info)) {
            return [null, 0, 0];
        }

        $mime = $info['mime'] ?? '';
        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : null,
            default => null,
        };

        return [$image, (int) ($info[0] ?? 0), (int) ($info[1] ?? 0)];
    }
}

