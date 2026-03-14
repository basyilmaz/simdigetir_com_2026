@if(data_get($landingContent, 'sections_visible.features', true))
<section class="section" style="background: linear-gradient(180deg, rgba(124, 58, 237, 0.05) 0%, transparent 100%);">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fa-solid fa-sparkles"></i> {{ $landingContent['features_badge_text'] ?? 'Neden Bizi Tercih Etmelisiniz?' }}
            </div>
            <h2 class="section-title">
                {!! $landingContent['features_title_html'] ?? "<span class='gradient-text'>Avantajlarımız</span>" !!}
            </h2>
            @if(!empty($landingContent['features_subtitle_text']))
                <p class="section-subtitle">{{ $landingContent['features_subtitle_text'] }}</p>
            @endif
        </div>

        <div class="features-grid">
            @foreach(($landingContent['feature_cards'] ?? []) as $feature)
                <div class="feature-card">
                    <span class="feature-icon">{{ $feature['icon'] ?? '★' }}</span>
                    <h4>{{ $feature['title'] ?? '' }}</h4>
                    <p>{{ $feature['description'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

