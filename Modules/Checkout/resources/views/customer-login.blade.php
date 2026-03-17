<x-checkout::layouts.master title="Musteri Girisi" description="SimdiGetir musteri paneli giris ekrani">
@push('styles')
<style>
body{margin:0;background:var(--sg-surface-page-light);color:var(--sg-ink-light);font-family:"Manrope",sans-serif}.shell{width:min(1120px,calc(100% - 32px));margin:0 auto;padding:28px 0 40px}.top{display:flex;justify-content:space-between;gap:16px;margin-bottom:20px}.brand{display:inline-flex;align-items:center;gap:12px;color:inherit;text-decoration:none;font-weight:800}.brand b{width:42px;height:42px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;background:var(--sg-brand-gradient);color:#fff;font-family:"Space Grotesk",sans-serif}.card{border:1px solid var(--sg-border-light);border-radius:24px;background:var(--sg-card-light);backdrop-filter:blur(14px);box-shadow:var(--sg-shadow-light)}.hero{padding:24px}.hero h1{margin:0 0 8px;font-family:"Space Grotesk",sans-serif;font-size:clamp(1.9rem,4vw,2.8rem)}.hero p,.muted,.field small{color:var(--sg-muted-light)}.grid{display:grid;grid-template-columns:1.1fr .9fr;gap:20px;margin-top:20px}.panel{padding:22px}.form{display:grid;gap:14px}.field{display:grid;gap:8px}.field label{font-size:13px;font-weight:700}.field input{width:100%;min-height:52px;padding:14px 16px;border-radius:16px;border:1px solid var(--sg-border-light);background:#fff;font:inherit;color:inherit}.btn{display:inline-flex;align-items:center;justify-content:center;min-height:52px;padding:0 18px;border-radius:16px;border:0;background:var(--sg-action-gradient);color:#fff;font:inherit;font-weight:800;cursor:pointer;text-decoration:none}.btn.secondary{background:var(--sg-card-light-strong);color:var(--sg-ink-light);border:1px solid var(--sg-border-light)}.alert{padding:14px 16px;border-radius:18px;font-size:14px;line-height:1.6}.alert.error{background:var(--sg-error-bg);color:var(--sg-error-text)}.alert.success{background:var(--sg-success-bg);color:var(--sg-success-text)}.list{display:grid;gap:12px}.item{padding:16px;border-radius:18px;border:1px solid var(--sg-border-light-soft);background:var(--sg-card-light-soft)}.item strong{display:block;margin-bottom:6px}@media (max-width:980px){.grid{grid-template-columns:1fr}}
</style>
@endpush

<div class="shell">
  <div class="top">
    <a href="{{ route('home') }}" class="brand"><b>SG</b><span>SimdiGetir Hesabim</span></a>
    <div class="muted">Phone + password ile son siparislerinizi ve takip linklerinizi goruntuleyin.</div>
  </div>

  <section class="card hero">
    <h1>Musteri Girisi</h1>
    <p>Checkout sirasinda olusturdugunuz hesapla giris yapin. Dashboard ekraninda aktif siparisler, odeme durumu ve takip linkleri listelenir.</p>
  </section>

  <div class="grid">
    <section class="card panel">
      @if (session('status'))
        <div class="alert success" style="margin-bottom:14px;">{{ session('status') }}</div>
      @endif
      @if ($errors->any())
        <div class="alert error" style="margin-bottom:14px;">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('checkout.customer.login.submit') }}" class="form">
        @csrf
        <div class="field">
          <label for="portal-phone">Telefon</label>
          <input id="portal-phone" name="phone" type="text" value="{{ old('phone') }}" placeholder="0551 356 72 92" autocomplete="tel">
        </div>
        <div class="field">
          <label for="portal-password">Sifre</label>
          <input id="portal-password" name="password" type="password" placeholder="Sifreniz" autocomplete="current-password">
        </div>
        <button type="submit" class="btn">Panele Gir</button>
        <a href="{{ route('checkout.customer.register') }}" class="btn secondary">Yeni hesap olustur</a>
      </form>
    </section>

    <section class="card panel">
      <div class="list">
        <article class="item">
          <strong>Aktif Siparisler</strong>
          <p>Olusturulan siparislerinizin durum degisimlerini tek ekranda gorebilirsiniz.</p>
        </article>
        <article class="item">
          <strong>Takip Linkleri</strong>
          <p>Her siparis kartinda dogrudan `/siparis-takip` linki bulunur.</p>
        </article>
        <article class="item">
          <strong>Odeme Durumu</strong>
          <p>Cash, havale ve kart akisinin o anki state bilgisi dashboard'ta listelenir.</p>
        </article>
        <a href="{{ route('checkout.tracking') }}" class="btn secondary">Siparis Takip Ekrani</a>
      </div>
    </section>
  </div>
</div>
</x-checkout::layouts.master>
