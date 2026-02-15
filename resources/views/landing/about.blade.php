@extends('layouts.landing')

@section('title', 'Hakkımızda - SimdiGetir Hızlı ve Güvenilir Kurye')
@section('meta_description', 'SimdiGetir - 7/24 güvenilir ve hızlı teslimat ile İstanbul\'un lider kurye şirketi.')
@section('meta_keywords', 'simdigetir hakkında, kurye şirketi istanbul, güvenilir kurye firması, profesyonel kurye şirketi, moto kurye firması istanbul')

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "SimdiGetir Kurye",
    "alternateName": "SimdiGetir",
    "url": "{{ url('/') }}",
    "description": "7/24 güvenilir ve hızlı teslimat ile İstanbul'un lider kurye şirketi.",
    "telephone": "+905324847292",
    "email": "webgetir@simdigetir.com",
    "foundingDate": "2020",
    "address": {
        "@type": "PostalAddress",
        "streetAddress": "Yeşilce Mahallesi Aytekin Sokak No:5/2",
        "addressLocality": "Kağıthane",
        "addressRegion": "İstanbul",
        "postalCode": "34418",
        "addressCountry": "TR"
    },
    "areaServed": {
        "@type": "City",
        "name": "İstanbul"
    },
    "knowsAbout": ["Kurye Hizmeti", "Moto Kurye", "Acil Teslimat", "Akıllı Lojistik"]
}
</script>
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero" style="min-height: auto; padding: 10rem 0 4rem;">
    <div class="container" style="text-align: center;">
        <div class="hero-badge animate__animated animate__fadeInUp">
            <span class="pulse"></span>
            Teknoloji & İnovasyon
        </div>
        <h1 class="animate__animated animate__fadeInUp animate__delay-1s" style="font-size: 3rem;">
            Kuryenin <span class="gradient-text">Geleceğini</span> İnşa Ediyoruz
        </h1>
        <p class="animate__animated animate__fadeInUp animate__delay-2s" style="max-width: 650px; margin: 0 auto;">
            İstanbul'da hızlı ve güvenilir teslimat. 
            Her gönderide daha hızlı, daha güvenli.
        </p>
        <div class="animate__animated animate__fadeInUp animate__delay-3s" style="margin-top: 3rem;">
            <img src="{{ asset('images/hero-about.svg') }}" alt="SimdiGetir Ekibi" style="max-width: 600px; width: 100%; border-radius: 20px;">
        </div>
    </div>
</section>

