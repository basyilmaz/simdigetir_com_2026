<x-checkout::layouts.public title="Musteri Girisi" description="SimdiGetir musteri paneli giris ekrani">
@php
    $pageCopy = is_array($pageCopy ?? null) ? $pageCopy : [];
    $support = is_array($support ?? null) ? $support : [];
@endphp

@push('styles')
<style>
    .customer-auth-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.08fr) minmax(320px, 0.92fr);
        gap: 20px;
    }

    .customer-auth-form {
        display: grid;
        gap: 14px;
    }

    .customer-auth-form .btn {
        width: 100%;
    }

    .customer-auth-actions {
        display: grid;
        gap: 10px;
    }

    .customer-auth-actions a {
        color: var(--text-primary);
        text-decoration: none;
        font-weight: 700;
    }

    .customer-auth-actions a:hover {
        color: var(--accent);
    }

    @media (max-width: 1080px) {
        .customer-auth-grid {
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
                    <i class="fa-solid fa-user-shield"></i> Kayitli musteriler icin
                </div>
                <div class="checkout-meta">
                    <span class="checkout-chip">Telefon + sifre ile giris</span>
                    <span class="checkout-chip checkout-chip--info">Siparis, odeme ve takip linkleri ayni panelde</span>
                </div>
                <h1>Hesabiniza girin, aktif siparislerinizi yonetin.</h1>
                <p>{{ $pageCopy['intro'] ?? 'Kayitli telefon numaraniz ve sifrenizle siparislerinizi, odeme durumunu ve takip linklerini goruntuleyin.' }}</p>
            </div>
        </article>

        <aside class="checkout-card checkout-card--support">
            <div class="checkout-lead" style="gap:10px;">
                <div class="section-badge">
                    <i class="fa-solid fa-headset"></i> Destek
                </div>
                <h2>Giris sorunu yasarsaniz hizli yardim alin.</h2>
                <p>{{ $support['support_note'] ?? 'Destek ekibimiz telefon, WhatsApp veya e-posta uzerinden yardimci olur.' }}</p>
            </div>

            <div class="checkout-list" style="margin-top:18px;">
                <div class="checkout-list-item">
                    <strong>Hizli yardim kanallari</strong>
                    <div class="customer-auth-actions">
                        <a href="{{ route('checkout.tracking') }}">Siparis Takip Ekrani</a>
                        <a href="{{ $support['phone_href'] ?? 'tel:+905513567292' }}">{{ $support['phone_display'] ?? '+90 551 356 72 92' }}</a>
                        <a href="{{ $support['whatsapp_href'] ?? 'https://wa.me/905513567292' }}" target="_blank" rel="noopener">WhatsApp ile Yardim Al</a>
                        <a href="{{ route('contact') }}">Iletisim Sayfasi</a>
                    </div>
                </div>
                <div class="checkout-list-item">
                    <strong>Guven notu</strong>
                    <p>{{ $pageCopy['help'] ?? 'Sifrenizi hatirlamiyorsaniz destek ekibimiz telefon numaranizi dogrulayarak size yardimci olur.' }}</p>
                </div>
            </div>
        </aside>
    </section>

    <section class="checkout-card checkout-card--panel">
        <div class="tracking-card-header">
            <div>
                <h2>Musteri Girisi</h2>
                <p>Kayitli hesabinizla panele gecin, siparislerinizi ve onceki hareketlerinizi gorun.</p>
            </div>
        </div>

        <div class="customer-auth-grid">
            <div>
                @if (session('status'))
                    <div class="checkout-alert checkout-alert--success" style="margin-bottom:14px;">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="checkout-alert checkout-alert--error" style="margin-bottom:14px;">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('checkout.customer.login.submit') }}" class="customer-auth-form">
                    @csrf
                    <div class="checkout-field">
                        <label for="portal-phone">Telefon</label>
                        <input id="portal-phone" name="phone" type="tel" inputmode="tel" value="{{ old('phone') }}" placeholder="0551 356 72 92" autocomplete="tel" required>
                    </div>
                    <div class="checkout-field">
                        <label for="portal-password">Sifre</label>
                        <input id="portal-password" name="password" type="password" placeholder="Sifreniz" autocomplete="current-password" required>
                    </div>
                    <div class="checkout-actions">
                        <button type="submit" class="btn btn-primary">Panele Gir</button>
                        <a href="{{ route('checkout.customer.register') }}" class="btn btn-outline">Yeni hesap olustur</a>
                    </div>
                </form>
            </div>

            <div class="checkout-list">
                <div class="checkout-list-item">
                    <strong>Panelde neler var?</strong>
                    <p>Aktif siparislerinizi, odeme bilgisini ve takip linklerini tek panelde gorebilirsiniz.</p>
                </div>
                <div class="checkout-list-item">
                    <strong>Hesabiniz yoksa</strong>
                    <p>Yeni bir musteri hesabi olusturup ayni hesapla checkout ve Hesabim ekranlarina devam edebilirsiniz.</p>
                </div>
                <div class="checkout-list-item">
                    <strong>Yasal baglantilar</strong>
                    <div class="customer-auth-actions">
                        <a href="{{ $support['privacy_href'] ?? url('/kvkk') }}">KVKK</a>
                        <a href="{{ $support['terms_href'] ?? url('/kullanim-kosullari') }}">Kullanim kosullari</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
</x-checkout::layouts.public>
