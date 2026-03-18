{{-- SimdiGetir Logo Component --}}
{{-- Usage: @include('components.logo', ['size' => 'md']) --}}
{{-- Sizes: sm (28px), md (36px), lg (44px) --}}
@php
    $sizes = [
        'sm' => ['height' => 36, 'fontSize' => 16, 'iconSize' => 28, 'gap' => 6],
        'md' => ['height' => 52, 'fontSize' => 20, 'iconSize' => 36, 'gap' => 8],
        'lg' => ['height' => 64, 'fontSize' => 26, 'iconSize' => 44, 'gap' => 10],
    ];

    $sizeKey = $size ?? 'md';
    $s = $sizes[$sizeKey] ?? $sizes['md'];

    $logoAlt = (string) \Modules\Settings\Models\Setting::getValue('brand.logo_alt', 'SimdiGetir');
    $heightOverride = \Modules\Settings\Models\Setting::getValue('brand.logo_height_'.$sizeKey, null);
    $logoHeight = is_numeric($heightOverride) ? (int) $heightOverride : $s['height'];

    $legacyLogoUrl = trim((string) \Modules\Settings\Models\Setting::getValue('brand.logo_url', ''));
    $defaultLogoLightUrl = asset('images/logo-light.png');
    $defaultLogoDarkUrl = asset('images/logo-dark.png');

    $logoLightUrl = trim((string) \Modules\Settings\Models\Setting::getValue(
        'brand.logo_url_light',
        $legacyLogoUrl !== '' ? $legacyLogoUrl : $defaultLogoLightUrl
    ));

    $logoDarkUrl = trim((string) \Modules\Settings\Models\Setting::getValue(
        'brand.logo_url_dark',
        $legacyLogoUrl !== '' ? $legacyLogoUrl : $defaultLogoDarkUrl
    ));

    $logoLightUrl = $logoLightUrl !== '' ? $logoLightUrl : $defaultLogoLightUrl;
    $logoDarkUrl = $logoDarkUrl !== '' ? $logoDarkUrl : $defaultLogoDarkUrl;
@endphp

<a
    href="/"
    class="simdigetir-logo"
    style="display:inline-flex; align-items:center; gap:{{ $s['gap'] }}px; text-decoration:none;"
    data-logo-light-url="{{ $logoLightUrl }}"
    data-logo-dark-url="{{ $logoDarkUrl }}"
>
    <span class="simdigetir-logo-media">
        <img
            src="{{ $logoLightUrl }}"
            alt="{{ $logoAlt }}"
            class="simdigetir-logo-image simdigetir-logo-image-light"
            style="height: {{ $logoHeight }}px; width: auto;"
            loading="eager"
            decoding="async"
        />
        <img
            src="{{ $logoDarkUrl }}"
            alt="{{ $logoAlt }}"
            class="simdigetir-logo-image simdigetir-logo-image-dark"
            style="height: {{ $logoHeight }}px; width: auto;"
            loading="eager"
            decoding="async"
            onerror="this.onerror=null;this.src='{{ $logoLightUrl }}';"
        />
    </span>
</a>