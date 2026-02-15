@extends('layouts.landing')

@section('title', 'SimdiGetir - Hızlı ve Güvenilir Kurye Hizmeti')
@section('meta_description', 'Hızlı ve güvenilir kurye hizmeti. Akıllı rotalama, anlık takip, 7/24 hizmet. İstanbul\'un en hızlı kurye ağı.')
@section('meta_keywords', 'kurye istanbul, moto kurye, acil kurye, araçlı kurye, aynı gün teslimat, hızlı kurye, 7/24 kurye hizmeti, istanbul kurye hizmeti, online kurye çağır')

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "@id": "{{ url('/') }}/#organization",
    "name": "SimdiGetir Kurye",
    "alternateName": "SimdiGetir",
    "description": "Hızlı ve güvenilir kurye hizmeti. Akıllı rotalama, anlık takip, 7/24 hizmet ile İstanbul'un en hızlı kurye ağı.",
    "url": "{{ url('/') }}",
    "telephone": "+905324847292",
    "email": "webgetir@simdigetir.com",
    "address": {
        "@type": "PostalAddress",
        "streetAddress": "Yeşilce Mahallesi Aytekin Sokak No:5/2",
        "addressLocality": "Kağıthane",
        "addressRegion": "İstanbul",
        "postalCode": "34418",
        "addressCountry": "TR"
    },
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": 41.0882,
        "longitude": 29.0014
    },
    "areaServed": {
        "@type": "City",
        "name": "İstanbul",
        "sameAs": "https://tr.wikipedia.org/wiki/%C4%B0stanbul"
    },
    "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],
        "opens": "00:00",
        "closes": "23:59"
    },
    "priceRange": "₺₺",
    "image": "{{ asset('images/og-default.svg') }}",
    "sameAs": [
        "https://www.instagram.com/simdigetir",
        "https://www.facebook.com/simdigetir"
    ],
    "hasOfferCatalog": {
        "@type": "OfferCatalog",
        "name": "Kurye Hizmetleri",
        "itemListElement": [
            {
                "@type": "Offer",
                "itemOffered": {
                    "@type": "Service",
                    "name": "Motorlu Kurye",
                    "description": "Trafiği atlatarak dakikalar içinde teslimat. Akıllı rota optimizasyonu."
                }
            },
            {
                "@type": "Offer",
                "itemOffered": {
                    "@type": "Service",
                    "name": "Acil Kurye",
                    "description": "3 saat içinde garantili teslimat. Öncelikli kurye ataması."
                }
            },
            {
                "@type": "Offer",
                "itemOffered": {
                    "@type": "Service",
                    "name": "Araçlı Kurye",
                    "description": "Büyük ve ağır gönderiler için araçlı kurye hizmeti."
                }
            }
        ]
    }
}
</script>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "SimdiGetir",
    "url": "{{ url('/') }}",
    "description": "Hızlı ve güvenilir kurye hizmeti",
    "publisher": {
        "@id": "{{ url('/') }}/#organization"
    }
}
</script>
@endsection

