<x-checkout::layouts.public title="Odeme Dekontu" description="SimdiGetir odeme dekontu">
@php
    $latestPayment = $order->paymentTransactions->first();
    $support = is_array($support ?? null) ? $support : [];
@endphp

@push('styles')
<style>
    .receipt-grid,
    .receipt-detail-grid {
        display: grid;
        gap: 14px;
    }

    .receipt-detail-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .receipt-box,
    .receipt-item {
        padding: 16px 18px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    [data-theme="light"] .receipt-box,
    [data-theme="light"] .receipt-item {
        background: rgba(255, 255, 255, 0.84);
        border-color: rgba(15, 23, 42, 0.06);
    }

    .receipt-box strong,
    .receipt-item strong {
        display: block;
        margin-bottom: 6px;
    }

    .receipt-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 18px;
    }

    .receipt-links {
        display: grid;
        gap: 10px;
    }

    .receipt-links a {
        color: var(--text-primary);
        text-decoration: none;
        font-weight: 700;
    }

    .receipt-links a:hover {
        color: var(--accent);
    }

    @media print {
        header,
        footer,
        #back-to-top,
        .floating-whatsapp,
        .receipt-print-hidden {
            display: none !important;
        }

        .checkout-public-page {
            padding: 0 !important;
        }

        .checkout-card {
            box-shadow: none !important;
            border: 1px solid #d1d5db !important;
            backdrop-filter: none !important;
        }

        .receipt-box,
        .receipt-item {
            background: #fff !important;
            border: 1px solid #e5e7eb !important;
        }
    }

    @media (max-width: 1080px) {
        .receipt-detail-grid {
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
                    <i class="fa-solid fa-file-invoice"></i> Odeme ozeti
                </div>
                <div class="checkout-meta">
                    <span class="checkout-chip">{{ $order->order_no }}</span>
                    <span class="checkout-chip checkout-chip--info">{{ optional($order->created_at)->format('d.m.Y H:i') }}</span>
                </div>
                <h1>Odeme dekontu</h1>
                <p>Bu sayfa siparisin odeme ozeti, son provider hareketi ve yazdirilabilir temel alanlarini bir arada sunar.</p>
            </div>

            <div class="receipt-actions receipt-print-hidden">
                <a href="{{ route('checkout.customer.orders.show', ['orderNo' => $order->order_no]) }}" class="btn btn-outline">Siparis detayina don</a>
                <button type="button" class="btn btn-primary" onclick="window.print()">Yazdir</button>
            </div>
        </article>

        <aside class="checkout-card checkout-card--support receipt-print-hidden">
            <div class="checkout-lead" style="gap:10px;">
                <div class="section-badge">
                    <i class="fa-solid fa-headset"></i> Destek
                </div>
                <h2>Dekont sonrasi destek kanallari</h2>
                <p>{{ $support['support_note'] ?? 'Destek ekibimiz telefon, WhatsApp veya e-posta uzerinden yardimci olur.' }}</p>
            </div>
            <div class="checkout-list" style="margin-top:18px;">
                <div class="checkout-list-item">
                    <strong>Iletisim</strong>
                    <div class="receipt-links">
                        <a href="{{ $support['phone_href'] ?? 'tel:+905513567292' }}">{{ $support['phone_display'] ?? '+90 551 356 72 92' }}</a>
                        <a href="{{ $support['whatsapp_href'] ?? 'https://wa.me/905513567292' }}" target="_blank" rel="noopener">WhatsApp destegi</a>
                        <a href="{{ $support['email_href'] ?? 'mailto:webgetir@simdigetir.com' }}">{{ $support['email'] ?? 'webgetir@simdigetir.com' }}</a>
                    </div>
                </div>
            </div>
        </aside>
    </section>

    <section class="checkout-card checkout-card--panel">
        <div class="checkout-panel-head">
            <div>
                <h2>Siparis ve odeme ozeti</h2>
                <p>Siparis durumu, odeme tipi ve son provider kaydi bu dekont sayfasinda sabitlenir.</p>
            </div>
        </div>

        <div class="receipt-detail-grid">
            <article class="receipt-item">
                <strong>Musteri</strong>
                <div>{{ $customer->name }}</div>
                <div class="checkout-muted">{{ $customer->phone ?? '-' }}</div>
                <div class="checkout-muted">{{ $customer->email ?? '-' }}</div>
            </article>

            <article class="receipt-item">
                <strong>Siparis ozeti</strong>
                <div>Durum: {{ $order->state }}</div>
                <div>Odeme durumu: {{ $order->payment_state }}</div>
                <div class="checkout-muted">Odeme tipi: {{ $order->payment_method ?? '-' }} / {{ $order->payment_timing ?? '-' }}</div>
            </article>

            <article class="receipt-item">
                <strong>Tutar</strong>
                <div>{{ number_format(((int) $order->total_amount) / 100, 2, ',', '.') }} {{ strtoupper((string) $order->currency) }}</div>
            </article>

            <article class="receipt-item">
                <strong>Son odeme islemi</strong>
                @if ($latestPayment)
                    <div>Provider: {{ $latestPayment->provider }}</div>
                    <div>Durum: {{ $latestPayment->status }}</div>
                    <div class="checkout-muted">Referans: {{ $latestPayment->provider_reference ?: '-' }}</div>
                    <div class="checkout-muted">Tutar: {{ number_format(((int) $latestPayment->amount) / 100, 2, ',', '.') }} {{ strtoupper((string) $latestPayment->currency) }}</div>
                @else
                    <div class="checkout-muted">Bu siparis icin odeme islemi kaydi bulunamadi.</div>
                @endif
            </article>
        </div>
    </section>
</div>
</x-checkout::layouts.public>
