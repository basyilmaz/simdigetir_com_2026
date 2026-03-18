<x-checkout::layouts.master title="Siparise Basla" description="SimdiGetir siparis baslangic ekrani">
@php
    $pageCopy = is_array($pageCopy ?? null) ? $pageCopy : [];
    $support = is_array($support ?? null) ? $support : [];
@endphp

@push('styles')
<style>
body{margin:0;background:var(--sg-surface-page-light);color:var(--sg-ink-light);font-family:var(--sg-font-body)}.shell{width:min(1100px,calc(100% - 32px));margin:0 auto;padding:28px 0 44px}.top{display:flex;justify-content:space-between;gap:16px;align-items:center;margin-bottom:20px;flex-wrap:wrap}.brand{display:inline-flex;align-items:center;gap:12px;color:inherit;text-decoration:none;font-weight:800}.brand b{width:42px;height:42px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;background:var(--sg-brand-gradient);color:#fff;font-family:var(--sg-font-display)}.card{border:1px solid var(--sg-border-light);border-radius:24px;background:var(--sg-card-light);backdrop-filter:blur(14px);box-shadow:var(--sg-shadow-light)}.hero,.grid section{padding:24px}.hero h1{margin:0 0 10px;font-family:var(--sg-font-display);font-size:var(--sg-type-display-lg)}.hero p,.muted,.item p,.support-note,.support-links small{color:var(--sg-muted-light)}.grid{display:grid;grid-template-columns:1.15fr .85fr;gap:20px;margin-top:20px}.actions,.list,.support-links{display:grid;gap:12px}.btn{display:inline-flex;align-items:center;justify-content:center;min-height:52px;padding:0 18px;border-radius:16px;border:0;background:var(--sg-action-gradient);color:#fff;font:inherit;font-weight:800;cursor:pointer;text-decoration:none}.btn.secondary{background:var(--sg-card-light-strong);color:var(--sg-ink-light);border:1px solid var(--sg-border-light)}.item{padding:16px;border-radius:18px;border:1px solid var(--sg-border-light-soft);background:var(--sg-card-light-soft)}.item strong{display:block;margin-bottom:6px}.eyebrow{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:999px;background:var(--sg-card-brand-soft-light);color:var(--sg-accent-warm-text);font-size:var(--sg-type-caption);font-weight:800;margin-bottom:14px}.support-note{margin-top:14px;font-size:var(--sg-type-body-sm)}.support-links a{display:inline-flex;align-items:center;justify-content:center;min-height:46px;padding:0 16px;border-radius:14px;border:1px solid var(--sg-border-light);background:#fff;color:var(--sg-link-warm);font-weight:800;text-decoration:none}.support-links small{display:block;margin-top:-4px}.trust-list{display:grid;gap:10px;margin-top:14px}.trust-pill{display:flex;align-items:center;gap:10px;padding:12px 14px;border-radius:16px;background:var(--sg-card-light-soft);border:1px solid var(--sg-border-light-soft);font-size:var(--sg-type-body-sm)}@media (max-width:980px){.grid{grid-template-columns:1fr}}
</style>
@endpush

<div class="shell">
  <div class="top">
    <a href="{{ route('home') }}" class="brand"><b>SG</b><span>SimdiGetir Siparis</span></a>
    <div class="muted">{{ $support['support_note'] ?? 'Siparis oncesi veya sonrasinda destek almak icin iletisim kanallarimiz her zaman acik.' }}</div>
  </div>

  <section class="card hero">
    <span class="eyebrow">Guvenli baslangic</span>
    <h1>Siparise Basla</h1>
    <p>{{ $pageCopy['intro'] ?? 'Adres bilgilerinizi girerek teklif alin, uygun oldugunuzda guvenli checkout akisina devam edin.' }}</p>
    <div class="actions" style="margin-top:18px">
      <a href="{{ route('home') }}" class="btn">Ana Sayfada Fiyat Hesapla</a>
      <a href="{{ route('checkout.customer.register') }}" class="btn secondary">Hesap Olustur</a>
    </div>
    <p class="support-note">{{ $pageCopy['help'] ?? 'Daha once hesap olusturduysaniz mevcut siparislerinizi ve takip linklerinizi Hesabim ekranindan gorebilirsiniz.' }}</p>
  </section>

  <div class="grid">
    <section class="card">
      <div class="list">
        <article class="item">
          <strong>1. Teklifinizi alin</strong>
          <p>Ana sayfada alis ve teslimat adresini girin, tahmini fiyat ve sureyi aninda gorun.</p>
        </article>
        <article class="item">
          <strong>2. Hesabinizi baglayin</strong>
          <p>Dilerseniz hesap olusturarak siparis gecmisinizi, odeme durumunu ve takip linklerini tek ekranda tutun.</p>
        </article>
        <article class="item">
          <strong>3. Siparisi tamamlayin</strong>
          <p>Gonderen ve alici bilgilerini dogrulayin, odeme yonteminizi secin ve siparisi onaylayin.</p>
        </article>
      </div>
    </section>
    <section class="card">
      <div class="list">
        <article class="item">
          <strong>Hizli erisim</strong>
          <p>Kayitli musteriyseniz hesabiniza girin ya da acik siparisiniz varsa takip ekranina gidin.</p>
        </article>
        <div class="support-links">
          <a href="{{ route('checkout.customer.login') }}">Musteri Girisi</a>
          <a href="{{ route('checkout.tracking') }}">Siparis Takip</a>
          <a href="{{ $support['phone_href'] ?? 'tel:+905513567292' }}">{{ $support['phone_display'] ?? '+90 551 356 72 92' }}</a>
          <small>Telefon, WhatsApp veya e-posta ile destek alabilirsiniz.</small>
        </div>
        <div class="trust-list" aria-label="Guven gostergeleri">
          <div class="trust-pill">Canli destek: <a href="{{ $support['whatsapp_href'] ?? 'https://wa.me/905513567292' }}" target="_blank" rel="noopener">WhatsApp</a></div>
          <div class="trust-pill">E-posta: <a href="{{ $support['email_href'] ?? 'mailto:webgetir@simdigetir.com' }}">{{ $support['email'] ?? 'webgetir@simdigetir.com' }}</a></div>
          <div class="trust-pill"><a href="{{ $support['privacy_href'] ?? url('/kvkk') }}">KVKK</a> ve <a href="{{ $support['terms_href'] ?? url('/kullanim-kosullari') }}">kullanim kosullari</a> baglantilari her zaman acik.</div>
        </div>
      </div>
    </section>
  </div>
</div>
</x-checkout::layouts.master>
