@extends('layouts.landing')

@section('title', $landingContent['meta_title'] ?? 'Hizmetlerimiz - SimdiGetir Profesyonel Kurye Hizmetleri')
@section('meta_description', $landingContent['meta_description'] ?? 'SimdiGetir profesyonel kurye hizmetleri. Motorlu kurye, acil kurye ve araçlı kurye hizmetleri ile 7/24 yanınızdayız.')
@section('meta_keywords', $landingContent['meta_keywords'] ?? 'motorlu kurye, acil kurye, aracli kurye, istanbul kurye')

@section('robots', $landingContent['robots'] ?? 'index, follow')
@section('canonical_url', $landingContent['canonical_url'] ?? url()->current())
@section('og_title', $landingContent['og_title'] ?? ($landingContent['meta_title'] ?? 'SimdiGetir'))
@section('og_description', $landingContent['og_description'] ?? ($landingContent['meta_description'] ?? 'Hizli ve guvenilir kurye hizmeti'))
@section('og_image', $landingContent['og_image'] ?? asset('images/og-banner.png'))

@section('structured_data')
@php
    $serviceSchemaItems = $landingContent['service_schema_items'] ?? [
        [
            'name' => 'Motorlu Kurye',
            'description' => 'Trafiği atlatarak dakikalar içinde teslimat. Akıllı rota optimizasyonu ile İstanbul\'un en hızlı moto kurye hizmeti.',
            'serviceType' => 'Motorlu Kurye Hizmeti',
            'url' => url('/hizmetler').'#motorlu-kurye',
        ],
        [
            'name' => 'Acil Kurye',
            'description' => '3 saat içinde garantili teslimat. Öncelikli kurye ataması ile acil gönderileriniz güvende.',
            'serviceType' => 'Acil Kurye Hizmeti',
            'url' => url('/hizmetler').'#acil-kurye',
        ],
        [
            'name' => 'Araçlı Kurye',
            'description' => 'Büyük ve ağır gönderiler için araçlı kurye hizmeti. Sigortalı taşıma garantisi.',
            'serviceType' => 'Araçlı Kurye Hizmeti',
            'url' => url('/hizmetler').'#aracli-kurye',
        ],
    ];

    $itemListElement = [];
    foreach ($serviceSchemaItems as $index => $serviceItem) {
        if (empty($serviceItem['name'])) {
            continue;
        }

        $itemListElement[] = [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'item' => [
                '@type' => 'Service',
                'name' => (string) $serviceItem['name'],
                'description' => (string) ($serviceItem['description'] ?? ''),
                'provider' => ['@id' => url('/').'#organization'],
                'areaServed' => ['@type' => 'City', 'name' => 'İstanbul'],
                'serviceType' => (string) ($serviceItem['serviceType'] ?? $serviceItem['name']),
                'url' => (string) ($serviceItem['url'] ?? url('/hizmetler')),
            ],
        ];
    }

    $servicesSchema = $landingContent['structured_data'] ?? [
        '@context' => 'https://schema.org',
        '@type' => 'ItemList',
        'itemListElement' => $itemListElement,
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($servicesSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE) !!}
</script>
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero" style="min-height: auto; padding: 10rem 0 4rem;">
    <div class="container" style="text-align: center;">
        <div class="hero-badge animate__animated animate__fadeInUp">
            <span class="pulse"></span>
            {{ $landingContent['hero_badge_text'] ?? 'Profesyonel Hizmetler' }}
        </div>
        <h1 class="animate__animated animate__fadeInUp animate__delay-1s" style="font-size: 3rem;">
            {!! $landingContent['hero_title_html'] ?? "Akıllı Kurye <span class='gradient-text'>Çözümleri</span>" !!}
        </h1>
        <p class="animate__animated animate__fadeInUp animate__delay-2s" style="max-width: 650px; margin: 0 auto;">
            {{ $landingContent['hero_description_text'] ?? 'Gönderinize en uygun hizmeti sunuyoruz. Hızlı, güvenilir, profesyonel.' }}
        </p>
        <div class="animate__animated animate__fadeInUp animate__delay-3s" style="margin-top: 3rem;">
            <img src="{{ asset('images/hero-services.svg') }}" alt="SimdiGetir Hizmetleri" width="600" height="400" loading="lazy" decoding="async" style="max-width: 600px; width: 100%; border-radius: 20px;">
        </div>
    </div>
</section>

