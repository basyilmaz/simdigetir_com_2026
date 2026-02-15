@extends('layouts.landing')

@section('title', 'Kurumsal Çözümler - SimdiGetir Kurye')
@section('meta_description', 'E-ticaret ve kurumsal firmalar için özel teslimat çözümleri. API entegrasyonu, özel fiyatlandırma, öncelikli destek.')
@section('meta_keywords', 'kurumsal kurye, firma kurye hizmeti, toplu gönderi, e-ticaret kurye, API kurye entegrasyonu, kurumsal teslimat, B2B kurye istanbul')

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Service",
    "name": "SimdiGetir Kurumsal Kurye Hizmeti",
    "description": "E-ticaret ve kurumsal firmalar için özel teslimat çözümleri. API entegrasyonu, özel fiyatlandırma, öncelikli destek.",
    "provider": {"@id": "{{ url('/') }}/#organization"},
    "areaServed": {"@type": "City", "name": "İstanbul"},
    "serviceType": "Kurumsal Kurye Hizmeti",
    "url": "{{ url('/kurumsal') }}",
    "offers": {
        "@type": "Offer",
        "description": "Kurumsal firmalar için özel fiyatlandırma ve aylık faturalama",
        "eligibleRegion": {"@type": "City", "name": "İstanbul"}
    }
}
</script>
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero" style="min-height: auto; padding: 10rem 0 4rem;">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
            <div>
                <div class="hero-badge animate__animated animate__fadeInUp">
                    <span class="pulse"></span>
                    Kurumsal Çözümler
                </div>
                <h1 class="animate__animated animate__fadeInUp animate__delay-1s" style="font-size: 3rem;">
                    İşletmeniz İçin<br>
                    <span class="gradient-text">Özel Teslimat</span><br>
                    Çözümleri
                </h1>
                <p class="animate__animated animate__fadeInUp animate__delay-2s" style="color: var(--text-secondary); margin-bottom: 2rem; font-size: 1.125rem;">
                    E-ticaret siteniz, mağazanız veya kurumunuz için ölçeklenebilir, güvenilir ve akıllı teslimat altyapısı.
                </p>
                <div class="hero-buttons animate__animated animate__fadeInUp animate__delay-3s">
                    <a href="#teklif-form" class="btn btn-accent">
                        <i class="fa-solid fa-building"></i> Teklif İsteyin
                    </a>
                    <a href="tel:+905324847292" class="btn btn-outline">
                        <i class="fa-solid fa-phone"></i> Hemen Arayın
                    </a>
                </div>
            </div>
            <div class="animate__animated animate__fadeInRight animate__delay-2s">
                <div class="glass corporate-stats-card" style="padding: 2.5rem;">
                    <div class="floating-orb orb-1"></div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <div class="corp-stat">
                            <div class="corp-stat-value"><span data-count="500">0</span>+</div>
                            <div class="corp-stat-label">Kurumsal Müşteri</div>
                        </div>
                        <div class="corp-stat">
                            <div class="corp-stat-value"><span data-count="99">0</span>.5%</div>
                            <div class="corp-stat-label">Zamanında Teslimat</div>
                        </div>
                        <div class="corp-stat">
                            <div class="corp-stat-value"><span data-count="45">0</span>dk</div>
                            <div class="corp-stat-label">Ort. Teslimat Süresi</div>
                        </div>
                        <div class="corp-stat">
                            <div class="corp-stat-value">7/<span data-count="24">0</span></div>
                            <div class="corp-stat-label">Operasyon</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Marquee Section -->
<div class="marquee-section">
    <div class="marquee-wrapper">
        <div class="marquee-group">
            <div class="marquee-item"><i class="fa-solid fa-star"></i> API Entegrasyonu</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Toplu Gönderi</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Özel Fiyatlandırma</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Öncelikli Destek</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Raporlama Paneli</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Aylık Fatura</div>
        </div>
        <div class="marquee-group">
            <div class="marquee-item"><i class="fa-solid fa-star"></i> API Entegrasyonu</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Toplu Gönderi</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Özel Fiyatlandırma</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Öncelikli Destek</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Raporlama Paneli</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Aylık Fatura</div>
        </div>
        <div class="marquee-group">
            <div class="marquee-item"><i class="fa-solid fa-star"></i> API Entegrasyonu</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Toplu Gönderi</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Özel Fiyatlandırma</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Öncelikli Destek</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Raporlama Paneli</div>
            <div class="marquee-item"><i class="fa-solid fa-star"></i> Aylık Fatura</div>
        </div>
    </div>
</div>

<!-- Solutions Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fa-solid fa-briefcase"></i> Kurumsal Çözümler
            </div>
            <h2 class="section-title">
                Akıllı <span class="gradient-text">Hizmetlerimiz</span>
            </h2>
            <p class="section-subtitle">
                Profesyonel kurye altyapımız ile işletmenizin teslimat süreçlerini optimize edin.
            </p>
        </div>

        <div class="services-grid">
            <div class="service-card">
                <div class="service-card-number">01</div>
                <div class="service-card-icon">🔌</div>
                <h3>API Entegrasyonu</h3>
                <p>E-ticaret platformunuza kolay entegrasyon. Trendyol, Hepsiburada, WooCommerce ve daha fazlası.</p>
                <ul class="service-card-features">
                    <li><i class="fa-solid fa-check"></i> RESTful API</li>
                    <li><i class="fa-solid fa-check"></i> Webhook desteği</li>
                    <li><i class="fa-solid fa-check"></i> Hazır SDK'lar</li>
                </ul>
            </div>

            <div class="service-card">
                <div class="service-card-number">02</div>
                <div class="service-card-icon" style="background: linear-gradient(135deg, #22d3ee 0%, #7c3aed 100%);">📊</div>
                <h3>Raporlama Paneli</h3>
                <p>Tüm gönderilerinizi anlık takip edin, detaylı raporlar ve analizler alın.</p>
                <ul class="service-card-features">
                    <li><i class="fa-solid fa-check"></i> Gerçek zamanlı takip</li>
                    <li><i class="fa-solid fa-check"></i> Performans metrikleri</li>
                    <li><i class="fa-solid fa-check"></i> Excel/PDF export</li>
                </ul>
            </div>

            <div class="service-card">
                <div class="service-card-number">03</div>
                <div class="service-card-icon" style="background: linear-gradient(135deg, #10b981 0%, #22d3ee 100%);">💰</div>
                <h3>Özel Fiyatlandırma</h3>
                <p>Gönderi hacminize göre özel fiyat teklifleri ve hacim indirimleri. Sürpriz masraf yok!</p>
                <ul class="service-card-features">
                    <li><i class="fa-solid fa-check"></i> Hacim indirimi</li>
                    <li><i class="fa-solid fa-check"></i> Aylık faturalama</li>
                    <li><i class="fa-solid fa-check"></i> Şeffaf fiyatlar</li>
                </ul>
            </div>
        </div>

        <!-- Second row -->
        <div class="services-grid" style="margin-top: 2rem;">
            <div class="service-card">
                <div class="service-card-number">04</div>
                <div class="service-card-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #ec4899 100%);">🎯</div>
                <h3>Öncelikli Destek</h3>
                <p>Kurumsal müşterilere özel hesap yöneticisi ve 7/24 destek hattı.</p>
                <ul class="service-card-features">
                    <li><i class="fa-solid fa-check"></i> Özel hesap yöneticisi</li>
                    <li><i class="fa-solid fa-check"></i> 7/24 destek hattı</li>
                    <li><i class="fa-solid fa-check"></i> SLA garantisi</li>
                </ul>
            </div>

            <div class="service-card">
                <div class="service-card-number">05</div>
                <div class="service-card-icon" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);">📅</div>
                <h3>Aylık Faturalama</h3>
                <p>Her teslimat için ödeme yerine aylık toplu fatura seçeneği ile nakit akışınızı yönetin.</p>
                <ul class="service-card-features">
                    <li><i class="fa-solid fa-check"></i> Toplu fatura</li>
                    <li><i class="fa-solid fa-check"></i> Detaylı döküm</li>
                    <li><i class="fa-solid fa-check"></i> Otomatik raporlama</li>
                </ul>
            </div>

            <div class="service-card">
                <div class="service-card-number">06</div>
                <div class="service-card-icon" style="background: linear-gradient(135deg, #ec4899 0%, #7c3aed 100%);">📦</div>
                <h3>Toplu Gönderi</h3>
                <p>Excel/CSV ile toplu sipariş yükleme ve tek tıkla operasyon başlatma imkanı.</p>
                <ul class="service-card-features">
                    <li><i class="fa-solid fa-check"></i> Excel/CSV import</li>
                    <li><i class="fa-solid fa-check"></i> Tek tıkla başlat</li>
                    <li><i class="fa-solid fa-check"></i> Akıllı rota optimizasyonu</li>
                </ul>
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
                Kurumsal <span class="gradient-text">Süreç Adımları</span>
            </h2>
            <p class="section-subtitle">
                4 basit adımda kurumsal teslimat altyapınızı kuruyoruz.
            </p>
        </div>

        <div class="process-grid" style="grid-template-columns: repeat(4, 1fr);">
            <div class="process-card">
                <div class="process-number">01</div>
                <h3>Başvurun</h3>
                <p>Formu doldurun, talebinizi analiz edelim ve size özel teklif hazırlayalım.</p>
            </div>
            <div class="process-card">
                <div class="process-number">02</div>
                <h3>Entegrasyon</h3>
                <p>API veya panel üzerinden sisteminizi dakikalar içinde entegre edin.</p>
            </div>
            <div class="process-card">
                <div class="process-number">03</div>
                <h3>Gönderin</h3>
                <p>Siparişlerinizi oluşturun, en uygun kurye anında atasın.</p>
            </div>
            <div class="process-card">
                <div class="process-number">04</div>
                <h3>Takip Edin</h3>
                <p>Tüm teslimatları gerçek zamanlı takip edin, raporlarınızı alın.</p>
            </div>
        </div>
    </div>
