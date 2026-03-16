<x-checkout::layouts.master title="Siparis Detayi" description="SimdiGetir musteri siparis detayi">
@php
    $checkoutSnapshot = (array) ($order->checkout_snapshot ?? []);
    $orderNotes = (array) ($order->notes ?? []);
    $deliveryNotes = (string) ($orderNotes['delivery_notes'] ?? $checkoutSnapshot['notes']['delivery_notes'] ?? '');
    $bankTransfer = (array) ($bankTransfer ?? []);
@endphp

@push('styles')
<style>
body{margin:0;background:linear-gradient(180deg,#fff7ed,#f3ede5);color:#201b17;font-family:"Manrope",sans-serif}.shell{width:min(1160px,calc(100% - 32px));margin:0 auto;padding:28px 0 40px}.top{display:flex;justify-content:space-between;gap:16px;margin-bottom:20px;align-items:center}.brand{display:inline-flex;align-items:center;gap:12px;color:inherit;text-decoration:none;font-weight:800}.brand b{width:42px;height:42px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#f97316,#ef4444);color:#fff;font-family:"Space Grotesk",sans-serif}.card{border:1px solid rgba(63,42,20,.12);border-radius:24px;background:rgba(255,255,255,.82);backdrop-filter:blur(14px);box-shadow:0 18px 60px rgba(63,42,20,.12)}.hero,.panel{padding:24px}.hero h1,.section-title{margin:0 0 8px;font-family:"Space Grotesk",sans-serif}.muted,.meta,.detail-note,.hero p{color:#6b6258}.hero-grid,.stats,.detail-grid,.stack,.list{display:grid;gap:14px}.hero-grid,.stats,.detail-grid{grid-template-columns:repeat(3,minmax(0,1fr))}.stat,.info-box,.detail-item,.timeline-item{padding:16px;border-radius:18px;border:1px solid rgba(63,42,20,.08);background:rgba(255,255,255,.68)}.stat strong,.order-no{display:block;font-size:1.8rem;font-family:"Space Grotesk",sans-serif}.badges,.actions{display:flex;gap:12px;flex-wrap:wrap}.badge{display:inline-flex;padding:8px 12px;border-radius:999px;background:rgba(249,115,22,.12);color:#9a3412;font-size:13px;font-weight:700}.btn{display:inline-flex;align-items:center;justify-content:center;min-height:48px;padding:0 18px;border-radius:16px;border:0;background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;font:inherit;font-weight:800;cursor:pointer;text-decoration:none}.btn.secondary{background:rgba(255,255,255,.9);color:#201b17;border:1px solid rgba(63,42,20,.12)}.section-title{font-size:1.2rem}.timeline-item strong,.detail-item strong{display:block;margin-bottom:4px}.list{gap:10px}.panel{margin-top:20px}.empty{padding:14px;border-radius:14px;border:1px dashed rgba(63,42,20,.16);color:#6b6258}.note-box{white-space:pre-line}@media (max-width:980px){.hero-grid,.stats,.detail-grid{grid-template-columns:1fr}.top{flex-direction:column;align-items:flex-start}}
</style>
@endpush

<div class="shell">
  <div class="top">
    <a href="{{ route('checkout.customer.dashboard') }}" class="brand"><b>SG</b><span>SimdiGetir Siparis Detayi</span></a>
    <div class="actions">
      <a href="{{ route('checkout.customer.dashboard') }}" class="btn secondary">Panele Don</a>
      <a href="{{ route('checkout.tracking', ['order_no' => $order->order_no, 'phone' => $customer->phone]) }}" class="btn">Canli Takip</a>
      <a href="{{ route('checkout.customer.orders.receipt', ['orderNo' => $order->order_no]) }}" class="btn secondary">Dekont</a>
    </div>
  </div>

  <section class="card hero">
    <div class="badges">
      <span class="badge">{{ $order->state }}</span>
      <span class="badge">{{ $order->payment_state }}</span>
      <span class="badge">{{ $order->payment_method ?? '-' }} / {{ $order->payment_timing ?? '-' }}</span>
    </div>
    <h1 class="order-no" style="margin-top:12px;">{{ $order->order_no }}</h1>
    <p>{{ optional($order->created_at)->format('Y-m-d H:i') }} | Musteri: {{ $customer->name }} | Telefon: {{ $customer->phone ?? '-' }}</p>

    <div class="stats">
      <div class="stat"><strong>{{ number_format(((int) $order->total_amount) / 100, 2, ',', '.') }}</strong><span class="meta">{{ $order->currency }} Toplam</span></div>
      <div class="stat"><strong>{{ $order->distance_meters ? number_format(((int) $order->distance_meters) / 1000, 1, ',', '.') : '-' }}</strong><span class="meta">KM</span></div>
      <div class="stat"><strong>{{ $order->duration_seconds ? ceil(((int) $order->duration_seconds) / 60) : '-' }}</strong><span class="meta">Tahmini Dakika</span></div>
    </div>

    <div class="hero-grid" style="margin-top:18px;">
      <div class="info-box">
        <div class="meta">Alinis</div>
        <strong>{{ $order->pickup_name ?: '-' }}</strong>
        <div>{{ $order->pickup_phone ?: '-' }}</div>
        <div>{{ $order->pickup_address ?: '-' }}</div>
      </div>
      <div class="info-box">
        <div class="meta">Teslimat</div>
        <strong>{{ $order->dropoff_name ?: '-' }}</strong>
        <div>{{ $order->dropoff_phone ?: '-' }}</div>
        <div>{{ $order->dropoff_address ?: '-' }}</div>
      </div>
      <div class="info-box">
        <div class="meta">Odeme ve Not</div>
        <strong>{{ $order->payment_method ?? '-' }} / {{ $order->payment_timing ?? '-' }}</strong>
        <div>Payer: {{ $order->payer_role ?? '-' }}</div>
        <div class="note-box">{{ $deliveryNotes !== '' ? $deliveryNotes : 'Teslimat notu yok.' }}</div>
      </div>
    </div>

    @if (($order->payment_method ?? '') === 'bank_transfer')
      <div class="info-box" style="margin-top:14px;">
        <div class="meta">{{ $bankTransfer['title'] ?? 'Havale / EFT Odeme Talimati' }}</div>
        <div class="note-box">{{ trim(collect([
            $bankTransfer['body'] ?? null,
            !empty($bankTransfer['bank_name']) ? 'Banka: '.$bankTransfer['bank_name'] : null,
            !empty($bankTransfer['account_holder']) ? 'Hesap Sahibi: '.$bankTransfer['account_holder'] : null,
            !empty($bankTransfer['iban']) ? 'IBAN: '.$bankTransfer['iban'] : null,
            $bankTransfer['reference_note'] ?? null,
        ])->filter()->implode("\n")) }}</div>
      </div>
    @endif
  </section>

  <section class="card panel">
    <h2 class="section-title">Paketler ve Odeme Kayitlari</h2>
    <div class="detail-grid" style="margin-top:16px;">
      <div>
        <div class="meta" style="margin-bottom:10px;">Paketler</div>
        <div class="list">
          @forelse ($order->packages as $item)
            <div class="detail-item">
              <strong>{{ $item->package_type ?: '-' }}</strong>
              <div class="detail-note">Adet: {{ $item->quantity }}</div>
              <div class="detail-note">Agirlik: {{ $item->weight_grams ?? '-' }} gr</div>
              <div class="detail-note">Deger: {{ $item->declared_value_amount ?? '-' }}</div>
              <div class="detail-note">{{ $item->description ?: 'Aciklama yok.' }}</div>
            </div>
          @empty
            <div class="empty">Paket kaydi yok.</div>
          @endforelse
        </div>
      </div>

      <div>
        <div class="meta" style="margin-bottom:10px;">Odeme Hareketleri</div>
        <div class="list">
          @forelse ($order->paymentTransactions as $item)
            <div class="detail-item">
              <strong>{{ $item->provider }}</strong>
              <div class="detail-note">Durum: {{ $item->status }}</div>
              <div class="detail-note">Ref: {{ $item->provider_reference ?: '-' }}</div>
              <div class="detail-note">{{ number_format(((int) $item->amount) / 100, 2, ',', '.') }} {{ $item->currency }}</div>
            </div>
          @empty
            <div class="empty">Odeme hareketi yok.</div>
          @endforelse
        </div>
      </div>

      <div>
        <div class="meta" style="margin-bottom:10px;">Proof Kayitlari</div>
        <div class="list">
          @forelse ($order->orderProofs as $item)
            <div class="detail-item">
              <strong>{{ $item->stage }} / {{ $item->proof_type }}</strong>
              <div class="detail-note">{{ optional($item->created_at)->format('Y-m-d H:i') }}</div>
              <div class="detail-note">
                @if ($item->file_url)
                  <a href="{{ $item->file_url }}" target="_blank" rel="noreferrer">Dosyayi Ac</a>
                @else
                  Dosya baglantisi yok.
                @endif
              </div>
            </div>
          @empty
            <div class="empty">Proof kaydi yok.</div>
          @endforelse
        </div>
      </div>
    </div>
  </section>

  <section class="card panel">
    <h2 class="section-title">Durum ve Operasyon Akisi</h2>
    <div class="detail-grid" style="margin-top:16px;">
      <div>
        <div class="meta" style="margin-bottom:10px;">State Timeline</div>
        <div class="list">
          @forelse ($order->stateLogs as $item)
            <div class="timeline-item">
              <strong>{{ $item->to_state }}</strong>
              <div class="detail-note">{{ $item->reason ?? '-' }}</div>
              <div class="detail-note">{{ optional($item->created_at)->format('Y-m-d H:i') }}</div>
            </div>
          @empty
            <div class="empty">Timeline kaydi yok.</div>
          @endforelse
        </div>
      </div>

      <div>
        <div class="meta" style="margin-bottom:10px;">Kurye Hareketleri</div>
        <div class="list">
          @forelse ($order->trackingEvents as $item)
            <div class="timeline-item">
              <strong>{{ $item->event_type }}</strong>
              <div class="detail-note">{{ $item->note ?? 'Not yok.' }}</div>
              <div class="detail-note">{{ optional($item->created_at)->format('Y-m-d H:i') }}</div>
            </div>
          @empty
            <div class="empty">Tracking eventi yok.</div>
          @endforelse
        </div>
      </div>

      <div>
        <div class="meta" style="margin-bottom:10px;">Checkout Snapshot</div>
        <div class="list">
          <div class="detail-item">
            <strong>Hizmet</strong>
            <div class="detail-note">{{ $checkoutSnapshot['service_label'] ?? $checkoutSnapshot['service_type'] ?? $order->vehicle_type ?? '-' }}</div>
          </div>
          <div class="detail-item">
            <strong>Same Person</strong>
            <div class="detail-note">{{ !empty($checkoutSnapshot['same_person']) ? 'Evet' : 'Hayir' }}</div>
          </div>
          <div class="detail-item">
            <strong>Fiyat Kaynagi</strong>
            <div class="detail-note">{{ data_get($order->price_breakdown, 'source', '-') }}</div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
</x-checkout::layouts.master>
