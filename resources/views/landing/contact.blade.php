@extends('layouts.landing')

@section('title', 'İletişim - SimdiGetir')
@section('meta_description', 'SimdiGetir kurye hizmeti ile iletişime geçin. 7/24 aktif müşteri desteği. Telefon: 0532 484 72 92')
@section('meta_keywords', 'simdigetir iletişim, kurye telefon, moto kurye ara, kurye çağır istanbul, 7/24 kurye hattı')

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "ContactPage",
    "name": "SimdiGetir İletişim",
    "description": "SimdiGetir kurye hizmeti iletişim bilgileri. 7/24 aktif müşteri desteği.",
    "url": "{{ url('/iletisim') }}",
    "mainEntity": {
        "@type": "LocalBusiness",
        "name": "SimdiGetir Kurye",
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
        "contactPoint": [
            {
                "@type": "ContactPoint",
                "telephone": "+905324847292",
                "contactType": "customer service",
                "availableLanguage": "Turkish",
                "hoursAvailable": {
                    "@type": "OpeningHoursSpecification",
                    "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],
                    "opens": "00:00",
                    "closes": "23:59"
                }
            }
        ]
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
            Müşteri Servisi Aktif
        </div>
        <h1 class="animate__animated animate__fadeInUp animate__delay-1s" style="font-size: 3rem;">
            <span class="gradient-text">7/24</span> Yanınızdayız
        </h1>
        <p class="animate__animated animate__fadeInUp animate__delay-2s" style="max-width: 600px; margin: 0 auto;">
            Uzman ekibimiz sorularınızı yanıtlamak için her zaman hazır.
        </p>
        <div class="animate__animated animate__fadeInUp animate__delay-3s" style="margin-top: 3rem;">
            <img src="{{ asset('images/hero-contact.svg') }}" alt="SimdiGetir İletişim" style="max-width: 550px; width: 100%; border-radius: 20px;">
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="section">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 4rem;">
            <!-- Contact Info -->
            <div>
                <div class="section-badge">
                    <i class="fa-solid fa-headset"></i> İletişim Kanalları
                </div>
                <h2 style="font-size: 2rem; margin-bottom: 2rem;">
                    Bize <span class="gradient-text">Ulaşın</span>
                </h2>
                
                <div class="contact-cards">
                    <a href="tel:+905324847292" class="contact-card">
                        <div class="contact-icon">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div class="contact-content">
                            <h4>Telefon</h4>
                            <p>+90 532 484 72 92</p>
                            <span class="contact-hint">7/24 Aktif Hat</span>
                        </div>
                        <i class="fa-solid fa-arrow-right contact-arrow"></i>
                    </a>
                    
                    <a href="https://wa.me/905324847292" target="_blank" class="contact-card contact-card-whatsapp">
                        <div class="contact-icon contact-icon-whatsapp">
                            <i class="fa-brands fa-whatsapp"></i>
                        </div>
                        <div class="contact-content">
                            <h4>WhatsApp</h4>
                            <p>Hızlı Mesaj</p>
                            <span class="contact-hint">Anlık Yanıt</span>
                        </div>
                        <i class="fa-solid fa-arrow-right contact-arrow"></i>
                    </a>
                    
                    <a href="mailto:webgetir@simdigetir.com" class="contact-card">
                        <div class="contact-icon contact-icon-email">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div class="contact-content">
                            <h4>E-posta</h4>
                            <p>webgetir@simdigetir.com</p>
                            <span class="contact-hint">Kurumsal İletişim</span>
                        </div>
                        <i class="fa-solid fa-arrow-right contact-arrow"></i>
                    </a>
                    
                    <div class="contact-card" style="cursor: default;">
                        <div class="contact-icon contact-icon-location">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div class="contact-content">
                            <h4>Adres</h4>
                            <p>Yeşilce Mahallesi Aytekin Sokak No:5/2</p>
                            <span class="contact-hint">Kağıthane / İstanbul</span>
                        </div>
                    </div>
                </div>
                
                <!-- WhatsApp Destek Card -->
                <div class="glass ai-assistant-card">
                    <div class="ai-assistant-header">
                        <div class="ai-avatar">💬</div>
                        <div>
                            <strong>SimdiGetir Destek</strong>
                            <span class="ai-online">● Çevrimiçi</span>
                        </div>
                    </div>
                    <p>
                        WhatsApp üzerinden "<strong>Merhaba</strong>" yazarak anında sohbet başlatabilirsiniz!
                    </p>
                    <a href="https://wa.me/905324847292?text=Merhaba" target="_blank" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                        <i class="fa-brands fa-whatsapp"></i> Hemen Yazın
                    </a>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="glass contact-form-wrapper">
                <h3 style="margin-bottom: 0.5rem; font-size: 1.5rem;">Mesaj Gönderin</h3>
                <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 0.9rem;">
                    Mesajınızı değerlendirip size uygun çözüm sunacağız.
                </p>
                
                <form id="contact-form" onsubmit="submitContactForm(event)">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Ad Soyad *</label>
                            <input type="text" name="name" required placeholder="Tam adınız">
                        </div>
                        <div class="form-group">
                            <label>Telefon *</label>
                            <input type="tel" name="phone" required placeholder="05XX XXX XX XX" pattern="0[0-9]{10}" title="Lütfen 05XX XXX XX XX formatında girin">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>E-posta</label>
                        <input type="email" name="email" placeholder="ornek@email.com">
                    </div>
                    
                    <div class="form-group">
                        <label>Konu</label>
                        <select name="subject">
                            <option value="">Konu Seçin</option>
                            <option value="Genel Bilgi">Genel Bilgi</option>
                            <option value="Fiyat Teklifi">Fiyat Teklifi</option>
                            <option value="Kurumsal Hizmet">Kurumsal Hizmet</option>
                            <option value="Kurye Başvurusu">Kurye Başvurusu</option>
                            <option value="Şikayet/Öneri">Şikayet/Öneri</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Mesajınız *</label>
                        <textarea name="message" rows="5" required placeholder="Size nasıl yardımcı olabiliriz?"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;" id="contact-submit">
                        <i class="fa-solid fa-paper-plane"></i> Mesaj Gönder
                    </button>
                </form>
                
                <div id="contact-success" style="display: none;" class="alert alert-success">
                    <i class="fa-solid fa-check-circle"></i> Mesajınız alındı! En kısa sürede size dönüş yapacağız.
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="section" style="padding-top: 0;">
    <div class="container">
        <div class="glass map-wrapper">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1503.5!2d28.9943!3d41.0956!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDHCsDA1JzQ0LjIiTiAyOcKwMDAnMDIuMCJF!5e0!3m2!1str!2str!4v1707609600000!5m2!1str!2str"
                width="100%" 
                height="450" 
                style="border:0; border-radius: 1rem; filter: grayscale(0.9) invert(0.92) contrast(1.1);" 
                allowfullscreen="" 
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section" style="padding-top: 0;">
    <div class="container">
        <div class="cta-section">
            <div class="cta-content">
                <h2>Hemen <span class="gradient-text">Arayın</span></h2>
                <p>
                    Uzman ekibimiz sizi bekliyor!
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
    .contact-cards {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .contact-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        background: var(--bg-glass);
        border: 1px solid var(--border-glass);
        border-radius: 1rem;
        text-decoration: none;
        color: inherit;
        transition: all 0.4s ease;
    }
    
    .contact-card:hover {
        border-color: var(--primary);
        transform: translateX(10px);
        box-shadow: 0 0 30px rgba(124, 58, 237, 0.15);
    }
    
    .contact-icon {
        width: 55px;
        height: 55px;
        background: var(--gradient-primary);
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }
    
    .contact-icon-whatsapp {
        background: linear-gradient(135deg, #25d366 0%, #128C7E 100%);
    }
    
    .contact-icon-email {
        background: linear-gradient(135deg, #22d3ee 0%, #7c3aed 100%);
    }
    
    .contact-icon-location {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    }
    
    .contact-content {
        flex: 1;
    }
    
    .contact-content h4 {
        font-size: 0.9rem;
        color: var(--text-muted);
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
    
    .contact-content p {
        font-size: 1.1rem;
        color: var(--text-primary);
        font-weight: 600;
        margin: 0;
    }
    
    .contact-hint {
        font-size: 0.8rem;
        color: var(--text-muted);
    }
    
    .contact-arrow {
        color: var(--text-muted);
        transition: all 0.3s ease;
    }
    
    .contact-card:hover .contact-arrow {
        color: var(--accent);
        transform: translateX(5px);
    }
    
    .ai-assistant-card {
        padding: 1.5rem;
    }
    
    .ai-assistant-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .ai-online {
        color: var(--success);
        font-size: 0.875rem;
        display: block;
    }
    
    .ai-assistant-card p {
        color: var(--text-secondary);
        font-size: 0.9rem;
        line-height: 1.6;
    }
    
    .contact-form-wrapper {
        padding: 2.5rem;
    }
    
    .map-wrapper {
        padding: 0.5rem;
        overflow: hidden;
    }
    
    @media (max-width: 768px) {
        .section > .container > div {
            grid-template-columns: 1fr !important;
        }
        
        .contact-form-wrapper {
            padding: 1.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    async function submitContactForm(event) {
        event.preventDefault();
        
        const form = event.target;
        const submitBtn = document.getElementById('contact-submit');
        const successDiv = document.getElementById('contact-success');
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="typing-dots"><span></span><span></span><span></span></span> Gönderiliyor...';
        
        const formData = new FormData(form);
        const data = {
            type: 'contact',
            name: formData.get('name'),
            phone: formData.get('phone'),
            email: formData.get('email'),
            subject: formData.get('subject'),
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
                trackEvent('lead_submit', { lead_type: 'contact' });
            } else {
                alert(result.message || 'Bir hata oluştu. Lütfen tekrar deneyin.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Mesaj Gönder';
            }
        } catch (error) {
            alert('Bağlantı hatası. Lütfen tekrar deneyin.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Mesaj Gönder';
        }
    }
</script>
@endpush
