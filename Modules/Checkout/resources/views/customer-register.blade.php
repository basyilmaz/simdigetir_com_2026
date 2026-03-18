<x-checkout::layouts.master title="Musteri Kaydi" description="SimdiGetir musteri hesap olusturma ekrani">
@php
    $pageCopy = is_array($pageCopy ?? null) ? $pageCopy : [];
    $support = is_array($support ?? null) ? $support : [];
@endphp

@push('styles')
<style>
body{margin:0;background:var(--sg-surface-page-light);color:var(--sg-ink-light);font-family:var(--sg-font-body)}.shell{width:min(1120px,calc(100% - 32px));margin:0 auto;padding:28px 0 40px}.top{display:flex;justify-content:space-between;gap:16px;margin-bottom:20px;flex-wrap:wrap}.brand{display:inline-flex;align-items:center;gap:12px;color:inherit;text-decoration:none;font-weight:800}.brand b{width:42px;height:42px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;background:var(--sg-brand-gradient);color:#fff;font-family:var(--sg-font-display)}.card{border:1px solid var(--sg-border-light);border-radius:24px;background:var(--sg-card-light);backdrop-filter:blur(14px);box-shadow:var(--sg-shadow-light)}.hero{padding:24px}.hero h1{margin:0 0 8px;font-family:var(--sg-font-display);font-size:var(--sg-type-display-lg)}.hero p,.muted,.field small,.support-copy,.consent-note{color:var(--sg-muted-light)}.grid{display:grid;grid-template-columns:1.05fr .95fr;gap:20px;margin-top:20px}.panel{padding:22px}.form{display:grid;gap:14px}.field{display:grid;gap:8px}.field label{font-size:var(--sg-type-caption);font-weight:700}.field input{width:100%;min-height:52px;padding:14px 16px;border-radius:16px;border:1px solid var(--sg-border-light);background:#fff;font:inherit;color:inherit}.checkbox{display:flex;align-items:flex-start;gap:10px;padding:12px 14px;border-radius:16px;border:1px solid var(--sg-border-light-soft);background:var(--sg-card-light-soft)}.checkbox input{margin-top:4px}.checkbox label{font-size:var(--sg-type-body-sm);line-height:1.6}.checkbox a{color:var(--sg-link-warm);font-weight:700}.btn{display:inline-flex;align-items:center;justify-content:center;min-height:52px;padding:0 18px;border-radius:16px;border:0;background:var(--sg-action-gradient);color:#fff;font:inherit;font-weight:800;cursor:pointer;text-decoration:none}.btn.secondary{background:var(--sg-card-light-strong);color:var(--sg-ink-light);border:1px solid var(--sg-border-light)}.alert{padding:14px 16px;border-radius:18px;font-size:var(--sg-type-body-sm);line-height:1.6}.alert.error{background:var(--sg-error-bg);color:var(--sg-error-text)}.alert.success{background:var(--sg-success-bg);color:var(--sg-success-text)}.list,.support-actions{display:grid;gap:12px}.item{padding:16px;border-radius:18px;border:1px solid var(--sg-border-light-soft);background:var(--sg-card-light-soft)}.item strong{display:block;margin-bottom:6px}.eyebrow{display:inline-flex;padding:8px 12px;border-radius:999px;background:var(--sg-card-brand-soft-light);color:var(--sg-accent-warm-text);font-size:var(--sg-type-caption);font-weight:800;margin-bottom:12px}.support-actions a{display:inline-flex;align-items:center;justify-content:center;min-height:46px;padding:0 16px;border-radius:14px;border:1px solid var(--sg-border-light);background:#fff;color:var(--sg-link-warm);font-weight:800;text-decoration:none}@media (max-width:980px){.grid{grid-template-columns:1fr}}
</style>
@endpush

<div class="shell">
  <div class="top">
    <a href="{{ route('home') }}" class="brand"><b>SG</b><span>SimdiGetir Hesabim</span></a>
    <div class="muted">{{ $support['support_note'] ?? 'Destek ekibimiz telefon, WhatsApp veya e-posta uzerinden yardimci olur.' }}</div>
  </div>

  <section class="card hero">
    <span class="eyebrow">Hizli kayit</span>
    <h1>Musteri Kaydi</h1>
    <p>{{ $pageCopy['intro'] ?? 'Hizli siparis, takip linkleri ve siparis gecmisi icin bir hesap olusturun.' }}</p>
  </section>

  <div class="grid">
    <section class="card panel">
      @if (session('status'))
        <div class="alert success" style="margin-bottom:14px;">{{ session('status') }}</div>
      @endif
      @if ($errors->any())
        <div class="alert error" style="margin-bottom:14px;">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('checkout.customer.register.submit') }}" class="form">
        @csrf
        <div class="field">
          <label for="register-name">Ad Soyad</label>
          <input id="register-name" name="name" type="text" value="{{ old('name') }}" placeholder="Orn: Ayse Yilmaz" autocomplete="name" required>
        </div>
        <div class="field">
          <label for="register-phone">Telefon</label>
          <input id="register-phone" name="phone" type="tel" inputmode="tel" value="{{ old('phone') }}" placeholder="0551 356 72 92" autocomplete="tel" required>
        </div>
        <div class="field">
          <label for="register-email">E-posta (opsiyonel)</label>
          <input id="register-email" name="email" type="email" value="{{ old('email') }}" placeholder="ornek@alanadi.com" autocomplete="email">
        </div>
        <div class="field">
          <label for="register-password">Sifre</label>
          <input id="register-password" name="password" type="password" placeholder="En az 8 karakter" autocomplete="new-password" required>
        </div>
        <div class="field">
          <label for="register-password-confirmation">Sifre Tekrar</label>
          <input id="register-password-confirmation" name="password_confirmation" type="password" placeholder="Sifreyi tekrar girin" autocomplete="new-password" required>
        </div>
        <div class="checkbox">
          <input id="register-legal-acceptance" name="legal_acceptance" type="checkbox" value="1" {{ old('legal_acceptance') ? 'checked' : '' }} required>
          <label for="register-legal-acceptance">
            <a href="{{ $support['privacy_href'] ?? url('/kvkk') }}" target="_blank" rel="noopener">KVKK metnini</a> ve
            <a href="{{ $support['terms_href'] ?? url('/kullanim-kosullari') }}" target="_blank" rel="noopener">kullanim kosullarini</a>
            okudum, kabul ediyorum.
          </label>
        </div>
        <p class="consent-note">Kayit olduktan sonra ayni hesapla siparis baslatabilir ve siparislerinizi takip edebilirsiniz.</p>
        <button type="submit" class="btn">Hesap Olustur</button>
        <a href="{{ route('checkout.customer.login') }}" class="btn secondary">Mevcut Hesapla Giris Yap</a>
      </form>
    </section>

    <section class="card panel">
      <div class="list">
        <article class="item">
          <strong>Tek hesaptan devam edin</strong>
          <p>{{ $pageCopy['help'] ?? 'Kayit olduktan sonra ayni hesapla checkout ve Hesabim ekranlarini kullanabilirsiniz.' }}</p>
        </article>
        <article class="item">
          <strong>Guven notu</strong>
          <p class="support-copy">Sadece siparis surecinizde kullanilacak temel bilgiler istenir. Destek ekibine dilediginiz zaman ulasabilirsiniz.</p>
        </article>
        <div class="support-actions">
          <a href="{{ $support['phone_href'] ?? 'tel:+905513567292' }}">{{ $support['phone_display'] ?? '+90 551 356 72 92' }}</a>
          <a href="{{ $support['whatsapp_href'] ?? 'https://wa.me/905513567292' }}" target="_blank" rel="noopener">WhatsApp ile Yardim Al</a>
          <a href="{{ route('contact') }}">Iletisim Sayfasi</a>
          <a href="{{ route('checkout.index') }}">Siparise Basla</a>
        </div>
      </div>
    </section>
  </div>
</div>
</x-checkout::layouts.master>
