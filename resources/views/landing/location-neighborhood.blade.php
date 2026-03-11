@extends('layouts.landing')

@section('title', $neighborhoodName . ' Mahallesi Kurye - ' . $district['name'] . ' | SimdiGetir')
@section('meta_description', $neighborhoodName . ' mahallesi ' . $district['name'] . ' kurye hizmeti. ' . $neighborhoodName . ' moto kurye, acil kurye. 7/24 hızlı ve güvenilir teslimat.')
@section('meta_keywords', $neighborhoodName . ' kurye, ' . $neighborhoodName . ' moto kurye, ' . $district['name'] . ' ' . $neighborhoodName . ' kurye, ' . mb_strtolower($neighborhoodName) . ' teslimat')
@section('geo_placename', $neighborhoodName . ', ' . $district['name'] . ', İstanbul')
@section('geo_position', $district['lat'] . ';' . $district['lng'])

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "SimdiGetir {{ $neighborhoodName }} Kurye",
    "description": "{{ $neighborhoodName }} mahallesi {{ $district['name'] }} bölgesinde 7/24 kurye hizmeti. Hızlı ve güvenilir teslimat.",
    "url": "{{ url()->current() }}",
    "telephone": "+905513567292",
    "address": {
        "@type": "PostalAddress",
        "addressLocality": "{{ $district['name'] }}",
        "addressRegion": "İstanbul",
        "addressCountry": "TR"
    },
    "areaServed": {
        "@type": "Place",
        "name": "{{ $neighborhoodName }}, {{ $district['name'] }}, İstanbul"
    },
    "parentOrganization": {"@id": "{{ url('/') }}/#organization"}
}
</script>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {"@type": "ListItem", "position": 1, "name": "Ana Sayfa", "item": "{{ url('/') }}"},
        {"@type": "ListItem", "position": 2, "name": "Hizmet Bölgeleri", "item": "{{ route('locations.index') }}"},
        {"@type": "ListItem", "position": 3, "name": "{{ $district['name'] }}", "item": "{{ route('locations.district', $districtSlug) }}"},
        {"@type": "ListItem", "position": 4, "name": "{{ $neighborhoodName }}", "item": "{{ url()->current() }}"}
    ]
}
</script>
@endsection

@section('content')
<!-- Breadcrumb -->
<section style="padding: 7rem 0 0;">
    <div class="container">
        <nav class="breadcrumb-nav">
            <a href="{{ url('/') }}">Ana Sayfa</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('locations.index') }}">Bölgeler</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('locations.district', $districtSlug) }}">{{ $district['name'] }}</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>{{ $neighborhoodName }}</span>
        </nav>
    </div>
</section>

<!-- Hero -->
<section class="hero" style="min-height: auto; padding: 3rem 0 4rem;">
    <div class="container" style="text-align: center;">
        <div class="hero-badge animate__animated animate__fadeInUp">
            <span class="pulse"></span>
            {{ $district['name'] }} · {{ $neighborhoodName }}
        </div>
        <h1 class="animate__animated animate__fadeInUp animate__delay-1s" style="font-size: 2.75rem;">
            <span class="gradient-text">{{ $neighborhoodName }}</span> Kurye Hizmeti
        </h1>
        <p class="animate__animated animate__fadeInUp animate__delay-2s" style="max-width: 650px; margin: 0 auto;">
            {{ $neighborhoodName }} mahallesine profesyonel motorlu kurye, acil kurye ve araçlı kurye hizmeti. 
            Akıllı rota optimizasyonu ile {{ $district['name'] }} bölgesinin en hızlı teslimatı.
        </p>
        <div class="hero-buttons animate__animated animate__fadeInUp animate__delay-3s" style="justify-content: center;">
            <a href="tel:+905513567292" class="btn btn-primary">
                <i class="fa-solid fa-phone"></i> Kurye Çağır
            </a>
            <a href="https://wa.me/905513567292?text={{ urlencode($neighborhoodName . ', ' . $district['name'] . ' bölgesinde kurye istiyorum') }}" target="_blank" class="btn btn-outline">
                <i class="fa-brands fa-whatsapp"></i> WhatsApp
            </a>
        </div>
    </div>
</section>

