@if(data_get($landingContent, 'sections_visible.stats', true))
<section class="funfact-section">
    <div class="container">
        <div class="funfact-wrapper">
            @foreach(($landingContent['funfact_items'] ?? []) as $stat)
                <div class="funfact-item">
                    <div class="funfact-value">
                        <span data-count="{{ $stat['count'] ?? 0 }}">0</span>{{ $stat['suffix'] ?? '' }}
                    </div>
                    <div class="funfact-label">{{ $stat['label'] ?? '' }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