@section('content')
<!-- Hero Section -->
<!-- Hero Section Slider -->
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
                                    7/24 Aktif Hizmet
                                </div>
                                
                                <h1 class="animate__animated animate__fadeInUp animate__delay-1s">
                                    Zamanın <span class="gradient-text">Değerli</span> Olduğu<br>
                                    Anlarda Yanınızdayız
                                </h1>
                                
                                <p class="animate__animated animate__fadeInUp animate__delay-2s">
                                    İstanbul'un en hızlı kurye ağı. Gönderinizi teslim alır, 
                                    en kısa rotadan güvenle ulaştırırız.
                                </p>
                                
                                <div class="hero-buttons animate__animated animate__fadeInUp animate__delay-3s">
                                    <a href="tel:+905324847292" class="btn btn-primary">
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
                                        <div class="ai-avatar">🚀</div>
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
                                    <img src="{{ asset('images/kuryeman.jpg') }}" alt="Kuryeman" style="width:100%; height:auto; border-radius:20px; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const swiper = new Swiper('.hero-swiper', {
            loop: true,
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            speed: 1000,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
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
<section class="section" id="hizmetler">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fa-solid fa-truck-fast"></i> Profesyonel Hizmetler
            </div>
            <h2 class="section-title">
                Kurye <span class="gradient-text">Çözümlerimiz</span>
            </h2>
            <p class="section-subtitle">
                Her gönderi için doğru çözüm. Hızlı, güvenilir, profesyonel.
            </p>
        </div>
        
        <div class="services-grid">
            <div class="service-card">
                <div class="service-card-number">01</div>
                <div class="service-card-icon">🏍️</div>
                <h3>Motorlu Kurye</h3>
                <p>Trafiği atlatarak dakikalar içinde hedefe ulaşın. Akıllı rota optimizasyonu ile en hızlı teslimat.</p>
                <ul class="service-card-features">
                    <li><i class="fa-solid fa-check"></i> Anlık trafik analizi</li>
                    <li><i class="fa-solid fa-check"></i> Akıllı rota optimizasyonu</li>
                    <li><i class="fa-solid fa-check"></i> Gerçek zamanlı takip</li>
                </ul>
                <a href="tel:+905324847292" class="btn btn-outline" style="margin-top:1rem;width:100%;justify-content:center;">
                    <i class="fa-solid fa-phone"></i> Hemen Ara
                </a>
            </div>
            
            <div class="service-card">
                <div class="service-card-number">02</div>
                <div class="service-card-icon" style="background: linear-gradient(135deg, #22d3ee 0%, #7c3aed 100%);">⚡</div>
                <h3>Acil Kurye</h3>
                <p>Saniyeler içinde en yakın kurye atanır. Öncelikli teslimat garantisi ile kritik gönderileriniz güvende.</p>
                <ul class="service-card-features">
                    <li><i class="fa-solid fa-check"></i> Anlık kurye eşleştirme</li>
                    <li><i class="fa-solid fa-check"></i> Öncelikli gönderi statüsü</li>
                    <li><i class="fa-solid fa-check"></i> SLA garantili teslimat</li>
                </ul>
                <a href="tel:+905324847292" class="btn btn-outline" style="margin-top:1rem;width:100%;justify-content:center;">
                    <i class="fa-solid fa-bolt"></i> Acil Çağır
                </a>
            </div>
            
            <div class="service-card">
                <div class="service-card-number">03</div>
                <div class="service-card-icon" style="background: linear-gradient(135deg, #10b981 0%, #22d3ee 100%);">🚗</div>
                <h3>Araçlı Kurye</h3>
                <p>Büyük hacimli gönderiler için özel araç filosu. Hassas eşya taşıma ve toplu teslimat imkanı.</p>
                <ul class="service-card-features">
                    <li><i class="fa-solid fa-check"></i> Büyük hacim kapasitesi</li>
                    <li><i class="fa-solid fa-check"></i> Hassas eşya koruması</li>
                    <li><i class="fa-solid fa-check"></i> Toplu teslimat imkanı</li>
                </ul>
                <a href="tel:+905324847292" class="btn btn-outline" style="margin-top:1rem;width:100%;justify-content:center;">
                    <i class="fa-solid fa-truck"></i> Araç Talep Et
                </a>
            </div>
        </div>
    </div>
</section>


<!-- Features Section -->
<section class="section" style="background: linear-gradient(180deg, rgba(124, 58, 237, 0.05) 0%, transparent 100%);">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fa-solid fa-sparkles"></i> Neden Bizi Tercih Etmelisiniz?
            </div>
            <h2 class="section-title">
                <span class="gradient-text">Avantajlarımız</span>
            </h2>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <span class="feature-icon">🚩</span>
                <h4>Akıllı Rotalama</h4>
                <p>En hızlı rota ile teslimat</p>
            </div>
            <div class="feature-card">
                <span class="feature-icon">📍</span>
                <h4>Canlı Takip</h4>
                <p>Gönderinizi gerçek zamanlı izleyin</p>
            </div>
            <div class="feature-card">
                <span class="feature-icon">⚡</span>
                <h4>Hızlı Eşleştirme</h4>
                <p>Saniyeler içinde en yakın kurye</p>
            </div>
            <div class="feature-card">
                <span class="feature-icon">🔒</span>
                <h4>Güvenli Teslimat</h4>
                <p>%99 başarılı teslimat oranı</p>
            </div>
            <div class="feature-card">
                <span class="feature-icon">💰</span>
                <h4>Şeffaf Fiyat</h4>
                <p>Sürpriz masraf yok, net fiyat</p>
            </div>
            <div class="feature-card">
                <span class="feature-icon">📱</span>
                <h4>Anlık Bildirim</h4>
                <p>Her adımda SMS/bildirim alın</p>
            </div>
            <div class="feature-card">
                <span class="feature-icon">🌙</span>
                <h4>7/24 Aktif</h4>
                <p>Gece gündüz hizmetinizdeyiz</p>
            </div>
            <div class="feature-card">
                <span class="feature-icon">🏢</span>
                <h4>Kurumsal Çözüm</h4>
                <p>İşletmelere özel paketler</p>
            </div>
        </div>
    </div>