<!-- About Section -->
<section class="section">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
            <div>
                <div class="section-badge">
                    <i class="fa-solid fa-eye"></i> Vizyonumuz
                </div>
                <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem;">
                    Hız ve Güvenin <span class="gradient-text">Adresi</span>
                </h2>
                <p style="color: var(--text-secondary); margin-bottom: 1.5rem; font-size: 1.1rem; line-height: 1.8;">
                    <strong style="color: var(--text-primary);">SimdiGetir</strong>, hızlı, güvenilir ve profesyonel kurye hizmetleri sunan 
                    İstanbul'un öncü kurye şirketidir. 2020 yılında kurulan şirketimiz, 
                    geleneksel kurye anlayışını kökten değiştirmeyi misyon edinmiştir.
                </p>
                <p style="color: var(--text-secondary); margin-bottom: 2rem; line-height: 1.8;">
                    Akıllı rota optimizasyonu, anlık kurye eşleştirme ve gerçek zamanlı takip sistemlerimiz ile 
                    müşterilerimize benzersiz bir teslimat deneyimi sunuyoruz.
                </p>
                
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                    <div class="about-stat">
                        <div class="about-stat-value"><span data-count="2020">0</span></div>
                        <div class="about-stat-label">Kuruluş Yılı</div>
                    </div>
                    <div class="about-stat">
                        <div class="about-stat-value"><span data-count="500">0</span>+</div>
                        <div class="about-stat-label">Aktif Kurye</div>
                    </div>
                </div>
            </div>
            <div>
                <div class="glass about-visual" style="padding: 2rem; position: relative;">
                    <div class="floating-orb orb-1"></div>
                    <div class="about-visual-header">
                        <div class="ai-avatar">🤖</div>
                        <div>
                            <strong>SimdiGetir Kurye Sistemi</strong>
                            <span style="color: var(--success); display: block; font-size: 0.875rem;">● Aktif</span>
                        </div>
                    </div>
                    <div class="about-visual-content">
                        <div class="about-visual-item">
                            <i class="fa-solid fa-route" style="color: var(--accent);"></i>
                            <span>Akıllı Rota Optimizasyonu</span>
                            <span class="status-ok">✓</span>
                        </div>
                        <div class="about-visual-item">
                            <i class="fa-solid fa-users" style="color: var(--primary);"></i>
                            <span>Kurye Eşleştirme Algoritması</span>
                            <span class="status-ok">✓</span>
                        </div>
                        <div class="about-visual-item">
                            <i class="fa-solid fa-chart-line" style="color: var(--accent-2);"></i>
                            <span>Trafik Analizi Modülü</span>
                            <span class="status-ok">✓</span>
                        </div>
                        <div class="about-visual-item">
                            <i class="fa-solid fa-clock" style="color: var(--success);"></i>
                            <span>ETA Tahmin Sistemi</span>
                            <span class="status-ok">✓</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="section" style="background: linear-gradient(180deg, rgba(124, 58, 237, 0.05) 0%, transparent 100%);">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fa-solid fa-heart"></i> Değerlerimiz
            </div>
            <h2 class="section-title">
                Bizi <span class="gradient-text">Farklı Kılan</span>
            </h2>
        </div>
        
        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon">🚀</div>
                <h4>Hız</h4>
                <p>Saniyeler içinde kurye eşleştirme ve dakikalar içinde teslimat.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">🔒</div>
                <h4>Güven</h4>
                <p>%99 başarılı teslimat oranı ve seçkin kurye kadromuz ile gönderileriniz her zaman güvende.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">💡</div>
                <h4>İnovasyon</h4>
                <p>Sürekli geliştirilen altyapımız ve teknolojik çözümlerimiz ile sektörün öncüsüyüz.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">🤝</div>
                <h4>Müşteri Odaklılık</h4>
                <p>7/24 destek ekibimiz ile her sorununuza anında çözüm üretiyoruz.</p>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fa-solid fa-users"></i> Ekibimiz
            </div>
            <h2 class="section-title">
                <span class="gradient-text">Teknoloji</span> Tutkunu Ekip
            </h2>
            <p class="section-subtitle">
                Yazılım mühendisleri, veri bilimcileri ve lojistik uzmanlarından oluşan 
                deneyimli ekibimiz ile sektörün en inovatif çözümlerini geliştiriyoruz.
            </p>
        </div>
        
        <div class="team-stats">
            <div class="team-stat">
                <div class="team-stat-icon">👨‍💻</div>
                <div class="team-stat-value"><span data-count="15">0</span>+</div>
                <div class="team-stat-label">Yazılım Mühendisi</div>
            </div>
            <div class="team-stat">
                <div class="team-stat-icon">📊</div>
                <div class="team-stat-value"><span data-count="5">0</span>+</div>
                <div class="team-stat-label">Veri Bilimci</div>
            </div>
            <div class="team-stat">
                <div class="team-stat-icon">🚚</div>
                <div class="team-stat-value"><span data-count="10">0</span>+</div>
                <div class="team-stat-label">Lojistik Uzmanı</div>
            </div>
            <div class="team-stat">
                <div class="team-stat-icon">📞</div>
                <div class="team-stat-value"><span data-count="20">0</span>+</div>
                <div class="team-stat-label">Müşteri Temsilcisi</div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section">
    <div class="container">
        <div class="cta-section">
            <div class="cta-content">
                <h2>Bizimle <span class="gradient-text">Çalışmak</span> İster misiniz?</h2>
                <p>
                    Kurye olarak ekibimize katılın veya kurumsal çözümlerimiz hakkında bilgi alın!
                </p>
                <div class="cta-buttons">
                    <a href="/kurye-basvuru" class="btn btn-accent">
                        <i class="fa-solid fa-user-plus"></i> Kurye Ol
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
    .about-stat {
        background: var(--bg-glass);
        border: 1px solid var(--border-glass);
        border-radius: 1rem;
        padding: 1.5rem;
        text-align: center;
    }
    
    .about-stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        font-family: 'JetBrains Mono', monospace;
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .about-stat-label {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }
    
    .about-visual {
        overflow: hidden;
    }
    
    .about-visual-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid var(--border-glass);
        margin-bottom: 1.5rem;
    }
    
    .about-visual-content {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .about-visual-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--bg-glass);
        border-radius: 0.75rem;
        font-size: 0.9rem;
    }
    
    .about-visual-item span:first-of-type {
        flex: 1;
    }
    
    .status-ok {
        color: var(--success);
        font-weight: 700;
    }
    
    .values-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
    }
    
    .value-card {
        background: var(--bg-glass);
        border: 1px solid var(--border-glass);
        border-radius: 1.5rem;
        padding: 2rem;
        text-align: center;
        transition: all 0.4s ease;
    }
    
    .value-card:hover {
        transform: translateY(-8px);
        border-color: var(--primary);
        box-shadow: 0 20px 40px rgba(124, 58, 237, 0.15);
    }
    
    .value-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        display: block;
    }
    
    .value-card h4 {
        font-size: 1.25rem;
        margin-bottom: 0.75rem;
    }
    
    .value-card p {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }
    
    .team-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
    }
    
    .team-stat {
        background: var(--bg-glass);
        border: 1px solid var(--border-glass);
        border-radius: 1.5rem;
        padding: 2rem;
        text-align: center;
        transition: all 0.4s ease;
    }
    
    .team-stat:hover {
        transform: translateY(-5px);
        border-color: var(--accent);
    }
    
    .team-stat-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        display: block;
    }
    
    .team-stat-value {
        font-size: 2.25rem;
        font-weight: 800;
        font-family: 'JetBrains Mono', monospace;
        background: var(--gradient-accent);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .team-stat-label {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }
    
    @media (max-width: 1024px) {
        .values-grid,
        .team-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .section > .container > div {
            grid-template-columns: 1fr !important;
        }
        
        .values-grid,
        .team-stats {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
