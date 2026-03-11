@extends('layouts.landing')

@section('title', $landingContent['meta_title'] ?? 'Sıkça Sorulan Sorular - SimdiGetir')
@section('meta_description', $landingContent['meta_description'] ?? 'SimdiGetir kurye hizmetleri hakkında sıkça sorulan sorular. Merak ettiklerinizi anında öğrenin!')
@section('meta_keywords', $landingContent['meta_keywords'] ?? 'kurye sss, kurye sikca sorulan sorular')

@section('robots', $landingContent['robots'] ?? 'index, follow')
@section('canonical_url', $landingContent['canonical_url'] ?? url()->current())
@section('og_title', $landingContent['og_title'] ?? ($landingContent['meta_title'] ?? 'SimdiGetir'))
@section('og_description', $landingContent['og_description'] ?? ($landingContent['meta_description'] ?? 'Hizli ve guvenilir kurye hizmeti'))
@section('og_image', $landingContent['og_image'] ?? asset('images/og-default.jpg'))

@section('structured_data')
@php
    $faqSchemaItems = [];
    foreach (($landingContent['faq_items'] ?? []) as $item) {
        $question = trim((string) ($item['question'] ?? ''));
        if ($question === '') {
            continue;
        }

        $answer = trim((string) ($item['answer_text'] ?? strip_tags((string) ($item['answer_html'] ?? ''))));
        if ($answer === '') {
            continue;
        }

        $faqSchemaItems[] = [
            '@type' => 'Question',
            'name' => $question,
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $answer,
            ],
        ];
    }

    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $faqSchemaItems,
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($faqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE) !!}
</script>
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero" style="min-height: auto; padding: 10rem 0 4rem;">
    <div class="container" style="text-align: center;">
        <div class="hero-badge animate__animated animate__fadeInUp">
            <span class="pulse"></span>
            {{ $landingContent['hero_badge_text'] ?? 'Yardım Merkezi' }}
        </div>
        <h1 class="animate__animated animate__fadeInUp animate__delay-1s" style="font-size: 3rem;">
            {!! $landingContent['hero_title_html'] ?? "<span class='gradient-text'>Sıkça Sorulan</span> Sorular" !!}
        </h1>
        <p class="animate__animated animate__fadeInUp animate__delay-2s" style="max-width: 600px; margin: 0 auto;">
            {{ $landingContent['hero_description_text'] ?? 'Kurye hizmetlerimiz hakkında merak ettiklerinizi öğrenin. Sorunuz cevaplanmadıysa bize ulaşın!' }}
        </p>
    </div>
</section>

<!-- FAQ Section -->
<section class="section">
    <div class="container">
        <div style="max-width: 900px; margin: 0 auto;">
            <div class="faq-grid">
                @foreach (($landingContent['faq_items'] ?? []) as $item)
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFaq(this)">
                            <div class="faq-icon">{{ $item['icon'] ?? '?' }}</div>
                            <span>{{ $item['question'] ?? '' }}</span>
                            <i class="fa-solid fa-plus faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>
                                @if (! empty($item['answer_html']))
                                    {!! $item['answer_html'] !!}
                                @else
                                    {{ $item['answer_text'] ?? '' }}
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section">
    <div class="container">
        <div class="cta-section">
            <div class="cta-content">
                <h2>Başka <span class="gradient-text">Sorularınız mı Var?</span></h2>
                <p>
                    Müşteri temsilcilerimiz size yardımcı olmaya hazır!
                </p>
                <div class="cta-buttons">
                    <a href="tel:+905513567292" class="btn btn-accent">
                        <i class="fa-solid fa-phone"></i> 0551 356 72 92
                    </a>
                    <a href="/iletisim" class="btn btn-outline">
                        <i class="fa-solid fa-envelope"></i> İletişime Geçin
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .faq-grid {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .faq-item {
        background: var(--bg-glass);
        border: 1px solid var(--border-glass);
        border-radius: 1rem;
        overflow: hidden;
        transition: all 0.4s ease;
    }
    
    .faq-item:hover {
        border-color: var(--primary);
        box-shadow: 0 0 30px rgba(124, 58, 237, 0.15);
    }
    
    .faq-question {
        padding: 1.5rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .faq-question span {
        flex: 1;
    }
    
    .faq-icon {
        width: 45px;
        height: 45px;
        background: var(--gradient-primary);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    
    .faq-toggle {
        width: 35px;
        height: 35px;
        background: var(--bg-glass);
        border: 1px solid var(--border-glass);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: var(--accent);
        transition: all 0.3s ease;
    }
    
    .faq-item.active .faq-toggle {
        transform: rotate(45deg);
        background: var(--gradient-primary);
        border-color: var(--primary);
        color: white;
    }
    
    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease, padding 0.4s ease;
    }
    
    .faq-item.active .faq-answer {
        max-height: 500px;
        padding: 0 1.5rem 1.5rem 5rem;
    }
    
    .faq-answer p {
        color: var(--text-secondary);
        line-height: 1.8;
    }
    
    .faq-answer strong {
        color: var(--text-primary);
    }
</style>
@endpush

@push('scripts')
<script>
    function toggleFaq(element) {
        const faqItem = element.parentElement;
        const isActive = faqItem.classList.contains('active');
        
        // Close all
        document.querySelectorAll('.faq-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Open clicked if wasn't active
        if (!isActive) {
            faqItem.classList.add('active');
        }
    }
    
    // Open first FAQ by default
    document.addEventListener('DOMContentLoaded', () => {
        const firstFaq = document.querySelector('.faq-item');
        if (firstFaq) firstFaq.classList.add('active');
    });
</script>
@endpush





