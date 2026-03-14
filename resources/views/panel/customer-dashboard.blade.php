@extends('layouts.landing')

@section('title', 'Musteri Paneli')
@section('meta_description', 'Musteri paneli siparis takibi ve gecmis siparisler.')

@section('content')
<section class="section" style="padding-top: 10rem;">
    <div class="container">
        <h1>Musteri Paneli</h1>
        <p><strong>{{ $customer->name }}</strong> | Aktif Siparis: <strong>{{ $activeOrders }}</strong></p>

        <h3>Son Siparisler</h3>
        <div style="overflow:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left; padding:.5rem;">Siparis No</th>
                        <th style="text-align:left; padding:.5rem;">Durum</th>
                        <th style="text-align:left; padding:.5rem;">Odeme</th>
                        <th style="text-align:left; padding:.5rem;">Tutar</th>
                        <th style="text-align:left; padding:.5rem;">Tarih</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td style="padding:.5rem;">{{ $order->order_no }}</td>
                            <td style="padding:.5rem;">{{ $order->state }}</td>
                            <td style="padding:.5rem;">{{ $order->payment_state }}</td>
                            <td style="padding:.5rem;">{{ number_format((int) $order->total_amount) }} {{ $order->currency }}</td>
                            <td style="padding:.5rem;">{{ optional($order->created_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="padding:.5rem;">Kayit yok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection

