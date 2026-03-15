<?php

namespace Modules\Landing\Services;

use Illuminate\Support\Facades\Schema;
use PDO;
use Throwable;

class LandingContentResolver
{
    private const SHARED_CHROME_KEYS = [
        'header_b2b_cta_enabled',
        'header_b2b_cta_label_text',
        'header_b2b_cta_href',
        'header_b2b_cta_target',
    ];

    /**
     * Resolve content map for landing home page with safe fallbacks.
     */
    public function resolveHomeContent(): array
    {
        $defaultQuoteServiceOptions = [
            [
                'value' => 'moto',
                'label' => 'Moto Kurye',
                'base_amount' => 25000,
                'fallback_minutes' => 45,
            ],
            [
                'value' => 'urgent',
                'label' => 'Acil Kurye',
                'base_amount' => 35000,
                'fallback_minutes' => 35,
            ],
            [
                'value' => 'van',
                'label' => 'Aracli Kurye',
                'base_amount' => 45000,
                'fallback_minutes' => 70,
            ],
        ];

        $defaultServiceCards = [
            [
                'number' => '01',
                'icon_text' => '🏍️',
                'icon_style' => '',
                'title' => 'Motorlu Kurye',
                'description' => 'Trafiği atlatarak dakikalar içinde hedefe ulaşın. Akıllı rota optimizasyonu ile en hızlı teslimat.',
                'features' => [
                    'Anlık trafik analizi',
                    'Akıllı rota optimizasyonu',
                    'Gerçek zamanlı takip',
                ],
                'button_icon' => 'fa-phone',
                'button_label' => 'Hemen Ara',
                'button_href' => 'tel:+905513567292',
            ],
            [
                'number' => '02',
                'icon_text' => '⚡',
                'icon_style' => 'background: linear-gradient(135deg, #22d3ee 0%, #7c3aed 100%);',
                'title' => 'Acil Kurye',
                'description' => 'Saniyeler içinde en yakın kurye atanır. Öncelikli teslimat garantisi ile kritik gönderileriniz güvende.',
                'features' => [
                    'Anlık kurye eşleştirme',
                    'Öncelikli gönderi statüsü',
                    'SLA garantili teslimat',
                ],
                'button_icon' => 'fa-bolt',
                'button_label' => 'Acil Çağır',
                'button_href' => 'tel:+905513567292',
            ],
            [
                'number' => '03',
                'icon_text' => '🚗',
                'icon_style' => 'background: linear-gradient(135deg, #10b981 0%, #22d3ee 100%);',
                'title' => 'Araçlı Kurye',
                'description' => 'Büyük hacimli gönderiler için özel araç filosu. Hassas eşya taşıma ve toplu teslimat imkanı.',
                'features' => [
                    'Büyük hacim kapasitesi',
                    'Hassas eşya koruması',
                    'Toplu teslimat imkanı',
                ],
                'button_icon' => 'fa-truck',
                'button_label' => 'Araç Talep Et',
                'button_href' => 'tel:+905513567292',
            ],
        ];

        $defaultFaqCards = [
            [
                'image_style' => 'background: linear-gradient(135deg, #7c3aed 0%, #22d3ee 100%);',
                'icon_class' => 'fa-route',
                'date_label' => '10 Şubat 2026',
                'title' => 'Akıllı Rota ile Daha Hızlı Teslimat',
                'description' => 'Akıllı rota optimizasyonu sayesinde teslimat sürelerimizi nasıl kısaltıyoruz? Hizmetlerimizi keşfedin.',
                'link' => '/hizmetler',
                'link_label' => 'Hizmetleri İncele',
            ],
            [
                'image_style' => 'background: linear-gradient(135deg, #ec4899 0%, #7c3aed 100%);',
                'icon_class' => 'fa-question-circle',
                'date_label' => '5 Şubat 2026',
                'title' => 'Sıkça Sorulan Sorular',
                'description' => 'Kurye hizmetlerimiz, fiyatlandırma, teslimat süreleri ve daha fazlası hakkında merak edilenler.',
                'link' => '/sss',
                'link_label' => 'Sorulara Bak',
            ],
        ];

        $defaultFeatureCards = [
            ['icon' => "\xF0\x9F\x9A\xA9", 'title' => "Ak\xC4\xB1ll\xC4\xB1 Rotalama", 'description' => "Yapay zeka destekli rota optimizasyonu ile en h\xC4\xB1zl\xC4\xB1 teslimat"],
            ['icon' => "\xF0\x9F\x93\x8D", 'title' => "Canl\xC4\xB1 Takip", 'description' => "G\xC3\xB6nderinizi harita \xC3\xBCzerinden ger\xC3\xA7ek zamanl\xC4\xB1 izleyin"],
            ['icon' => "\xE2\x9A\xA1", 'title' => "H\xC4\xB1zl\xC4\xB1 E\xC5\x9Fle\xC5\x9Ftirme", 'description' => "Saniyeler i\xC3\xA7inde en yak\xC4\xB1n kurye otomatik atan\xC4\xB1r"],
            ['icon' => "\xF0\x9F\x94\x92", 'title' => "G\xC3\xBCvenli Teslimat", 'description' => "%99.5 ba\xC5\x9Far\xC4\xB1l\xC4\xB1 teslimat oran\xC4\xB1 ile sekt\xC3\xB6r lideri"],
            ['icon' => "\xF0\x9F\x92\xB0", 'title' => "\xC5\x9Eeffaf Fiyat", 'description' => "S\xC3\xBCrpriz masraf yok, sipari\xC5\x9F \xC3\xB6ncesi net fiyat bilgisi"],
            ['icon' => "\xF0\x9F\x93\xB1", 'title' => "Anl\xC4\xB1k Bildirim", 'description' => "Teslimat\xC4\xB1n her a\xC5\x9Famas\xC4\xB1nda SMS ve WhatsApp bildirimi"],
            ['icon' => "\xF0\x9F\x8C\x99", 'title' => '7/24 Aktif', 'description' => "Gece g\xC3\xBCnd\xC3\xBCz, hafta sonu dahil kesintisiz hizmet"],
            ['icon' => "\xF0\x9F\x8F\xA2", 'title' => "Kurumsal \xC3\x87\xC3\xB6z\xC3\xBCm", 'description' => "\xC4\xB0\xC5\x9Fletmelere \xC3\xB6zel hacim indirimi ve API entegrasyonu"],
        ];

        $defaultProcessSteps = [
            ['number' => '01', 'title' => "G\xC3\xB6nderi Bilgisi", 'description' => "Bizi aray\xC4\xB1n veya WhatsApp'tan yaz\xC4\xB1n. G\xC3\xB6nderi detaylar\xC4\xB1na g\xC3\xB6re en uygun hizmeti belirleyelim."],
            ['number' => '02', 'title' => "Ak\xC4\xB1ll\xC4\xB1 E\xC5\x9Fle\xC5\x9Ftirme", 'description' => "En yak\xC4\xB1n ve uygun kuryeyi saniyeler i\xC3\xA7inde bulur, en k\xC4\xB1sa rotay\xC4\xB1 hesaplar\xC4\xB1z."],
            ['number' => '03', 'title' => "H\xC4\xB1zl\xC4\xB1 Teslimat", 'description' => "Kuryeniz yola \xC3\xA7\xC4\xB1kar, siz ger\xC3\xA7ek zamanl\xC4\xB1 takip edersiniz. Her an nerede oldu\xC4\x9Funu g\xC3\xB6r\xC3\xBCn."],
        ];

        $defaultFunfactItems = [
            ['count' => 52, 'suffix' => 'K+', 'label' => "Mutlu M\xC3\xBC\xC5\x9Fteri"],
            ['count' => 150, 'suffix' => 'K+', 'label' => 'Tamamlanan Teslimat'],
            ['count' => 500, 'suffix' => '+', 'label' => 'Aktif Kurye'],
            ['count' => 99, 'suffix' => '%', 'label' => "Memnuniyet Oran\xC4\xB1"],
        ];

        $defaultTestimonials = [
            [
                'avatar_text' => 'AY',
                'avatar_style' => 'background:linear-gradient(135deg,#7c3aed,#a855f7);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;color:white;',
                'avatar_image_url' => null,
                'avatar_image_alt' => null,
                'avatar_image_srcset' => null,
                'avatar_image_sizes' => null,
                'stars' => 5,
                'text' => 'E-ticaret g\xC3\xB6nderilerimizde h\xC4\xB1z ve m\xC3\xBC\xC5\x9Fteri memnuniyeti g\xC3\xB6zle g\xC3\xB6r\xC3\xBCl\xC3\xBCr \xC5\x9Fekilde artt\xC4\xB1. \xC3\x96zellikle ayn\xC4\xB1 g\xC3\xBCn teslimat se\xC3\xA7ene\xC4\x9Fi m\xC3\xBC\xC5\x9Fterilerimizi \xC3\xA7ok memnun ediyor.',
                'author_name' => "Ahmet Y\xC4\xB1lmaz",
                'author_role' => "E-Ticaret \xC4\xB0\xC5\x9Fletmecisi",
            ],
            [
                'avatar_text' => 'EK',
                'avatar_style' => 'background:linear-gradient(135deg,#22d3ee,#06b6d4);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;color:white;',
                'avatar_image_url' => null,
                'avatar_image_alt' => null,
                'avatar_image_srcset' => null,
                'avatar_image_sizes' => null,
                'stars' => 5,
                'text' => "Acil numune g\xC3\xB6nderilerimizde g\xC3\xBCvenilir ve s\xC3\xBCrekli bir \xC3\xA7\xC3\xB6z\xC3\xBCm sa\xC4\x9Flad\xC4\xB1. Laboratuvar sonu\xC3\xA7lar\xC4\xB1m\xC4\xB1z art\xC4\xB1k ge\xC3\xA7 kalm\xC4\xB1yor.",
                'author_name' => 'Dr. Elif Kaya',
                'author_role' => "Klinik Y\xC3\xB6neticisi",
            ],
            [
                'avatar_text' => 'MD',
                'avatar_style' => 'background:linear-gradient(135deg,#db2777,#ec4899);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;color:white;',
                'avatar_image_url' => null,
                'avatar_image_alt' => null,
                'avatar_image_srcset' => null,
                'avatar_image_sizes' => null,
                'stars' => 5,
                'text' => "Ofisler aras\xC4\xB1 sevkiyatlar\xC4\xB1m\xC4\xB1z art\xC4\xB1k \xC3\xA7ok daha h\xC4\xB1zl\xC4\xB1 ve izlenebilir. Canl\xC4\xB1 takip \xC3\xB6zelli\xC4\x9Fi i\xC5\x9F ak\xC4\xB1\xC5\x9F\xC4\xB1m\xC4\xB1z\xC4\xB1 \xC3\xA7ok kolayla\xC5\x9Ft\xC4\xB1rd\xC4\xB1.",
                'author_name' => 'Mehmet Demir',
                'author_role' => "\xC5\x9Eirket M\xC3\xBCd\xC3\xBCr\xC3\xBC",
            ],
            [
                'avatar_text' => 'SA',
                'avatar_style' => 'background:linear-gradient(135deg,#f59e0b,#ef4444);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;color:white;',
                'avatar_image_url' => null,
                'avatar_image_alt' => null,
                'avatar_image_srcset' => null,
                'avatar_image_sizes' => null,
                'stars' => 5,
                'text' => "Restoran\xC4\xB1m\xC4\xB1z i\xC3\xA7in moto kurye hizmeti kullan\xC4\xB1yoruz. 7/24 aktif olmalar\xC4\xB1 gece sipari\xC5\x9Flerimiz i\xC3\xA7in \xC3\xA7ok b\xC3\xBCy\xC3\xBCk avantaj.",
                'author_name' => "Selin Arslan",
                'author_role' => "Restoran Sahibi",
            ],
            [
                'avatar_text' => 'BT',
                'avatar_style' => 'background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;color:white;',
                'avatar_image_url' => null,
                'avatar_image_alt' => null,
                'avatar_image_srcset' => null,
                'avatar_image_sizes' => null,
                'stars' => 5,
                'text' => "Hukuk evraklar\xC4\xB1m\xC4\xB1z\xC4\xB1n zaman\xC4\xB1nda ula\xC5\x9Fmas\xC4\xB1 kritik \xC3\xB6nem ta\xC5\x9F\xC4\xB1yor. SimdiGetir ile hi\xC3\xA7 sorun ya\xC5\x9Famad\xC4\xB1k, SLA garantisi bizi rahatlatıyor.",
                'author_name' => "Burak Ta\xC5\x9Fk\xC4\xB1n",
                'author_role' => 'Avukat',
            ],
        ];

        $defaults = [
            'sections_visible' => [
                'hero' => true,
                'services' => true,
                'features' => true,
                'process' => true,
                'stats' => true,
                'testimonials' => true,
                'main_cta' => true,
                'faq' => true,
                'corporate_cta' => true,
                'courier_cta' => true,
            ],
            'hero_badge_text' => '7/24 Aktif Hizmet',
            'hero_title_html' => "Zamanın <span class=\"gradient-text\">Değerli</span> Olduğu<br>Anlarda Yanınızdayız",
            'hero_description_text' => "İstanbul'un en hızlı kurye ağı. Gönderinizi teslim alır, en kısa rotadan güvenle ulaştırırız.",
            'hero_slide2_image_url' => null,
            'hero_slide2_image_alt' => 'Kuryeman',
            'hero_slide2_image_srcset' => null,
            'hero_slide2_image_sizes' => '(max-width: 768px) 100vw, 50vw',
            'quote_widget_enabled' => true,
            'quote_widget_title_text' => 'Aninda Fiyat Hesapla',
            'quote_widget_subtitle_text' => 'Alinis ve teslimat adresini girin, tahmini fiyat ve sureyi aninda gorun.',
            'quote_widget_pickup_label_text' => 'Alinis Adresi',
            'quote_widget_pickup_placeholder_text' => 'Orn: Sisli Mecidiyekoy',
            'quote_widget_dropoff_label_text' => 'Teslimat Adresi',
            'quote_widget_dropoff_placeholder_text' => 'Orn: Kadikoy Moda',
            'quote_widget_service_label_text' => 'Hizmet Tipi',
            'quote_widget_submit_label_text' => 'Fiyat Hesapla',
            'quote_widget_whatsapp_label_text' => 'WhatsApp ile Devam Et',
            'quote_widget_call_label_text' => 'Beni Arayin',
            'header_b2b_cta_enabled' => false,
            'header_b2b_cta_label_text' => 'Kurumsal Giris',
            'header_b2b_cta_href' => '/kurumsal',
            'header_b2b_cta_target' => '_self',
            'quote_widget_service_options' => $defaultQuoteServiceOptions,
            'services_badge_text' => 'Profesyonel Hizmetler',
            'services_title_html' => "Kurye <span class=\"gradient-text\">Çözümlerimiz</span>",
            'services_subtitle_text' => 'Her gönderi için doğru çözüm. Hızlı, güvenilir, profesyonel.',
            'faq_card_title_text' => 'Sıkça Sorulan Sorular',
            'faq_card_description_text' => 'Kurye hizmetlerimiz, fiyatlandırma, teslimat süreleri ve daha fazlası hakkında merak edilenler.',
            'corporate_cta_form_title_text' => 'Teklif İsteyin',
            'corporate_cta_form_subtitle_text' => 'Talebiniz hızla değerlendirilecek',
            'courier_cta_card_title_text' => 'Kurye Ailemize Katıl',
            'courier_cta_card_description_text' => 'Esnek çalışma saatleri, hızlı ödeme!',
            'courier_cta_side_title_html' => "<span class=\"gradient-text\">Kurye Ol</span>, Özgürce Kazan",
            'service_cards' => $defaultServiceCards,
            'features_badge_text' => 'Neden Bizi Tercih Etmelisiniz?',
            'features_title_html' => "<span class=\"gradient-text\">Avantajlarimiz</span>",
            'features_subtitle_text' => '',
            'feature_cards' => $defaultFeatureCards,
            'process_badge_text' => 'Nasil Calisir?',
            'process_title_html' => "3 Adimda <span class=\"gradient-text\">Teslimat</span>",
            'process_subtitle_text' => '3 basit adimda gonderinizi en hizli sekilde teslim ediyoruz.',
            'process_steps' => $defaultProcessSteps,
            'funfact_items' => $defaultFunfactItems,
            'testimonials_badge_text' => 'Musteri Yorumlari',
            'testimonials_title_html' => "Musterilerimiz <span class=\"gradient-text\">Ne Diyor?</span>",
            'testimonial_items' => $defaultTestimonials,
            'main_cta_title_html' => "Gonderinizi <span class=\"gradient-text\">Bize Emanet Edin</span>",
            'main_cta_description_text' => 'Zamanin degerli oldugu anlarda yaninizdayiz.',
            'main_cta_phone_href' => 'tel:+905513567292',
            'main_cta_phone_icon' => 'fa-phone',
            'main_cta_phone_text' => '0551 356 72 92',
            'main_cta_secondary_href' => 'https://wa.me/905513567292',
            'main_cta_secondary_icon' => 'fa-whatsapp',
            'main_cta_secondary_text' => 'WhatsApp',
            'faq_cards' => $defaultFaqCards,
            'corporate_cta_benefits' => [
                ['icon_class' => 'fa-check-circle', 'text' => 'Öncelikli kurye ataması'],
                ['icon_class' => 'fa-check-circle', 'text' => 'Toplu gönderi indirimi'],
                ['icon_class' => 'fa-check-circle', 'text' => 'API entegrasyonu'],
                ['icon_class' => 'fa-check-circle', 'text' => 'Özel müşteri temsilcisi'],
            ],
            'courier_cta_features' => [
                ['icon_class' => 'fa-wallet', 'title' => 'Esnek Çalışma', 'subtitle' => 'İstediğin saatlerde çalış'],
                ['icon_class' => 'fa-mobile-screen', 'title' => 'Akıllı Navigasyon', 'subtitle' => 'Akıllı rota önerileri'],
                ['icon_class' => 'fa-bolt', 'title' => 'Hızlı Ödeme', 'subtitle' => 'Haftalık ödemeler'],
                ['icon_class' => 'fa-bullseye', 'title' => 'Akıllı Görev Dağılımı', 'subtitle' => 'Yakınındaki siparişler öncelikli'],
            ],
        ];

        try {
            if (! $this->canUseDatabase()) {
                return $defaults;
            }

            if (! Schema::hasTable('landing_pages') || ! Schema::hasTable('landing_page_sections')) {
                return $defaults;
            }

            $page = app(LandingPageService::class)->getPublishedPage('home');
            if (! $page) {
                return $defaults;
            }

            $content = array_merge(
                $defaults,
                $this->sharedChromeDefaults(),
                $this->resolveSharedChromeOverrides()
            );
            if (is_array($page->meta)) {
                $content = array_merge($content, $page->meta);
            }
            $sectionMergeOrder = ['hero', 'services', 'features', 'process', 'stats', 'testimonials', 'main_cta', 'faq', 'corporate_cta', 'courier_cta'];
            $visibleSections = [];
            foreach ($page->sections as $activeSection) {
                $visibleSections[$activeSection->key] = true;
            }
            $content['sections_visible'] = array_merge(
                $content['sections_visible'] ?? [],
                array_fill_keys(array_keys($content['sections_visible'] ?? []), false),
                array_intersect_key($visibleSections, $content['sections_visible'] ?? [])
            );

            foreach ($sectionMergeOrder as $sectionKey) {
                $section = $page->sections->firstWhere('key', $sectionKey);
                if ($section && is_array($section->payload)) {
                    // Filter out empty arrays from payload so they don't override rich defaults
                    $filtered = array_filter($section->payload, fn ($v) => !(is_array($v) && empty($v)));
                    $content = array_merge($content, $filtered);
                }
            }

            $servicesSection = $page->sections->firstWhere('key', 'services');
            if ($servicesSection && $servicesSection->items->isNotEmpty()) {
                $serviceCards = $servicesSection->items
                    ->map(fn ($item) => is_array($item->payload) ? $item->payload : null)
                    ->filter()
                    ->values()
                    ->all();

                if (! empty($serviceCards)) {
                    $content['service_cards'] = $serviceCards;
                }
            }

            $faqSection = $page->sections->firstWhere('key', 'faq');
            if ($faqSection && $faqSection->items->isNotEmpty()) {
                $faqCards = $faqSection->items
                    ->map(fn ($item) => is_array($item->payload) ? $item->payload : null)
                    ->filter()
                    ->values()
                    ->all();

                if (! empty($faqCards)) {
                    $content['faq_cards'] = $faqCards;
                }
            } elseif (
                ! empty($content['faq_card_title_text']) ||
                ! empty($content['faq_card_description_text'])
            ) {
                // Backward compatibility: keep old payload keys functional when no item rows exist.
                $faqCards = $content['faq_cards'] ?? [];
                if (! empty($faqCards)) {
                    $faqCards[1]['title'] = $content['faq_card_title_text'] ?? ($faqCards[1]['title'] ?? null);
                    $faqCards[1]['description'] = $content['faq_card_description_text'] ?? ($faqCards[1]['description'] ?? null);
                    $content['faq_cards'] = $faqCards;
                }
            }

            $corporateCtaSection = $page->sections->firstWhere('key', 'corporate_cta');
            if ($corporateCtaSection && $corporateCtaSection->items->isNotEmpty()) {
                $corporateBenefits = $corporateCtaSection->items
                    ->map(fn ($item) => is_array($item->payload) ? $item->payload : null)
                    ->filter(fn ($payload) => ! empty($payload['text']))
                    ->values()
                    ->all();

                if (! empty($corporateBenefits)) {
                    $content['corporate_cta_benefits'] = $corporateBenefits;
                }
            }

            $courierCtaSection = $page->sections->firstWhere('key', 'courier_cta');
            if ($courierCtaSection && $courierCtaSection->items->isNotEmpty()) {
                $courierFeatures = $courierCtaSection->items
                    ->map(fn ($item) => is_array($item->payload) ? $item->payload : null)
                    ->filter(fn ($payload) => ! empty($payload['title']))
                    ->values()
                    ->all();

                if (! empty($courierFeatures)) {
                    $content['courier_cta_features'] = $courierFeatures;
                }
            }

            $homeItemMap = [
                'feature_cards' => 'features',
                'process_steps' => 'process',
                'funfact_items' => 'stats',
                'testimonial_items' => 'testimonials',
            ];

            foreach ($homeItemMap as $contentKey => $sectionKey) {
                $section = $page->sections->firstWhere('key', $sectionKey);
                if (! $section || $section->items->isEmpty()) {
                    continue;
                }

                $items = $section->items
                    ->map(fn ($item) => is_array($item->payload) ? $item->payload : null)
                    ->filter()
                    ->values()
                    ->all();

                if (! empty($items)) {
                    $content[$contentKey] = $items;
                }
            }

            return $content;
        } catch (Throwable) {
            return $defaults;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function sharedChromeDefaults(): array
    {
        return [
            'header_b2b_cta_enabled' => false,
            'header_b2b_cta_label_text' => 'Kurumsal Giris',
            'header_b2b_cta_href' => '/kurumsal',
            'header_b2b_cta_target' => '_self',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveSharedChromeOverrides(): array
    {
        try {
            $page = app(LandingPageService::class)->getPublishedPage('home');
            if (! $page) {
                return [];
            }

            $heroSection = $page->sections->firstWhere('key', 'hero');
            if (! $heroSection || ! is_array($heroSection->payload)) {
                return [];
            }

            return $this->extractSharedChromePayload($heroSection->payload);
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function extractSharedChromePayload(array $payload): array
    {
        $sharedPayload = [];

        foreach (self::SHARED_CHROME_KEYS as $key) {
            if (array_key_exists($key, $payload)) {
                $sharedPayload[$key] = $payload[$key];
            }
        }

        return $sharedPayload;
    }

    /**
     * Resolve standard landing page content (meta + hero) with safe fallbacks.
     */
    public function resolveStandardPageContent(string $slug, array $defaults, array $itemMap = []): array
    {
        try {
            if (! $this->canUseDatabase()) {
                return $defaults;
            }

            if (! Schema::hasTable('landing_pages') || ! Schema::hasTable('landing_page_sections')) {
                return $defaults;
            }

            $content = array_merge(
                $defaults,
                $this->sharedChromeDefaults(),
                $this->resolveSharedChromeOverrides()
            );

            $page = app(LandingPageService::class)->getPublishedPage($slug);
            if (! $page) {
                return $content;
            }

            if (is_array($page->meta)) {
                $content = array_merge($content, $page->meta);
            }

            $heroSection = $page->sections->firstWhere('key', 'hero');
            if ($heroSection && is_array($heroSection->payload)) {
                $content = array_merge($content, $heroSection->payload);
            }

            foreach ($itemMap as $contentKey => $sectionKey) {
                $section = $page->sections->firstWhere('key', $sectionKey);
                if (! $section || $section->items->isEmpty()) {
                    continue;
                }

                $items = $section->items
                    ->map(fn ($item) => is_array($item->payload) ? $item->payload : null)
                    ->filter()
                    ->values()
                    ->all();

                if (! empty($items)) {
                    $content[$contentKey] = $items;
                }
            }

            return $content;
        } catch (Throwable) {
            return $defaults;
        }
    }

    private function canUseDatabase(): bool
    {
        $defaultConnection = (string) config('database.default');
        $connection = (array) config("database.connections.$defaultConnection", []);
        $driver = (string) ($connection['driver'] ?? '');

        if ($driver === 'sqlite') {
            return true;
        }

        $timeoutSeconds = max((float) env('DB_RUNTIME_PROBE_TIMEOUT', 0.3), 0.1);
        $host = $connection['host'] ?? null;
        if (is_array($host)) {
            $host = $host[0] ?? null;
        }

        if (is_string($host) && $host !== '') {
            $port = (int) ($connection['port'] ?? ($driver === 'pgsql' ? 5432 : ($driver === 'sqlsrv' ? 1433 : 3306)));
            $socket = @fsockopen($host, $port, $errno, $errstr, $timeoutSeconds);

            if ($socket === false) {
                return false;
            }

            fclose($socket);
        }

        if (! in_array($driver, ['mysql', 'pgsql', 'sqlsrv'], true)) {
            return true;
        }

        $host = (string) ($host ?: '127.0.0.1');
        $port = (int) ($connection['port'] ?? ($driver === 'pgsql' ? 5432 : ($driver === 'sqlsrv' ? 1433 : 3306)));
        $database = (string) ($connection['database'] ?? '');
        $username = (string) ($connection['username'] ?? '');
        $password = (string) ($connection['password'] ?? '');

        if ($driver === 'mysql') {
            $database = $database !== '' ? $database : 'information_schema';
            $charset = (string) ($connection['charset'] ?? 'utf8mb4');
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";
        } elseif ($driver === 'pgsql') {
            $database = $database !== '' ? $database : 'postgres';
            $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
        } else {
            $database = $database !== '' ? $database : 'master';
            $dsn = "sqlsrv:Server={$host},{$port};Database={$database}";
        }

        try {
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => max((int) ceil($timeoutSeconds), 1),
            ]);
            $pdo = null;
        } catch (Throwable) {
            return false;
        }

        return true;
    }
}
