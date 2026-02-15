@extends('layouts.landing')

@section('title', $district['name'] . ' Kurye - Hızlı Moto Kurye Hizmeti | SimdiGetir')
@section('meta_description', $district['name'] . ' bölgesinde hızlı ve güvenilir kurye hizmeti. ' . $district['name'] . ' moto kurye, acil kurye, araçlı kurye. 7/24 hızlı ve güvenilir teslimat.')
@section('meta_keywords', $district['name'] . ' kurye, ' . $district['name'] . ' moto kurye, ' . $district['name'] . ' acil kurye, ' . $district['name'] . ' teslimat, ' . mb_strtolower($district['name']) . ' kurye hizmeti')
@section('geo_placename', $district['name'] . ', İstanbul')
@section('geo_position', $district['lat'] . ';' . $district['lng'])

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "SimdiGetir {{ $district['name'] }} Kurye",
    "description": "{{ $district['name'] }} bölgesinde hızlı ve güvenilir moto kurye, acil kurye ve araçlı kurye hizmeti. 7/24 hızlı teslimat.",
    "url": "{{ url()->current() }}",
    "telephone": "+905324847292",
    "address": {
        "@type": "PostalAddress",
        "addressLocality": "{{ $district['name'] }}",
        "addressRegion": "İstanbul",
        "addressCountry": "TR"
    },
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": {{ $district['lat'] }},
        "longitude": {{ $district['lng'] }}
    },
    "areaServed": {
        "@type": "AdministrativeArea",
        "name": "{{ $district['name'] }}, İstanbul"
    },
    "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],
        "opens": "00:00",
        "closes": "23:59"
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
        {"@type": "ListItem", "position": 3, "name": "{{ $district['name'] }}", "item": "{{ url()->current() }}"}
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
            <a href="{{ route('locations.index') }}">Hizmet Bölgeleri</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>{{ $district['name'] }}</span>
        </nav>
    </div>
</section>

<!-- Hero Section -->
<section class="hero" style="min-height: auto; padding: 3rem 0 4rem;">
    <div class="container">
        <div class="hero-content" style="grid-template-columns: 1.2fr 0.8fr;">
            <div>
                <div class="hero-badge animate__animated animate__fadeInUp">
                    <span class="pulse"></span>
                    {{ ucfirst($district['side']) }} Yakası · {{ $district['name'] }}
                </div>
                <h1 class="animate__animated animate__fadeInUp animate__delay-1s" style="font-size: 2.75rem;">
                    <span class="gradient-text">{{ $district['name'] }}</span> Kurye Hizmeti
                </h1>
                <p class="animate__animated animate__fadeInUp animate__delay-2s">
                    {{ $district['name'] }} ve çevresinde profesyonel motorlu kurye, acil kurye ve araçlı kurye hizmetleri. 
                    Akıllı rota optimizasyonu ile en hızlı teslimat garantisi.
                </p>
                <div class="hero-buttons animate__animated animate__fadeInUp animate__delay-3s">
                    <a href="tel:+905324847292" class="btn btn-primary">
                        <i class="fa-solid fa-phone"></i> {{ $district['name'] }}{{ $district['suffix'] }} Kurye Çağır
                    </a>
                    <a href="https://wa.me/905324847292?text={{ urlencode($district['name'] . ' bölgesinde kurye çağırmak istiyorum') }}" target="_blank" class="btn btn-outline">
                        <i class="fa-brands fa-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>
            <div class="hero-visual animate__animated animate__fadeInRight animate__delay-2s">
                <div class="hero-card">
                    <div class="floating-orb orb-1"></div>
                    <div class="hero-card-header">
                        <div class="ai-avatar">🚀</div>
                        <div class="ai-status">
                            <span class="ai-status-name">SimdiGetir Kurye</span>
                            <span class="ai-status-text">
                                {{ $district['name'] }} bölgesi aktif
                                <span class="typing-dots"><span></span><span></span><span></span></span>
                            </span>
                        </div>
                    </div>
                    <div class="hero-card-content">
                        <p>
                            <span class="highlight">[✓]</span> {{ $district['name'] }} taranıyor...<br>
                            <span class="success">✓</span> <span class="highlight">{{ count($district['neighborhoods']) }} mahalle</span> kapsama alanında<br>
                            <span class="success">✓</span> Müsait kuryeler: <span class="highlight">{{ rand(5, 30) }}+</span><br>
                            <span class="success">✓</span> Ort. teslimat: <span class="highlight">{{ rand(25, 55) }} dk</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Hizmetler -->