</section>

<!-- Process Steps Section -->
<section class="process-section">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fa-solid fa-diagram-project"></i> Nasıl Çalışır?
            </div>
            <h2 class="section-title">
                3 Adımda <span class="gradient-text">Teslimat</span>
            </h2>
            <p class="section-subtitle">
                3 basit adımda gönderinizi en hızlı şekilde teslim ediyoruz.
            </p>
        </div>
        
        <div class="process-grid">
            <div class="process-card">
                <div class="process-number">01</div>
                <h3>Gönderi Bilgisi</h3>
                <p>Bizi arayın veya WhatsApp'tan yazın. Gönderi detaylarınıza göre en uygun hizmeti belirleyelim.</p>
            </div>
            <div class="process-card">
                <div class="process-number">02</div>
                <h3>Akıllı Eşleştirme</h3>
                <p>En yakın ve uygun kuryeyi saniyeler içinde bulur, en kısa rotayı hesaplarız.</p>
            </div>
            <div class="process-card">
                <div class="process-number">03</div>
                <h3>Hızlı Teslimat</h3>
                <p>Kuryeniz yola çıkar, siz gerçek zamanlı takip edersiniz. Her an nerede olduğunu görün.</p>
            </div>
        </div>
    </div>
</section>

<!-- Fun Facts Section -->
<section class="funfact-section">
    <div class="container">
        <div class="funfact-wrapper">
            <div class="funfact-item">
                <div class="funfact-value"><span data-count="52">0</span>K+</div>
                <div class="funfact-label">Mutlu Müşteri</div>
            </div>
            <div class="funfact-item">
                <div class="funfact-value"><span data-count="150">0</span>K+</div>
                <div class="funfact-label">Tamamlanan Teslimat</div>
            </div>
            <div class="funfact-item">
                <div class="funfact-value"><span data-count="500">0</span>+</div>
                <div class="funfact-label">Aktif Kurye</div>
            </div>
            <div class="funfact-item">
                <div class="funfact-value"><span data-count="99">0</span>%</div>
                <div class="funfact-label">Memnuniyet Oranı</div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonial Section -->
