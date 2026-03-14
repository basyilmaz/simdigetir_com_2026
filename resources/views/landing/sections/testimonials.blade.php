@if(data_get($landingContent, 'sections_visible.testimonials', true))
<section class="testimonial-section">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fa-solid fa-comments"></i> {{ $landingContent['testimonials_badge_text'] ?? 'Müşteri Yorumları' }}
            </div>
            <h2 class="section-title">
                {!! $landingContent['testimonials_title_html'] ?? "Müşterilerimiz <span class='gradient-text'>Ne Diyor?</span>" !!}
            </h2>
        </div>

        <div class="testimonial-slider" id="testimonial-slider">
            <div class="testimonial-track" id="testimonial-track">
                @foreach(($landingContent['testimonial_items'] ?? []) as $testimonial)
                    <div class="testimonial-slide">
                        <div class="testimonial-card">
                            <div class="testimonial-avatar" style="{{ $testimonial['avatar_style'] ?? '' }}">
                                @if(!empty($testimonial['avatar_image_url']))
                                    <img
                                        src="{{ \App\Support\ResponsiveImage::resolveUrl($testimonial['avatar_image_url']) }}"
                                        alt="{{ $testimonial['avatar_image_alt'] ?? ($testimonial['author_name'] ?? 'Musteri') }}"
                                        srcset="{{ $testimonial['avatar_image_srcset'] ?? \App\Support\ResponsiveImage::buildSrcset($testimonial['avatar_image_url']) }}"
                                        sizes="{{ \App\Support\ResponsiveImage::normalizeSizes($testimonial['avatar_image_sizes'] ?? null, '56px') }}"
                                        loading="lazy"
                                        decoding="async"
                                        style="width:100%;height:100%;object-fit:cover;border-radius:50%;"
                                    >
                                @else
                                    {{ $testimonial['avatar_text'] ?? '' }}
                                @endif
                            </div>
                            <div class="testimonial-content">
                                <div class="testimonial-stars">
                                    @for($i = 0; $i < (int) ($testimonial['stars'] ?? 5); $i++)
                                        <i class="fa-solid fa-star"></i>
                                    @endfor
                                </div>
                                <p class="testimonial-text">{{ $testimonial['text'] ?? '' }}</p>
                                <div class="testimonial-author">
                                    <h4>{{ $testimonial['author_name'] ?? '' }}</h4>
                                    <span>{{ $testimonial['author_role'] ?? '' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="testimonial-controls">
                <button class="testimonial-btn" id="testimonial-prev">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <button class="testimonial-btn" id="testimonial-next">
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
</section>
@endif
