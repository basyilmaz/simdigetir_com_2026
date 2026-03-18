<x-checkout::layouts.public title="Siparise Basla" description="SimdiGetir siparis baslangic ekrani">
@php
    $pageCopy = is_array($pageCopy ?? null) ? $pageCopy : [];
    $support = is_array($support ?? null) ? $support : [];
@endphp

@push('styles')
<style>
    .checkout-entry-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.95fr);
        gap: 20px;
    }

    .checkout-entry-steps,
    .checkout-entry-links,
    .checkout-entry-trust {
        display: grid;
        gap: 12px;
    }

    .checkout-entry-step,
    .checkout-entry-trust-item {
        padding: 16px 18px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    [data-theme="light"] .checkout-entry-step,
    [data-theme="light"] .checkout-entry-trust-item {
        background: rgba(255, 255, 255, 0.84);
        border-color: rgba(15, 23, 42, 0.06);
    }

    .checkout-entry-step strong,
    .checkout-entry-trust-item strong {
        display: block;
        margin-bottom: 6px;
        font-size: 1rem;
    }

    .checkout-entry-links a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 48px;
        padding: 0 16px;
        border-radius: 14px;
        border: 1px solid var(--border-glass);
        background: rgba(255, 255, 255, 0.04);
        color: var(--text-primary);
        font-weight: 700;
        text-decoration: none;
    }

    [data-theme="light"] .checkout-entry-links a {
        background: rgba(255, 255, 255, 0.88);
        border-color: rgba(15, 23, 42, 0.08);
    }

    .checkout-entry-links a:hover {
        color: var(--accent);
    }

    @media (max-width: 1080px) {
        .checkout-entry-grid {
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
                    <i class="fa-solid fa-bolt"></i> Guvenli baslangic
                </div>
                <div class="checkout-meta">
                    <span class="checkout-chip">Landing ile ayni shell</span>
                    <span class="checkout-chip checkout-chip--info">Quote -> checkout gecisi icin hazir</span>
                </div>
                <h1>Siparise basla.</h1>
                <p>{{ $pageCopy['intro'] ?? 'Adres bilgilerinizi girerek teklif alin, uygun oldugunuzda guvenli checkout akisina devam edin.' }}</p>
            </div>

            <div class="checkout-actions" style="margin-top:18px;">
                <a href="{{ route('home') }}" class="btn btn-primary">Ana sayfada fiyat hesapla</a>
                <a href="{{ route('checkout.customer.register') }}" class="btn btn-outline">Hesap olustur</a>
            </div>

            <div class="checkout-alert checkout-alert--info" style="margin-top:16px;">
                {{ $pageCopy['help'] ?? 'Daha once hesap olusturduysaniz mevcut siparislerinizi ve takip linklerinizi Hesabim ekranindan gorebilirsiniz.' }}
            </div>
        </article>

        <aside class="checkout-card checkout-card--support">
            <div class="checkout-lead" style="gap:10px;">
                <div class="section-badge">
                    <i class="fa-solid fa-headset"></i> Destek ve hizli baglantilar
                </div>
                <h2>Checkout oncesi yardim kanallari</h2>
                <p>{{ $support['support_note'] ?? 'Siparis oncesi veya sonrasinda destek almak icin iletisim kanallarimiz her zaman acik.' }}</p>
            </div>

            <div class="checkout-list" style="margin-top:18px;">
                <div class="checkout-list-item">
                    <strong>Hizli erisim</strong>
                    <div class="checkout-entry-links">
                        <a href="{{ route('checkout.customer.login') }}">Musteri girisi</a>
                        <a href="{{ route('checkout.tracking') }}">Siparis takip</a>
                        <a href="{{ $support['phone_href'] ?? 'tel:+905513567292' }}">{{ $support['phone_display'] ?? '+90 551 356 72 92' }}</a>
                    </div>
                </div>
                <div class="checkout-list-item">
                    <strong>Canli destek</strong>
                    <div class="checkout-entry-links">
                        <a href="{{ $support['whatsapp_href'] ?? 'https://wa.me/905513567292' }}" target="_blank" rel="noopener">WhatsApp</a>
                        <a href="{{ $support['email_href'] ?? 'mailto:webgetir@simdigetir.com' }}">{{ $support['email'] ?? 'webgetir@simdigetir.com' }}</a>
                        <a href="{{ $support['contact_href'] ?? route('contact') }}">Iletisim sayfasi</a>
                    </div>
                </div>
            </div>
        </aside>
    </section>

    <section class="checkout-card checkout-card--panel">
        <div class="checkout-panel-head">
            <div>
                <h2>Akis ozeti</h2>
                <p>Landing quote sonucu sonrasinda kullanicinin dead-end'e dusmeden ilerleyecegi cekirdek checkout baslangici.</p>
            </div>
        </div>

        <div class="checkout-entry-grid">
            <div class="checkout-entry-steps">
                <article class="checkout-entry-step">
                    <strong>1. Teklifinizi alin</strong>
                    <p>Ana sayfada alis ve teslimat adresini girin, tahmini fiyat ve sureyi aninda gorun.</p>
                </article>
                <article class="checkout-entry-step">
                    <strong>2. Hesabinizi baglayin</strong>
                    <p>Dilerseniz hesap olusturarak siparis gecmisinizi, odeme durumunu ve takip linklerini tek ekranda tutun.</p>
                </article>
                <article class="checkout-entry-step">
                    <strong>3. Siparisi tamamlayin</strong>
                    <p>Gonderen ve alici bilgilerini dogrulayin, odeme yonteminizi secin ve siparisi onaylayin.</p>
                </article>
            </div>

            <div class="checkout-entry-trust">
                <article class="checkout-entry-trust-item">
                    <strong>Yasal guvence</strong>
                    <p><a href="{{ $support['privacy_href'] ?? url('/kvkk') }}">KVKK</a> ve <a href="{{ $support['terms_href'] ?? url('/kullanim-kosullari') }}">kullanim kosullari</a> baglantilari her zaman acik.</p>
                </article>
                <article class="checkout-entry-trust-item">
                    <strong>Takip ve panel</strong>
                    <p>Kayitli musteriyseniz Hesabim ekranindan onceki siparislerinizi, acik siparisiniz varsa takip ekranindan anlik durumu gorebilirsiniz.</p>
                </article>
            </div>
        </div>
    </section>
</div>
</x-checkout::layouts.public>
