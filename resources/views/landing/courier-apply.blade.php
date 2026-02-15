@extends('layouts.landing')

@section('title', 'Kurye Ol - SimdiGetir')
@section('meta_description', 'SimdiGetir kurye ailesine katılın! Esnek çalışma, yüksek kazanç, akıllı navigasyon. Hemen başvurun.')
@section('meta_keywords', 'kurye iş ilanı, moto kurye başvuru, kurye olmak istiyorum, istanbul kurye iş, kurye başvuru formu, esnek çalışma kurye')

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "JobPosting",
    "title": "Kurye - SimdiGetir",
    "description": "SimdiGetir kurye ailesine katılın! Esnek çalışma saatleri, yüksek kazanç potansiyeli, akıllı navigasyon sistemi ile İstanbul genelinde kurye olarak çalışın.",
    "datePosted": "{{ date('Y-m-d') }}",
    "validThrough": "{{ date('Y-m-d', strtotime('+6 months')) }}",
    "employmentType": "FULL_TIME",
    "hiringOrganization": {
        "@type": "Organization",
        "name": "SimdiGetir Kurye",
        "sameAs": "{{ url('/') }}"
    },
    "jobLocation": {
        "@type": "Place",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "İstanbul",
            "addressRegion": "İstanbul",
            "addressCountry": "TR"
        }
    },
    "baseSalary": {
        "@type": "MonetaryAmount",
        "currency": "TRY",
        "value": {
            "@type": "QuantitativeValue",
            "minValue": 15000,
            "unitText": "MONTH"
        }
    }
}
</script>
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero" style="min-height: auto; padding: 10rem 0 4rem;">
    <div class="container" style="text-align: center;">
        <div class="hero-badge animate__animated animate__fadeInUp">
            <span class="pulse"></span>
            Kariyer Fırsatı
        </div>
        <h1 class="animate__animated animate__fadeInUp animate__delay-1s" style="font-size: 3rem;">
            <span class="gradient-text">Kurye Ailemize</span> Katılın
        </h1>
        <p class="animate__animated animate__fadeInUp animate__delay-2s" style="max-width: 650px; margin: 0 auto;">
            Esnek çalışma saatleri, yüksek kazanç potansiyeli! 
            Akıllı rota önerileri ve hızlı ödemelerle hayatınızı kolaylaştırın.
        </p>
        <div class="animate__animated animate__fadeInUp animate__delay-3s" style="margin-top: 3rem;">
            <img src="{{ asset('images/hero-courier.svg') }}" alt="SimdiGetir Kurye Ol" style="max-width: 600px; width: 100%; border-radius: 20px;">
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="section" style="padding-top: 0;">
    <div class="container">
        <div class="benefits-grid">
            <div class="benefit-card">
                <div class="benefit-icon">💰</div>
                <h4>Yüksek Kazanç</h4>
                <p>Rekabetçi teslimat ücreti ve bonus sistemimiz ile yüksek gelir elde edin</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">⏰</div>
                <h4>Esnek Çalışma</h4>
                <p>İstediğiniz saatlerde, istediğiniz kadar çalışın. Tam kontrol sizde!</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">🧭</div>
                <h4>Akıllı Navigasyon</h4>
                <p>Akıllı rota önerileri ile zamandan tasarruf edin, daha çok teslimat yapın</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">⚡</div>
                <h4>Hızlı Ödeme</h4>
                <p>Haftalık ödemeler ile kazancınızı beklemeden alın</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">📱</div>
                <h4>Kolay Uygulama</h4>
                <p>Kullanıcı dostu mobil uygulamamız ile siparişleri kolayca yönetin</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">🎯</div>
                <h4>Yakın Siparişler</h4>
                <p>Sistem size en yakın siparişleri öncelikli olarak bildirir</p>
            </div>
        </div>
    </div>
</section>

