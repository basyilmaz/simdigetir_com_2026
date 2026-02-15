@extends('layouts.landing')

@section('title', 'KVKK Aydınlatma Metni - SimdiGetir Kurye')
@section('meta_description', 'SimdiGetir Kişisel Verilerin Korunması Kanunu (KVKK) kapsamında aydınlatma metni.')

@section('content')
<!-- Hero Section -->
<section class="hero" style="min-height: auto; padding: 10rem 0 4rem;">
    <div class="container" style="text-align: center;">
        <div class="hero-badge animate__animated animate__fadeInUp">
            <span class="pulse"></span>
            Yasal Bilgilendirme
        </div>
        <h1 class="animate__animated animate__fadeInUp animate__delay-1s" style="font-size: 3rem;">
            <span class="gradient-text">KVKK</span> Aydınlatma Metni
        </h1>
        <p class="animate__animated animate__fadeInUp animate__delay-2s" style="max-width: 600px; margin: 0 auto;">
            Kişisel verilerinizin güvenliği bizim için önemlidir. Verilerinizi en yüksek güvenlik standartlarıyla koruruz.
        </p>
    </div>
</section>

<!-- KVKK Content Section -->
<section class="section">
    <div class="container">
        <div style="max-width: 900px; margin: 0 auto;">
            <div class="glass kvkk-content" style="padding: 3rem;">
                <div class="kvkk-update-badge">
                    <i class="fa-solid fa-calendar"></i> Son Güncelleme: {{ date('d.m.Y') }}
                </div>

                <div class="kvkk-section">
                    <h2><span class="kvkk-section-number">01</span> Veri Sorumlusu</h2>
                    <p>
                        SimdiGetir Kurye Hizmetleri ("SimdiGetir" veya "Şirket") olarak, 6698 sayılı Kişisel Verilerin Korunması Kanunu ("KVKK") kapsamında veri sorumlusu sıfatıyla kişisel verilerinizi işlemekteyiz.
                    </p>
                </div>

                <div class="kvkk-section">
                    <h2><span class="kvkk-section-number">02</span> Hangi Kişisel Veriler İşlenmektedir?</h2>
                    <p>Aşağıdaki kategorilerde kişisel verileriniz işlenmektedir:</p>
                    <ul class="kvkk-list">
                        <li>
                            <i class="fa-solid fa-shield-halved"></i>
                            <div>
                                <strong>Kimlik Bilgileri:</strong> Ad, soyad
                            </div>
                        </li>
                        <li>
                            <i class="fa-solid fa-shield-halved"></i>
                            <div>
                                <strong>İletişim Bilgileri:</strong> Telefon numarası, e-posta adresi
                            </div>
                        </li>
                        <li>
                            <i class="fa-solid fa-shield-halved"></i>
                            <div>
                                <strong>Firma Bilgileri:</strong> Şirket adı, unvanı (kurumsal başvurularda)
                            </div>
                        </li>
                        <li>
                            <i class="fa-solid fa-shield-halved"></i>
                            <div>
                                <strong>Konum Bilgileri:</strong> Teslimat adresi, alım adresi
                            </div>
                        </li>
                        <li>
                            <i class="fa-solid fa-shield-halved"></i>
                            <div>
                                <strong>Talep/Şikayet İçeriği:</strong> Formlar aracılığıyla ilettiğiniz mesajlar
                            </div>
                        </li>
                        <li>
                            <i class="fa-solid fa-shield-halved"></i>
                            <div>
                                <strong>Dijital İzler:</strong> IP adresi, sayfa görüntüleme verileri, UTM parametreleri
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="kvkk-section">
                    <h2><span class="kvkk-section-number">03</span> Kişisel Verilerin İşlenme Amaçları</h2>
                    <p>Kişisel verileriniz aşağıdaki amaçlarla işlenmektedir:</p>
                    <ul class="kvkk-list">
                        <li>
                            <i class="fa-solid fa-check-circle" style="color: var(--accent);"></i>
                            <span>Kurye ve teslimat hizmetlerinin sunulması</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-check-circle" style="color: var(--accent);"></i>
                            <span>Müşteri taleplerinin karşılanması ve iletişim faaliyetlerinin yürütülmesi</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-check-circle" style="color: var(--accent);"></i>
                            <span>Kurumsal teklif hazırlama ve satış süreçlerinin yönetimi</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-check-circle" style="color: var(--accent);"></i>
                            <span>Kurye başvuru süreçlerinin değerlendirilmesi</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-check-circle" style="color: var(--accent);"></i>
                            <span>Hizmet kalitesinin artırılması</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-check-circle" style="color: var(--accent);"></i>
                            <span>Yasal yükümlülüklerin yerine getirilmesi</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-check-circle" style="color: var(--accent);"></i>
                            <span>Pazarlama ve analiz faaliyetlerinin yürütülmesi (rızanız dahilinde)</span>
                        </li>
                    </ul>
                </div>

                <div class="kvkk-section">
                    <h2><span class="kvkk-section-number">04</span> Kişisel Verilerin Aktarımı</h2>
                    <p>
                        Kişisel verileriniz, yukarıda belirtilen amaçların gerçekleştirilmesi doğrultusunda; iş ortaklarımıza, tedarikçilerimize, hizmet aldığımız üçüncü taraflara, yetkili kamu kurum ve kuruluşlarına KVKK'nın 8. ve 9. maddelerinde belirtilen kişisel veri işleme şartları çerçevesinde aktarılabilecektir.
                    </p>
                </div>

                <div class="kvkk-section">
                    <h2><span class="kvkk-section-number">05</span> Kişisel Verilerin Toplanma Yöntemi ve Hukuki Sebebi</h2>
                    <p>
                        Kişisel verileriniz; web sitemiz üzerindeki formlar, telefon görüşmeleri, e-posta yazışmaları ve benzeri yollarla toplanmaktadır. Bu veriler KVKK'nın 5. maddesinde yer alan hukuki sebeplere dayanarak işlenmektedir.
                    </p>
                </div>

                <div class="kvkk-section">
                    <h2><span class="kvkk-section-number">06</span> Veri Sahibinin Hakları</h2>
                    <p>KVKK'nın 11. maddesi uyarınca aşağıdaki haklara sahipsiniz:</p>
                    <ul class="kvkk-list">
                        <li>
                            <i class="fa-solid fa-circle-info" style="color: var(--primary-light);"></i>
                            <span>Kişisel veri işlenip işlenmediğini öğrenme</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle-info" style="color: var(--primary-light);"></i>
                            <span>Kişisel veriler işlenmişse buna ilişkin bilgi talep etme</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle-info" style="color: var(--primary-light);"></i>
                            <span>Kişisel verilerin işlenme amacını ve bunların amacına uygun kullanılıp kullanılmadığını öğrenme</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle-info" style="color: var(--primary-light);"></i>
                            <span>Yurt içinde veya yurt dışında kişisel verilerin aktarıldığı üçüncü kişileri bilme</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle-info" style="color: var(--primary-light);"></i>
                            <span>Kişisel verilerin eksik veya yanlış işlenmiş olması halinde bunların düzeltilmesini isteme</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle-info" style="color: var(--primary-light);"></i>
                            <span>KVKK kapsamında öngörülen şartlar çerçevesinde kişisel verilerin silinmesini veya yok edilmesini isteme</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle-info" style="color: var(--primary-light);"></i>
                            <span>İşlenen verilerin münhasıran otomatik sistemler vasıtasıyla analiz edilmesi suretiyle aleyhinize bir sonucun ortaya çıkmasına itiraz etme</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle-info" style="color: var(--primary-light);"></i>
                            <span>Kişisel verilerin kanuna aykırı olarak işlenmesi sebebiyle zarara uğramanız halinde zararın giderilmesini talep etme</span>
                        </li>
                    </ul>
                </div>

                <div class="kvkk-section kvkk-contact">
                    <h2><span class="kvkk-section-number">07</span> İletişim</h2>
                    <p>KVKK kapsamındaki haklarınızı kullanmak için aşağıdaki iletişim bilgilerinden bize ulaşabilirsiniz:</p>
                    <div class="kvkk-contact-grid">
                        <div class="kvkk-contact-item">
                            <i class="fa-solid fa-envelope"></i>
                            <div>
                                <strong>E-posta</strong>
                                <span>{{ \Modules\Settings\Models\Setting::getValue('contact.email', 'kvkk@simdigetir.com') }}</span>
                            </div>
                        </div>
                        <div class="kvkk-contact-item">
                            <i class="fa-solid fa-phone"></i>
                            <div>
                                <strong>Telefon</strong>
                                <span>{{ \Modules\Settings\Models\Setting::getValue('contact.phone', '+90 532 484 72 92') }}</span>
                            </div>
                        </div>
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
                <h2>Sorularınız mı <span class="gradient-text">Var?</span></h2>
                <p>
                    KVKK ile ilgili tüm sorularınız için bizimle iletişime geçebilirsiniz.
                </p>
                <div class="cta-buttons">
                    <a href="/iletisim" class="btn btn-accent">
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
    .kvkk-content {
        position: relative;
        overflow: hidden;
    }

    .kvkk-update-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--bg-card);
        border: 1px solid var(--border-glass);
        border-radius: 2rem;
        padding: 0.5rem 1.25rem;
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-bottom: 2.5rem;
    }

    .kvkk-update-badge i {
        color: var(--accent);
    }

    .kvkk-section {
        margin-bottom: 2.5rem;
        padding-bottom: 2.5rem;
        border-bottom: 1px solid var(--border-glass);
    }

    .kvkk-section:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .kvkk-section h2 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: var(--text-primary);
    }

    .kvkk-section-number {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.875rem;
        font-weight: 700;
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        opacity: 0.6;
    }

    .kvkk-section p {
        color: var(--text-secondary);
        line-height: 1.9;
        font-size: 1rem;
    }

    .kvkk-section strong {
        color: var(--text-primary);
    }

    .kvkk-list {
        list-style: none;
        padding: 0;
        margin-top: 1rem;
    }

    .kvkk-list li {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        background: var(--bg-glass);
        border-radius: 0.75rem;
        margin-bottom: 0.5rem;
        color: var(--text-secondary);
        font-size: 0.95rem;
        line-height: 1.6;
        transition: all 0.3s ease;
    }

    .kvkk-list li:hover {
        background: var(--bg-card);
        border-color: var(--border-glow);
        transform: translateX(5px);
    }

    .kvkk-list li i {
        margin-top: 0.2rem;
        flex-shrink: 0;
        font-size: 0.9rem;
    }

    .kvkk-list li strong {
        color: var(--text-primary);
        display: block;
        margin-bottom: 0.15rem;
    }

    .kvkk-contact-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    .kvkk-contact-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        background: var(--bg-glass);
        border: 1px solid var(--border-glass);
        border-radius: 1rem;
        transition: all 0.4s ease;
    }

    .kvkk-contact-item:hover {
        border-color: var(--primary);
        box-shadow: 0 0 30px rgba(124, 58, 237, 0.15);
    }

    .kvkk-contact-item i {
        width: 45px;
        height: 45px;
        background: var(--gradient-primary);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .kvkk-contact-item strong {
        display: block;
        margin-bottom: 0.25rem;
        color: var(--text-primary);
        font-size: 0.9rem;
    }

    .kvkk-contact-item span {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .kvkk-content {
            padding: 1.5rem !important;
        }

        .kvkk-section h2 {
            font-size: 1.25rem;
        }

        .kvkk-contact-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
