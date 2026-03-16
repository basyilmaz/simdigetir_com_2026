<x-checkout::layouts.master title="Ödeme Dekontu" description="SimdiGetir ödeme dekontu">
@php
    $latestPayment = $order->paymentTransactions->first();
@endphp

@push('styles')
<style>
body{margin:0;background:linear-gradient(180deg,#0b1220,#0f172a);color:#e2e8f0;font-family:"Manrope",sans-serif}
.shell{width:min(980px,calc(100% - 32px));margin:0 auto;padding:28px 0 40px}
.top{display:flex;justify-content:space-between;gap:16px;align-items:center;margin-bottom:20px;flex-wrap:wrap}
.brand{display:inline-flex;align-items:center;gap:12px;color:inherit;text-decoration:none;font-weight:800}
.brand b{width:42px;height:42px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#f97316,#ef4444);color:#fff;font-family:"Space Grotesk",sans-serif}
.card{border:1px solid rgba(148,163,184,.16);border-radius:24px;background:rgba(15,23,42,.72);backdrop-filter:blur(14px);box-shadow:0 18px 60px rgba(2,6,23,.45);padding:24px}
.head h1{margin:0 0 8px;font-family:"Space Grotesk",sans-serif;font-size:clamp(1.7rem,4vw,2.4rem)}
.muted,.meta{color:#cbd5e1}
.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin-top:16px}
.item{padding:14px;border-radius:16px;border:1px solid rgba(148,163,184,.16);background:rgba(15,23,42,.5)}
.item strong{display:block;margin-bottom:4px}
.actions{display:flex;gap:10px;flex-wrap:wrap}
.btn{display:inline-flex;align-items:center;justify-content:center;min-height:46px;padding:0 16px;border-radius:14px;border:0;background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;font:inherit;font-weight:800;text-decoration:none;cursor:pointer}
.btn.secondary{background:rgba(15,23,42,.9);color:#e2e8f0;border:1px solid rgba(148,163,184,.22)}
@media print {
  body{background:#fff;color:#111827}
  .top .actions{display:none}
  .card{background:#fff;border:1px solid #d1d5db;box-shadow:none}
  .item{background:#f9fafb;border:1px solid #e5e7eb}
}
@media (max-width:820px){.grid{grid-template-columns:1fr}}
</style>
@endpush

<div class="shell">
  <div class="top">
    <a href="{{ route('checkout.customer.orders.show', ['orderNo' => $order->order_no]) }}" class="brand"><b>SG</b><span>SimdiGetir Dekont</span></a>
    <div class="actions">
      <a href="{{ route('checkout.customer.orders.show', ['orderNo' => $order->order_no]) }}" class="btn secondary">Sipariş Detayına Dön</a>
      <button type="button" class="btn" onclick="window.print()">Yazdır</button>
    </div>
  </div>

  <section class="card">
    <div class="head">
      <h1>Ödeme Dekontu</h1>
      <p class="muted">Sipariş No: <strong>{{ $order->order_no }}</strong> | Tarih: {{ optional($order->created_at)->format('d.m.Y H:i') }}</p>
    </div>

    <div class="grid">
      <article class="item">
        <strong>Müşteri</strong>
        <div>{{ $customer->name }}</div>
        <div class="meta">{{ $customer->phone ?? '-' }}</div>
        <div class="meta">{{ $customer->email ?? '-' }}</div>
      </article>

      <article class="item">
        <strong>Sipariş Özeti</strong>
        <div>Durum: {{ $order->state }}</div>
        <div>Ödeme Durumu: {{ $order->payment_state }}</div>
        <div class="meta">Ödeme Tipi: {{ $order->payment_method ?? '-' }} / {{ $order->payment_timing ?? '-' }}</div>
      </article>

      <article class="item">
        <strong>Tutar</strong>
        <div>{{ number_format(((int) $order->total_amount) / 100, 2, ',', '.') }} {{ strtoupper((string) $order->currency) }}</div>
      </article>

      <article class="item">
        <strong>Son Ödeme İşlemi</strong>
        @if ($latestPayment)
          <div>Provider: {{ $latestPayment->provider }}</div>
          <div>Durum: {{ $latestPayment->status }}</div>
          <div class="meta">Referans: {{ $latestPayment->provider_reference ?: '-' }}</div>
          <div class="meta">Tutar: {{ number_format(((int) $latestPayment->amount) / 100, 2, ',', '.') }} {{ strtoupper((string) $latestPayment->currency) }}</div>
        @else
          <div class="meta">Bu sipariş için ödeme işlemi kaydı bulunamadı.</div>
        @endif
      </article>
    </div>
  </section>
</div>
</x-checkout::layouts.master>
