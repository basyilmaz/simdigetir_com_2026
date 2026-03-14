@extends('layouts.landing')

@section('title', ($document->title ?? 'Yasal Metin').' - SimdiGetir')
@section('meta_description', $document->summary ?? 'Yasal bilgilendirme metni')
@section('meta_keywords', 'kvkk, cerez politikasi, kullanim kosullari, yasal metin')
@section('robots', 'index, follow')
@section('canonical_url', url('/'.ltrim((string) ($document->slug ?? ''), '/')))
@section('og_title', ($document->title ?? 'Yasal Metin').' - SimdiGetir')
@section('og_description', $document->summary ?? 'Yasal bilgilendirme metni')

@section('content')
<section class="section" style="padding-top: 10rem;">
    <div class="container">
        <div class="glass" style="padding:2rem;">
            <h1 style="font-size:2rem; margin-bottom:1rem;">{{ $document->title }}</h1>
            @if($document->published_at)
                <p style="color:var(--text-muted); margin-bottom:1.25rem;">
                    Yayin Tarihi: {{ $document->published_at->format('Y-m-d H:i') }} | Versiyon: v{{ $document->version }}
                </p>
            @endif
            <div style="color:var(--text-secondary); line-height:1.8;">
                {!! $document->content !!}
            </div>
        </div>
    </div>
</section>
@endsection