</section>

<!-- Quote Form Section -->
<section class="section" id="teklif-form" style="background: linear-gradient(180deg, transparent 0%, rgba(124, 58, 237, 0.05) 100%);">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
            <div>
                <div class="section-badge">
                    <i class="fa-solid fa-rocket"></i> Hemen Başlayın
                </div>
                <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem;">
                    İşletmenize Özel<br>
                    <span class="gradient-text">Özel Teklif</span>
                </h2>
                <p style="color: var(--text-secondary); margin-bottom: 2rem; font-size: 1.125rem;">
                    İşletmenize özel fiyat ve çözüm hazırlayalım. 
                    Ücretsiz danışmanlık ve demo imkanından yararlanın.
                </p>

                <ul style="list-style: none;">
                    <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 1rem; color: var(--text-secondary);">
                        <i class="fa-solid fa-check-circle" style="color: var(--accent); font-size: 1.25rem;"></i> Ücretsiz kurulum ve entegrasyon
                    </li>
                    <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 1rem; color: var(--text-secondary);">
                        <i class="fa-solid fa-check-circle" style="color: var(--accent); font-size: 1.25rem;"></i> 30 gün ücretsiz deneme
                    </li>
                    <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 1rem; color: var(--text-secondary);">
                        <i class="fa-solid fa-check-circle" style="color: var(--accent); font-size: 1.25rem;"></i> Özel hesap yöneticisi
                    </li>
                    <li style="display: flex; align-items: center; gap: 1rem; color: var(--text-secondary);">
                        <i class="fa-solid fa-check-circle" style="color: var(--accent); font-size: 1.25rem;"></i> SLA garantili hizmet
                    </li>
                </ul>
            </div>

            <div class="glass" style="padding: 2.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
                    <div class="ai-avatar">💼</div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.5rem;">Kurumsal Teklif</h3>
                        <span style="color: var(--text-muted); font-size: 0.9rem;">Talebiniz hızla değerlendirilecek</span>
                    </div>
                </div>

                <form id="corporate-form" onsubmit="submitCorporateForm(event)">
                    <div class="form-group">
                        <label>Firma Adı *</label>
                        <input type="text" name="company_name" required placeholder="Şirket adınız">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Yetkili Adı *</label>
                            <input type="text" name="name" required placeholder="Ad Soyad">
                        </div>
                        <div class="form-group">
                            <label>Telefon *</label>
                            <input type="tel" name="phone" required placeholder="05XX XXX XX XX" pattern="0[0-9]{10}" title="Lütfen 05XX XXX XX XX formatında girin">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>E-posta *</label>
                        <input type="email" name="email" required placeholder="ornek@sirket.com">
                    </div>

                    <div class="form-group">
                        <label>Sektör</label>
                        <select name="sector">
                            <option value="">Seçiniz</option>
                            <option value="E-ticaret">E-ticaret</option>
                            <option value="Restoran / Yiyecek">Restoran / Yiyecek</option>
                            <option value="Perakende">Perakende</option>
                            <option value="Sağlık / Eczane">Sağlık / Eczane</option>
                            <option value="Hukuk / Evrak">Hukuk / Evrak</option>
                            <option value="Diğer">Diğer</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Aylık Tahmini Gönderi Sayısı</label>
                        <select name="message">
                            <option value="">Seçiniz</option>
                            <option value="1-50 gönderi/ay">1-50 gönderi/ay</option>
                            <option value="50-200 gönderi/ay">50-200 gönderi/ay</option>
                            <option value="200-500 gönderi/ay">200-500 gönderi/ay</option>
                            <option value="500-1000 gönderi/ay">500-1000 gönderi/ay</option>
                            <option value="1000+ gönderi/ay">1000+ gönderi/ay</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;" id="corporate-submit">
                        <i class="fa-solid fa-rocket"></i> Teklif İste
                    </button>
                </form>

                <div id="corporate-success" style="display: none;" class="alert alert-success">
                    <i class="fa-solid fa-check-circle"></i> Talebiniz alındı! Size özel teklif en geç 24 saat içinde gönderilecektir.
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section">
    <div class="container">
        <div class="cta-section">
            <div class="cta-content">
                <h2>Hemen <span class="gradient-text">Başlayın</span></h2>
                <p>
                    Kurumsal teslimat çözümlerimiz hakkında bilgi almak için bizi arayın!
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
@endsection

