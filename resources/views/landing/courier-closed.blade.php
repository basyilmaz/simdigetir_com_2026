@extends('layouts.landing')

{{-- Kurye alımı kapalı (Yılmaz kararı, 2026-11-09). /kurye-basvuru 410 döner, dizinden çıkar.
     Alım yeniden açılırsa: routes/web.php'de landing.courier-apply görünümüne dön; sitemap, alt bilgi ve llms.txt bağlantılarını geri ekle. --}}
@section('title', 'Kurye Alımı Yapılmıyor - SimdiGetir')
@section('meta_description', 'SimdiGetir şu anda kurye alımı yapmıyor. Gönderi için kurye çağırmak isterseniz bizi arayın.')
@section('robots', 'noindex, nofollow')

@section('content')
<section class="hero" style="min-height: auto; padding: 9rem 0 4rem;">
    <div class="container" style="text-align: center; max-width: 720px;">
        <h1 style="font-size: 2rem;">Şu anda kurye alımı yapılmıyor</h1>
        <p style="margin: 1rem auto 2rem;">
            Kurye başvurusu kabul etmiyoruz. Bir gönderiniz için kurye çağırmak isterseniz bize ulaşabilirsiniz.
        </p>
        <div class="hero-buttons" style="justify-content: center;">
            <a href="/" class="btn btn-primary"><i class="fa-solid fa-house"></i> Ana Sayfa</a>
            <a href="/iletisim" class="btn btn-outline"><i class="fa-solid fa-envelope"></i> İletişim</a>
        </div>
    </div>
</section>
@endsection
