@extends('layouts.landing')

@section('title', $landingContent['meta_title'] ?? 'SimdiGetir - Hizli ve Guvenilir Kurye Hizmeti')
@section('meta_description', $landingContent['meta_description'] ?? 'Hizli ve guvenilir kurye hizmeti. Akilli rotalama, anlik takip, 7/24 hizmet.')
@section('meta_keywords', $landingContent['meta_keywords'] ?? 'kurye, moto kurye, acil kurye, istanbul kurye')

@section('robots', $landingContent['robots'] ?? 'index, follow')
@section('canonical_url', $landingContent['canonical_url'] ?? url()->current())
@section('og_title', $landingContent['og_title'] ?? ($landingContent['meta_title'] ?? 'SimdiGetir'))
@section('og_description', $landingContent['og_description'] ?? ($landingContent['meta_description'] ?? 'Hizli ve guvenilir kurye hizmeti'))
@section('og_image', $landingContent['og_image'] ?? asset('images/og-default.jpg'))

@section('structured_data')
@php
    $structuredDataBlocks = [];

    if (is_array($landingContent['structured_data_blocks'] ?? null)) {
        foreach ($landingContent['structured_data_blocks'] as $block) {
            if (is_array($block) && ! empty($block)) {
                $structuredDataBlocks[] = $block;
            }
        }
    } elseif (is_array($landingContent['structured_data'] ?? null)) {
        $structuredDataBlocks[] = $landingContent['structured_data'];
    }

    if (empty($structuredDataBlocks)) {
        $structuredDataBlocks = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'LocalBusiness',
                '@id' => url('/').'#organization',
                'name' => 'SimdiGetir Kurye',
                'alternateName' => 'SimdiGetir',
                'description' => (string) ($landingContent['meta_description'] ?? 'Hizli ve guvenilir kurye hizmeti. Akilli rotalama, anlik takip, 7/24 hizmet.'),
                'url' => url('/'),
                'telephone' => '+905513567292',
                'email' => 'webgetir@simdigetir.com',
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => 'SimdiGetir',
                'url' => url('/'),
                'description' => (string) ($landingContent['meta_description'] ?? 'Hizli ve guvenilir kurye hizmeti'),
                'publisher' => [
                    '@id' => url('/').'#organization',
                ],
            ],
        ];
    }
@endphp
@foreach($structuredDataBlocks as $schemaBlock)
<script type="application/ld+json">
{!! json_encode($schemaBlock, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE) !!}
</script>
@endforeach
@endsection

@section('content')
@php
    $heroSlide2Fallback = asset('images/kuryeman.jpg');
    $heroSlide2Resolved = \App\Support\ResponsiveImage::resolveUrl($landingContent['hero_slide2_image_url'] ?? null) ?: $heroSlide2Fallback;