<!-- Motorlu Kurye Section -->
<section class="section" id="motorlu-kurye">
    <div class="container">
        <div class="responsive-stack" style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
            <div class="service-showcase">
                <div class="glass" style="padding: 3rem; position: relative; overflow: hidden;">
                    <div class="floating-orb orb-1"></div>
                    <div class="service-number">01</div>
                    <span style="font-size: 5rem; display: block; margin-bottom: 1.5rem;">🏍️</span>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <span class="tag">En Hızlı</span>
                        <span class="tag">Trafik Yok</span>
                        <span class="tag">Dakikalar İçinde</span>
                    </div>
                </div>
            </div>
            <div>
                <div class="section-badge">
                    <i class="fa-solid fa-bolt"></i> En Popüler
                </div>
                <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem;">
                    Motorlu <span class="gradient-text">Kurye</span>
                </h2>
                <p style="color: var(--text-secondary); margin-bottom: 2rem; font-size: 1.125rem;">
                    Trafiği atlatarak dakikalar içinde hedefe ulaşın. Akıllı rota optimizasyonumuz 
                    sayesinde en hızlı teslimat deneyimini yaşayın.
                </p>
                
                <div class="service-features">
                    <div class="sf-item">
                        <div class="sf-icon">🧭</div>
                        <div>
                            <strong>Akıllı Rota Optimizasyonu</strong>
                            <p>Anlık trafik verileri ile en hızlı rota belirlenir</p>
                        </div>
                    </div>
                    <div class="sf-item">
                        <div class="sf-icon">⚡</div>
                        <div>
                            <strong>Dakikalar İçinde</strong>
                            <p>Acil gönderileriniz için saniyeler içinde kurye atanır</p>
                        </div>
                    </div>
                    <div class="sf-item">
                        <div class="sf-icon">📍</div>
                        <div>
                            <strong>Canlı Takip</strong>
                            <p>Gönderinizi harita üzerinden gerçek zamanlı izleyin</p>
                        </div>
                    </div>
                </div>
                
                <a href="tel:+905513567292" class="btn btn-primary" style="margin-top: 2rem;">
                    <i class="fa-solid fa-phone"></i> Hemen Ara
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Acil Kurye Section -->
<section class="section" id="acil-kurye" style="background: linear-gradient(180deg, rgba(124, 58, 237, 0.05) 0%, transparent 100%);">
    <div class="container">
        <div class="responsive-stack" style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
            <div>
                <div class="section-badge" style="background: linear-gradient(135deg, rgba(34, 211, 238, 0.15) 0%, rgba(124, 58, 237, 0.15) 100%); border-color: var(--accent);">
                    <i class="fa-solid fa-rocket" style="color: var(--accent);"></i> <span style="color: var(--accent);">Öncelikli</span>
                </div>
                <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem;">
                    Acil <span class="gradient-text">Kurye</span>
                </h2>
                <p style="color: var(--text-secondary); margin-bottom: 2rem; font-size: 1.125rem;">
                    Kritik gönderileriniz için öncelikli hizmet. Saniyeler içinde en yakın 
                    kurye atanır ve garantili teslimat sağlanır.
                </p>
                
                <div class="service-features">
                    <div class="sf-item">
                        <div class="sf-icon" style="background: linear-gradient(135deg, #22d3ee 0%, #7c3aed 100%);">🎯</div>
                        <div>
                            <strong>Anlık Kurye Atama</strong>
                            <p>En yakın müsait kurye saniyeler içinde size yönlendirilir</p>
                        </div>
                    </div>
                    <div class="sf-item">
                        <div class="sf-icon" style="background: linear-gradient(135deg, #22d3ee 0%, #7c3aed 100%);">🛡️</div>
                        <div>
                            <strong>SLA Garantisi</strong>
                            <p>Belirlenen sürede teslimat garantisi, gecikmelere karşı koruma</p>
                        </div>
                    </div>
                    <div class="sf-item">
                        <div class="sf-icon" style="background: linear-gradient(135deg, #22d3ee 0%, #7c3aed 100%);">⚡</div>
                        <div>
                            <strong>Öncelikli Statü</strong>
                            <p>Gönderiniz diğer tüm siparişlerden önce işleme alınır</p>
                        </div>
                    </div>
                </div>
                
                <a href="tel:+905513567292" class="btn btn-accent" style="margin-top: 2rem;">
                    <i class="fa-solid fa-bolt"></i> Acil Kurye Çağır
                </a>
            </div>
            <div class="service-showcase">
                <div class="glass" style="padding: 3rem; position: relative; overflow: hidden; background: linear-gradient(135deg, rgba(34, 211, 238, 0.05) 0%, rgba(124, 58, 237, 0.1) 100%);">
                    <div class="floating-orb orb-2"></div>
                    <div class="service-number">02</div>
                    <span style="font-size: 5rem; display: block; margin-bottom: 1.5rem;">⚡</span>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <span class="tag tag-accent">Öncelikli</span>
                        <span class="tag tag-accent">Garantili</span>
                        <span class="tag tag-accent">Anlık</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Araçlı Kurye Section -->
