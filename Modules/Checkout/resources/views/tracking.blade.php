<x-checkout::layouts.master title="Siparis Takip" description="SimdiGetir siparis takip ekrani">
@php
    $order = (array) ($tracking['order'] ?? []);
    $timeline = (array) ($tracking['timeline'] ?? []);
    $trackingEvents = (array) ($tracking['tracking_events'] ?? []);
    $proofs = (array) ($tracking['proofs'] ?? []);
@endphp

@push('styles')
<style>
body{margin:0;background:linear-gradient(180deg,#fff7ed,#f3ede5);color:#201b17;font-family:"Manrope",sans-serif}.shell{width:min(1120px,calc(100% - 32px));margin:0 auto;padding:28px 0 40px}.top{display:flex;justify-content:space-between;gap:16px;margin-bottom:20px}.brand{display:inline-flex;align-items:center;gap:12px;color:inherit;text-decoration:none;font-weight:800}.brand b{width:42px;height:42px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#f97316,#ef4444);color:#fff;font-family:"Space Grotesk",sans-serif}.card{border:1px solid rgba(63,42,20,.12);border-radius:24px;background:rgba(255,255,255,.82);backdrop-filter:blur(14px);box-shadow:0 18px 60px rgba(63,42,20,.12)}.hero{padding:24px}.hero h1{margin:0 0 8px;font-family:"Space Grotesk",sans-serif;font-size:clamp(1.9rem,4vw,2.8rem)}.hero p,.muted,.item p,.item small,.empty{color:#6b6258}.form{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:18px}.field{display:grid;gap:8px}.field label{font-size:13px;font-weight:700}.field input{width:100%;min-height:52px;padding:14px 16px;border-radius:16px;border:1px solid rgba(63,42,20,.12);background:#fff;font:inherit;color:inherit}.btn{display:inline-flex;align-items:center;justify-content:center;min-height:52px;padding:0 18px;border-radius:16px;border:0;background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;font:inherit;font-weight:800;cursor:pointer;text-decoration:none}.grid{display:grid;grid-template-columns:1.1fr .9fr;gap:20px;margin-top:20px}.panel{padding:22px}.stack{display:grid;gap:12px}.badge{display:inline-flex;padding:8px 12px;border-radius:999px;background:rgba(249,115,22,.12);color:#9a3412;font-size:13px;font-weight:700}.summary{display:grid;gap:10px}.row{display:flex;justify-content:space-between;gap:16px}.timeline,.events,.proofs{display:grid;gap:12px}.item{padding:16px;border-radius:18px;border:1px solid rgba(63,42,20,.08);background:rgba(255,255,255,.68)}.item strong{display:block;margin-bottom:6px}.alert{margin-top:14px;padding:14px 16px;border-radius:18px;font-size:14px;line-height:1.6}.alert.error{background:rgba(180,35,24,.12);color:#9d1c12}.alert.info{background:rgba(33,150,243,.12);color:#164a8b}.proof-link{color:#c2410c;font-weight:700;text-decoration:none}@media (max-width:980px){.form,.grid{grid-template-columns:1fr}}
</style>
@endpush

<div class="shell">
  <div class="top">
    <a href="{{ route('home') }}" class="brand"><b>SG</b><span>SimdiGetir Takip</span></a>
    <div class="muted">Siparis numarasi ve dogrulama telefonu ile guncel durumu goruntuleyin.</div>
  </div>

  <section class="card hero">
    <h1>Siparis Takip</h1>
    <p>Bu ekran bireysel musteri icin hafif bir takip girisidir. Siparis numarasi ve sistemde kayitli telefon eslesirse timeline, kurye hareketleri ve proof ozeti gosterilir.</p>

    <form method="GET" action="{{ route('checkout.tracking') }}" class="form">
      <div class="field">
        <label for="tracking-order-no">Siparis Numarasi</label>
        <input id="tracking-order-no" name="order_no" type="text" value="{{ $prefillOrderNo }}" placeholder="ORN: ORD20260314ABCDE">
      </div>
      <div class="field">
        <label for="tracking-phone">Telefon</label>
        <input id="tracking-phone" name="phone" type="text" value="{{ $prefillPhone }}" placeholder="0551 356 72 92">
      </div>
      <div class="field">
        <label>&nbsp;</label>
        <button type="submit" class="btn">Siparisi Sorgula</button>
      </div>
    </form>

    @if ($lookupError)
      <div class="alert error">{{ $lookupError }}</div>
    @elseif (! $lookupSubmitted)
      <div class="alert info">Siparis no ve telefon girildiginde mevcut durum, timeline ve proof bilgileri burada gosterilir.</div>
    @endif
  </section>

  @if ($tracking)
    <div
      class="alert info"
      style="margin-top:20px;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;"
      data-tracking-autorefresh
      data-order-no="{{ $prefillOrderNo }}"
      data-phone="{{ $prefillPhone }}"
      data-endpoint="{{ route('api.v1.order-tracking.show') }}"
    >
      <span>Canlı takip aktif. Ekran her <strong data-tracking-countdown>30</strong> sn güncellenir.</span>
      <button type="button" class="btn" style="min-height:42px;padding:0 14px;" data-tracking-toggle>Otomatik Yenilemeyi Durdur</button>
    </div>

    <div class="grid">
      <section class="card panel">
        <div class="stack">
          <div>
            <span class="badge">Durum: {{ $order['state'] ?? '-' }}</span>
          </div>
          <div class="summary">
            <div class="row"><small>Siparis No</small><strong>{{ $order['order_no'] ?? '-' }}</strong></div>
            <div class="row"><small>Odeme</small><strong>{{ $order['payment_state'] ?? '-' }}</strong></div>
            <div class="row"><small>Toplam</small><strong>{{ $order['total_amount_formatted'] ?? '-' }}</strong></div>
            <div class="row"><small>Alinis</small><strong>{{ $order['pickup_name'] ?? '-' }}</strong></div>
            <div class="row"><small>Alinis Adresi</small><strong>{{ $order['pickup_address'] ?? '-' }}</strong></div>
            <div class="row"><small>Teslim</small><strong>{{ $order['dropoff_name'] ?? '-' }}</strong></div>
            <div class="row"><small>Teslimat Adresi</small><strong>{{ $order['dropoff_address'] ?? '-' }}</strong></div>
          </div>
        </div>
      </section>

      <section class="card panel">
        <h2>State Timeline</h2>
        <div class="timeline">
          @forelse ($timeline as $item)
            <article class="item">
              <strong>{{ $item['to_state'] ?? '-' }}</strong>
              <p>Onceki durum: {{ $item['from_state'] ?? '-' }}</p>
              <p>Reason: {{ $item['reason'] ?? '-' }}</p>
              <small>{{ $item['created_at'] ?? '-' }}</small>
            </article>
          @empty
            <div class="empty">Henuz timeline kaydi yok.</div>
          @endforelse
        </div>
      </section>
    </div>

    <div class="grid">
      <section class="card panel">
        <h2>Kurye Hareketleri</h2>
        <div class="events">
          @forelse ($trackingEvents as $item)
            <article class="item">
              <strong>{{ $item['event_type'] ?? '-' }}</strong>
              <p>{{ $item['note'] ?? 'Not yok.' }}</p>
              <p>ETA: {{ isset($item['eta_seconds']) ? $item['eta_seconds'].' sn' : '-' }}</p>
              <small>{{ $item['created_at'] ?? '-' }}</small>
            </article>
          @empty
            <div class="empty">Aktif kurye hareketi henuz kaydedilmedi.</div>
          @endforelse
        </div>
      </section>

      <section class="card panel">
        <h2>Proof Ozeti</h2>
        <div class="proofs">
          @forelse ($proofs as $item)
            <article class="item">
              <strong>{{ $item['stage'] ?? '-' }} / {{ $item['proof_type'] ?? '-' }}</strong>
              @if (!empty($item['file_url']))
                <p><a class="proof-link" href="{{ $item['file_url'] }}" target="_blank" rel="noreferrer">Dosyayi ac</a></p>
              @else
                <p>Dosya baglantisi yok.</p>
              @endif
              <small>{{ $item['created_at'] ?? '-' }}</small>
            </article>
          @empty
            <div class="empty">Henuz proof olusmadi.</div>
          @endforelse
        </div>
      </section>
    </div>
  @endif
</div>

@if ($tracking && $lookupSubmitted && $prefillOrderNo !== '' && $prefillPhone !== '')
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const refreshNode = document.querySelector('[data-tracking-autorefresh]');
  if (!refreshNode) {
    return;
  }

  const orderNo = String(refreshNode.getAttribute('data-order-no') || '').trim();
  const phone = String(refreshNode.getAttribute('data-phone') || '').trim();
  const endpoint = String(refreshNode.getAttribute('data-endpoint') || '').trim();
  const countdownNode = refreshNode.querySelector('[data-tracking-countdown]');
  const toggleButton = refreshNode.querySelector('[data-tracking-toggle]');
  const initialSeconds = 30;
  let enabled = true;
  let remainingSeconds = initialSeconds;

  const updateCountdown = () => {
    if (!countdownNode) {
      return;
    }
    countdownNode.textContent = String(Math.max(0, remainingSeconds));
  };

  const buildRefreshUrl = () => {
    const params = new URLSearchParams({
      order_no: orderNo,
      phone: phone,
    });

    return `${endpoint}?${params.toString()}`;
  };

  const refreshTracking = async () => {
    if (!enabled || document.hidden || orderNo === '' || phone === '' || endpoint === '') {
      return;
    }

    try {
      const response = await fetch(buildRefreshUrl(), {
        headers: { Accept: 'application/json' },
      });

      if (response.ok) {
        window.location.reload();
      }
    } catch (error) {
      // Keep page stable on transient network issues; next cycle will retry.
    }
  };

  toggleButton?.addEventListener('click', function () {
    enabled = !enabled;
    toggleButton.textContent = enabled
      ? 'Otomatik Yenilemeyi Durdur'
      : 'Otomatik Yenilemeyi Başlat';

    if (enabled) {
      remainingSeconds = initialSeconds;
      updateCountdown();
    }
  });

  updateCountdown();
  window.setInterval(function () {
    if (!enabled) {
      return;
    }

    remainingSeconds -= 1;
    if (remainingSeconds <= 0) {
      remainingSeconds = initialSeconds;
      refreshTracking();
    }

    updateCountdown();
  }, 1000);
});
</script>
@endpush
@endif
</x-checkout::layouts.master>