@endphp
<!-- Hero Section -->
<!-- Hero Section Slider -->
@if(data_get($landingContent, 'sections_visible.hero', true))
<section class="hero-slider-section" style="position: relative; overflow: hidden;">
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper">
            <!-- Slide 1: Standard Hero -->
            <div class="swiper-slide">
                <section class="hero">
                    <div class="container">
                        <div class="hero-content">
                            <div>
                                <div class="hero-badge animate__animated animate__fadeInUp">
                                    <span class="pulse"></span>
                                    {{ $landingContent['hero_badge_text'] ?? '7/24 Aktif Hizmet' }}
                                </div>
                                
                                <h1 class="animate__animated animate__fadeInUp animate__delay-1s">
                                    {!! $landingContent['hero_title_html'] ?? "Zamanın <span class='gradient-text'>Değerli</span> Olduğu<br>Anlarda Yanınızdayız" !!}
                                </h1>
                                
                                <p class="animate__animated animate__fadeInUp animate__delay-2s">
                                    {{ $landingContent['hero_description_text'] ?? "İstanbul'un en hızlı kurye ağı. Gönderinizi teslim alır, en kısa rotadan güvenle ulaştırırız." }}
                                </p>
                                
                                <div class="hero-buttons animate__animated animate__fadeInUp animate__delay-3s">
                                    <a href="tel:+905513567292" class="btn btn-primary">
                                        <i class="fa-solid fa-phone"></i> Kurye Çağır
                                    </a>
                                    <a href="#hizmetler" class="btn btn-outline">
                                        <i class="fa-solid fa-rocket"></i> Hizmetleri Keşfet
                                    </a>
                                </div>
                                
                                <div class="hero-stats animate__animated animate__fadeInUp animate__delay-4s">
                                    <div class="hero-stat">
                                        <div class="hero-stat-value"><span data-count="724">0</span></div>
                                        <div class="hero-stat-label">7/24 Aktif</div>
                                    </div>
                                    <div class="hero-stat">
                                        <div class="hero-stat-value">&lt;<span data-count="3">0</span>h</div>
                                        <div class="hero-stat-label">Teslimat Süresi</div>
                                    </div>
                                    <div class="hero-stat">
                                        <div class="hero-stat-value"><span data-count="99">0</span>%</div>
                                        <div class="hero-stat-label">Başarı Oranı</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="hero-visual animate__animated animate__fadeInRight animate__delay-2s">
                                <div class="hero-card">
                                    <div class="floating-orb orb-1"></div>
                                    <div class="floating-orb orb-2"></div>
                                    
                                    <div class="hero-card-header">
                                        <div class="ai-avatar">⚡</div>
                                        <div class="ai-status">
                                            <span class="ai-status-name">SimdiGetir Kurye</span>
                                            <span class="ai-status-text">
                                                Gönderi hazırlanıyor
                                                <span class="typing-dots">
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="hero-card-content">
                                        <p>
                                            <span class="highlight">[✓]</span> Rota optimizasyonu tamamlandı<br>
                                            <span class="highlight">[✓]</span> En yakın kurye aranıyor...<br>
                                            <span class="success">✓</span> <span class="highlight">Kurye #247</span> 2.3 km uzaklıkta<br>
                                            <span class="success">✓</span> Tahmini teslimat: <span class="highlight">45 dakika</span><br>
                                            <span class="success">✓</span> Gerçek zamanlı takip aktif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            
            <!-- Slide 2: Kuryeman Visual -->
            <div class="swiper-slide">
                <section class="hero">
                    <div class="container">
                        <div class="hero-content">
                            <div>
                                <div class="hero-badge animate__animated animate__fadeInUp">
                                    <span class="pulse" style="background: #22d3ee;"></span>
                                    Yeni Nesil Teslimat
                                </div>
                                
                                <h1 class="animate__animated animate__fadeInUp animate__delay-1s">
                                    Kuryeman: <span class="gradient-text" style="background: linear-gradient(135deg, #FF6B35 0%, #22d3ee 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Hızlı Teslimatın</span><br>
                                    Süper Gücü
                                </h1>
                                
                                <p class="animate__animated animate__fadeInUp animate__delay-2s">
                                    Size özel kahraman kuryeniz yolda. 
                                    Işık hızında, güvenli ve temassız teslimat deneyimi.
                                </p>
                                
                                <div class="hero-buttons animate__animated animate__fadeInUp animate__delay-3s">
                                    <a href="/kurye-basvuru" class="btn btn-primary" style="background: linear-gradient(135deg, #22d3ee 0%, #06b6d4 100%); border: none;">
                                        <i class="fa-solid fa-bolt"></i> Hemen Başvur
                                    </a>
                                </div>
                            </div>
                            
                            <div class="hero-visual animate__animated animate__fadeInRight animate__delay-2s">
                                <div class="hero-card" style="padding:0; overflow:hidden; border:none; background:transparent; box-shadow:none;">
                                    <img
                                        src="{{ $heroSlide2Resolved }}"
                                        alt="{{ $landingContent['hero_slide2_image_alt'] ?? 'Kuryeman' }}"
                                        srcset="{{ $landingContent['hero_slide2_image_srcset'] ?? \App\Support\ResponsiveImage::buildSrcset($heroSlide2Resolved) }}"
                                        sizes="{{ \App\Support\ResponsiveImage::normalizeSizes($landingContent['hero_slide2_image_sizes'] ?? null, '(max-width: 768px) 100vw, 50vw') }}"
                                        width="853"
                                        height="361"
                                        loading="lazy"
                                        decoding="async"
                                        onerror="this.onerror=null;this.src='{{ $heroSlide2Fallback }}';"
                                        style="width:100%; height:auto; border-radius:20px; box-shadow: 0 20px 50px rgba(0,0,0,0.5);"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

        </div>
        
        <!-- Navigation & Pagination -->
        <div class="swiper-pagination"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