<section class="section" id="aracli-kurye">
    <div class="container">
        <div class="responsive-stack" style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
            <div class="service-showcase">
                <div class="glass" style="padding: 3rem; position: relative; overflow: hidden;">
                    <div class="floating-orb orb-1"></div>
                    <div class="service-number">03</div>
                    <span style="font-size: 5rem; display: block; margin-bottom: 1.5rem;">🚗</span>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <span class="tag tag-green">Büyük Hacim</span>
                        <span class="tag tag-green">Hassas Eşya</span>
                        <span class="tag tag-green">Toplu Teslimat</span>
                    </div>
                </div>
            </div>
            <div>
                <div class="section-badge" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(34, 211, 238, 0.15) 100%); border-color: var(--success);">
                    <i class="fa-solid fa-truck" style="color: var(--success);"></i> <span style="color: var(--success);">Büyük Hacim</span>
                </div>
                <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem;">
                    Araçlı <span class="gradient-text">Kurye</span>
                </h2>
                <p style="color: var(--text-secondary); margin-bottom: 2rem; font-size: 1.125rem;">
                    Büyük hacimli gönderiler, hassas eşyalar ve toplu teslimatlar için özel araç filosu. 
                    Gönderi boyutuna göre en uygun araç seçilir.
                </p>
                
                <div class="service-features">
                    <div class="sf-item">
                        <div class="sf-icon" style="background: linear-gradient(135deg, #10b981 0%, #22d3ee 100%);">📦</div>
                        <div>
                            <strong>Büyük Hacim Kapasitesi</strong>
                            <p>Sedan, minivan veya kamyonet seçenekleri ile her boyutta gönderi</p>
                        </div>
                    </div>
                    <div class="sf-item">
                        <div class="sf-icon" style="background: linear-gradient(135deg, #10b981 0%, #22d3ee 100%);">🛡️</div>
                        <div>
                            <strong>Hassas Eşya Koruması</strong>
                            <p>Özel ambalaj ve dikkatli taşıma ile kırılacak eşyalarınız güvende</p>
                        </div>
                    </div>
                    <div class="sf-item">
                        <div class="sf-icon" style="background: linear-gradient(135deg, #10b981 0%, #22d3ee 100%);">🗺️</div>
                        <div>
                            <strong>Toplu Teslimat</strong>
                            <p>Birden fazla noktaya teslimat için optimize edilmiş rotalar</p>
                        </div>
                    </div>
                </div>
                
                <a href="tel:+905513567292" class="btn btn-primary" style="margin-top: 2rem;">
                    <i class="fa-solid fa-phone"></i> Araç Talep Et
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Fun Facts Section -->
<section class="funfact-section">
    <div class="container">
        <div class="funfact-wrapper">
            <div class="funfact-item">
                <div class="funfact-value">&lt;<span data-count="3">0</span>h</div>
                <div class="funfact-label">Ortalama Teslimat</div>
            </div>
            <div class="funfact-item">
                <div class="funfact-value"><span data-count="500">0</span>+</div>
                <div class="funfact-label">Aktif Kurye</div>
            </div>
            <div class="funfact-item">
                <div class="funfact-value"><span data-count="99">0</span>%</div>
                <div class="funfact-label">Başarı Oranı</div>
            </div>
            <div class="funfact-item">
                <div class="funfact-value">7/<span data-count="24">0</span></div>
                <div class="funfact-label">Aktif Hizmet</div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section">
    <div class="container">
        <div class="cta-section">
            <div class="cta-content">
                <h2>Hangi Hizmeti <span class="gradient-text">Seçeceğinizden</span> Emin Değil misiniz?</h2>
                <p>
                    Endişelenmeyin! Bizi arayın, gönderinize en uygun hizmeti birlikte belirleyelim.
                </p>
                <div class="cta-buttons">
                    <a href="tel:+905513567292" class="btn btn-accent">
                        <i class="fa-solid fa-phone"></i> 0551 356 72 92
                    </a>
                    <a href="https://wa.me/905513567292" class="btn btn-outline">
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
    .service-showcase {
        position: relative;
    }
    
    .service-number {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        font-family: 'JetBrains Mono', monospace;
        font-size: 1rem;
        color: var(--text-muted);
        background: var(--bg-glass);
        padding: 0.5rem 1rem;
        border-radius: 50px;
        border: 1px solid var(--border-glass);
    }
    
    .tag {
        display: inline-block;
        padding: 0.5rem 1rem;
        background: var(--gradient-primary);
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    
    .tag-accent {
        background: linear-gradient(135deg, rgba(34, 211, 238, 0.2) 0%, rgba(124, 58, 237, 0.2) 100%);
        border: 1px solid var(--accent);
        color: var(--accent);
    }
    
    .tag-green {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(34, 211, 238, 0.2) 100%);
        border: 1px solid var(--success);
        color: var(--success);
    }
    
    .service-features {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    
    .sf-item {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .sf-icon {
        width: 50px;
        height: 50px;
        background: var(--gradient-primary);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    
    .sf-item strong {
        display: block;
        margin-bottom: 0.25rem;
        color: var(--text-primary);
    }
    
    .sf-item p {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin: 0;
    }
    
    @media (max-width: 768px) {
        .section > .container > div {
            grid-template-columns: 1fr !important;
        }
        
        .service-showcase {
            order: -1;
        }
    }
</style>
@endpush

