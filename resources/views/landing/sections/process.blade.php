@if(data_get($landingContent, 'sections_visible.process', true))
<section class="process-section">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fa-solid fa-diagram-project"></i> {{ $landingContent['process_badge_text'] ?? 'Nasıl Çalışır?' }}
            </div>
            <h2 class="section-title">
                {!! $landingContent['process_title_html'] ?? "3 Adımda <span class='gradient-text'>Teslimat</span>" !!}
            </h2>
            <p class="section-subtitle">
                {{ $landingContent['process_subtitle_text'] ?? '3 basit adımda gönderinizi en hızlı şekilde teslim ediyoruz.' }}
            </p>
        </div>

        <div class="process-grid">
            @foreach(($landingContent['process_steps'] ?? []) as $step)
                <div class="process-card">
                    <div class="process-number">{{ $step['number'] ?? '00' }}</div>
                    <h3>{{ $step['title'] ?? '' }}</h3>
                    <p>{{ $step['description'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