</section>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const heroSwiperEl = document.querySelector('.hero-swiper');
        if (!heroSwiperEl) {
            return;
        }

        const shouldReduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const swiper = new Swiper('.hero-swiper', {
            loop: true,
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            speed: 1000,
            autoplay: shouldReduceMotion ? false : {
                delay: 5000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            autoHeight: true, // Enable auto height
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                0: {
                    allowTouchMove: true
                },
                1024: {
                    allowTouchMove: true
                }
            }
        });

        document.addEventListener('visibilitychange', function() {
            if (!swiper.autoplay) {
                return;
            }
            if (document.hidden) {
                swiper.autoplay.stop();
            } else {
                swiper.autoplay.start();
            }
        });
    });
</script>
<style>
    .hero-slider-section {
        width: 100%;
        min-height: 100vh; /* Force minimum height */
        position: relative;
    }
    .hero-swiper {
        width: 100%;
        height: 100%;
        min-height: 100vh;
    }
    .swiper-slide {
        height: auto;
    }
    .swiper-slide .hero {
        min-height: 100vh; /* Ensure hero takes full height */
        display: flex;
        align-items: center;
        padding-top: 150px; /* Account for header */
        padding-bottom: 80px;
    }
    .swiper-pagination-bullet {
        background: rgba(255, 255, 255, 0.3);
        opacity: 1;
    }
    .swiper-pagination-bullet-active {
        background: var(--primary);
        width: 20px;
        border-radius: 4px;
        transition: width 0.3s;
    }
    .swiper-button-next, .swiper-button-prev {
        color: rgba(255, 255, 255, 0.3);
        transition: color 0.3s;
        z-index: 50; /* Ensure on top */
    }
    .swiper-button-next:hover, .swiper-button-prev:hover {
        color: var(--primary);
    }
    /* Glass Card Style Support */
    .hero-card-glass {
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 2rem;
        border-radius: 1.5rem;
        max-width: 600px;
    }

    .swiper-button-next::after, .swiper-button-prev::after {
        font-size: 1.5rem;
    }
    @media (max-width: 768px) {
        .swiper-button-next,
        .swiper-button-prev {
            display: none;
        }
    }
</style>
@endpush

<!-- Marquee Section - AIForge Style -->
<div class="marquee-section">
    <div class="marquee-wrapper">
        <div class="marquee-group">
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Hızlı Teslimat</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Güvenilir Teslimat</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> 7/24 Aktif Sistem</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Akıllı Rotalama</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Anlık Takip</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Güvenli Gönderi</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Motorlu Kurye</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Araçlı Kurye</div>
        </div>
        <div class="marquee-group">
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Hızlı Teslimat</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Güvenilir Teslimat</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> 7/24 Aktif Sistem</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Akıllı Rotalama</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Anlık Takip</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Güvenli Gönderi</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Motorlu Kurye</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Araçlı Kurye</div>
        </div>
        <div class="marquee-group">
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Hızlı Teslimat</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Güvenilir Teslimat</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> 7/24 Aktif Sistem</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Akıllı Rotalama</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Anlık Takip</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Güvenli Gönderi</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Motorlu Kurye</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Araçlı Kurye</div>
        </div>
    </div>
</div>

