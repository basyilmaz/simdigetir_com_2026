<x-checkout::layouts.public title="Hesabim" description="SimdiGetir musteri paneli ve siparis yonetimi">
@php
    $support = is_array($support ?? null) ? $support : [];
    $stateLabel = static fn (?string $value): string => trim((string) $value) !== ''
        ? ucfirst(str_replace('_', ' ', (string) $value))
        : '-';
@endphp

@push('styles')
<style>
    .portal-hero-actions form {
        margin: 0;
    }

    .portal-kpi-grid,
    .portal-order-grid,
    .portal-detail-grid {
        display: grid;
        gap: 14px;
    }

    .portal-kpi-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        margin-top: 18px;
    }

    .portal-kpi,
    .portal-order-box,
    .portal-detail-item,
    .portal-detail-empty,
    .portal-detail-toggle {
        padding: 16px 18px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    [data-theme="light"] .portal-kpi,
    [data-theme="light"] .portal-order-box,
    [data-theme="light"] .portal-detail-item,
    [data-theme="light"] .portal-detail-empty,
    [data-theme="light"] .portal-detail-toggle {
        background: rgba(255, 255, 255, 0.82);
        border-color: rgba(15, 23, 42, 0.06);
    }

    .portal-kpi strong,
    .portal-order-head strong {
        display: block;
        font-family: var(--sg-font-display);
        font-size: clamp(1.7rem, 3vw, 2.3rem);
        line-height: 1;
    }

    .portal-toolbar {
        display: grid;
        gap: 16px;
    }

    .portal-toolbar-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: end;
        flex-wrap: wrap;
    }

    .portal-filter-form {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(240px, 0.8fr) auto;
        gap: 12px;
        flex: 1 1 700px;
    }

    .portal-chip-row,
    .portal-badges,
    .portal-inline-actions,
    .portal-hero-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .portal-chip {
        display: inline-flex;
        align-items: center;
        min-height: 42px;
        padding: 0 14px;
        border-radius: 999px;
        border: 1px solid var(--border-glass);
        background: rgba(255, 255, 255, 0.04);
        color: var(--text-primary);
        font-weight: 700;
        text-decoration: none;
    }

    [data-theme="light"] .portal-chip {
        background: rgba(255, 255, 255, 0.86);
        border-color: rgba(15, 23, 42, 0.08);
    }

    .portal-chip.is-active {
        background: linear-gradient(135deg, var(--sg-primary), var(--sg-secondary));
        border-color: transparent;
        color: #fff;
    }

    .portal-order-stack,
    .portal-detail-stack {
        display: grid;
        gap: 14px;
    }

    .portal-order-card {
        padding: 22px;
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        display: grid;
        gap: 16px;
    }

    [data-theme="light"] .portal-order-card {
        background: rgba(255, 255, 255, 0.88);
        border-color: rgba(15, 23, 42, 0.06);
    }

    .portal-order-head {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: start;
        flex-wrap: wrap;
    }

    .portal-order-head strong {
        font-size: clamp(1.25rem, 2vw, 1.55rem);
        margin-bottom: 6px;
    }

    .portal-order-grid,
    .portal-detail-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .portal-detail-grid--triple {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .portal-order-box strong,
    .portal-detail-item strong {
        display: block;
        margin-bottom: 6px;
        font-size: 1rem;
    }

    .portal-detail-toggle summary {
        cursor: pointer;
        list-style: none;
        font-weight: 800;
    }

    .portal-detail-toggle summary::-webkit-details-marker {
        display: none;
    }

    .portal-detail-toggle[open] summary {
        margin-bottom: 14px;
    }

    .portal-summary-count {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 14px;
        background: rgba(249, 115, 22, 0.12);
        border: 1px solid rgba(249, 115, 22, 0.18);
        font-size: 0.92rem;
    }

    .portal-empty {
        text-align: center;
        padding: 18px;
        border-radius: 18px;
        border: 1px dashed rgba(255, 255, 255, 0.18);
        color: var(--text-secondary);
    }

    [data-theme="light"] .portal-empty {
        border-color: rgba(15, 23, 42, 0.16);
    }

    .portal-proof-link {
        color: var(--accent);
        font-weight: 800;
        text-decoration: none;
    }

    .portal-proof-link:hover {
        text-decoration: underline;
    }

    @media (max-width: 1080px) {
        .portal-kpi-grid,
        .portal-order-grid,
        .portal-detail-grid,
        .portal-detail-grid--triple,
        .portal-filter-form {
            grid-template-columns: 1fr;
        }

        .portal-toolbar-row,
        .portal-order-head {
            align-items: stretch;
        }
    }
</style>
@endpush

<div class="checkout-shell checkout-shell--wide">
    <section class="checkout-hero-grid">
        <article class="checkout-card checkout-card--hero">
            <div class="checkout-lead">
                <div class="section-badge">
                    <i class="fa-solid fa-id-card"></i> Kayitli musteri paneli
                </div>
                <div class="checkout-meta">
                    <span class="checkout-chip">{{ $customer->name }}</span>
                    <span class="checkout-chip checkout-chip--info">{{ $customer->phone ?? '-' }}</span>
                    @if ($customer->email)
                        <span class="checkout-chip">{{ $customer->email }}</span>
                    @endif
                </div>
                <h1>Siparislerinizi tek panelden takip edin.</h1>
                <p>Aktif siparisler, onceki hareketler, dekontlar ve takip linkleri ayni hesaba bagli sekilde burada listelenir.</p>
            </div>

            <div class="portal-kpi-grid">
                <div class="portal-kpi">
                    <small class="checkout-muted">Aktif siparis</small>
                    <strong>{{ $activeOrders }}</strong>
                </div>
                <div class="portal-kpi">
                    <small class="checkout-muted">Teslim edilen</small>
                    <strong>{{ $completedOrders }}</strong>
                </div>
                <div class="portal-kpi">
                    <small class="checkout-muted">Listelenen toplam</small>
                    <strong>{{ $orders->count() }}</strong>
                </div>
            </div>

            <div class="checkout-actions portal-hero-actions" style="margin-top:18px;">
                <a href="{{ route('home') }}" class="btn btn-primary">Yeni siparis baslat</a>
                <a href="{{ route('checkout.tracking') }}" class="btn btn-outline">Genel takip ekrani</a>
                <form method="POST" action="{{ route('checkout.customer.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline">Cikis yap</button>
                </form>
            </div>
        </article>

        <aside class="checkout-card checkout-card--support">
            <div class="checkout-lead" style="gap:10px;">
                <div class="section-badge">
                    <i class="fa-solid fa-headset"></i> Destek ve hizli erisim
                </div>
                <h2>Panel disinda da ayni destek hatlari acik.</h2>
                <p>{{ $support['support_note'] ?? 'Destek ekibimiz telefon, WhatsApp veya e-posta uzerinden yardimci olur.' }}</p>
            </div>

            <div class="checkout-list" style="margin-top:18px;">
                <div class="checkout-list-item">
                    <strong>Canli destek</strong>
                    <div class="checkout-link-list">
                        <a href="{{ $support['phone_href'] ?? 'tel:+905513567292' }}">{{ $support['phone_display'] ?? '+90 551 356 72 92' }}</a>
                        <a href="{{ $support['whatsapp_href'] ?? 'https://wa.me/905513567292' }}" target="_blank" rel="noopener">WhatsApp destegi</a>
                        <a href="{{ $support['email_href'] ?? 'mailto:webgetir@simdigetir.com' }}">{{ $support['email'] ?? 'webgetir@simdigetir.com' }}</a>
                    </div>
                </div>
                <div class="checkout-list-item">
                    <strong>Filtre ozetiniz</strong>
                    <div class="portal-summary-count">
                        <strong>{{ $filteredOrdersCount }}</strong> sonuc
                        @if ($searchTerm !== '' || $selectedState !== 'all')
                            <span>/ {{ $totalOrdersCount }} toplam</span>
                        @endif
                    </div>
                </div>
                <div class="checkout-list-item">
                    <strong>Yasal baglantilar</strong>
                    <div class="checkout-link-list">
                        <a href="{{ $support['privacy_href'] ?? url('/kvkk') }}">KVKK</a>
                        <a href="{{ $support['terms_href'] ?? url('/kullanim-kosullari') }}">Kullanim kosullari</a>
                    </div>
                </div>
            </div>
        </aside>
    </section>

    <section class="checkout-card checkout-card--panel">
        @if (session('status'))
            <div class="checkout-alert checkout-alert--success" style="margin-bottom:16px;">{{ session('status') }}</div>
        @endif

        <div class="checkout-panel-head">
            <div>
                <h2>Siparis listeniz</h2>
                <p>Arama, durum filtresi ve detay acilirlari ile aktif ve onceki siparislerinize hizli ulasin.</p>
            </div>
        </div>

        <div class="portal-toolbar">
            <div class="portal-toolbar-row">
                <form method="GET" action="{{ route('checkout.customer.dashboard') }}" class="portal-filter-form">
                    <div class="checkout-field">
                        <label for="portal-search">Siparis ara</label>
                        <input
                            id="portal-search"
                            type="search"
                            name="search"
                            value="{{ $searchTerm }}"
                            placeholder="Siparis no, alis, teslimat, kisi veya odeme tipi"
                        >
                    </div>
                    <div class="checkout-field">
                        <label for="portal-state">Durum filtresi</label>
                        <select id="portal-state" name="state">
                            @foreach ($availableStateFilters as $value => $label)
                                <option value="{{ $value }}" @selected($selectedState === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="checkout-actions">
                        <button type="submit" class="btn btn-primary">Filtrele</button>
                        @if ($searchTerm !== '' || $selectedState !== 'all')
                            <a href="{{ route('checkout.customer.dashboard') }}" class="btn btn-outline">Temizle</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="portal-chip-row">
                @foreach ($availableStateFilters as $value => $label)
                    <a
                        href="{{ route('checkout.customer.dashboard', array_filter(['state' => $value !== 'all' ? $value : null, 'search' => $searchTerm !== '' ? $searchTerm : null])) }}"
                        class="portal-chip @if($selectedState === $value) is-active @endif"
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="portal-order-stack" style="margin-top:18px;">
            @forelse ($orders as $order)
                <article class="portal-order-card">
                    <div class="portal-order-head">
                        <div>
                            <strong>{{ $order->order_no }}</strong>
                            <div class="checkout-muted">{{ optional($order->created_at)->format('Y-m-d H:i') }}</div>
                        </div>
                        <div class="portal-badges">
                            <span class="checkout-chip">{{ $stateLabel($order->state) }}</span>
                            <span class="checkout-chip checkout-chip--info">{{ $stateLabel($order->payment_state) }}</span>
                        </div>
                    </div>

                    <div class="portal-order-grid">
                        <div class="portal-order-box">
                            <strong>Alis</strong>
                            <div>{{ $order->pickup_address }}</div>
                        </div>
                        <div class="portal-order-box">
                            <strong>Teslimat</strong>
                            <div>{{ $order->dropoff_address }}</div>
                        </div>
                        <div class="portal-order-box">
                            <strong>Odeme</strong>
                            <div>{{ $order->payment_method ?? '-' }} / {{ $order->payment_timing ?? '-' }}</div>
                        </div>
                        <div class="portal-order-box">
                            <strong>Tutar</strong>
                            <div>{{ number_format(((int) $order->total_amount) / 100, 2, ',', '.') }} {{ $order->currency }}</div>
                        </div>
                    </div>

                    <div class="checkout-actions portal-inline-actions">
                        <a href="{{ route('checkout.customer.orders.show', ['orderNo' => $order->order_no]) }}" class="btn btn-outline">Siparis detayi</a>
                        <a href="{{ route('checkout.customer.orders.receipt', ['orderNo' => $order->order_no]) }}" class="btn btn-outline">Dekont</a>
                        <a href="{{ route('checkout.tracking', ['order_no' => $order->order_no, 'phone' => $customer->phone]) }}" class="btn btn-primary">Takip et</a>
                    </div>

                    <details class="portal-detail-toggle">
                        <summary>Detaylari goster</summary>
                        <div class="portal-detail-grid portal-detail-grid--triple">
                            <div class="portal-detail-stack">
                                <div class="checkout-muted">State timeline</div>
                                @forelse ($order->stateLogs as $item)
                                    <div class="portal-detail-item">
                                        <strong>{{ $stateLabel($item->to_state) }}</strong>
                                        <div class="checkout-muted">{{ $item->reason ?? '-' }}</div>
                                        <div class="checkout-muted">{{ optional($item->created_at)->format('Y-m-d H:i') }}</div>
                                    </div>
                                @empty
                                    <div class="portal-detail-empty">Timeline kaydi yok.</div>
                                @endforelse
                            </div>

                            <div class="portal-detail-stack">
                                <div class="checkout-muted">Kurye hareketleri</div>
                                @forelse ($order->trackingEvents as $item)
                                    <div class="portal-detail-item">
                                        <strong>{{ $stateLabel($item->event_type) }}</strong>
                                        <div class="checkout-muted">{{ $item->note ?? 'Not yok.' }}</div>
                                        <div class="checkout-muted">{{ optional($item->created_at)->format('Y-m-d H:i') }}</div>
                                    </div>
                                @empty
                                    <div class="portal-detail-empty">Tracking eventi yok.</div>
                                @endforelse
                            </div>

                            <div class="portal-detail-stack">
                                <div class="checkout-muted">Proof ozeti</div>
                                @forelse ($order->orderProofs as $item)
                                    <div class="portal-detail-item">
                                        <strong>{{ $stateLabel($item->stage) }} / {{ $stateLabel($item->proof_type) }}</strong>
                                        <div class="checkout-muted">
                                            @if ($item->file_url)
                                                <a href="{{ $item->file_url }}" target="_blank" rel="noreferrer" class="portal-proof-link">Dosyayi ac</a>
                                            @else
                                                Dosya baglantisi yok.
                                            @endif
                                        </div>
                                        <div class="checkout-muted">{{ optional($item->created_at)->format('Y-m-d H:i') }}</div>
                                    </div>
                                @empty
                                    <div class="portal-detail-empty">Proof kaydi yok.</div>
                                @endforelse
                            </div>
                        </div>
                    </details>
                </article>
            @empty
                <div class="portal-empty">
                    @if ($searchTerm !== '' || $selectedState !== 'all')
                        Bu filtreye uygun siparis bulunamadi.
                    @else
                        Bu hesap icin siparis kaydi bulunamadi.
                    @endif
                </div>
            @endforelse
        </div>
    </section>
</div>
</x-checkout::layouts.public>