<section class="testimonial-section">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fa-solid fa-comments"></i> Müşteri Yorumları
            </div>
            <h2 class="section-title">
                Müşterilerimiz <span class="gradient-text">Ne Diyor?</span>
            </h2>
        </div>
        
        <div class="testimonial-slider" id="testimonial-slider">
            <div class="testimonial-track" id="testimonial-track">
                <div class="testimonial-slide">
                    <div class="testimonial-card">
                        <div class="testimonial-avatar" style="background:linear-gradient(135deg,#7c3aed,#a855f7);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;color:white;">AY</div>
                        <div class="testimonial-content">
                            <div class="testimonial-stars">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                            </div>
                            <p class="testimonial-text">
                                E-ticaret sitemizin tüm kargo ihtiyacını SimdiGetir ile karşılıyoruz. Akıllı rota optimizasyonu sayesinde teslimat sürelerimiz %40 azaldı. Müşteri memnuniyetimiz rekor seviyede!
                            </p>
                            <div class="testimonial-author">
                                <h4>Ahmet Yılmaz</h4>
                                <span>E-Ticaret Mağaza Sahibi</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-slide">
                    <div class="testimonial-card">
                        <div class="testimonial-avatar" style="background:linear-gradient(135deg,#22d3ee,#06b6d4);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;color:white;">EK</div>
                        <div class="testimonial-content">
                            <div class="testimonial-stars">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                            </div>
                            <p class="testimonial-text">
                                Acil ilaç ve tıbbi malzeme gönderimlerinde SimdiGetir vazgeçilmezimiz oldu. 7/24 hizmet verdikleri için gece yarısı bile güvenle gönderi yapabiliyoruz.
                            </p>
                            <div class="testimonial-author">
                                <h4>Dr. Elif Kaya</h4>
                                <span>Klinik Yöneticisi</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-slide">
                    <div class="testimonial-card">
                        <div class="testimonial-avatar" style="background:linear-gradient(135deg,#db2777,#ec4899);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;color:white;">MD</div>
                        <div class="testimonial-content">
                            <div class="testimonial-stars">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                            </div>
                            <p class="testimonial-text">
                                Ofisler arası evrak ve paket gönderimlerinde artık sadece SimdiGetir kullanıyoruz. Anlık takip özelliği ve profesyonel kurye kadrosu ile iş süreçlerimiz çok hızlandı.
                            </p>
                            <div class="testimonial-author">
                                <h4>Mehmet Demir</h4>
                                <span>Şirket Müdürü</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="testimonial-controls">
                <button class="testimonial-btn" id="testimonial-prev">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <button class="testimonial-btn" id="testimonial-next">
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section">
    <div class="container">
        <div class="cta-section">
            <div class="cta-content">
                <h2>Gönderinizi <span class="gradient-text">Bize Emanet Edin</span></h2>
                <p>
                    Zamanın paradan daha değerli olduğu anlarda yanınızdayız. 
                    Hemen arayın, en uygun çözümü birlikte bulalım.
                </p>
                <div class="cta-buttons">
                    <a href="tel:+905324847292" class="btn btn-accent">
                        <i class="fa-solid fa-phone"></i> 0532 484 72 92
                    </a>
                    <a href="https://wa.me/905324847292" class="btn btn-outline">
                        <i class="fa-brands fa-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quote Form Section -->
<section class="section" id="teklif-al" style="background: linear-gradient(180deg, transparent 0%, rgba(124, 58, 237, 0.05) 100%);">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
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
                    <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 1rem; color: var(--text-secondary);">
                        <i class="fa-solid fa-check-circle" style="color: var(--accent); font-size: 1.25rem;"></i> Öncelikli kurye ataması
                    </li>
                    <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 1rem; color: var(--text-secondary);">
                        <i class="fa-solid fa-check-circle" style="color: var(--accent); font-size: 1.25rem;"></i> Toplu gönderi indirimi
                    </li>
                    <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 1rem; color: var(--text-secondary);">
                        <i class="fa-solid fa-check-circle" style="color: var(--accent); font-size: 1.25rem;"></i> API entegrasyonu
                    </li>
                    <li style="display: flex; align-items: center; gap: 1rem; color: var(--text-secondary);">
                        <i class="fa-solid fa-check-circle" style="color: var(--accent); font-size: 1.25rem;"></i> Özel müşteri temsilcisi
                    </li>
                </ul>
            </div>
            
            <div class="glass" style="padding: 2.5rem;">
                <h3 style="margin-bottom: 0.5rem; font-size: 1.5rem;">Teklif İsteyin</h3>
                <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 0.9rem;">Talebiniz hızla değerlendirilecek</p>
                
                <form id="corporate-form" onsubmit="submitLeadForm(event, 'corporate_quote')">
                    <div class="form-group">
                        <label>Firma Adı *</label>
                        <input type="text" name="company_name" required placeholder="Şirket adınız">
                    </div>
                    <div class="form-group">
                        <label>Yetkili Adı *</label>
                        <input type="text" name="name" required placeholder="Ad Soyad">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
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

