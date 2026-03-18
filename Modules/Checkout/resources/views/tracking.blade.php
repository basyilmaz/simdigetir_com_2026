<x-checkout::layouts.public title="Siparis Takip" description="SimdiGetir siparis takip ekrani">
@php
    $order = (array) ($tracking['order'] ?? []);
    $timeline = (array) ($tracking['timeline'] ?? []);
    $trackingEvents = (array) ($tracking['tracking_events'] ?? []);
    $proofs = (array) ($tracking['proofs'] ?? []);
    $pageCopy = is_array($pageCopy ?? null) ? $pageCopy : [];
    $support = is_array($support ?? null) ? $support : [];
    $latestTrackingEvent = collect($trackingEvents)->filter(fn ($item) => is_array($item))->sortByDesc('created_at')->first();
    $stateLabel = fn (?string $state): string => match ((string) $state) {
        'draft' => 'Hazirlaniyor',
        'pending_payment' => 'Odeme bekleniyor',
        'paid' => 'Hazir',
        'assigned' => 'Kurye atandi',
        'picked_up' => 'Yolda',
        'delivered' => 'Teslim edildi',
        'closed' => 'Tamamlandi',
        'cancelled' => 'Iptal edildi',
        'failed' => 'Islem tamamlanamadi',
        default => trim((string) $state) !== '' ? ucfirst(str_replace('_', ' ', (string) $state)) : '-',
    };
    $paymentLabel = fn (?string $state): string => match ((string) $state) {
        'pending' => 'Beklemede',
        'awaiting_reconcile' => 'Kontrol ediliyor',
        'cash_on_delivery' => 'Kapida odeme',
        'succeeded' => 'Tamamlandi',
        'failed' => 'Basarisiz',
        'cancelled' => 'Iptal edildi',
        default => trim((string) $state) !== '' ? ucfirst(str_replace('_', ' ', (string) $state)) : '-',
    };
    $formatDate = function (?string $value): string {
        if (! $value) {
            return '-';
        }

        try {
            return \Illuminate\Support\Carbon::parse($value)->timezone(config('app.timezone'))->format('d.m.Y H:i');
        } catch (\Throwable $exception) {
            return (string) $value;
        }
    };
    $formatEta = function ($seconds): string {
        if (! is_numeric($seconds) || (int) $seconds <= 0) {
            return 'Hesaplaniyor';
        }

        return (string) max(1, (int) ceil(((int) $seconds) / 60)).' dk';
    };
    $eventLabel = fn (?string $eventType): string => match ((string) $eventType) {
        'eta_update' => 'Tahmini varis guncellendi',
        'courier_assigned' => 'Kurye atama bildirimi',
        'courier_arriving' => 'Kurye yaklasiyor',
        'pickup_confirmed' => 'Alis islemi tamamlandi',
        'delivery_confirmed' => 'Teslimat onaylandi',
        default => trim((string) $eventType) !== '' ? ucfirst(str_replace('_', ' ', (string) $eventType)) : 'Kurye guncellemesi',
    };
    $proofStageLabel = fn (?string $stage): string => match ((string) $stage) {
        'pickup' => 'Alis kaniti',
        'delivery' => 'Teslimat kaniti',
        default => trim((string) $stage) !== '' ? ucfirst(str_replace('_', ' ', (string) $stage)) : 'Kanit',
    };
    $proofTypeLabel = fn (?string $proofType): string => match ((string) $proofType) {
        'photo' => 'Fotograf',
        'signature' => 'Imza',
        'barcode' => 'Barkod',
        default => trim((string) $proofType) !== '' ? ucfirst(str_replace('_', ' ', (string) $proofType)) : 'Dosya',
    };
@endphp

