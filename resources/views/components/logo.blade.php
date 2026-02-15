{{-- SimdiGetir Logo Component --}}
{{-- Usage: @include('components.logo', ['size' => 'md']) --}}
{{-- Sizes: sm (28px), md (36px), lg (44px) --}}
@php
    $sizes = [
        'sm' => ['height' => 28, 'fontSize' => 16, 'iconSize' => 28, 'gap' => 6],
        'md' => ['height' => 36, 'fontSize' => 20, 'iconSize' => 36, 'gap' => 8],
        'lg' => ['height' => 44, 'fontSize' => 26, 'iconSize' => 44, 'gap' => 10],
    ];
    $s = $sizes[$size ?? 'md'];
@endphp

<a href="/" class="simdigetir-logo" style="display:inline-flex; align-items:center; gap:{{ $s['gap'] }}px; text-decoration:none;">
    {{-- Icon --}}
    <svg width="{{ $s['iconSize'] }}" height="{{ $s['iconSize'] }}" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <defs>
            <linearGradient id="sg-grad-{{ $size ?? 'md' }}" x1="0" y1="0" x2="44" y2="44" gradientUnits="userSpaceOnUse">
                <stop stop-color="#FF6B35"/>
                <stop offset="1" stop-color="#E63946"/>
            </linearGradient>
        </defs>
        {{-- Background circle --}}
        <rect width="44" height="44" rx="12" fill="url(#sg-grad-{{ $size ?? 'md' }})"/>
        {{-- Package/box shape --}}
        <path d="M22 10L32 16V28L22 34L12 28V16L22 10Z" fill="white" fill-opacity="0.95"/>
        {{-- Inner lines for depth --}}
        <path d="M22 10L32 16L22 22L12 16L22 10Z" fill="white"/>
        <path d="M22 22V34L12 28V16L22 22Z" fill="white" fill-opacity="0.75"/>
        <path d="M22 22V34L32 28V16L22 22Z" fill="white" fill-opacity="0.55"/>
        {{-- Arrow/speed mark --}}
        <path d="M18 14L26 18" stroke="url(#sg-grad-{{ $size ?? 'md' }})" stroke-width="1.5" stroke-linecap="round" opacity="0.6"/>
        {{-- Checkmark --}}
        <path d="M19 22L21.5 24.5L26 19" stroke="url(#sg-grad-{{ $size ?? 'md' }})" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    {{-- Text --}}
    <span style="font-family: 'Inter', system-ui, -apple-system, sans-serif; font-size: {{ $s['fontSize'] }}px; font-weight: 800; letter-spacing: -0.5px; line-height: 1;">
        <span style="color: var(--text-primary, #1a1a2e);">Simdi</span><span style="background: linear-gradient(135deg, #FF6B35, #E63946); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Getir</span>
    </span>
</a>
