@if(data_get($landingContent, 'sections_visible.main_cta', true))
<section class="section">
    <div class="container">
        <div class="cta-section">
            <div class="cta-content">
                <h2>{!! $landingContent['main_cta_title_html'] ?? "Gönderinizi <span class='gradient-text'>Bize Emanet Edin</span>" !!}</h2>
                <p>{{ $landingContent['main_cta_description_text'] ?? 'Zamanın paradan daha değerli olduğu anlarda yanınızdayız. Hemen arayın, en uygun çözümü birlikte bulalım.' }}</p>
                <div class="cta-buttons">
                    <a href="{{ $landingContent['main_cta_phone_href'] ?? 'tel:+905513567292' }}" class="btn btn-accent">
                        <i class="fa-solid {{ $landingContent['main_cta_phone_icon'] ?? 'fa-phone' }}"></i> {{ $landingContent['main_cta_phone_text'] ?? '0551 356 72 92' }}
                    </a>
                    <a href="{{ $landingContent['main_cta_secondary_href'] ?? 'https://wa.me/905513567292' }}" class="btn btn-outline">
                        <i class="fa-brands {{ $landingContent['main_cta_secondary_icon'] ?? 'fa-whatsapp' }}"></i> {{ $landingContent['main_cta_secondary_text'] ?? 'WhatsApp' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
