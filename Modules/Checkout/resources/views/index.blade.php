<x-checkout::layouts.master title="Siparişe Başla" description="SimdiGetir checkout başlangıç ekranı">
@push('styles')
<style>
body{margin:0;background:linear-gradient(180deg,#fff7ed,#f3ede5);color:#201b17;font-family:"Manrope",sans-serif}.shell{width:min(1100px,calc(100% - 32px));margin:0 auto;padding:28px 0 44px}.top{display:flex;justify-content:space-between;gap:16px;align-items:center;margin-bottom:20px}.brand{display:inline-flex;align-items:center;gap:12px;color:inherit;text-decoration:none;font-weight:800}.brand b{width:42px;height:42px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#f97316,#ef4444);color:#fff;font-family:"Space Grotesk",sans-serif}.card{border:1px solid rgba(63,42,20,.12);border-radius:24px;background:rgba(255,255,255,.82);backdrop-filter:blur(14px);box-shadow:0 18px 60px rgba(63,42,20,.12)}.hero,.grid section{padding:24px}.hero h1{margin:0 0 10px;font-family:"Space Grotesk",sans-serif;font-size:clamp(1.9rem,4vw,2.8rem)}.hero p,.muted,.item p{color:#6b6258}.grid{display:grid;grid-template-columns:1.15fr .85fr;gap:20px;margin-top:20px}.actions,.list{display:grid;gap:12px}.btn{display:inline-flex;align-items:center;justify-content:center;min-height:52px;padding:0 18px;border-radius:16px;border:0;background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;font:inherit;font-weight:800;cursor:pointer;text-decoration:none}.btn.secondary{background:rgba(255,255,255,.9);color:#201b17;border:1px solid rgba(63,42,20,.12)}.item{padding:16px;border-radius:18px;border:1px solid rgba(63,42,20,.08);background:rgba(255,255,255,.68)}.item strong{display:block;margin-bottom:6px}@media (max-width:980px){.grid{grid-template-columns:1fr}}
</style>
@endpush
<div class="shell">
  <div class="top">
    <a href="{{ route('home') }}" class="brand"><b>SG</b><span>SimdiGetir Checkout</span></a>
    <div class="muted">Hero teklifinden gelen token ile checkout akışı açılır. Bu ekran artık 404 yerine yönlendirici bir giriş noktası olarak çalışır.</div>
  </div>

  <section class="card hero">
    <h1>Siparişe Başla</h1>
    <p>Sipariş oluşturmak için önce ana sayfadaki <strong>Anında Fiyat Hesapla</strong> aracından teklif alın. Teklif sonrasında sistem sizi otomatik olarak güvenli checkout oturumuna taşır.</p>
    <div class="actions" style="margin-top:18px">
      <a href="{{ route('home') }}" class="btn">Ana Sayfada Fiyat Hesapla</a>
      <a href="{{ route('checkout.customer.register') }}" class="btn secondary">Hesap Oluştur</a>
    </div>
  </section>

  <div class="grid">
    <section class="card">
      <div class="list">
        <article class="item">
          <strong>1. Teklif Al</strong>
          <p>Alış ve teslimat adresini ana sayfada girin, sistem fiyat ve tahmini süreyi oluştursun.</p>
        </article>
        <article class="item">
          <strong>2. Hesabınızı Bağlayın</strong>
          <p>Checkout sırasında veya önceden telefon + şifre ile kayıt olun. Sonraki siparişlerde aynı hesapla devam edin.</p>
        </article>
        <article class="item">
          <strong>3. Siparişi Tamamlayın</strong>
          <p>Gönderen/alıcı bilgilerini girin, ödeme yöntemini seçin, siparişi onaylayın.</p>
        </article>
      </div>
    </section>
    <section class="card">
      <div class="list">
        <article class="item">
          <strong>Hesabım</strong>
          <p>Mevcut siparişlerinizi ve takip linklerinizi panelden görebilirsiniz.</p>
        </article>
        <article class="item">
          <strong>Sipariş Takip</strong>
          <p>Sipariş numarası ve telefon ile herkese açık takip ekranına ulaşın.</p>
        </article>
        <a href="{{ route('checkout.customer.login') }}" class="btn secondary">Müşteri Girişi</a>
        <a href="{{ route('checkout.tracking') }}" class="btn secondary">Sipariş Takip</a>
      </div>
    </section>
  </div>
</div>
</x-checkout::layouts.master>
