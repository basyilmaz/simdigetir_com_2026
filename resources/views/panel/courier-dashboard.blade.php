@extends('layouts.landing')

@section('title', 'Kurye Paneli')
@section('meta_description', 'Kurye paneli gorev, durum ve kazanc ozeti.')

@section('content')
<section class="section" style="padding-top: 10rem;">
    <div class="container">
        <h1>Kurye Paneli</h1>
        <p><strong>{{ $courier->full_name }}</strong> | Durum: <strong>{{ $courier->status }}</strong></p>

        <div class="grid" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:1rem; margin:1.5rem 0;">
            <div class="glass" style="padding:1rem; border-radius:12px;">
                <div>Bakiye</div>
                <strong>{{ number_format($walletBalance) }} TRY</strong>
            </div>
            <div class="glass" style="padding:1rem; border-radius:12px;">
                <div>Toplam Gorev</div>
                <strong>{{ $assignments->count() }}</strong>
            </div>
        </div>

        <h3>Son Gorevler</h3>
        <div style="overflow:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left; padding:.5rem;">Siparis</th>
                        <th style="text-align:left; padding:.5rem;">Durum</th>
                        <th style="text-align:left; padding:.5rem;">Toplam</th>
                        <th style="text-align:left; padding:.5rem;">Atanma</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                        <tr>
                            <td style="padding:.5rem;">{{ $assignment->order->order_no ?? '-' }}</td>
                            <td style="padding:.5rem;">{{ $assignment->status }}</td>
                            <td style="padding:.5rem;">{{ number_format((int) ($assignment->order->total_amount ?? 0)) }} {{ $assignment->order->currency ?? 'TRY' }}</td>
                            <td style="padding:.5rem;">{{ optional($assignment->assigned_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="padding:.5rem;">Kayit yok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h3 style="margin-top:2rem;">Cuzdan Hareketleri</h3>
        <div style="overflow:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left; padding:.5rem;">Tip</th>
                        <th style="text-align:left; padding:.5rem;">Tutar</th>
                        <th style="text-align:left; padding:.5rem;">Bakiye Sonrasi</th>
                        <th style="text-align:left; padding:.5rem;">Tarih</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($walletEntries as $entry)
                        <tr>
                            <td style="padding:.5rem;">{{ $entry->entry_type }}</td>
                            <td style="padding:.5rem;">{{ number_format((int) $entry->amount) }} {{ $entry->currency }}</td>
                            <td style="padding:.5rem;">{{ number_format((int) $entry->balance_after) }} {{ $entry->currency }}</td>
                            <td style="padding:.5rem;">{{ optional($entry->entry_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="padding:.5rem;">Kayit yok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection

