<x-checkout::layouts.master title="Musteri Paneli" description="SimdiGetir musteri paneli">
@push('styles')
<style>
body{margin:0;background:var(--sg-surface-page-light);color:var(--sg-ink-light);font-family:"Manrope",sans-serif}.shell{width:min(1160px,calc(100% - 32px));margin:0 auto;padding:28px 0 40px}.top{display:flex;justify-content:space-between;gap:16px;margin-bottom:20px}.brand{display:inline-flex;align-items:center;gap:12px;color:inherit;text-decoration:none;font-weight:800}.brand b{width:42px;height:42px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;background:var(--sg-brand-gradient);color:#fff;font-family:"Space Grotesk",sans-serif}.card{border:1px solid var(--sg-border-light);border-radius:24px;background:var(--sg-card-light);backdrop-filter:blur(14px);box-shadow:var(--sg-shadow-light)}.hero,.panel{padding:24px}.hero h1{margin:0 0 8px;font-family:"Space Grotesk",sans-serif;font-size:clamp(1.9rem,4vw,2.8rem)}.muted,.hero p,.meta,.detail-note,.toolbar-summary{color:var(--sg-muted-light)}.stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-top:18px}.stat{padding:18px;border-radius:18px;border:1px solid var(--sg-border-light-soft);background:var(--sg-card-light-soft)}.stat strong{display:block;font-size:1.8rem;font-family:"Space Grotesk",sans-serif}.actions{display:flex;gap:12px;flex-wrap:wrap}.btn{display:inline-flex;align-items:center;justify-content:center;min-height:48px;padding:0 18px;border-radius:16px;border:0;background:var(--sg-action-gradient);color:#fff;font:inherit;font-weight:800;cursor:pointer;text-decoration:none}.btn.secondary{background:var(--sg-card-light-strong);color:var(--sg-ink-light);border:1px solid var(--sg-border-light)}.btn.inline{min-height:42px;padding:0 14px}.stack{display:grid;gap:16px;margin-top:20px}.order-card{padding:18px;border-radius:20px;border:1px solid var(--sg-border-light-soft);background:var(--sg-card-light-soft)}.order-head{display:flex;justify-content:space-between;gap:16px;align-items:flex-start;margin-bottom:12px}.order-head strong{font-family:"Space Grotesk",sans-serif;font-size:1.1rem}.badges{display:flex;gap:8px;flex-wrap:wrap}.badge{display:inline-flex;padding:8px 12px;border-radius:999px;background:var(--sg-accent-warm-bg);color:var(--sg-accent-warm-text);font-size:13px;font-weight:700}.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.empty{padding:20px;border-radius:18px;border:1px dashed var(--sg-border-light-dashed);color:var(--sg-muted-light);text-align:center}.detail-box{margin-top:14px;padding:16px;border-radius:18px;border:1px solid var(--sg-border-light-soft);background:var(--sg-card-light-muted)}.detail-box summary{cursor:pointer;font-weight:800;list-style:none}.detail-box summary::-webkit-details-marker{display:none}.detail-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:14px}.detail-list{display:grid;gap:10px}.detail-item{padding:12px;border-radius:14px;border:1px solid var(--sg-border-light-soft);background:var(--sg-card-light-soft)}.detail-item strong{display:block;margin-bottom:4px}.detail-empty{padding:12px;border-radius:14px;border:1px dashed var(--sg-border-light-dashed);color:var(--sg-muted-light)}.toolbar{display:grid;gap:16px;margin-top:20px}.toolbar-row{display:flex;justify-content:space-between;gap:16px;align-items:flex-end;flex-wrap:wrap}.toolbar-form{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(220px,.8fr) auto;gap:12px;flex:1 1 680px}.field{display:grid;gap:8px}.field label{font-size:13px;font-weight:800;color:var(--sg-muted-light);text-transform:uppercase;letter-spacing:.04em}.field input,.field select{min-height:50px;border-radius:16px;border:1px solid var(--sg-border-light);background:var(--sg-card-light-strong);padding:0 16px;font:inherit;color:var(--sg-ink-light)}.field input:focus,.field select:focus{outline:none;border-color:var(--sg-focus-border);box-shadow:0 0 0 4px var(--sg-accent-warm-bg)}.filter-actions{display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap}.chips{display:flex;gap:10px;flex-wrap:wrap}.chip{display:inline-flex;align-items:center;min-height:42px;padding:0 14px;border-radius:999px;border:1px solid var(--sg-border-light);background:var(--sg-card-light-muted);color:var(--sg-ink-light);text-decoration:none;font-weight:700}.chip.active{background:var(--sg-action-gradient);border-color:transparent;color:#fff}.toolbar-summary strong{color:var(--sg-ink-light)}.pill-note{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:14px;background:var(--sg-accent-warm-bg-soft);border:1px solid var(--sg-accent-warm-border);font-size:14px}.alert{padding:14px 16px;border-radius:18px;font-size:14px;line-height:1.6}.alert.success{background:var(--sg-success-bg);color:var(--sg-success-text)}.order-card mark{background:var(--sg-accent-warm-bg-strong);color:inherit;padding:0 .2em;border-radius:4px}@media (max-width:980px){.stats,.grid,.detail-grid{grid-template-columns:1fr}.order-head,.toolbar-row{flex-direction:column;align-items:stretch}.toolbar-form{grid-template-columns:1fr}.filter-actions{align-items:stretch}.filter-actions .btn{width:100%}}
</style>
@endpush

<div class="shell">
  <div class="top">
    <a href="{{ route('home') }}" class="brand"><b>SG</b><span>SimdiGetir Hesabim</span></a>
    <form method="POST" action="{{ route('checkout.customer.logout') }}">
      @csrf
      <button type="submit" class="btn secondary">Cikis Yap</button>
    </form>
  </div>

  @if (session('status'))
    <div class="alert success" style="margin-bottom:20px;">{{ session('status') }}</div>
  @endif

  <section class="card hero">
    <h1>{{ $customer->name }}</h1>
    <p>Telefon: {{ $customer->phone ?? '-' }} @if($customer->email) | E-posta: {{ $customer->email }} @endif</p>

    <div class="stats">
      <div class="stat"><strong>{{ $activeOrders }}</strong><span class="meta">Aktif Siparis</span></div>
      <div class="stat"><strong>{{ $completedOrders }}</strong><span class="meta">Teslim Edilen</span></div>
      <div class="stat"><strong>{{ $orders->count() }}</strong><span class="meta">Listelenen Toplam</span></div>
    </div>
  </section>

  <section class="card panel" style="margin-top:20px;">
    <div class="actions">
      <a href="{{ route('home') }}" class="btn secondary">Yeni Siparis Baslat</a>
      <a href="{{ route('checkout.tracking') }}" class="btn secondary">Genel Takip Ekrani</a>
    </div>

    <div class="toolbar">
      <div class="toolbar-row">
        <form method="GET" action="{{ route('checkout.customer.dashboard') }}" class="toolbar-form">
          <div class="field">
            <label for="portal-search">Siparis Ara</label>
            <input
              id="portal-search"
              type="search"
              name="search"
              value="{{ $searchTerm }}"
              placeholder="Siparis no, alinis, teslimat, kisi veya odeme tipi"
            >
          </div>
          <div class="field">
            <label for="portal-state">Durum Filtresi</label>
            <select id="portal-state" name="state">
              @foreach ($availableStateFilters as $value => $label)
                <option value="{{ $value }}" @selected($selectedState === $value)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="filter-actions">
            <button type="submit" class="btn">Filtrele</button>
            @if ($searchTerm !== '' || $selectedState !== 'all')
              <a href="{{ route('checkout.customer.dashboard') }}" class="btn secondary">Temizle</a>
            @endif
          </div>
        </form>

        <div class="toolbar-summary">
          <div class="pill-note">
            <strong>{{ $filteredOrdersCount }}</strong> sonuc
            @if ($searchTerm !== '' || $selectedState !== 'all')
              <span>/ {{ $totalOrdersCount }} toplam</span>
            @endif
          </div>
        </div>
      </div>

      <div class="chips">
        @foreach ($availableStateFilters as $value => $label)
          <a
            href="{{ route('checkout.customer.dashboard', array_filter(['state' => $value !== 'all' ? $value : null, 'search' => $searchTerm !== '' ? $searchTerm : null])) }}"
            class="chip @if($selectedState === $value) active @endif"
          >
            {{ $label }}
          </a>
        @endforeach
      </div>
    </div>

    <div class="stack">
      @forelse ($orders as $order)
        <article class="order-card">
          <div class="order-head">
            <div>
              <strong>{{ $order->order_no }}</strong>
              <div class="meta">{{ optional($order->created_at)->format('Y-m-d H:i') }}</div>
            </div>
            <div class="badges">
              <span class="badge">{{ $order->state }}</span>
              <span class="badge">{{ $order->payment_state }}</span>
            </div>
          </div>

          <div class="grid">
            <div>
              <div class="meta">Alinis</div>
              <div>{{ $order->pickup_address }}</div>
            </div>
            <div>
              <div class="meta">Teslimat</div>
              <div>{{ $order->dropoff_address }}</div>
            </div>
            <div>
              <div class="meta">Odeme</div>
              <div>{{ $order->payment_method ?? '-' }} / {{ $order->payment_timing ?? '-' }}</div>
            </div>
            <div>
              <div class="meta">Tutar</div>
              <div>{{ number_format(((int) $order->total_amount) / 100, 2, ',', '.') }} {{ $order->currency }}</div>
            </div>
          </div>

          <div class="actions" style="margin-top:14px;">
            <a href="{{ route('checkout.customer.orders.show', ['orderNo' => $order->order_no]) }}" class="btn inline secondary">Siparis Detayi</a>
            <a href="{{ route('checkout.customer.orders.receipt', ['orderNo' => $order->order_no]) }}" class="btn inline secondary">Dekont</a>
            <a href="{{ route('checkout.tracking', ['order_no' => $order->order_no, 'phone' => $customer->phone]) }}" class="btn inline">Takip Et</a>
          </div>

          <details class="detail-box">
            <summary>Detaylari Goster</summary>
            <div class="detail-grid">
              <div>
                <div class="detail-note">State Timeline</div>
                <div class="detail-list">
                  @forelse ($order->stateLogs as $item)
                    <div class="detail-item">
                      <strong>{{ $item->to_state }}</strong>
                      <div class="detail-note">{{ $item->reason ?? '-' }}</div>
                      <div class="detail-note">{{ optional($item->created_at)->format('Y-m-d H:i') }}</div>
                    </div>
                  @empty
                    <div class="detail-empty">Timeline kaydi yok.</div>
                  @endforelse
                </div>
              </div>

              <div>
                <div class="detail-note">Kurye Hareketleri</div>
                <div class="detail-list">
                  @forelse ($order->trackingEvents as $item)
                    <div class="detail-item">
                      <strong>{{ $item->event_type }}</strong>
                      <div class="detail-note">{{ $item->note ?? 'Not yok.' }}</div>
                      <div class="detail-note">{{ optional($item->created_at)->format('Y-m-d H:i') }}</div>
                    </div>
                  @empty
                    <div class="detail-empty">Tracking eventi yok.</div>
                  @endforelse
                </div>
              </div>

              <div>
                <div class="detail-note">Proof Ozeti</div>
                <div class="detail-list">
                  @forelse ($order->orderProofs as $item)
                    <div class="detail-item">
                      <strong>{{ $item->stage }} / {{ $item->proof_type }}</strong>
                      <div class="detail-note">
                        @if ($item->file_url)
                          <a href="{{ $item->file_url }}" target="_blank" rel="noreferrer">Dosyayi Ac</a>
                        @else
                          Dosya baglantisi yok.
                        @endif
                      </div>
                      <div class="detail-note">{{ optional($item->created_at)->format('Y-m-d H:i') }}</div>
                    </div>
                  @empty
                    <div class="detail-empty">Proof kaydi yok.</div>
                  @endforelse
                </div>
              </div>
            </div>
          </details>
        </article>
      @empty
        <div class="empty">
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
</x-checkout::layouts.master>