<!-- Blog/News Section -->
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
            <div class="blog-card">
                <div class="blog-card-image" style="background: linear-gradient(135deg, #7c3aed 0%, #22d3ee 100%);">
                    <i class="fa-solid fa-route" style="color: rgba(255,255,255,0.8);"></i>
                </div>
                <div class="blog-card-content">
                    <div class="blog-card-meta">
                        <i class="fa-solid fa-calendar-days"></i> 10 Şubat 2026
                    </div>
                    <h3><a href="/hizmetler">Akıllı Rota ile Daha Hızlı Teslimat</a></h3>
                    <p style="color: var(--text-secondary); margin-bottom: 1.25rem; font-size: 0.95rem;">
                        Akıllı rota optimizasyonu sayesinde teslimat sürelerimizi nasıl kısaltıyoruz? Hizmetlerimizi keşfedin.
                    </p>
                    <a href="/hizmetler" class="read-more">
                        Hizmetleri İncele <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="blog-card">
                <div class="blog-card-image" style="background: linear-gradient(135deg, #ec4899 0%, #7c3aed 100%);">
                    <i class="fa-solid fa-question-circle" style="color: rgba(255,255,255,0.8);"></i>
                </div>
                <div class="blog-card-content">
                    <div class="blog-card-meta">
                        <i class="fa-solid fa-calendar-days"></i> 5 Şubat 2026
                    </div>
                    <h3><a href="/sss">Sıkça Sorulan Sorular</a></h3>
                    <p style="color: var(--text-secondary); margin-bottom: 1.25rem; font-size: 0.95rem;">
                        Kurye hizmetlerimiz, fiyatlandırma, teslimat süreleri ve daha fazlası hakkında merak edilenler.
                    </p>
                    <a href="/sss" class="read-more">
                        Sorulara Bak <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Courier CTA Section -->
<section class="section">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
            <div class="glass" style="padding: 3rem; text-align: center; background: linear-gradient(135deg, rgba(124, 58, 237, 0.1) 0%, rgba(34, 211, 238, 0.05) 100%);">
                <span style="font-size: 5rem; display: block; margin-bottom: 1.5rem;">🏍️</span>
                <h3 style="margin-bottom: 1rem; font-size: 1.75rem;">Kurye Ailemize Katıl</h3>
                <p style="color: var(--text-secondary); margin-bottom: 2rem;">
                    Esnek çalışma saatleri, hızlı ödeme!
                </p>
                <a href="/kurye-basvuru" class="btn btn-primary">
                    <i class="fa-solid fa-user-plus"></i> Hemen Başvur
                </a>
            </div>
            <div>
                <h2 style="font-size: 2rem; margin-bottom: 1.5rem;">
                    <span class="gradient-text">Kurye Ol</span>, Özgürce Kazan
                </h2>
                <ul style="list-style: none; color: var(--text-secondary);">
                    <li style="margin-bottom: 1.25rem; display: flex; align-items: center; gap: 1rem;">
                        <i class="fa-solid fa-wallet" style="color: var(--accent); font-size: 1.5rem;"></i> 
                        <div>
                            <strong style="color: var(--text-primary);">Esnek Çalışma</strong><br>
                            <span style="font-size: 0.9rem;">İstediğin saatlerde çalış</span>
                        </div>
                    </li>
                    <li style="margin-bottom: 1.25rem; display: flex; align-items: center; gap: 1rem;">
                        <i class="fa-solid fa-mobile-screen" style="color: var(--accent); font-size: 1.5rem;"></i>
                        <div>
                            <strong style="color: var(--text-primary);">Akıllı Navigasyon</strong><br>
                            <span style="font-size: 0.9rem;">Akıllı rota önerileri</span>
                        </div>
                    </li>
                    <li style="margin-bottom: 1.25rem; display: flex; align-items: center; gap: 1rem;">
                        <i class="fa-solid fa-bolt" style="color: var(--accent); font-size: 1.5rem;"></i>
                        <div>
                            <strong style="color: var(--text-primary);">Hızlı Ödeme</strong><br>
                            <span style="font-size: 0.9rem;">Haftalık ödemeler</span>
                        </div>
                    </li>
                    <li style="display: flex; align-items: center; gap: 1rem;">
                        <i class="fa-solid fa-bullseye" style="color: var(--accent); font-size: 1.5rem;"></i>
                        <div>
                            <strong style="color: var(--text-primary);">Akıllı Görev Dağılımı</strong><br>
                            <span style="font-size: 0.9rem;">Yakınındaki siparişler öncelikli</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
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
            const response = await fetch('/api/leads', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            });
            
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
