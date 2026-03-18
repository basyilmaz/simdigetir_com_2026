@extends('layouts.landing')

@section('title', ($document->title ?? 'Yasal Metin').' - SimdiGetir')
@section('meta_description', $document->summary ?? 'Yasal bilgilendirme metni')
@section('meta_keywords', 'kvkk, cerez politikasi, kullanim kosullari, yasal metin')
@section('robots', 'index, follow')
@section('canonical_url', url('/'.ltrim((string) ($document->slug ?? ''), '/')))
@section('og_title', ($document->title ?? 'Yasal Metin').' - SimdiGetir')
@section('og_description', $document->summary ?? 'Yasal bilgilendirme metni')

@section('content')
@php
    $tableOfContents = is_array($tableOfContents ?? null) ? $tableOfContents : [];
    $contentHtml = (string) ($contentHtml ?? $document->content ?? '');
@endphp
<section class="section" style="padding-top: 10rem;">
    <div class="container">
        <div class="legal-shell">
            @if (! empty($tableOfContents))
                <aside class="glass legal-toc" aria-label="Icerik ozeti">
                    <p class="legal-toc-eyebrow">Icerik Ozeti</p>
                    <nav class="legal-toc-links">
                        @foreach ($tableOfContents as $item)
                            <a href="#{{ $item['id'] }}" class="{{ ($item['level'] ?? 'h2') === 'h3' ? 'is-child' : '' }}">
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>
                </aside>
            @endif

            <div class="glass legal-document-card">
                <div class="legal-document-header">
                    <h1 style="font-size:2rem; margin-bottom:1rem;">{{ $document->title }}</h1>
                    @if($document->published_at)
                        <p style="color:var(--text-muted); margin-bottom:1rem;">
                            Yayin Tarihi: {{ $document->published_at->format('Y-m-d H:i') }} | Versiyon: v{{ $document->version }}
                        </p>
                    @endif
                    @if (filled($document->summary))
                        <p class="legal-summary">{{ $document->summary }}</p>
                    @endif
                </div>

                @if (! empty($tableOfContents))
                    <div class="legal-toc-mobile" aria-label="Bolum listesi">
                        @foreach ($tableOfContents as $item)
                            <a href="#{{ $item['id'] }}" class="{{ ($item['level'] ?? 'h2') === 'h3' ? 'is-child' : '' }}">
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="legal-content">
                    {!! $contentHtml !!}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .legal-shell {
        display: grid;
        grid-template-columns: minmax(0, 280px) minmax(0, 1fr);
        gap: 1.5rem;
        align-items: start;
    }

    .legal-document-card {
        padding: 2rem;
    }

    .legal-document-header {
        margin-bottom: 1.5rem;
    }

    .legal-summary {
        color: var(--text-secondary);
        line-height: 1.8;
        margin: 0;
    }

    .legal-toc {
        position: sticky;
        top: 120px;
        padding: 1.25rem;
    }

    .legal-toc-eyebrow {
        margin: 0 0 0.85rem;
        color: var(--text-muted);
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .legal-toc-links,
    .legal-toc-mobile {
        display: grid;
        gap: 0.55rem;
    }

    .legal-toc-links a,
    .legal-toc-mobile a {
        color: var(--text-secondary);
        text-decoration: none;
        line-height: 1.5;
    }

    .legal-toc-links a:hover,
    .legal-toc-mobile a:hover {
        color: var(--accent);
    }

    .legal-toc-links a.is-child,
    .legal-toc-mobile a.is-child {
        padding-left: 0.9rem;
        font-size: 0.92rem;
    }

    .legal-toc-mobile {
        display: none;
        margin-bottom: 1.5rem;
        padding: 1rem;
        border: 1px solid var(--border-glass);
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.03);
    }

    .legal-content {
        color: var(--text-secondary);
        line-height: 1.8;
    }

    .legal-content h2,
    .legal-content h3 {
        color: var(--text-primary);
        scroll-margin-top: 110px;
    }

    .legal-content h2 {
        margin-top: 2rem;
        margin-bottom: 0.9rem;
    }

    .legal-content h3 {
        margin-top: 1.5rem;
        margin-bottom: 0.7rem;
    }

    .legal-content p,
    .legal-content ul,
    .legal-content ol {
        margin-top: 0;
        margin-bottom: 1rem;
    }

    @media (max-width: 960px) {
        .legal-shell {
            grid-template-columns: 1fr;
        }

        .legal-toc {
            display: none;
        }

        .legal-toc-mobile {
            display: grid;
        }

        .legal-document-card {
            padding: 1.5rem;
        }
    }
</style>
@endpush