@push('styles')
<style>
    .tracking-form {
        margin-top: 10px;
    }

    .tracking-status-bar {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }

    .tracking-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .tracking-grid .badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 800;
        background: rgba(124, 58, 237, 0.12);
        border: 1px solid rgba(124, 58, 237, 0.18);
        color: var(--accent);
    }

    .summary,
    .timeline,
    .events,
    .proofs {
        display: grid;
        gap: 12px;
    }

    .row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: flex-start;
    }

    .row strong {
        max-width: 64%;
        text-align: right;
    }

    .item,
    .empty {
        padding: 16px 18px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    [data-theme="light"] .item,
    [data-theme="light"] .empty {
        background: rgba(255, 255, 255, 0.84);
        border-color: rgba(15, 23, 42, 0.06);
    }

    .item strong {
        display: block;
        margin-bottom: 6px;
        font-size: 1rem;
    }

    .proof-link {
        color: var(--accent);
        font-weight: 800;
        text-decoration: none;
    }

    .proof-link:hover {
        text-decoration: underline;
    }

    .tracking-card-header {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: center;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .tracking-card-header p {
        margin: 6px 0 0;
    }

    .tracking-hero-actions {
        display: grid;
        gap: 10px;
        margin-top: 8px;
    }

    .tracking-hero-actions a {
        color: var(--text-primary);
        text-decoration: none;
        font-weight: 700;
    }

    .tracking-hero-actions a:hover {
        color: var(--accent);
    }

    @media (max-width: 1080px) {
        .tracking-grid {
            grid-template-columns: 1fr;
        }

        .row {
            flex-direction: column;
        }

        .row strong {
            max-width: none;
            text-align: left;
        }
    }
</style>
@endpush

<div class="checkout-shell checkout-shell--wide">
    <section class="checkout-hero-grid">
        <article class="checkout-card checkout-card--hero">
            <div class="checkout-lead">
                <div class="section-badge">
                    <i class="fa-solid fa-route"></i> Acik siparisler icin
                </div>
                <div class="checkout-meta">
                    <span class="checkout-chip">Telefon + siparis no ile</span>
                    <span class="checkout-chip checkout-chip--info">Anlik durum ve kanit kayitlari</span>
                </div>
                <h1>Siparisinizi tek ekrandan takip edin.</h1>
                <p>{{ $pageCopy['intro'] ?? 'Siparis numaraniz ve sipariste kullandiginiz telefon ile guncel durum bilgilerini goruntuleyin.' }}</p>
            </div>

            <form method="GET" action="{{ route('checkout.tracking') }}" class="checkout-form-grid checkout-form-grid--inline tracking-form">
                <div class="checkout-field">
                    <label for="tracking-order-no">Siparis Numarasi</label>
                    <input id="tracking-order-no" name="order_no" type="text" value="{{ $prefillOrderNo }}" placeholder="ORN: ORD20260314ABCDE" autocomplete="off" required>
                </div>
                <div class="checkout-field">
                    <label for="tracking-phone">Telefon</label>
                    <input id="tracking-phone" name="phone" type="tel" inputmode="tel" value="{{ $prefillPhone }}" placeholder="0551 356 72 92" autocomplete="tel" required>
                </div>
                <div class="checkout-actions">
                    <button type="submit" class="btn btn-primary">Siparisi Sorgula</button>
                </div>
            </form>

            @if ($lookupError)
                <div class="checkout-alert checkout-alert--error">
                    <div>{{ $lookupError }}</div>
                    <div class="checkout-note" style="margin-top:8px;">{{ $pageCopy['error_help'] ?? 'Bilgiler eslesmiyorsa destek hattimizla iletisime gecin; ekip siparis kaydini kontrol etsin.' }}</div>
                </div>
            @elseif (! $lookupSubmitted)
                <div class="checkout-alert checkout-alert--info">
                    {{ $pageCopy['help'] ?? 'Siparis numarasini SMS, e-posta veya musteri panelinizdeki siparis kartindan bulabilirsiniz.' }}
                </div>
            @endif
        </article>

        <aside class="checkout-card checkout-card--support">
            <div class="checkout-lead" style="gap:10px;">
                <div class="section-badge">
                    <i class="fa-solid fa-headset"></i> Destek ve guven
                </div>
                <h2>Takipte zorlanirsaniz hizli destek alin.</h2>
                <p>{{ $support['support_note'] ?? 'Destek ekibimiz telefon, WhatsApp veya e-posta uzerinden yardimci olur.' }}</p>
            </div>

            <div class="checkout-list" style="margin-top:18px;">
                <div class="checkout-list-item">
                    <strong>Canli destek kanallari</strong>
                    <div class="tracking-hero-actions">
                        <a href="{{ $support['phone_href'] ?? 'tel:+905513567292' }}">{{ $support['phone_display'] ?? '+90 551 356 72 92' }}</a>
                        <a href="{{ $support['whatsapp_href'] ?? 'https://wa.me/905513567292' }}" target="_blank" rel="noopener">WhatsApp destegi</a>
                        <a href="{{ $support['contact_href'] ?? route('contact') }}">Iletisim sayfasi</a>
                    </div>
                </div>
                <div class="checkout-list-item">
                    <strong>Bilmeniz gerekenler</strong>
                    <p>Takip akisi durum gecmisi, kurye hareketleri ve alis/teslim kanitlarini ayni ekranda sunar.</p>
                </div>
                <div class="checkout-list-item">
                    <strong>Yasal baglantilar</strong>
                    <div class="tracking-hero-actions">
                        <a href="{{ $support['privacy_href'] ?? url('/kvkk') }}">KVKK</a>
                        <a href="{{ $support['terms_href'] ?? url('/kullanim-kosullari') }}">Kullanim kosullari</a>
                    </div>
                </div>
            </div>
        </aside>
    </section>

    @if ($tracking)
        <section
            class="checkout-card checkout-card--panel"
            data-tracking-autorefresh
            data-order-no="{{ $prefillOrderNo }}"
            data-phone="{{ $prefillPhone }}"
            data-endpoint="{{ route('api.v1.order-tracking.show') }}"
        >
            <div class="tracking-status-bar">
                <div class="checkout-alert checkout-alert--info" style="flex:1;">
                    Canli takip acik. Bilgiler her <strong data-tracking-countdown>30</strong> sn yenilenir.
                </div>
                <button type="button" class="btn btn-outline" data-tracking-toggle>Otomatik Yenilemeyi Durdur</button>
            </div>
        </section>

        <div class="tracking-grid" data-tracking-root>
            <section class="checkout-card checkout-card--panel">
                <div class="tracking-card-header">
                    <div>
                        <h2>Siparis Ozeti</h2>
                        <p>Aktif siparisin durum, odeme ve rota bilgisini buradan takip edin.</p>
                    </div>
                    <span class="badge" data-tracking-state-badge>Durum: {{ $stateLabel($order['state'] ?? null) }}</span>
                </div>
                <div class="summary" data-tracking-summary>
                    <div class="row"><small>Siparis No</small><strong>{{ $order['order_no'] ?? '-' }}</strong></div>
                    <div class="row"><small>Odeme</small><strong>{{ $paymentLabel($order['payment_state'] ?? null) }}</strong></div>
                    <div class="row"><small>Toplam</small><strong>{{ $order['total_amount_formatted'] ?? '-' }}</strong></div>
                    <div class="row"><small>Alis adresi</small><strong>{{ $order['pickup_address'] ?? '-' }}</strong></div>
                    <div class="row"><small>Teslimat adresi</small><strong>{{ $order['dropoff_address'] ?? '-' }}</strong></div>
                    <div class="row"><small>Son guncelleme</small><strong>{{ $latestTrackingEvent['note'] ?? 'Yeni durum bilgisi bekleniyor.' }}</strong></div>
                    <div class="row"><small>Tahmini varis</small><strong>{{ $formatEta($latestTrackingEvent['eta_seconds'] ?? null) }}</strong></div>
                </div>
            </section>

            <section class="checkout-card checkout-card--panel">
                <div class="tracking-card-header">
                    <div>
                        <h2>Durum Gecmisi</h2>
                        <p>Siparisin sistemde hangi adimlardan gectigini izleyin.</p>
                    </div>
                </div>
                <div class="timeline" data-tracking-timeline>
                    @forelse ($timeline as $item)
                        <article class="item">
                            <strong>{{ $stateLabel($item['to_state'] ?? null) }}</strong>
                            <p>{{ ! empty($item['from_state']) ? 'Bir onceki asama: '.$stateLabel($item['from_state']) : 'Siparisiniz sisteme alindi.' }}</p>
                            <small>{{ $formatDate($item['created_at'] ?? null) }}</small>
                        </article>
                    @empty
                        <div class="empty">Henuz yeni bir guncelleme yok.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="tracking-grid">
            <section class="checkout-card checkout-card--panel">
                <div class="tracking-card-header">
                    <div>
                        <h2>Kurye Hareketleri</h2>
                        <p>Kurye tarafindan kayda gecen not ve ETA guncellemeleri.</p>
                    </div>
                </div>
                <div class="events" data-tracking-events>
                    @forelse ($trackingEvents as $item)
                        <article class="item">
                            <strong>{{ $eventLabel($item['event_type'] ?? null) }}</strong>
                            <p>{{ $item['note'] ?? 'Yeni kurye notu bulunmuyor.' }}</p>
                            <small>ETA: {{ $formatEta($item['eta_seconds'] ?? null) }} | {{ $formatDate($item['created_at'] ?? null) }}</small>
                        </article>
                    @empty
                        <div class="empty">Aktif kurye hareketi henuz kaydedilmedi.</div>
                    @endforelse
                </div>
            </section>

            <section class="checkout-card checkout-card--panel">
                <div class="tracking-card-header">
                    <div>
                        <h2>Teslimat Kanitlari</h2>
                        <p>Alis ve teslim aninda toplanan gorsel veya imza kayitlari.</p>
                    </div>
                </div>
                <div class="proofs" data-tracking-proofs>
                    @forelse ($proofs as $item)
                        <article class="item">
                            <strong>{{ $proofStageLabel($item['stage'] ?? null) }} / {{ $proofTypeLabel($item['proof_type'] ?? null) }}</strong>
                            @if (! empty($item['file_url']))
                                <p><a class="proof-link" href="{{ $item['file_url'] }}" target="_blank" rel="noreferrer">Dosyayi ac</a></p>
                            @else
                                <p>Dosya baglantisi henuz eklenmedi.</p>
                            @endif
                            <small>{{ $formatDate($item['created_at'] ?? null) }}</small>
                        </article>
                    @empty
                        <div class="empty">Alis veya teslimata ait kanit dosyasi henuz eklenmedi.</div>
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

  const endpoint = String(refreshNode.getAttribute('data-endpoint') || '').trim();
  const orderNo = String(refreshNode.getAttribute('data-order-no') || '').trim();
  const phone = String(refreshNode.getAttribute('data-phone') || '').trim();
  const countdownNode = refreshNode.querySelector('[data-tracking-countdown]');
  const toggleButton = refreshNode.querySelector('[data-tracking-toggle]');
  const summaryNode = document.querySelector('[data-tracking-summary]');
  const timelineNode = document.querySelector('[data-tracking-timeline]');
  const eventsNode = document.querySelector('[data-tracking-events]');
  const proofsNode = document.querySelector('[data-tracking-proofs]');
  const badgeNode = document.querySelector('[data-tracking-state-badge]');
  const initialSeconds = 30;
  let enabled = true;
  let remainingSeconds = initialSeconds;

  const stateLabels = {
    draft: 'Hazirlaniyor',
    pending_payment: 'Odeme bekleniyor',
    paid: 'Hazir',
    assigned: 'Kurye atandi',
    picked_up: 'Yolda',
    delivered: 'Teslim edildi',
    closed: 'Tamamlandi',
    cancelled: 'Iptal edildi',
    failed: 'Islem tamamlanamadi',
  };
  const paymentLabels = {
    pending: 'Beklemede',
    awaiting_reconcile: 'Kontrol ediliyor',
    cash_on_delivery: 'Kapida odeme',
    succeeded: 'Tamamlandi',
    failed: 'Basarisiz',
    cancelled: 'Iptal edildi',
  };
  const eventLabels = {
    eta_update: 'Tahmini varis guncellendi',
    courier_assigned: 'Kurye atama bildirimi',
    courier_arriving: 'Kurye yaklasiyor',
    pickup_confirmed: 'Alis islemi tamamlandi',
    delivery_confirmed: 'Teslimat onaylandi',
  };
  const proofStageLabels = {
    pickup: 'Alis kaniti',
    delivery: 'Teslimat kaniti',
  };
  const proofTypeLabels = {
    photo: 'Fotograf',
    signature: 'Imza',
    barcode: 'Barkod',
  };

  const escapeHtml = (value) => String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');

  const stateLabel = (value) => stateLabels[String(value || '')] || String(value || '-').replace(/_/g, ' ');
  const paymentLabel = (value) => paymentLabels[String(value || '')] || String(value || '-').replace(/_/g, ' ');
  const eventLabel = (value) => eventLabels[String(value || '')] || String(value || 'Kurye guncellemesi').replace(/_/g, ' ');
  const proofStageLabel = (value) => proofStageLabels[String(value || '')] || String(value || 'Kanit').replace(/_/g, ' ');
  const proofTypeLabel = (value) => proofTypeLabels[String(value || '')] || String(value || 'Dosya').replace(/_/g, ' ');
  const formatEta = (value) => {
    const seconds = Number(value || 0);
    if (!Number.isFinite(seconds) || seconds <= 0) {
      return 'Hesaplaniyor';
    }

    return `${Math.max(1, Math.ceil(seconds / 60))} dk`;
  };
  const formatDate = (value) => {
    if (!value) {
      return '-';
    }

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
      return String(value);
    }

    return new Intl.DateTimeFormat('tr-TR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    }).format(date);
  };

  const updateCountdown = () => {
    if (countdownNode) {
      countdownNode.textContent = String(Math.max(0, remainingSeconds));
    }
  };

  const buildRefreshUrl = () => {
    const params = new URLSearchParams({
      order_no: orderNo,
      phone: phone,
    });

    return `${endpoint}?${params.toString()}`;
  };

  const renderSummary = (order, trackingEvents) => {
    if (!summaryNode || !order) {
      return;
    }

    const latestEvent = Array.isArray(trackingEvents) && trackingEvents.length > 0
      ? trackingEvents.slice().sort((left, right) => new Date(right.created_at || 0) - new Date(left.created_at || 0))[0]
      : null;

    summaryNode.innerHTML = [
      ['Siparis No', order.order_no || '-'],
      ['Odeme', paymentLabel(order.payment_state)],
      ['Toplam', order.total_amount_formatted || '-'],
      ['Alis adresi', order.pickup_address || '-'],
      ['Teslimat adresi', order.dropoff_address || '-'],
      ['Son guncelleme', latestEvent?.note || 'Yeni durum bilgisi bekleniyor.'],
      ['Tahmini varis', formatEta(latestEvent?.eta_seconds)],
    ].map(([label, value]) => `<div class="row"><small>${escapeHtml(label)}</small><strong>${escapeHtml(value)}</strong></div>`).join('');
  };

  const renderTimeline = (timeline) => {
    if (!timelineNode) {
      return;
    }

    if (!Array.isArray(timeline) || timeline.length === 0) {
      timelineNode.innerHTML = '<div class="empty">Henuz yeni bir guncelleme yok.</div>';
      return;
    }

    timelineNode.innerHTML = timeline.map((item) => {
      const fromState = item?.from_state ? `Bir onceki asama: ${stateLabel(item.from_state)}` : 'Siparisiniz sisteme alindi.';
      return `
        <article class="item">
          <strong>${escapeHtml(stateLabel(item?.to_state))}</strong>
          <p>${escapeHtml(fromState)}</p>
          <small>${escapeHtml(formatDate(item?.created_at))}</small>
        </article>
      `;
    }).join('');
  };

  const renderTrackingEvents = (trackingEvents) => {
    if (!eventsNode) {
      return;
    }

    if (!Array.isArray(trackingEvents) || trackingEvents.length === 0) {
      eventsNode.innerHTML = '<div class="empty">Aktif kurye hareketi henuz kaydedilmedi.</div>';
      return;
    }

    eventsNode.innerHTML = trackingEvents.map((item) => `
      <article class="item">
        <strong>${escapeHtml(eventLabel(item?.event_type))}</strong>
        <p>${escapeHtml(item?.note || 'Yeni kurye notu bulunmuyor.')}</p>
        <small>${escapeHtml(`ETA: ${formatEta(item?.eta_seconds)} | ${formatDate(item?.created_at)}`)}</small>
      </article>
    `).join('');
  };

  const renderProofs = (proofs) => {
    if (!proofsNode) {
      return;
    }

    if (!Array.isArray(proofs) || proofs.length === 0) {
      proofsNode.innerHTML = '<div class="empty">Alis veya teslimata ait kanit dosyasi henuz eklenmedi.</div>';
      return;
    }

    proofsNode.innerHTML = proofs.map((item) => {
      const fileUrl = String(item?.file_url || '').trim();
      const fileLink = fileUrl !== ''
        ? `<a class="proof-link" href="${escapeHtml(fileUrl)}" target="_blank" rel="noreferrer">Dosyayi ac</a>`
        : 'Dosya baglantisi henuz eklenmedi.';

      return `
        <article class="item">
          <strong>${escapeHtml(`${proofStageLabel(item?.stage)} / ${proofTypeLabel(item?.proof_type)}`)}</strong>
          <p>${fileLink}</p>
          <small>${escapeHtml(formatDate(item?.created_at))}</small>
        </article>
      `;
    }).join('');
  };

  const renderTracking = (payload) => {
    const order = payload?.order || null;
    const trackingEvents = Array.isArray(payload?.tracking_events) ? payload.tracking_events : [];
    const proofs = Array.isArray(payload?.proofs) ? payload.proofs : [];

    if (badgeNode && order) {
      badgeNode.textContent = `Durum: ${stateLabel(order.state)}`;
    }

    renderSummary(order, trackingEvents);
    renderTimeline(Array.isArray(payload?.timeline) ? payload.timeline : []);
    renderTrackingEvents(trackingEvents);
    renderProofs(proofs);
  };

  const refreshTracking = async () => {
    if (!enabled || document.hidden || endpoint === '' || orderNo === '' || phone === '') {
      return;
    }

    try {
      const response = await fetch(buildRefreshUrl(), {
        headers: { Accept: 'application/json' },
      });
      const json = await response.json();

      if (response.ok && json?.success && json?.data) {
        renderTracking(json.data);
      }
    } catch (error) {
      // Keep page stable on transient network issues; the next cycle will retry.
    }
  };

  toggleButton?.addEventListener('click', function () {
    enabled = !enabled;
    toggleButton.textContent = enabled
      ? 'Otomatik Yenilemeyi Durdur'
      : 'Otomatik Yenilemeyi Baslat';

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
      void refreshTracking();
    }

    updateCountdown();
  }, 1000);
});
</script>
@endpush
@endif
</x-checkout::layouts.public>