<!-- Application Form Section -->
<section class="section" style="background: linear-gradient(180deg, rgba(124, 58, 237, 0.05) 0%, transparent 100%);">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 4rem; align-items: flex-start;">
            <!-- Requirements -->
            <div>
                <div class="section-badge">
                    <i class="fa-solid fa-clipboard-check"></i> Gereksinimler
                </div>
                <h2 style="font-size: 2rem; margin-bottom: 2rem;">
                    Kurye <span class="gradient-text">Olmak İçin</span>
                </h2>
                
                <div class="requirements-list">
                    <div class="requirement-item">
                        <div class="requirement-icon">✓</div>
                        <div>
                            <strong>18 Yaş ve Üzeri</strong>
                            <p>Ehliyet sahibi olmak için minimum yaş şartı</p>
                        </div>
                    </div>
                    <div class="requirement-item">
                        <div class="requirement-icon">✓</div>
                        <div>
                            <strong>Geçerli Ehliyet</strong>
                            <p>Aktif A veya B sınıfı sürücü belgesi</p>
                        </div>
                    </div>
                    <div class="requirement-item">
                        <div class="requirement-icon">✓</div>
                        <div>
                            <strong>Kendi Aracınız</strong>
                            <p>Motosiklet veya otomobil (araç yok ise destek sağlıyoruz)</p>
                        </div>
                    </div>
                    <div class="requirement-item">
                        <div class="requirement-icon">✓</div>
                        <div>
                            <strong>Smartphone</strong>
                            <p>Android veya iOS işletim sistemli akıllı telefon</p>
                        </div>
                    </div>
                    <div class="requirement-item">
                        <div class="requirement-icon">✓</div>
                        <div>
                            <strong>İstanbul Adresi</strong>
                            <p>İstanbul il sınırları içinde ikamet</p>
                        </div>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="courier-stats">
                    <div class="courier-stat">
                        <span class="courier-stat-value"><span data-count="500">0</span>+</span>
                        <span class="courier-stat-label">Aktif Kurye</span>
                    </div>
                    <div class="courier-stat">
                        <span class="courier-stat-value">₺<span data-count="15000">0</span>+</span>
                        <span class="courier-stat-label">Aylık Kazanç Potansiyeli</span>
                    </div>
                </div>
            </div>
            
            <!-- Application Form -->
            <div class="glass application-form-wrapper">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
                    <div class="ai-avatar">🏍️</div>
                    <div>
                        <h3 style="margin: 0;">Başvuru Formu</h3>
                        <span style="color: var(--text-muted); font-size: 0.9rem;">Başvurunuz hızla değerlendirilecek</span>
                    </div>
                </div>
                
                <form id="courier-form" onsubmit="submitCourierForm(event)">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Ad *</label>
                            <input type="text" name="first_name" required placeholder="Adınız">
                        </div>
                        <div class="form-group">
                            <label>Soyad *</label>
                            <input type="text" name="last_name" required placeholder="Soyadınız">
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Telefon *</label>
                            <input type="tel" name="phone" required placeholder="05XX XXX XX XX" pattern="0[0-9]{10}" title="Lütfen 05XX XXX XX XX formatında girin">
                        </div>
                        <div class="form-group">
                            <label>E-posta</label>
                            <input type="email" name="email" placeholder="ornek@email.com">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>İlçe *</label>
                        <select name="district" required>
                            <option value="">İlçe Seçin</option>
                            <option value="Kadıköy">Kadıköy</option>
                            <option value="Beşiktaş">Beşiktaş</option>
                            <option value="Şişli">Şişli</option>
                            <option value="Üsküdar">Üsküdar</option>
                            <option value="Beyoğlu">Beyoğlu</option>
                            <option value="Kağıthane">Kağıthane</option>
                            <option value="Sarıyer">Sarıyer</option>
                            <option value="Ataşehir">Ataşehir</option>
                            <option value="Maltepe">Maltepe</option>
                            <option value="Bakırköy">Bakırköy</option>
                            <option value="Diğer">Diğer</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Araç Tipi *</label>
                        <select name="vehicle_type" required>
                            <option value="">Araç Tipini Seçin</option>
                            <option value="Motosiklet">Motosiklet</option>
                            <option value="Otomobil">Otomobil</option>
                            <option value="Minivan">Minivan</option>
                            <option value="Aracım Yok">Aracım Yok (Destek İstiyorum)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Deneyim</label>
                        <select name="experience">
                            <option value="">Kurye Deneyimi</option>
                            <option value="Deneyimim Yok">Deneyimim Yok</option>
                            <option value="0-1 Yıl">0-1 Yıl</option>
                            <option value="1-3 Yıl">1-3 Yıl</option>
                            <option value="3+ Yıl">3+ Yıl</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" required style="width: auto; margin-right: 0.5rem;">
                            <a href="/kvkk" target="_blank" style="color: var(--accent);">KVKK</a> metnini okudum ve kabul ediyorum
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;" id="courier-submit">
                        <i class="fa-solid fa-rocket"></i> Başvurumu Gönder
                    </button>
                </form>
                
                <div id="courier-success" style="display: none;" class="alert alert-success">
                    <i class="fa-solid fa-check-circle"></i> Başvurunuz alındı! En kısa sürede sizinle iletişime geçeceğiz.
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
                <h2>Sorularınız mı Var?</h2>
                <p>
                    Kurye olmak hakkında detaylı bilgi almak için bizi arayın!
                </p>
                <div class="cta-buttons">
                    <a href="tel:+905324847292" class="btn btn-accent">
                        <i class="fa-solid fa-phone"></i> 0532 484 72 92
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .benefits-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 1.5rem;
    }
    
    .benefit-card {
        background: var(--bg-glass);
        border: 1px solid var(--border-glass);
        border-radius: 1.25rem;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.4s ease;
    }
    
    .benefit-card:hover {
        transform: translateY(-8px);
        border-color: var(--primary);
        box-shadow: 0 15px 35px rgba(124, 58, 237, 0.15);
    }
    
    .benefit-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        display: block;
    }
    
    .benefit-card h4 {
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .benefit-card p {
        color: var(--text-secondary);
        font-size: 0.85rem;
        margin: 0;
    }
    
    .requirements-list {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    
    .requirement-item {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .requirement-icon {
        width: 32px;
        height: 32px;
        background: var(--gradient-primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
    }
    
    .requirement-item strong {
        display: block;
        margin-bottom: 0.25rem;
    }
    
    .requirement-item p {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin: 0;
    }
    
    .courier-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    .courier-stat {
        background: var(--bg-glass);
        border: 1px solid var(--border-glass);
        border-radius: 1rem;
        padding: 1.5rem;
        text-align: center;
    }
    
    .courier-stat-value {
        display: block;
        font-size: 1.75rem;
        font-weight: 800;
        font-family: 'JetBrains Mono', monospace;
        background: var(--gradient-accent);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.25rem;
    }
    
    .courier-stat-label {
        color: var(--text-secondary);
        font-size: 0.85rem;
    }
    
    .application-form-wrapper {
        padding: 2.5rem;
    }
    
    @media (max-width: 1024px) {
        .benefits-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .benefits-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .section > .container > div {
            grid-template-columns: 1fr !important;
        }
        
        .application-form-wrapper {
            padding: 1.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    async function submitCourierForm(event) {
        event.preventDefault();
        
        const form = event.target;
        const submitBtn = document.getElementById('courier-submit');
        const successDiv = document.getElementById('courier-success');
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="typing-dots"><span></span><span></span><span></span></span> Gönderiliyor...';
        
        const formData = new FormData(form);
        const data = {
            type: 'courier_application',
            name: formData.get('first_name') + ' ' + formData.get('last_name'),
            phone: formData.get('phone'),
            email: formData.get('email'),
            district: formData.get('district'),
            vehicle_type: formData.get('vehicle_type'),
            experience: formData.get('experience'),
            message: `İlçe: ${formData.get('district')}, Araç: ${formData.get('vehicle_type')}, Deneyim: ${formData.get('experience')}`,
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
                trackEvent('lead_submit', { lead_type: 'courier_application' });
            } else {
                alert(result.message || 'Bir hata oluştu. Lütfen tekrar deneyin.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-rocket"></i> Başvurumu Gönder';
            }
        } catch (error) {
            alert('Bağlantı hatası. Lütfen tekrar deneyin.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-rocket"></i> Başvurumu Gönder';
        }
    }
</script>
@endpush
