@extends('layouts.landing')

@section('title', 'İstanbul Kurye Hizmet Bölgeleri - SimdiGetir')
@section('meta_description', 'SimdiGetir kurye hizmeti İstanbul\'un tüm ilçe ve mahallelerinde. Avrupa ve Anadolu yakasında 7/24 hızlı teslimat.')
@section('meta_keywords', 'istanbul kurye, ilçe kurye, mahalle kurye, istanbul moto kurye, avrupa yakası kurye, anadolu yakası kurye')

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Service",
    "name": "SimdiGetir İstanbul Kurye Hizmeti",
    "description": "İstanbul'un tüm ilçe ve mahallelerine 7/24 kurye hizmeti.",
    "provider": {"@id": "{{ url('/') }}/#organization"},
    "areaServed": {
        "@type": "City",
        "name": "İstanbul",
        "containsPlace": [
            @foreach(array_merge($avrupa, $anadolu) as $slug => $d)
            {"@type": "AdministrativeArea", "name": "{{ $d['name'] }}"}@if(!$loop->last),@endif
            @endforeach
        ]
    },
    "serviceType": "Kurye Hizmeti"
}
</script>
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero" style="min-height: auto; padding: 10rem 0 4rem;">
    <div class="container" style="text-align: center;">
        <div class="hero-badge animate__animated animate__fadeInUp">
            <span class="pulse"></span>
            {{ $totalDistricts }} İlçe · {{ $totalNeighborhoods }}+ Mahalle
        </div>
        <h1 class="animate__animated animate__fadeInUp animate__delay-1s" style="font-size: 3rem;">
            <span class="gradient-text">İstanbul</span> Kurye Hizmet Bölgeleri
        </h1>
        <p class="animate__animated animate__fadeInUp animate__delay-2s" style="max-width: 650px; margin: 0 auto;">
            İstanbul'un tüm ilçe ve mahallelerine 7/24 kurye hizmeti vermekteyiz. Bölgenizi seçerek hemen kurye çağırın.
        </p>
    </div>
</section>

<!-- Avrupa Yakası -->
<section class="section" style="padding-top: 3rem;">
    <div class="container">
        <div class="section-header" style="text-align: left; margin-bottom: 2rem;">
            <div class="section-badge">
                <i class="fa-solid fa-map-location-dot"></i> Avrupa Yakası
            </div>
            <h2 class="section-title" style="font-size: 2rem;">Avrupa Yakası <span class="gradient-text">İlçeleri</span></h2>
        </div>
        <div class="location-grid">
            @foreach($avrupa as $slug => $district)
            <a href="{{ route('locations.district', $slug) }}" class="location-card">
                <div class="location-card-icon">📍</div>
                <div class="location-card-content">
                    <h3>{{ $district['name'] }}</h3>
                    <span class="location-card-count">{{ $district['neighborhood_count'] }} mahalle</span>
                </div>
                <i class="fa-solid fa-arrow-right location-card-arrow"></i>
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Anadolu Yakası -->
<section class="section" style="padding-top: 2rem;">
    <div class="container">
        <div class="section-header" style="text-align: left; margin-bottom: 2rem;">
            <div class="section-badge">
                <i class="fa-solid fa-map-location-dot"></i> Anadolu Yakası
            </div>
            <h2 class="section-title" style="font-size: 2rem;">Anadolu Yakası <span class="gradient-text">İlçeleri</span></h2>
        </div>
        <div class="location-grid">
            @foreach($anadolu as $slug => $district)
            <a href="{{ route('locations.district', $slug) }}" class="location-card">
                <div class="location-card-icon">📍</div>
                <div class="location-card-content">
                    <h3>{{ $district['name'] }}</h3>
                    <span class="location-card-count">{{ $district['neighborhood_count'] }} mahalle</span>
                </div>
                <i class="fa-solid fa-arrow-right location-card-arrow"></i>
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section">
    <div class="container">
        <div class="cta-section">
            <div class="cta-content">
                <h2>Bölgeniz <span class="gradient-text">Hangisi?</span></h2>
                <p>İstanbul'un neresinde olursanız olun, size en yakın kuryeyi anında atayız.</p>
                <div class="cta-buttons">
                    <a href="tel:+905324847292" class="btn btn-accent">
                        <i class="fa-solid fa-phone"></i> Hemen Ara
                    </a>
                    <a href="https://wa.me/905324847292" target="_blank" class="btn btn-outline">
                        <i class="fa-brands fa-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .location-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
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

    .location-card-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .location-card-content {
        flex: 1;
    }

    .location-card-content h3 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.15rem;
    }

    .location-card-count {
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    .location-card-arrow {
        color: var(--text-muted);
        font-size: 0.8rem;
        transition: all 0.3s;
    }

    .location-card:hover .location-card-arrow {
        color: var(--accent);
        transform: translateX(4px);
    }

    @media (max-width: 768px) {
        .location-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .location-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
