<x-checkout::layouts.public title="Siparis Detayi" description="SimdiGetir musteri siparis detayi">
@php
    $checkoutSnapshot = (array) ($order->checkout_snapshot ?? []);
    $orderNotes = (array) ($order->notes ?? []);
    $deliveryNotes = (string) ($orderNotes['delivery_notes'] ?? $checkoutSnapshot['notes']['delivery_notes'] ?? '');
    $bankTransfer = (array) ($bankTransfer ?? []);
    $support = is_array($support ?? null) ? $support : [];
    $labelize = static fn (?string $value): string => trim((string) $value) !== ''
        ? ucfirst(str_replace('_', ' ', (string) $value))
        : '-';
@endphp

@push('styles')
<style>
    .order-detail-stats,
    .order-detail-grid,
    .order-detail-columns {
        display: grid;
        gap: 14px;
    }

    .order-detail-stats {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        margin-top: 18px;
    }

    .order-detail-stat,
    .order-detail-box,
    .order-detail-item,
    .order-detail-empty {
        padding: 16px 18px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    [data-theme="light"] .order-detail-stat,
    [data-theme="light"] .order-detail-box,
    [data-theme="light"] .order-detail-item,
    [data-theme="light"] .order-detail-empty {
        background: rgba(255, 255, 255, 0.84);
        border-color: rgba(15, 23, 42, 0.06);
    }

    .order-detail-stat strong,
    .order-detail-box strong,
    .order-detail-item strong {
        display: block;
        margin-bottom: 6px;
    }

    .order-detail-stat strong {
        font-family: var(--sg-font-display);
        font-size: clamp(1.6rem, 3vw, 2.2rem);
        line-height: 1;
    }

    .order-detail-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .order-detail-columns {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .order-detail-stack,
    .order-detail-actions,
    .order-detail-links {
        display: grid;
        gap: 12px;
    }

    .order-detail-links a {
        color: var(--text-primary);
        text-decoration: none;
        font-weight: 700;
    }

    .order-detail-links a:hover {
        color: var(--accent);
    }

    .order-detail-proof-link {
        color: var(--accent);
        font-weight: 800;
        text-decoration: none;
    }

    .order-detail-proof-link:hover {
        text-decoration: underline;
    }

    .order-detail-note {
        white-space: pre-line;
    }

    @media (max-width: 1080px) {
        .order-detail-stats,
        .order-detail-grid,
        .order-detail-columns {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

<div class="checkout-shell checkout-shell--wide">
    <section class="checkout-hero-grid">
        <article class="checkout-card checkout-card--hero">
            <div class="checkout-lead">
                <div class="section-badge">
                    <i class="fa-solid fa-box-open"></i> Musteri siparis detayi
                </div>
                <div class="checkout-meta">
                    <span class="checkout-chip">{{ $labelize($order->state) }}</span>
                    <span class="checkout-chip checkout-chip--info">{{ $labelize($order->payment_state) }}</span>
                    <span class="checkout-chip">{{ $order->payment_method ?? '-' }} / {{ $order->payment_timing ?? '-' }}</span>
                </div>
                <h1>{{ $order->order_no }}</h1>
                <p>{{ optional($order->created_at)->format('Y-m-d H:i') }} | Musteri: {{ $customer->name }} | Telefon: {{ $customer->phone ?? '-' }}</p>
            </div>

            <div class="order-detail-stats">
                <div class="order-detail-stat">
                    <small class="checkout-muted">Toplam</small>
                    <strong>{{ number_format(((int) $order->total_amount) / 100, 2, ',', '.') }}</strong>
                    <span class="checkout-muted">{{ $order->currency }}</span>
                </div>
                <div class="order-detail-stat">
                    <small class="checkout-muted">Mesafe</small>
                    <strong>{{ $order->distance_meters ? number_format(((int) $order->distance_meters) / 1000, 1, ',', '.') : '-' }}</strong>
                    <span class="checkout-muted">km</span>
                </div>
                <div class="order-detail-stat">
                    <small class="checkout-muted">Tahmini sure</small>
                    <strong>{{ $order->duration_seconds ? ceil(((int) $order->duration_seconds) / 60) : '-' }}</strong>
                    <span class="checkout-muted">dakika</span>
                </div>
            </div>

            <div class="checkout-actions" style="margin-top:18px;">
                <a href="{{ route('checkout.customer.dashboard') }}" class="btn btn-outline">Panele don</a>
                <a href="{{ route('checkout.tracking', ['order_no' => $order->order_no, 'phone' => $customer->phone]) }}" class="btn btn-primary">Canli takip</a>
                <a href="{{ route('checkout.customer.orders.receipt', ['orderNo' => $order->order_no]) }}" class="btn btn-outline">Dekont</a>
            </div>
        </article>

        <aside class="checkout-card checkout-card--support">
            <div class="checkout-lead" style="gap:10px;">
                <div class="section-badge">
                    <i class="fa-solid fa-circle-info"></i> Operasyon ve destek
                </div>
                <h2>Siparis bilgileri ve destek kanallari</h2>
                <p>{{ $support['support_note'] ?? 'Destek ekibimiz telefon, WhatsApp veya e-posta uzerinden yardimci olur.' }}</p>
            </div>

            <div class="checkout-list" style="margin-top:18px;">
                <div class="checkout-list-item">
                    <strong>Musteri bilgisi</strong>
                    <p>{{ $customer->name }}</p>
                    <div class="checkout-link-list">
                        <a href="{{ $support['phone_href'] ?? 'tel:+905513567292' }}">{{ $support['phone_display'] ?? '+90 551 356 72 92' }}</a>
                        <a href="{{ $support['whatsapp_href'] ?? 'https://wa.me/905513567292' }}" target="_blank" rel="noopener">WhatsApp destegi</a>
                    </div>
                </div>
                @if (($order->payment_method ?? '') === 'bank_transfer')
                    <div class="checkout-list-item">
                        <strong>{{ $bankTransfer['title'] ?? 'Havale / EFT odeme talimati' }}</strong>
                        <p class="order-detail-note">{{ trim(collect([
                            $bankTransfer['body'] ?? null,
                            !empty($bankTransfer['bank_name']) ? 'Banka: '.$bankTransfer['bank_name'] : null,
                            !empty($bankTransfer['account_holder']) ? 'Hesap Sahibi: '.$bankTransfer['account_holder'] : null,
                            !empty($bankTransfer['iban']) ? 'IBAN: '.$bankTransfer['iban'] : null,
                            $bankTransfer['reference_note'] ?? null,
                        ])->filter()->implode("\n")) }}</p>
                    </div>
                @endif
                <div class="checkout-list-item">
                    <strong>Yasal baglantilar</strong>
                    <div class="order-detail-links">
                        <a href="{{ $support['privacy_href'] ?? url('/kvkk') }}">KVKK</a>
                        <a href="{{ $support['terms_href'] ?? url('/kullanim-kosullari') }}">Kullanim kosullari</a>
                    </div>
                </div>
            </div>
        </aside>
    </section>

    <section class="checkout-card checkout-card--panel">
        <div class="checkout-panel-head">
            <div>
                <h2>Adres, paket ve odeme kayitlari</h2>
                <p>Gonderen, alici, paketler, odeme hareketleri ve proof kayitlari ayni siparis detayinda tutulur.</p>
            </div>
        </div>

        <div class="order-detail-grid">
            <div class="order-detail-box">
                <strong>Alis</strong>
                <div class="checkout-muted">{{ $order->pickup_name ?: '-' }}</div>
                <div>{{ $order->pickup_phone ?: '-' }}</div>
                <div>{{ $order->pickup_address ?: '-' }}</div>
            </div>
            <div class="order-detail-box">
                <strong>Teslimat</strong>
                <div class="checkout-muted">{{ $order->dropoff_name ?: '-' }}</div>
                <div>{{ $order->dropoff_phone ?: '-' }}</div>
                <div>{{ $order->dropoff_address ?: '-' }}</div>
            </div>
            <div class="order-detail-box">
                <strong>Odeme ve not</strong>
                <div>{{ $order->payment_method ?? '-' }} / {{ $order->payment_timing ?? '-' }}</div>
                <div class="checkout-muted">Payer: {{ $order->payer_role ?? '-' }}</div>
                <div class="order-detail-note">{{ $deliveryNotes !== '' ? $deliveryNotes : 'Teslimat notu yok.' }}</div>
            </div>
        </div>

        <div class="order-detail-columns" style="margin-top:18px;">
            <div class="order-detail-stack">
                <div class="checkout-muted">Paketler</div>
                @forelse ($order->packages as $item)
                    <div class="order-detail-item">
                        <strong>{{ $item->package_type ?: '-' }}</strong>
                        <div class="checkout-muted">Adet: {{ $item->quantity }}</div>
                        <div class="checkout-muted">Agirlik: {{ $item->weight_grams ?? '-' }} gr</div>
                        <div class="checkout-muted">Deger: {{ $item->declared_value_amount ?? '-' }}</div>
                        <div>{{ $item->description ?: 'Aciklama yok.' }}</div>
                    </div>
                @empty
                    <div class="order-detail-empty">Paket kaydi yok.</div>
                @endforelse
            </div>

            <div class="order-detail-stack">
                <div class="checkout-muted">Odeme hareketleri</div>
                @forelse ($order->paymentTransactions as $item)
                    <div class="order-detail-item">
                        <strong>{{ $item->provider }}</strong>
                        <div class="checkout-muted">Durum: {{ $item->status }}</div>
                        <div class="checkout-muted">Ref: {{ $item->provider_reference ?: '-' }}</div>
                        <div>{{ number_format(((int) $item->amount) / 100, 2, ',', '.') }} {{ $item->currency }}</div>
                    </div>
                @empty
                    <div class="order-detail-empty">Odeme hareketi yok.</div>
                @endforelse
            </div>

            <div class="order-detail-stack">
                <div class="checkout-muted">Proof kayitlari</div>
                @forelse ($order->orderProofs as $item)
                    <div class="order-detail-item">
                        <strong>{{ $labelize($item->stage) }} / {{ $labelize($item->proof_type) }}</strong>
                        <div class="checkout-muted">{{ optional($item->created_at)->format('Y-m-d H:i') }}</div>
                        <div>
                            @if ($item->file_url)
                                <a href="{{ $item->file_url }}" target="_blank" rel="noreferrer" class="order-detail-proof-link">Dosyayi ac</a>
                            @else
                                Dosya baglantisi yok.
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="order-detail-empty">Proof kaydi yok.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="checkout-card checkout-card--panel">
        <div class="checkout-panel-head">
            <div>
                <h2>Durum ve operasyon akisi</h2>
                <p>State timeline, kurye hareketleri ve checkout snapshot bilgileri ayni yerde tutulur.</p>
            </div>
        </div>

        <div class="order-detail-columns">
            <div class="order-detail-stack">
                <div class="checkout-muted">State timeline</div>
                @forelse ($order->stateLogs as $item)
                    <div class="order-detail-item">
                        <strong>{{ $labelize($item->to_state) }}</strong>
                        <div class="checkout-muted">{{ $item->reason ?? '-' }}</div>
                        <div class="checkout-muted">{{ optional($item->created_at)->format('Y-m-d H:i') }}</div>
                    </div>
                @empty
                    <div class="order-detail-empty">Timeline kaydi yok.</div>
                @endforelse
            </div>

            <div class="order-detail-stack">
                <div class="checkout-muted">Kurye hareketleri</div>
                @forelse ($order->trackingEvents as $item)
                    <div class="order-detail-item">
                        <strong>{{ $labelize($item->event_type) }}</strong>
                        <div class="checkout-muted">{{ $item->note ?? 'Not yok.' }}</div>
                        <div class="checkout-muted">{{ optional($item->created_at)->format('Y-m-d H:i') }}</div>
                    </div>
                @empty
                    <div class="order-detail-empty">Tracking eventi yok.</div>
                @endforelse
            </div>

            <div class="order-detail-stack">
                <div class="checkout-muted">Checkout snapshot</div>
                <div class="order-detail-item">
                    <strong>Hizmet</strong>
                    <div>{{ $checkoutSnapshot['service_label'] ?? $checkoutSnapshot['service_type'] ?? $order->vehicle_type ?? '-' }}</div>
                </div>
                <div class="order-detail-item">
                    <strong>Ayni kisi</strong>
                    <div>{{ !empty($checkoutSnapshot['same_person']) ? 'Evet' : 'Hayir' }}</div>
                </div>
                <div class="order-detail-item">
                    <strong>Fiyat kaynagi</strong>
                    <div>{{ data_get($order->price_breakdown, 'source', '-') }}</div>
                </div>
            </div>
        </div>
    </section>
</div>
</x-checkout::layouts.public>
