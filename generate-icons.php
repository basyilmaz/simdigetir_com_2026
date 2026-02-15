<?php
/**
 * Generate favicon and PWA icons from SVG
 * Run: php generate-icons.php
 */

$sizes = [
    'favicon-16x16.png' => 16,
    'favicon-32x32.png' => 32,
    'apple-touch-icon.png' => 180,
    'icon-192.png' => 192,
    'icon-512.png' => 512,
];

// Icon SVG template (gradient square with package)
function generateIcon($size) {
    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="$size" height="$size" viewBox="0 0 44 44">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="44" y2="44" gradientUnits="userSpaceOnUse">
      <stop stop-color="#FF6B35"/>
      <stop offset="1" stop-color="#E63946"/>
    </linearGradient>
  </defs>
  <rect width="44" height="44" rx="8" fill="url(#g)"/>
  <path d="M22 10L32 16V28L22 34L12 28V16L22 10Z" fill="white" fill-opacity="0.95"/>
  <path d="M22 10L32 16L22 22L12 16L22 10Z" fill="white"/>
  <path d="M22 22V34L12 28V16L22 22Z" fill="white" fill-opacity="0.75"/>
  <path d="M22 22V34L32 28V16L22 22Z" fill="white" fill-opacity="0.55"/>
  <path d="M19 22L21.5 24.5L26 19" stroke="url(#g)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
SVG;
    return $svg;
}

$outputDir = __DIR__ . '/public/images/';

// Check if GD or Imagick is available
if (extension_loaded('imagick')) {
    echo "Using Imagick...\n";
    foreach ($sizes as $filename => $size) {
        $svg = generateIcon($size);
        $im = new Imagick();
        $im->setBackgroundColor(new ImagickPixel('transparent'));
        $im->setResolution($size * 2, $size * 2);
        $im->readImageBlob($svg);
        $im->setImageFormat('png');
        $im->resizeImage($size, $size, Imagick::FILTER_LANCZOS, 1);
        $im->writeImage($outputDir . $filename);
        $im->destroy();
        echo "Created: $filename ({$size}x{$size})\n";
    }
} else {
    echo "Imagick not available. Creating SVG-based icons instead...\n";
    // Create SVG files that can be used directly
    foreach ($sizes as $filename => $size) {
        $svgFilename = str_replace('.png', '.svg', $filename);
        $svg = generateIcon($size);
        file_put_contents($outputDir . $svgFilename, $svg);
        echo "Created SVG: $svgFilename ({$size}x{$size})\n";
    }
    echo "\nNote: For PNG conversion, use an online SVG-to-PNG converter or install the Imagick PHP extension.\n";
}

echo "\nDone!\n";