<section class="section" style="padding-top: 2rem;">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fa-solid fa-motorcycle"></i> {{ $district['name'] }} Hizmetleri
            </div>
            <h2 class="section-title">{{ $district['name'] }} <span class="gradient-text">Kurye Seçenekleri</span></h2>
        </div>
        <div class="services-grid">
            <div class="service-card">
                <span class="service-card-number">01</span>
                <div class="service-card-icon">🏍️</div>
                <h3>Motorlu Kurye</h3>
                <p>{{ $district['name'] }} bölgesinde trafiği atlatarak dakikalar içinde teslimat.</p>
                <ul class="service-card-features">
                    <li><i class="fa-solid fa-check"></i> Akıllı rota optimizasyonu</li>
                    <li><i class="fa-solid fa-check"></i> Canlı takip</li>
                    <li><i class="fa-solid fa-check"></i> Hızlı atama</li>
                </ul>
            </div>
            <div class="service-card">
                <span class="service-card-number">02</span>
                <div class="service-card-icon">⚡</div>
                <h3>Acil Kurye</h3>
                <p>{{ $district['name'] }} ve çevresine 3 saat içinde garantili teslimat.</p>
                <ul class="service-card-features">
                    <li><i class="fa-solid fa-check"></i> Öncelikli atama</li>
                    <li><i class="fa-solid fa-check"></i> 3 saat garanti</li>
                    <li><i class="fa-solid fa-check"></i> Anlık bildirim</li>
                </ul>
            </div>
            <div class="service-card">
                <span class="service-card-number">03</span>
                <div class="service-card-icon">🚗</div>
                <h3>Araçlı Kurye</h3>
                <p>{{ $district['name'] }}{{ $district['suffix'] }} büyük ve ağır gönderiler için araçlı kurye.</p>
                <ul class="service-card-features">
                    <li><i class="fa-solid fa-check"></i> Büyük ebat taşıma</li>
                    <li><i class="fa-solid fa-check"></i> Sigortalı gönderi</li>
                    <li><i class="fa-solid fa-check"></i> Farklı araç seçenekleri</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Mahalleler -->
@if(count($district['neighborhoods']) > 0)
<section class="section">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fa-solid fa-map-pin"></i> Mahalleler
            </div>
            <h2 class="section-title">{{ $district['name'] }} <span class="gradient-text">Mahalleleri</span></h2>
            <p class="section-subtitle">{{ $district['name'] }} ilçesindeki tüm mahallelere kurye hizmeti sunuyoruz.</p>
        </div>
        <div class="neighborhood-grid">
            @foreach($district['neighborhoods'] as $nSlug => $nName)
            <a href="{{ route('locations.neighborhood', [$slug, $nSlug]) }}" class="neighborhood-card">
                <i class="fa-solid fa-location-dot"></i>
                <span>{{ $nName }}</span>
                <i class="fa-solid fa-arrow-right neighborhood-arrow"></i>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Komşu İlçeler -->
@if($neighbors->count() > 0)
<section class="section" style="padding-top: 2rem;">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fa-solid fa-map"></i> Yakın Bölgeler
            </div>
            <h2 class="section-title">Komşu <span class="gradient-text">İlçeler</span></h2>
        </div>
        <div class="neighbor-grid">
            @foreach($neighbors as $nSlug => $neighbor)
            <a href="{{ route('locations.district', $nSlug) }}" class="location-card">
                <div class="location-card-icon">📍</div>
                <div class="location-card-content">
                    <h3>{{ $neighbor['name'] }}</h3>
                    <span class="location-card-count">{{ count($neighbor['neighborhoods'] ?? []) }} mahalle</span>
                </div>
                <i class="fa-solid fa-arrow-right location-card-arrow"></i>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA -->
<section class="section">
    <div class="container">
        <div class="cta-section">
            <div class="cta-content">
                <h2>{{ $district['name'] }}{{ $district['suffix'] }} <span class="gradient-text">Kurye Mi Lazım?</span></h2>
                <p>{{ $district['name'] }} bölgesindeki en yakın kuryeyi saniyeler içinde atayız.</p>
                <div class="cta-buttons">
                    <a href="tel:+905324847292" class="btn btn-accent">
                        <i class="fa-solid fa-phone"></i> Hemen Ara
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
    }

    .breadcrumb-nav a {
        color: var(--text-secondary);
        text-decoration: none;
        transition: color 0.3s;
    }

    .breadcrumb-nav a:hover {
        color: var(--accent);
    }

    .breadcrumb-nav i {
        font-size: 0.6rem;
    }

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
        font-size: 0.95rem;
    }

    .neighborhood-card:hover {
        border-color: var(--primary);
        transform: translateX(5px);
        box-shadow: 0 0 20px rgba(124, 58, 237, 0.1);
    }

    .neighborhood-card i:first-child {
        color: var(--accent);
        font-size: 0.85rem;
    }

    .neighborhood-card span {
        flex: 1;
    }

    .neighborhood-arrow {
        color: var(--text-muted);
        font-size: 0.7rem;
        transition: all 0.3s;
    }

    .neighborhood-card:hover .neighborhood-arrow {
        color: var(--accent);
        transform: translateX(3px);
    }

    .neighbor-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }

    .location-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem 1.5rem;
        background: var(--bg-glass);
        border: 1px solid var(--border-glass);
        border-radius: 1rem;
        text-decoration: none;
        color: var(--text-primary);
        transition: all 0.4s ease;
    }

    .location-card:hover {
        border-color: var(--primary);
        transform: translateY(-4px);
        box-shadow: 0 15px 40px rgba(124, 58, 237, 0.15);
    }

    .location-card-icon { font-size: 1.5rem; flex-shrink: 0; }
    .location-card-content { flex: 1; }
    .location-card-content h3 { font-size: 1rem; font-weight: 600; margin-bottom: 0.15rem; }
    .location-card-count { font-size: 0.8rem; color: var(--text-muted); }
    .location-card-arrow { color: var(--text-muted); font-size: 0.8rem; transition: all 0.3s; }
    .location-card:hover .location-card-arrow { color: var(--accent); transform: translateX(4px); }

    @media (max-width: 768px) {
        .neighborhood-grid { grid-template-columns: repeat(2, 1fr); }
        .neighbor-grid { grid-template-columns: 1fr; }
        .hero-content { grid-template-columns: 1fr !important; }
    }

    @media (max-width: 480px) {
        .neighborhood-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush
