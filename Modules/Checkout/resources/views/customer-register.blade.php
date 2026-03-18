<x-checkout::layouts.public title="Musteri Kaydi" description="SimdiGetir musteri hesap olusturma ekrani">
@php
    $pageCopy = is_array($pageCopy ?? null) ? $pageCopy : [];
    $support = is_array($support ?? null) ? $support : [];
@endphp

@push('styles')
<style>
    .customer-register-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.95fr);
        gap: 20px;
    }

    .customer-register-form {
        display: grid;
        gap: 14px;
    }

    .customer-register-form .btn {
        width: 100%;
    }

    .customer-register-links {
        display: grid;
        gap: 10px;
    }

    .customer-register-links a {
        color: var(--text-primary);
        text-decoration: none;
        font-weight: 700;
    }

    .customer-register-links a:hover {
        color: var(--accent);
    }

    @media (max-width: 1080px) {
        .customer-register-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

<div class="checkout-shell">
    <section class="checkout-hero-grid">
        <article class="checkout-card checkout-card--hero">
            <div class="checkout-lead">
                <div class="section-badge">
                    <i class="fa-solid fa-user-plus"></i> Hizli kayit
                </div>
                <div class="checkout-meta">
                    <span class="checkout-chip">Tek hesapla devam edin</span>
                    <span class="checkout-chip checkout-chip--info">Checkout, takip ve Hesabim ayni dilde</span>
                </div>
                <h1>Bir hesap olusturun, sonraki siparislerde hizlanin.</h1>
                <p>{{ $pageCopy['intro'] ?? 'Hizli siparis, takip linkleri ve siparis gecmisi icin bir hesap olusturun.' }}</p>
            </div>
        </article>

        <aside class="checkout-card checkout-card--support">
            <div class="checkout-lead" style="gap:10px;">
                <div class="section-badge">
                    <i class="fa-solid fa-shield-halved"></i> Guven ve destek
                </div>
                <h2>Kisa form, net onay, destek ekibi her zaman ulasilabilir.</h2>
                <p>{{ $support['support_note'] ?? 'Destek ekibimiz telefon, WhatsApp veya e-posta uzerinden yardimci olur.' }}</p>
            </div>

            <div class="checkout-list" style="margin-top:18px;">
                <div class="checkout-list-item">
                    <strong>Kayit sonrasi avantajlar</strong>
                    <p>{{ $pageCopy['help'] ?? 'Kayit olduktan sonra ayni hesapla checkout ve Hesabim ekranlarini kullanabilirsiniz.' }}</p>
                </div>
                <div class="checkout-list-item">
                    <strong>Yardim kanallari</strong>
                    <div class="customer-register-links">
                        <a href="{{ $support['phone_href'] ?? 'tel:+905513567292' }}">{{ $support['phone_display'] ?? '+90 551 356 72 92' }}</a>
                        <a href="{{ $support['whatsapp_href'] ?? 'https://wa.me/905513567292' }}" target="_blank" rel="noopener">WhatsApp ile Yardim Al</a>
                        <a href="{{ route('contact') }}">Iletisim Sayfasi</a>
                    </div>
                </div>
            </div>
        </aside>
    </section>

    <section class="checkout-card checkout-card--panel">
        <div class="tracking-card-header">
            <div>
                <h2>Musteri Kaydi</h2>
                <p>Temel bilgilerinizle hesabinizi acin. Sonraki siparis ve takip akislarinda ayni hesap kullanilir.</p>
            </div>
        </div>

        <div class="customer-register-grid">
            <div>
                @if (session('status'))
                    <div class="checkout-alert checkout-alert--success" style="margin-bottom:14px;">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="checkout-alert checkout-alert--error" style="margin-bottom:14px;">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('checkout.customer.register.submit') }}" class="customer-register-form">
                    @csrf
                    <div class="checkout-field">
                        <label for="register-name">Ad Soyad</label>
                        <input id="register-name" name="name" type="text" value="{{ old('name') }}" placeholder="Orn: Ayse Yilmaz" autocomplete="name" required>
                    </div>
                    <div class="checkout-field">
                        <label for="register-phone">Telefon</label>
                        <input id="register-phone" name="phone" type="tel" inputmode="tel" value="{{ old('phone') }}" placeholder="0551 356 72 92" autocomplete="tel" required>
                    </div>
                    <div class="checkout-field">
                        <label for="register-email">E-posta (opsiyonel)</label>
                        <input id="register-email" name="email" type="email" value="{{ old('email') }}" placeholder="ornek@alanadi.com" autocomplete="email">
                    </div>
                    <div class="checkout-field">
                        <label for="register-password">Sifre</label>
                        <input id="register-password" name="password" type="password" placeholder="En az 8 karakter" autocomplete="new-password" required>
                    </div>
                    <div class="checkout-field">
                        <label for="register-password-confirmation">Sifre Tekrar</label>
                        <input id="register-password-confirmation" name="password_confirmation" type="password" placeholder="Sifreyi tekrar girin" autocomplete="new-password" required>
                    </div>

                    <div class="checkout-checkbox">
                        <input id="register-legal-acceptance" name="legal_acceptance" type="checkbox" value="1" {{ old('legal_acceptance') ? 'checked' : '' }} required>
                        <label for="register-legal-acceptance">
                            <a href="{{ $support['privacy_href'] ?? url('/kvkk') }}" target="_blank" rel="noopener">KVKK metnini</a> ve
                            <a href="{{ $support['terms_href'] ?? url('/kullanim-kosullari') }}" target="_blank" rel="noopener">kullanim kosullarini</a>
                            okudum, kabul ediyorum.
                        </label>
                    </div>

                    <p class="checkout-note">Kayit olduktan sonra ayni hesapla siparis baslatabilir ve siparislerinizi takip edebilirsiniz.</p>

                    <div class="checkout-actions">
                        <button type="submit" class="btn btn-primary">Hesap Olustur</button>
                        <a href="{{ route('checkout.customer.login') }}" class="btn btn-outline">Mevcut Hesapla Giris Yap</a>
                    </div>
                </form>
            </div>

            <div class="checkout-list">
                <div class="checkout-list-item">
                    <strong>Tek hesaptan devam edin</strong>
                    <p>Kayit olduktan sonra checkout, siparis takibi ve Hesabim ekranlari ayni profil uzerinden devam eder.</p>
                </div>
                <div class="checkout-list-item">
                    <strong>Guven notu</strong>
                    <p>Sadece siparis surecinde gerekli temel bilgiler istenir. Gerektiginde destek ekibine dogrudan ulasabilirsiniz.</p>
                </div>
                <div class="checkout-list-item">
                    <strong>Hizli baglantilar</strong>
                    <div class="customer-register-links">
                        <a href="{{ route('checkout.index') }}">Siparise Basla</a>
                        <a href="{{ route('checkout.tracking') }}">Siparis Takip</a>
                        <a href="{{ route('contact') }}">Iletisim Sayfasi</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
</x-checkout::layouts.public>
