<x-checkout::layouts.master title="Musteri Kaydi" description="SimdiGetir musteri hesap olusturma ekrani">
@push('styles')
<style>
body{margin:0;background:linear-gradient(180deg,#fff7ed,#f3ede5);color:#201b17;font-family:"Manrope",sans-serif}.shell{width:min(1120px,calc(100% - 32px));margin:0 auto;padding:28px 0 40px}.top{display:flex;justify-content:space-between;gap:16px;margin-bottom:20px}.brand{display:inline-flex;align-items:center;gap:12px;color:inherit;text-decoration:none;font-weight:800}.brand b{width:42px;height:42px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#f97316,#ef4444);color:#fff;font-family:"Space Grotesk",sans-serif}.card{border:1px solid rgba(63,42,20,.12);border-radius:24px;background:rgba(255,255,255,.82);backdrop-filter:blur(14px);box-shadow:0 18px 60px rgba(63,42,20,.12)}.hero{padding:24px}.hero h1{margin:0 0 8px;font-family:"Space Grotesk",sans-serif;font-size:clamp(1.9rem,4vw,2.8rem)}.hero p,.muted,.field small{color:#6b6258}.grid{display:grid;grid-template-columns:1.05fr .95fr;gap:20px;margin-top:20px}.panel{padding:22px}.form{display:grid;gap:14px}.field{display:grid;gap:8px}.field label{font-size:13px;font-weight:700}.field input{width:100%;min-height:52px;padding:14px 16px;border-radius:16px;border:1px solid rgba(63,42,20,.12);background:#fff;font:inherit;color:inherit}.btn{display:inline-flex;align-items:center;justify-content:center;min-height:52px;padding:0 18px;border-radius:16px;border:0;background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;font:inherit;font-weight:800;cursor:pointer;text-decoration:none}.btn.secondary{background:rgba(255,255,255,.9);color:#201b17;border:1px solid rgba(63,42,20,.12)}.alert{padding:14px 16px;border-radius:18px;font-size:14px;line-height:1.6}.alert.error{background:rgba(180,35,24,.12);color:#9d1c12}.alert.success{background:rgba(22,163,74,.12);color:#166534}.list{display:grid;gap:12px}.item{padding:16px;border-radius:18px;border:1px solid rgba(63,42,20,.08);background:rgba(255,255,255,.68)}.item strong{display:block;margin-bottom:6px}@media (max-width:980px){.grid{grid-template-columns:1fr}}
</style>
@endpush

<div class="shell">
  <div class="top">
    <a href="{{ route('home') }}" class="brand"><b>SG</b><span>SimdiGetir Hesabim</span></a>
    <div class="muted">Telefon + sifre ile onceden kayit olun, sonra checkout veya panel akisini ayni hesapla devam ettirin.</div>
  </div>

  <section class="card hero">
    <h1>Musteri Kaydi</h1>
    <p>Checkout akisi calismasa bile yeni musteri edinimi bloke olmasin diye bu sayfa ayri olarak aciktir. Kayit olduktan sonra dogrudan panelinize giris yapmis olursunuz.</p>
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
          <input id="register-name" name="name" type="text" value="{{ old('name') }}" placeholder="Orn: Ayse Yilmaz" autocomplete="name">
        </div>
        <div class="field">
          <label for="register-phone">Telefon</label>
          <input id="register-phone" name="phone" type="text" value="{{ old('phone') }}" placeholder="0551 356 72 92" autocomplete="tel">
        </div>
        <div class="field">
          <label for="register-email">E-posta (opsiyonel)</label>
          <input id="register-email" name="email" type="email" value="{{ old('email') }}" placeholder="ornek@alanadi.com" autocomplete="email">
        </div>
        <div class="field">
          <label for="register-password">Sifre</label>
          <input id="register-password" name="password" type="password" placeholder="En az 8 karakter" autocomplete="new-password">
        </div>
        <div class="field">
          <label for="register-password-confirmation">Sifre Tekrar</label>
          <input id="register-password-confirmation" name="password_confirmation" type="password" placeholder="Sifreyi tekrar girin" autocomplete="new-password">
        </div>
        <button type="submit" class="btn">Hesap Olustur</button>
        <a href="{{ route('checkout.customer.login') }}" class="btn secondary">Mevcut Hesapla Giris Yap</a>
      </form>
    </section>

    <section class="card panel">
      <div class="list">
        <article class="item">
          <strong>Checkout Hazirligi</strong>
          <p>Kayitli musteri oldugunuzda checkout ekraninda hesap baglama adimini hizla gecersiniz.</p>
        </article>
        <article class="item">
          <strong>Siparislerim</strong>
          <p>Kayit sonrasi panelde mevcut ve gecmis siparislerinizi gorebilir, takip linklerini acabilirsiniz.</p>
        </article>
        <article class="item">
          <strong>Telefon Esasli Giris</strong>
          <p>Phase 1 kararina uygun olarak OTP degil, telefon + sifre kullanilir.</p>
        </article>
        <a href="{{ route('checkout.index') }}" class="btn secondary">Siparise Basla</a>
      </div>
    </section>
  </div>
</div>
</x-checkout::layouts.master>