<!-- Hizmetler -->
<section class="section" style="padding-top: 2rem;">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fa-solid fa-bolt"></i> {{ $neighborhoodName }} Hizmetleri
            </div>
            <h2 class="section-title">{{ $neighborhoodName }} <span class="gradient-text">Kurye Seçenekleri</span></h2>
        </div>
        <div class="services-grid">
            <div class="service-card">
                <span class="service-card-number">01</span>
                <div class="service-card-icon">🏍️</div>
                <h3>{{ $neighborhoodName }} Moto Kurye</h3>
                <p>{{ $neighborhoodName }} mahallesinde trafiği atlatarak dakikalar içinde teslimat.</p>
                <ul class="service-card-features">
                    <li><i class="fa-solid fa-check"></i> Akıllı rota optimizasyonu</li>
                    <li><i class="fa-solid fa-check"></i> Canlı konum takibi</li>
                    <li><i class="fa-solid fa-check"></i> Anlık kurye ataması</li>
                </ul>
            </div>
            <div class="service-card">
                <span class="service-card-number">02</span>
                <div class="service-card-icon">⚡</div>
                <h3>{{ $neighborhoodName }} Acil Kurye</h3>
                <p>{{ $neighborhoodName }} bölgesine 3 saat içinde garantili acil teslimat.</p>
                <ul class="service-card-features">
                    <li><i class="fa-solid fa-check"></i> Öncelikli atama</li>
                    <li><i class="fa-solid fa-check"></i> 3 saat garanti</li>
                    <li><i class="fa-solid fa-check"></i> SMS bildirim</li>
                </ul>
            </div>
            <div class="service-card">
                <span class="service-card-number">03</span>
                <div class="service-card-icon">🚗</div>
                <h3>{{ $neighborhoodName }} Araçlı Kurye</h3>
                <p>{{ $neighborhoodName }} mahallesine büyük ebatlı gönderiler için araçlı kurye.</p>
                <ul class="service-card-features">
                    <li><i class="fa-solid fa-check"></i> Büyük ebat taşıma</li>
                    <li><i class="fa-solid fa-check"></i> Sigortalı gönderi</li>
                    <li><i class="fa-solid fa-check"></i> Farklı araç tipi</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Diğer Mahalleler -->
@if($otherNeighborhoods->count() > 0)
<section class="section">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fa-solid fa-map-pin"></i> {{ $district['name'] }} Mahalleleri
            </div>
            <h2 class="section-title">{{ $district['name'] }} Diğer <span class="gradient-text">Mahalleler</span></h2>
        </div>
        <div class="neighborhood-grid">
            @foreach($otherNeighborhoods as $nSlug => $nName)
            <a href="{{ route('locations.neighborhood', [$districtSlug, $nSlug]) }}" class="neighborhood-card">
                <i class="fa-solid fa-location-dot"></i>
                <span>{{ $nName }}</span>
                <i class="fa-solid fa-arrow-right neighborhood-arrow"></i>
            </a>
            @endforeach
        </div>
        <div style="text-align: center; margin-top: 2rem;">
            <a href="{{ route('locations.district', $districtSlug) }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> {{ $district['name'] }} Tüm Mahalleler
            </a>
        </div>
    </div>
</section>
@endif

<!-- CTA -->
<section class="section">
    <div class="container">
        <div class="cta-section">
            <div class="cta-content">
                <h2>{{ $neighborhoodName }}{{ \App\Helpers\TurkishHelper::locativeSuffix($neighborhoodName) }} <span class="gradient-text">Kurye Çağırın</span></h2>
                <p>{{ $neighborhoodName }} mahallesindeki en yakın kuryeyi saniyeler içinde atayız.</p>
                <div class="cta-buttons">
                    <a href="tel:+905513567292" class="btn btn-accent">
                        <i class="fa-solid fa-phone"></i> 0551 356 72 92
                    </a>
                    <a href="/iletisim" class="btn btn-outline">
                        <i class="fa-solid fa-envelope"></i> İletişime Geç
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.9rem;
        color: var(--text-muted);
        flex-wrap: wrap;
    }
    .breadcrumb-nav a { color: var(--text-secondary); text-decoration: none; transition: color 0.3s; }
    .breadcrumb-nav a:hover { color: var(--accent); }
    .breadcrumb-nav i.fa-chevron-right { font-size: 0.6rem; }

    .neighborhood-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }
    .neighborhood-card {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        background: var(--bg-glass);
        border: 1px solid var(--border-glass);
        border-radius: 0.75rem;
        text-decoration: none;
        color: var(--text-primary);
        transition: all 0.3s ease;
    }
    .neighborhood-card:hover {
        border-color: var(--primary);
        transform: translateX(5px);
    }
    .neighborhood-card i:first-child { color: var(--accent); font-size: 0.85rem; }
    .neighborhood-card span { flex: 1; }
    .neighborhood-arrow { color: var(--text-muted); font-size: 0.7rem; transition: all 0.3s; }
    .neighborhood-card:hover .neighborhood-arrow { color: var(--accent); transform: translateX(3px); }

    @media (max-width: 768px) {
        .neighborhood-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 480px) {
        .neighborhood-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush
