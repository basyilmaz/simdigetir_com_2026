@extends('layouts.landing')

@section('title', 'Sıkça Sorulan Sorular - SimdiGetir')
@section('meta_description', 'SimdiGetir kurye hizmetleri hakkında sıkça sorulan sorular. Merak ettiklerinizi anında öğrenin!')
@section('meta_keywords', 'kurye sss, kurye sıkça sorulan sorular, moto kurye soru, kurye hizmeti bilgi, istanbul kurye bilgi')

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        {
            "@type": "Question",
            "name": "SimdiGetir.com hangi kurye hizmetlerini sunmaktadır?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Motorlu kurye, acil kurye ve araçlı kurye hizmetleri sunuyoruz. Gönderiniz için en uygun kurye tipini ve rotayı belirleriz."
            }
        },
        {
            "@type": "Question",
            "name": "Çalışma saatleriniz nedir?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "7 gün 24 saat aktif hizmet veriyoruz! Gece veya gündüz fark etmeksizin kurye hizmetlerimizden yararlanabilirsiniz."
            }
        },
        {
            "@type": "Question",
            "name": "Hangi bölgelerde kurye hizmeti sunuyorsunuz?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "İstanbul genelinde tüm ilçelere ve semtlere hizmet vermekteyiz. Akıllı rota optimizasyonumuz sayesinde en uzak noktalara bile hızlı teslimat sağlıyoruz."
            }
        },
        {
            "@type": "Question",
            "name": "Acil gönderilerimi nasıl hızlı teslim edebilirsiniz?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Acil gönderileri saniyeler içinde en yakın müsait kuryeye atarız. Akıllı rotalama ile trafik durumunu analiz eder ve en hızlı güzergahı belirleriz. En uzun mesafe gönderileri bile 3 saat içinde teslim edilir."
            }
        },
        {
            "@type": "Question",
            "name": "Büyük eşyalarımı nasıl gönderebilirim?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Araçlı kurye hizmetimiz ile otomobil veya minibüs kullanarak büyük eşyalarınızı güvenle taşıyoruz. Gönderi boyutuna göre uygun araç tipi önerilir."
            }
        },
        {
            "@type": "Question",
            "name": "Gönderi ücretleri nasıl hesaplanmaktadır?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Fiyatı mesafe, gönderi boyutu, aciliyet ve trafik durumuna göre şeffaf olarak hesaplıyoruz. Sürpriz masraf yoktur!"
            }
        },
        {
            "@type": "Question",
            "name": "Gönderilerimin güvenliğini nasıl sağlıyorsunuz?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Tüm kuryelerimiz titizlikle seçilmiş ve güvenlik taramalarından geçirilmiştir. Takip sistemimiz gönderinizi anlık olarak izler ve herhangi bir anormallikte sizi bilgilendirir. %99 başarılı teslimat oranımız bunun kanıtıdır."
            }
        },
        {
            "@type": "Question",
            "name": "Gönderi takibi yapabilir miyim?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Evet! Gerçek zamanlı takip sistemimiz sayesinde gönderinizin konumunu anlık olarak izleyebilirsiniz. Her aşamada SMS/bildirim alırsınız."
            }
        },
        {
            "@type": "Question",
            "name": "İstanbul dışına gönderi yapıyor musunuz?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Evet, şehirler arası gönderi hizmeti de sunuyoruz. En uygun rotayı ve taşıma yöntemini belirleriz. Detaylı bilgi için lütfen bizimle iletişime geçin."
            }
        },
        {
            "@type": "Question",
            "name": "Ödemeyi hangi yöntemlerle yapabilirim?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Nakit, kredi kartı veya banka transferi ile ödeme yapabilirsiniz. Kurumsal müşterilerimiz için aylık faturalama seçeneği de mevcuttur."
            }
        }
    ]
}
</script>
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero" style="min-height: auto; padding: 10rem 0 4rem;">
    <div class="container" style="text-align: center;">
        <div class="hero-badge animate__animated animate__fadeInUp">
            <span class="pulse"></span>
            Yardım Merkezi
        </div>
        <h1 class="animate__animated animate__fadeInUp animate__delay-1s" style="font-size: 3rem;">
            <span class="gradient-text">Sıkça Sorulan</span> Sorular
        </h1>
        <p class="animate__animated animate__fadeInUp animate__delay-2s" style="max-width: 600px; margin: 0 auto;">
            Kurye hizmetlerimiz hakkında merak ettiklerinizi öğrenin. Sorunuz cevaplanmadıysa bize ulaşın!
        </p>
    </div>
</section>

<!-- FAQ Section -->
<section class="section">
    <div class="container">
        <div style="max-width: 900px; margin: 0 auto;">
            <div class="faq-grid">
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <div class="faq-icon">📦</div>
                        <span>SimdiGetir.com hangi kurye hizmetlerini sunmaktadır?</span>
                        <i class="fa-solid fa-plus faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p>
                            <strong>Motorlu kurye</strong>, <strong>acil kurye</strong> ve <strong>araçlı kurye</strong> hizmetleri sunuyoruz. 
                            Gönderiniz için en uygun kurye tipini ve rotayı belirleriz.
                        </p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <div class="faq-icon">⏰</div>
                        <span>Çalışma saatleriniz nedir?</span>
                        <i class="fa-solid fa-plus faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p>
                            <strong>7 gün 24 saat</strong> aktif hizmet veriyoruz! Gece veya gündüz fark etmeksizin, 
                            kurye hizmetlerimizden yararlanabilirsiniz.
                        </p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <div class="faq-icon">📍</div>
                        <span>Hangi bölgelerde kurye hizmeti sunuyorsunuz?</span>
                        <i class="fa-solid fa-plus faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p>
                            <strong>İstanbul genelinde</strong> tüm ilçelere ve semtlere hizmet vermekteyiz. 
                            Akıllı rota optimizasyonumuz sayesinde en uzak noktalara bile hızlı teslimat sağlıyoruz.
                        </p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <div class="faq-icon">⚡</div>
                        <span>Acil gönderilerimi nasıl hızlı teslim edebilirsiniz?</span>
                        <i class="fa-solid fa-plus faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p>
                            Acil gönderileriniz <strong>saniyeler içinde</strong> en yakın müsait kuryeye atanır. 
                            Akıllı rotalama ile trafik durumunu analiz eder ve en hızlı güzergahı belirler. 
                            En uzun mesafe gönderileri bile <strong>3 saat içinde</strong> teslim edilir.
                        </p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <div class="faq-icon">📦</div>
                        <span>Büyük eşyalarımı nasıl gönderebilirim?</span>
                        <i class="fa-solid fa-plus faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p>
                            <strong>Araçlı kurye</strong> hizmetimiz ile otomobil veya minibüs kullanarak büyük eşyalarınızı güvenle taşıyoruz. 
                            Gönderi boyutuna göre uygun araç tipi önerilir.
                        </p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <div class="faq-icon">💰</div>
                        <span>Gönderi ücretleri nasıl hesaplanmaktadır?</span>
                        <i class="fa-solid fa-plus faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p>
                            Fiyatı <strong>mesafe</strong>, <strong>gönderi boyutu</strong>, 
                            <strong>aciliyet</strong> ve <strong>trafik durumu</strong>na göre şeffaf fiyatlandırma sunar. 
                            Sürpriz masraf yoktur!
                        </p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <div class="faq-icon">🔒</div>
                        <span>Gönderilerimin güvenliğini nasıl sağlıyorsunuz?</span>
                        <i class="fa-solid fa-plus faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p>
                            Tüm kuryelerimiz <strong>titizlikle seçilmiş</strong> ve güvenlik taramalarından geçirilmiştir. 
                            Takip sistemimiz gönderinizi <strong>anlık olarak</strong> izler ve herhangi bir anormallikte sizi bilgilendirir. 
                            <strong>%99 başarılı teslimat</strong> oranımız bunun kanıtıdır.
                        </p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <div class="faq-icon">📱</div>
                        <span>Gönderim takibi yapabilir miyim?</span>
                        <i class="fa-solid fa-plus faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p>
                            Evet! <strong>Gerçek zamanlı takip</strong> sistemimiz sayesinde gönderinizin konumunu 
                            anlık olarak izleyebilirsiniz. Her aşamada <strong>SMS/bildirim</strong> alırsınız.
                        </p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <div class="faq-icon">🌍</div>
                        <span>İstanbul dışına gönderi yapıyor musunuz?</span>
                        <i class="fa-solid fa-plus faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p>
                            Evet, <strong>şehirler arası</strong> gönderi hizmeti de sunuyoruz. 
                            En uygun rotayı ve taşıma yöntemini belirleriz. 
                            Detaylı bilgi için lütfen bizimle iletişime geçin.
                        </p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <div class="faq-icon">💳</div>
                        <span>Ödemeyi hangi yöntemlerle yapabilirim?</span>
                        <i class="fa-solid fa-plus faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p>
                            <strong>Nakit</strong>, <strong>kredi kartı</strong> veya <strong>banka transferi</strong> ile ödeme yapabilirsiniz. 
                            Kurumsal müşterilerimiz için aylık faturalama seçeneği de mevcuttur.
                        </p>
                    </div>
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
                <h2>Başka <span class="gradient-text">Sorularınız mı Var?</span></h2>
                <p>
                    Müşteri temsilcilerimiz size yardımcı olmaya hazır!
                </p>
                <div class="cta-buttons">
                    <a href="tel:+905324847292" class="btn btn-accent">
                        <i class="fa-solid fa-phone"></i> 0532 484 72 92
                    </a>
                    <a href="/iletisim" class="btn btn-outline">
                        <i class="fa-solid fa-envelope"></i> İletişime Geçin
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .faq-grid {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .faq-item {
        background: var(--bg-glass);
        border: 1px solid var(--border-glass);
        border-radius: 1rem;
        overflow: hidden;
        transition: all 0.4s ease;
    }
    
    .faq-item:hover {
        border-color: var(--primary);
        box-shadow: 0 0 30px rgba(124, 58, 237, 0.15);
    }
    
    .faq-question {
        padding: 1.5rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .faq-question span {
        flex: 1;
    }
    
    .faq-icon {
        width: 45px;
        height: 45px;
        background: var(--gradient-primary);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    
    .faq-toggle {
        width: 35px;
        height: 35px;
        background: var(--bg-glass);
        border: 1px solid var(--border-glass);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: var(--accent);
        transition: all 0.3s ease;
    }
    
    .faq-item.active .faq-toggle {
        transform: rotate(45deg);
        background: var(--gradient-primary);
        border-color: var(--primary);
        color: white;
    }
    
    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease, padding 0.4s ease;
    }
    
    .faq-item.active .faq-answer {
        max-height: 500px;
        padding: 0 1.5rem 1.5rem 5rem;
    }
    
    .faq-answer p {
        color: var(--text-secondary);
        line-height: 1.8;
    }
    
    .faq-answer strong {
        color: var(--text-primary);
    }
</style>
@endpush

@push('scripts')
<script>
    function toggleFaq(element) {
        const faqItem = element.parentElement;
        const isActive = faqItem.classList.contains('active');
        
        // Close all
        document.querySelectorAll('.faq-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Open clicked if wasn't active
        if (!isActive) {
            faqItem.classList.add('active');
        }
    }
    
    // Open first FAQ by default
    document.addEventListener('DOMContentLoaded', () => {
        const firstFaq = document.querySelector('.faq-item');
        if (firstFaq) firstFaq.classList.add('active');
    });
</script>
@endpush