<!-- Services Section -->
@if(data_get($landingContent, 'sections_visible.services', true))
<section class="section" id="hizmetler">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fa-solid fa-truck-fast"></i> {{ $landingContent['services_badge_text'] ?? 'Profesyonel Hizmetler' }}
            </div>
            <h2 class="section-title">
                {!! $landingContent['services_title_html'] ?? "Kurye <span class='gradient-text'>Çözümlerimiz</span>" !!}
            </h2>
            <p class="section-subtitle">
                {{ $landingContent['services_subtitle_text'] ?? 'Her gönderi için doğru çözüm. Hızlı, güvenilir, profesyonel.' }}
            </p>
        </div>
        
        <div class="services-grid">
            @foreach(($landingContent['service_cards'] ?? []) as $serviceCard)
                <div class="service-card">
                    <div class="service-card-number">{{ $serviceCard['number'] ?? '00' }}</div>
                    @if(!empty($serviceCard['image_url']))
                        <img
                            src="{{ \App\Support\ResponsiveImage::resolveUrl($serviceCard['image_url']) }}"
                            alt="{{ $serviceCard['image_alt'] ?? ($serviceCard['title'] ?? 'Kurye Hizmeti') }}"
                            srcset="{{ $serviceCard['image_srcset'] ?? \App\Support\ResponsiveImage::buildSrcset($serviceCard['image_url']) }}"
                            sizes="{{ \App\Support\ResponsiveImage::normalizeSizes($serviceCard['image_sizes'] ?? null) }}"
                            loading="lazy"
                            decoding="async"
                            style="width:100%; aspect-ratio: 16/9; object-fit:cover; border-radius:16px; margin-bottom:1rem;"
                        >
                    @else
                        <div class="service-card-icon" @if(!empty($serviceCard['icon_style'])) style="{{ $serviceCard['icon_style'] }}" @endif>{{ $serviceCard['icon_text'] ?? '🚚' }}</div>
                    @endif
                    <h3>{{ $serviceCard['title'] ?? 'Kurye Hizmeti' }}</h3>
                    <p>{{ $serviceCard['description'] ?? '' }}</p>
                    <ul class="service-card-features">
                        @foreach(($serviceCard['features'] ?? []) as $feature)
                            <li><i class="fa-solid fa-check"></i> {{ $feature }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ $serviceCard['button_href'] ?? 'tel:+905513567292' }}" class="btn btn-outline" style="margin-top:1rem;width:100%;justify-content:center;">
                        <i class="fa-solid {{ $serviceCard['button_icon'] ?? 'fa-phone' }}"></i> {{ $serviceCard['button_label'] ?? 'Hemen Ara' }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif


@php($homeSectionViews = \Modules\Landing\Support\SectionViewRegistry::home())
@include($homeSectionViews['features'], ['landingContent' => $landingContent])
@include($homeSectionViews['process'], ['landingContent' => $landingContent])
@include($homeSectionViews['stats'], ['landingContent' => $landingContent])
@include($homeSectionViews['testimonials'], ['landingContent' => $landingContent])
@include($homeSectionViews['main_cta'], ['landingContent' => $landingContent])

<!-- Quote Form Section -->
@if(data_get($landingContent, 'sections_visible.corporate_cta', true))
<section class="section" id="teklif-al" style="background: linear-gradient(180deg, transparent 0%, rgba(124, 58, 237, 0.05) 100%);">
    <div class="container">
        <div class="responsive-stack" style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
            <div>
                <div class="section-badge">
                    <i class="fa-solid fa-building"></i> Kurumsal
                </div>
                <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem;">
                    İşletmeniz İçin<br>
                    <span class="gradient-text">Özel Fiyat Teklifi</span>
                </h2>
                <p style="color: var(--text-secondary); margin-bottom: 2rem; font-size: 1.125rem;">
                    E-ticaret siteniz veya işletmeniz için kurye çözümlerimizi keşfedin. 
                    Size özel fiyat ve avantajlar sunalım.
                </p>
                
                <ul style="list-style: none;">
                    @foreach(($landingContent['corporate_cta_benefits'] ?? []) as $benefit)
                        <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 1rem; color: var(--text-secondary);">
                            <i class="fa-solid {{ $benefit['icon_class'] ?? 'fa-check-circle' }}" style="color: var(--accent); font-size: 1.25rem;"></i> {{ $benefit['text'] ?? '' }}
                        </li>
                    @endforeach
                </ul>
            </div>
            
            <div class="glass" style="padding: 2.5rem;">
                <h3 style="margin-bottom: 0.5rem; font-size: 1.5rem;">{{ $landingContent['corporate_cta_form_title_text'] ?? 'Teklif İsteyin' }}</h3>
                <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 0.9rem;">{{ $landingContent['corporate_cta_form_subtitle_text'] ?? 'Talebiniz hızla değerlendirilecek' }}</p>
                
                <form id="corporate-form" onsubmit="submitLeadForm(event, 'corporate_quote')">
                    <div class="form-group">
                        <label>Firma Adı *</label>
                        <input type="text" name="company_name" required placeholder="Şirket adınız">
                    </div>
                    <div class="form-group">
                        <label>Yetkili Adı *</label>
                        <input type="text" name="name" required placeholder="Ad Soyad">
                    </div>
                    <div class="responsive-stack" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Telefon *</label>
                            <input type="tel" name="phone" required placeholder="05XX XXX XX XX" pattern="0[0-9]{10}" title="Lütfen 05XX XXX XX XX formatında girin">
                        </div>
                        <div class="form-group">
                            <label>E-posta</label>
                            <input type="email" name="email" placeholder="ornek@sirket.com">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Aylık Tahmini Gönderi</label>
                        <select name="message">
                            <option value="">Seçiniz</option>
                            <option value="1-50 gönderi">1-50 gönderi</option>
                            <option value="50-200 gönderi">50-200 gönderi</option>
                            <option value="200-500 gönderi">200-500 gönderi</option>
                            <option value="500+ gönderi">500+ gönderi</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;" id="corporate-submit">
                        <i class="fa-solid fa-paper-plane"></i> Teklif İste
                    </button>
                </form>
                
                <div id="corporate-success" style="display: none;" class="alert alert-success">
                    <i class="fa-solid fa-check-circle"></i> Talebiniz alındı! En kısa sürede iletişime geçeceğiz.
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Blog/News Section -->
@if(data_get($landingContent, 'sections_visible.faq', true))
<section class="blog-section" style="background: linear-gradient(180deg, transparent 0%, rgba(124, 58, 237, 0.05) 100%);">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fa-solid fa-newspaper"></i> Blog & Haberler
            </div>
            <h2 class="section-title">
                <span class="gradient-text">Güncel</span> Yazılarımız
            </h2>
            <p class="section-subtitle">
                Kurye sektörü ve lojistik dünyasından son gelişmeler.
            </p>
        </div>
        
        <div class="blog-grid">
            @foreach(($landingContent['faq_cards'] ?? []) as $faqCard)
                <div class="blog-card">
                    <div class="blog-card-image" @if(!empty($faqCard['image_style'])) style="{{ $faqCard['image_style'] }}" @endif>
                        @if(!empty($faqCard['image_url']))
                            <img
                                src="{{ \App\Support\ResponsiveImage::resolveUrl($faqCard['image_url']) }}"
                                alt="{{ $faqCard['image_alt'] ?? ($faqCard['title'] ?? 'Bilgi Kartı') }}"
                                srcset="{{ $faqCard['image_srcset'] ?? \App\Support\ResponsiveImage::buildSrcset($faqCard['image_url']) }}"
                                sizes="{{ \App\Support\ResponsiveImage::normalizeSizes($faqCard['image_sizes'] ?? null, '(max-width: 768px) 100vw, 50vw') }}"
                                loading="lazy"
                                decoding="async"
                                style="width:100%; height:100%; object-fit:cover;"
                            >
                        @else
                            <i class="fa-solid {{ $faqCard['icon_class'] ?? 'fa-newspaper' }}" style="color: rgba(255,255,255,0.8);"></i>
                        @endif
                    </div>
                    <div class="blog-card-content">
                        <div class="blog-card-meta">
                            <i class="fa-solid fa-calendar-days"></i> {{ $faqCard['date_label'] ?? 'Güncel' }}
                        </div>
                        <h3><a href="{{ $faqCard['link'] ?? '/sss' }}">{{ $faqCard['title'] ?? 'Bilgi Kartı' }}</a></h3>
                        <p style="color: var(--text-secondary); margin-bottom: 1.25rem; font-size: 0.95rem;">
                            {{ $faqCard['description'] ?? '' }}
                        </p>
                        <a href="{{ $faqCard['link'] ?? '/sss' }}" class="read-more">
                            {{ $faqCard['link_label'] ?? 'Detayı Gör' }} <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Courier CTA Section -->
@if(data_get($landingContent, 'sections_visible.courier_cta', true))
<section class="section">
    <div class="container">
        <div class="responsive-stack" style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
            <div class="glass" style="padding: 3rem; text-align: center; background: linear-gradient(135deg, rgba(124, 58, 237, 0.1) 0%, rgba(34, 211, 238, 0.05) 100%);">
                <span style="font-size: 5rem; display: block; margin-bottom: 1.5rem;">🏍️</span>
                <h3 style="margin-bottom: 1rem; font-size: 1.75rem;">{{ $landingContent['courier_cta_card_title_text'] ?? 'Kurye Ailemize Katıl' }}</h3>
                <p style="color: var(--text-secondary); margin-bottom: 2rem;">
                    {{ $landingContent['courier_cta_card_description_text'] ?? 'Esnek çalışma saatleri, hızlı ödeme!' }}
                </p>
                <a href="/kurye-basvuru" class="btn btn-primary">
                    <i class="fa-solid fa-user-plus"></i> Hemen Başvur
                </a>
            </div>
            <div>
                <h2 style="font-size: 2rem; margin-bottom: 1.5rem;">
                    {!! $landingContent['courier_cta_side_title_html'] ?? "<span class='gradient-text'>Kurye Ol</span>, Özgürce Kazan" !!}
                </h2>
                <ul style="list-style: none; color: var(--text-secondary);">
                    @foreach(($landingContent['courier_cta_features'] ?? []) as $feature)
                        <li style="margin-bottom: 1.25rem; display: flex; align-items: center; gap: 1rem;">
                            <i class="fa-solid {{ $feature['icon_class'] ?? 'fa-circle-check' }}" style="color: var(--accent); font-size: 1.5rem;"></i>
                            <div>
                                <strong style="color: var(--text-primary);">{{ $feature['title'] ?? '' }}</strong><br>
                                <span style="font-size: 0.9rem;">{{ $feature['subtitle'] ?? '' }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
@endif
@endsection

@push('scripts')
<script>
    async function submitLeadForm(event, type) {
        event.preventDefault();
        
        const form = event.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const successDiv = document.getElementById('corporate-success');
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="typing-dots"><span></span><span></span><span></span></span> Gönderiliyor...';
        
        const formData = new FormData(form);
        const data = {
            type: type,
            name: formData.get('name'),
            company_name: formData.get('company_name'),
            phone: formData.get('phone'),
            email: formData.get('email'),
            message: formData.get('message'),
            page_url: window.location.href,
            referrer: document.referrer,
            utm_source: new URLSearchParams(window.location.search).get('utm_source'),
            utm_medium: new URLSearchParams(window.location.search).get('utm_medium'),
            utm_campaign: new URLSearchParams(window.location.search).get('utm_campaign'),
        };
        
        try {
            let response = await fetch('/api/forms/corporate-quote/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            });

            if (response.status === 404) {
                response = await fetch('/api/leads', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(data),
                });
            }
            
            const result = await response.json();
            
            if (result.success) {
                form.style.display = 'none';
                successDiv.style.display = 'block';
                trackEvent('lead_submit', { lead_type: type });
            } else {
                alert(result.message || 'Bir hata oluştu. Lütfen tekrar deneyin.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Teklif İste';
            }
        } catch (error) {
            alert('Bağlantı hatası. Lütfen tekrar deneyin.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Teklif İste';
        }
    }
</script>

<script>
    // Testimonial Slider
    (function() {
        const track = document.getElementById('testimonial-track');
        const prevBtn = document.getElementById('testimonial-prev');
        const nextBtn = document.getElementById('testimonial-next');
        let currentSlide = 0;
        const totalSlides = track ? track.children.length : 0;
        
        function updateSlider() {
            if (track) {
                track.style.transform = `translateX(-${currentSlide * 100}%)`;
            }
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateSlider();
            });
        }
        
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                updateSlider();
            });
        }
        
        // Auto-play
        setInterval(() => {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateSlider();
        }, 5000);
    })();
</script>
@endpush

@push('styles')
<style>
    @media (max-width: 768px) {
        #teklif-al > .container > div,
        .section > .container > div:last-child {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endpush