@push('styles')
<style>
    .corporate-stats-card {
        position: relative;
        overflow: hidden;
    }

    .corp-stat {
        text-align: center;
        padding: 1rem;
    }

    .corp-stat-value {
        font-size: 2rem;
        font-weight: 800;
        font-family: 'JetBrains Mono', monospace;
        background: var(--gradient-accent);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.25rem;
    }

    .corp-stat-label {
        color: var(--text-secondary);
        font-size: 0.85rem;
    }

    @media (max-width: 768px) {
        .section > .container > div,
        .hero > .container > div {
            grid-template-columns: 1fr !important;
        }

        .process-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    async function submitCorporateForm(event) {
        event.preventDefault();

        const form = event.target;
        const submitBtn = document.getElementById('corporate-submit');
        const successDiv = document.getElementById('corporate-success');

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="typing-dots"><span></span><span></span><span></span></span> Gönderiliyor...';

        const formData = new FormData(form);
        const data = {
            type: 'corporate_quote',
            name: formData.get('name'),
            company_name: formData.get('company_name'),
            phone: formData.get('phone'),
            email: formData.get('email'),
            message: `Sektör: ${formData.get('sector') || '-'}, Hacim: ${formData.get('message') || '-'}`,
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
                trackEvent('lead_submit', { lead_type: 'corporate_quote' });
            } else {
                alert(result.message || 'Bir hata oluştu. Lütfen tekrar deneyin.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-rocket"></i> Teklif İste';
            }
        } catch (error) {
            alert('Bağlantı hatası. Lütfen tekrar deneyin.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-rocket"></i> Teklif İste';
        }
    }
</script>
@endpush
