<?php

namespace Modules\Landing\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Landing\Models\LandingPage;
use Modules\Landing\Models\LandingPageSection;
use Modules\Landing\Models\LandingSectionItem;

class LandingDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $homePage = LandingPage::query()->updateOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home',
                'is_active' => true,
                'meta' => [
                    'title' => 'SimdiGetir - Hızlı ve Güvenilir Kurye Hizmeti',
                ],
            ]
        );

        $hero = LandingPageSection::query()->updateOrCreate(
            ['page_id' => $homePage->id, 'key' => 'hero'],
            [
                'type' => 'hero',
                'title' => 'Hero',
                'is_active' => true,
                'sort_order' => 1,
                'payload' => [
                    'hero_badge_text' => '7/24 Aktif Hizmet',
                    'hero_title_html' => "Zamanın <span class='gradient-text'>Değerli</span> Olduğu<br>Anlarda Yanınızdayız",
                    'hero_description_text' => "İstanbul'un en hızlı kurye ağı. Gönderinizi teslim alır, en kısa rotadan güvenle ulaştırırız.",
                ],
            ]
        );

        $services = LandingPageSection::query()->updateOrCreate(
            ['page_id' => $homePage->id, 'key' => 'services'],
            [
                'type' => 'services',
                'title' => 'Services',
                'is_active' => true,
                'sort_order' => 2,
                'payload' => [
                    'services_badge_text' => 'Profesyonel Hizmetler',
                    'services_title_html' => "Kurye <span class='gradient-text'>Çözümlerimiz</span>",
                    'services_subtitle_text' => 'Her gönderi için doğru çözüm. Hızlı, güvenilir, profesyonel.',
                ],
            ]
        );

        $faq = LandingPageSection::query()->updateOrCreate(
            ['page_id' => $homePage->id, 'key' => 'faq'],
            [
                'type' => 'faq',
                'title' => 'FAQ',
                'is_active' => true,
                'sort_order' => 8,
                'payload' => [
                    'faq_card_title_text' => 'Sıkça Sorulan Sorular',
                    'faq_card_description_text' => 'Kurye hizmetlerimiz, fiyatlandırma, teslimat süreleri ve daha fazlası hakkında merak edilenler.',
                ],
            ]
        );

        $features = LandingPageSection::query()->updateOrCreate(
            ['page_id' => $homePage->id, 'key' => 'features'],
            [
                'type' => 'features',
                'title' => 'Features',
                'is_active' => true,
                'sort_order' => 3,
                'payload' => [
                    'features_badge_text' => 'Neden Bizi Tercih Etmelisiniz?',
                    'features_title_html' => "<span class='gradient-text'>Avantajlarimiz</span>",
                    'features_subtitle_text' => '',
                ],
            ]
        );

        $process = LandingPageSection::query()->updateOrCreate(
            ['page_id' => $homePage->id, 'key' => 'process'],
            [
                'type' => 'process',
                'title' => 'Process',
                'is_active' => true,
                'sort_order' => 4,
                'payload' => [
                    'process_badge_text' => 'Nasil Calisir?',
                    'process_title_html' => "3 Adimda <span class='gradient-text'>Teslimat</span>",
                    'process_subtitle_text' => '3 basit adimda gonderinizi en hizli sekilde teslim ediyoruz.',
                ],
            ]
        );

        $stats = LandingPageSection::query()->updateOrCreate(
            ['page_id' => $homePage->id, 'key' => 'stats'],
            [
                'type' => 'stats',
                'title' => 'Stats',
                'is_active' => true,
                'sort_order' => 5,
                'payload' => [],
            ]
        );

        $testimonials = LandingPageSection::query()->updateOrCreate(
            ['page_id' => $homePage->id, 'key' => 'testimonials'],
            [
                'type' => 'testimonials',
                'title' => 'Testimonials',
                'is_active' => true,
                'sort_order' => 6,
                'payload' => [
                    'testimonials_badge_text' => 'Musteri Yorumlari',
                    'testimonials_title_html' => "Musterilerimiz <span class='gradient-text'>Ne Diyor?</span>",
                ],
            ]
        );

        $mainCta = LandingPageSection::query()->updateOrCreate(
            ['page_id' => $homePage->id, 'key' => 'main_cta'],
            [
                'type' => 'cta',
                'title' => 'Main CTA',
                'is_active' => true,
                'sort_order' => 7,
                'payload' => [
                    'main_cta_title_html' => "Gonderinizi <span class='gradient-text'>Bize Emanet Edin</span>",
                    'main_cta_description_text' => 'Zamanin degerli oldugu anlarda yaninizdayiz.',
                    'main_cta_phone_href' => 'tel:+905513567292',
                    'main_cta_phone_icon' => 'fa-phone',
                    'main_cta_phone_text' => '0551 356 72 92',
                    'main_cta_secondary_href' => 'https://wa.me/905513567292',
                    'main_cta_secondary_icon' => 'fa-whatsapp',
                    'main_cta_secondary_text' => 'WhatsApp',
                ],
            ]
        );

        $corporateCta = LandingPageSection::query()->updateOrCreate(
            ['page_id' => $homePage->id, 'key' => 'corporate_cta'],
            [
                'type' => 'cta',
                'title' => 'Corporate CTA',
                'is_active' => true,
                'sort_order' => 9,
                'payload' => [
                    'corporate_cta_form_title_text' => 'Teklif İsteyin',
                    'corporate_cta_form_subtitle_text' => 'Talebiniz hızla değerlendirilecek',
                ],
            ]
        );

        $courierCta = LandingPageSection::query()->updateOrCreate(
            ['page_id' => $homePage->id, 'key' => 'courier_cta'],
            [
                'type' => 'cta',
                'title' => 'Courier CTA',
                'is_active' => true,
                'sort_order' => 10,
                'payload' => [
                    'courier_cta_card_title_text' => 'Kurye Ailemize Katıl',
                    'courier_cta_card_description_text' => 'Esnek çalışma saatleri, hızlı ödeme!',
                    'courier_cta_side_title_html' => "<span class='gradient-text'>Kurye Ol</span>, Özgürce Kazan",
                ],
            ]
        );

        $this->seedServiceItems($services->id);
        $this->seedFaqItems($faq->id);
        $this->seedFeatureItems($features->id);
        $this->seedProcessItems($process->id);
        $this->seedStatsItems($stats->id);
        $this->seedTestimonialItems($testimonials->id);
        $this->seedMainCtaItems($mainCta->id);
        $this->seedCorporateCtaItems($corporateCta->id);
        $this->seedCourierCtaItems($courierCta->id);

        // Keep variable explicitly used for clarity and future extension.
        $this->seedHeroItems($hero->id);

        $this->seedStandardPage(
            slug: 'about',
            title: 'About',
            meta: [
                'meta_title' => 'Hakkımızda - SimdiGetir Hızlı ve Güvenilir Kurye',
                'meta_description' => "SimdiGetir - 7/24 güvenilir ve hızlı teslimat ile İstanbul'un lider kurye şirketi.",
                'meta_keywords' => 'simdigetir hakkında, kurye şirketi istanbul, güvenilir kurye firması, profesyonel kurye şirketi, moto kurye firması istanbul',
            ],
            heroPayload: [
                'hero_badge_text' => 'Teknoloji & İnovasyon',
                'hero_title_html' => "Kuryenin <span class='gradient-text'>Geleceğini</span> İnşa Ediyoruz",
                'hero_description_text' => "İstanbul'da hızlı ve güvenilir teslimat. Her gönderide daha hızlı, daha güvenli.",
            ],
            sortOrder: 1
        );

        $this->seedStandardPage(
            slug: 'services',
            title: 'Services',
            meta: [
                'meta_title' => 'Hizmetlerimiz - SimdiGetir Profesyonel Kurye Hizmetleri',
                'meta_description' => 'SimdiGetir profesyonel kurye hizmetleri. Motorlu kurye, acil kurye ve araçlı kurye hizmetleri ile 7/24 yanınızdayız.',
                'meta_keywords' => 'motorlu kurye, moto kurye istanbul, acil kurye, araçlı kurye, hızlı teslimat, aynı gün teslim, kurye hizmeti fiyat, istanbul kurye',
            ],
            heroPayload: [
                'hero_badge_text' => 'Profesyonel Hizmetler',
                'hero_title_html' => "Akıllı Kurye <span class='gradient-text'>Çözümleri</span>",
                'hero_description_text' => 'Gönderinize en uygun hizmeti sunuyoruz. Hızlı, güvenilir, profesyonel.',
            ],
            sortOrder: 1
        );

        $this->seedStandardPage(
            slug: 'contact',
            title: 'Contact',
            meta: [
                'meta_title' => 'İletişim - SimdiGetir',
                'meta_description' => 'SimdiGetir kurye hizmeti ile iletişime geçin. 7/24 aktif müşteri desteği. Telefon: 0551 356 72 92',
                'meta_keywords' => 'simdigetir iletişim, kurye telefon, moto kurye ara, kurye çağır istanbul, 7/24 kurye hattı',
            ],
            heroPayload: [
                'hero_badge_text' => 'Müşteri Servisi Aktif',
                'hero_title_html' => "<span class='gradient-text'>7/24</span> Yanınızdayız",
                'hero_description_text' => 'Uzman ekibimiz sorularınızı yanıtlamak için her zaman hazır.',
            ],
            sortOrder: 1
        );
        $this->seedStandardContactChannels();

        $this->seedStandardPage(
            slug: 'faq',
            title: 'FAQ',
            meta: [
                'meta_title' => 'Sıkça Sorulan Sorular - SimdiGetir',
                'meta_description' => 'SimdiGetir kurye hizmetleri hakkında sıkça sorulan sorular. Merak ettiklerinizi anında öğrenin!',
                'meta_keywords' => 'kurye sss, kurye sıkça sorulan sorular, moto kurye soru, kurye hizmeti bilgi, istanbul kurye bilgi',
            ],
            heroPayload: [
                'hero_badge_text' => 'Yardım Merkezi',
                'hero_title_html' => "<span class='gradient-text'>Sıkça Sorulan</span> Sorular",
                'hero_description_text' => 'Kurye hizmetlerimiz hakkında merak ettiklerinizi öğrenin. Sorunuz cevaplanmadıysa bize ulaşın!',
            ],
            sortOrder: 1
        );
        $this->seedStandardFaqItems();

        $this->seedStandardPage(
            slug: 'corporate',
            title: 'Corporate',
            meta: [
                'meta_title' => 'Kurumsal Çözümler - SimdiGetir Kurye',
                'meta_description' => 'E-ticaret ve kurumsal firmalar için özel teslimat çözümleri. API entegrasyonu, özel fiyatlandırma, öncelikli destek.',
                'meta_keywords' => 'kurumsal kurye, firma kurye hizmeti, toplu gönderi, e-ticaret kurye, API kurye entegrasyonu, kurumsal teslimat, B2B kurye istanbul',
            ],
            heroPayload: [
                'hero_badge_text' => 'Kurumsal Çözümler',
                'hero_title_html' => "İşletmeniz İçin<br><span class='gradient-text'>Özel Teslimat</span><br>Çözümleri",
                'hero_description_text' => 'E-ticaret siteniz, mağazanız veya kurumunuz için ölçeklenebilir, güvenilir ve akıllı teslimat altyapısı.',
            ],
            sortOrder: 1
        );
    }

    private function seedStandardPage(
        string $slug,
        string $title,
        array $meta,
        array $heroPayload,
        int $sortOrder = 1
    ): void {
        $page = LandingPage::query()->updateOrCreate(
            ['slug' => $slug],
            [
                'title' => $title,
                'is_active' => true,
                'meta' => $meta,
            ]
        );

        LandingPageSection::query()->updateOrCreate(
            ['page_id' => $page->id, 'key' => 'hero'],
            [
                'type' => 'hero',
                'title' => ucfirst($slug).' Hero',
                'is_active' => true,
                'sort_order' => $sortOrder,
                'payload' => $heroPayload,
            ]
        );
    }

    private function seedStandardContactChannels(): void
    {
        $section = $this->upsertStandardPageSection(
            pageSlug: 'contact',
            key: 'contact_channels',
            type: 'contact_channels',
            title: 'Contact Channels',
            sortOrder: 2
        );

        if (! $section) {
            return;
        }

        $items = [
            'channel_phone' => [
                'sort_order' => 1,
                'payload' => [
                    'title' => 'Telefon',
                    'value' => '+90 551 356 72 92',
                    'hint' => '7/24 Aktif Hat',
                    'icon_class' => 'fa-phone',
                    'link' => 'tel:+905513567292',
                    'card_class' => '',
                    'icon_wrapper_class' => '',
                    'target_blank' => false,
                ],
            ],
            'channel_whatsapp' => [
                'sort_order' => 2,
                'payload' => [
                    'title' => 'WhatsApp',
                    'value' => 'Hızlı Mesaj',
                    'hint' => 'Anlık Yanıt',
                    'icon_class' => 'fa-whatsapp',
                    'link' => 'https://wa.me/905513567292',
                    'card_class' => 'contact-card-whatsapp',
                    'icon_wrapper_class' => 'contact-icon-whatsapp',
                    'target_blank' => true,
                ],
            ],
            'channel_email' => [
                'sort_order' => 3,
                'payload' => [
                    'title' => 'E-posta',
                    'value' => 'webgetir@simdigetir.com',
                    'hint' => 'Kurumsal İletişim',
                    'icon_class' => 'fa-envelope',
                    'link' => 'mailto:webgetir@simdigetir.com',
                    'card_class' => '',
                    'icon_wrapper_class' => 'contact-icon-email',
                    'target_blank' => false,
                ],
            ],
            'channel_address' => [
                'sort_order' => 4,
                'payload' => [
                    'title' => 'Adres',
                    'value' => 'Yeşilce Mahallesi Aytekin Sokak No:5/2',
                    'hint' => 'Kağıthane / İstanbul',
                    'icon_class' => 'fa-location-dot',
                    'link' => null,
                    'card_class' => '',
                    'icon_wrapper_class' => 'contact-icon-location',
                    'target_blank' => false,
                ],
            ],
        ];

        $this->upsertItems($section->id, $items);
    }

    private function seedStandardFaqItems(): void
    {
        $section = $this->upsertStandardPageSection(
            pageSlug: 'faq',
            key: 'faq_items',
            type: 'faq',
            title: 'FAQ Items',
            sortOrder: 2
        );

        if (! $section) {
            return;
        }

        $items = [
            'faq_services' => [
                'sort_order' => 1,
                'payload' => [
                    'icon' => '📦',
                    'question' => 'SimdiGetir.com hangi kurye hizmetlerini sunmaktadır?',
                    'answer_html' => "<strong>Motorlu kurye</strong>, <strong>acil kurye</strong> ve <strong>araçlı kurye</strong> hizmetleri sunuyoruz. Gönderiniz için en uygun kurye tipini ve rotayı belirleriz.",
                ],
            ],
            'faq_hours' => [
                'sort_order' => 2,
                'payload' => [
                    'icon' => '⏰',
                    'question' => 'Çalışma saatleriniz nedir?',
                    'answer_html' => "<strong>7 gün 24 saat</strong> aktif hizmet veriyoruz. Gece veya gündüz fark etmeksizin kurye hizmetlerimizden yararlanabilirsiniz.",
                ],
            ],
            'faq_regions' => [
                'sort_order' => 3,
                'payload' => [
                    'icon' => '📍',
                    'question' => 'Hangi bölgelerde kurye hizmeti sunuyorsunuz?',
                    'answer_html' => "<strong>İstanbul genelinde</strong> tüm ilçelere ve semtlere hizmet vermekteyiz. Akıllı rota optimizasyonumuz sayesinde en uzak noktalara bile hızlı teslimat sağlıyoruz.",
                ],
            ],
            'faq_urgent' => [
                'sort_order' => 4,
                'payload' => [
                    'icon' => '⚡',
                    'question' => 'Acil gönderilerimi nasıl hızlı teslim edebilirsiniz?',
                    'answer_html' => "Acil gönderileriniz <strong>saniyeler içinde</strong> en yakın müsait kuryeye atanır. Akıllı rotalama ile en hızlı güzergah belirlenir ve uzun mesafeli gönderiler bile hızlıca teslim edilir.",
                ],
            ],
        ];

        $this->upsertItems($section->id, $items);
    }

    private function upsertStandardPageSection(
        string $pageSlug,
        string $key,
        string $type,
        string $title,
        int $sortOrder
    ): ?LandingPageSection {
        $page = LandingPage::query()->where('slug', $pageSlug)->first();

        if (! $page) {
            return null;
        }

        return LandingPageSection::query()->updateOrCreate(
            ['page_id' => $page->id, 'key' => $key],
            [
                'type' => $type,
                'title' => $title,
                'is_active' => true,
                'sort_order' => $sortOrder,
                'payload' => [],
            ]
        );
    }

    private function seedServiceItems(int $sectionId): void
    {
        $items = [
            'service_motor' => [
                'sort_order' => 1,
                'payload' => [
                    'number' => '01',
                    'icon_text' => '🏍️',
                    'title' => 'Motorlu Kurye',
                    'description' => 'Trafiği atlatarak dakikalar içinde hedefe ulaşın. Akıllı rota optimizasyonu ile en hızlı teslimat.',
                    'features' => ['Anlık trafik analizi', 'Akıllı rota optimizasyonu', 'Gerçek zamanlı takip'],
                    'button_icon' => 'fa-phone',
                    'button_label' => 'Hemen Ara',
                    'button_href' => 'tel:+905513567292',
                ],
            ],
            'service_urgent' => [
                'sort_order' => 2,
                'payload' => [
                    'number' => '02',
                    'icon_text' => '⚡',
                    'icon_style' => 'background: linear-gradient(135deg, #22d3ee 0%, #7c3aed 100%);',
                    'title' => 'Acil Kurye',
                    'description' => 'Saniyeler içinde en yakın kurye atanır. Öncelikli teslimat garantisi ile kritik gönderileriniz güvende.',
                    'features' => ['Anlık kurye eşleştirme', 'Öncelikli gönderi statüsü', 'SLA garantili teslimat'],
                    'button_icon' => 'fa-bolt',
                    'button_label' => 'Acil Çağır',
                    'button_href' => 'tel:+905513567292',
                ],
            ],
            'service_vehicle' => [
                'sort_order' => 3,
                'payload' => [
                    'number' => '03',
                    'icon_text' => '🚗',
                    'icon_style' => 'background: linear-gradient(135deg, #10b981 0%, #22d3ee 100%);',
                    'title' => 'Araçlı Kurye',
                    'description' => 'Büyük hacimli gönderiler için özel araç filosu. Hassas eşya taşıma ve toplu teslimat imkanı.',
                    'features' => ['Büyük hacim kapasitesi', 'Hassas eşya koruması', 'Toplu teslimat imkanı'],
                    'button_icon' => 'fa-truck',
                    'button_label' => 'Araç Talep Et',
                    'button_href' => 'tel:+905513567292',
                ],
            ],
        ];

        $this->upsertItems($sectionId, $items);
    }

    private function seedFaqItems(int $sectionId): void
    {
        $items = [
            'faq_route' => [
                'sort_order' => 1,
                'payload' => [
                    'image_style' => 'background: linear-gradient(135deg, #7c3aed 0%, #22d3ee 100%);',
                    'icon_class' => 'fa-route',
                    'date_label' => '10 Şubat 2026',
                    'title' => 'Akıllı Rota ile Daha Hızlı Teslimat',
                    'description' => 'Akıllı rota optimizasyonu sayesinde teslimat sürelerimizi nasıl kısaltıyoruz? Hizmetlerimizi keşfedin.',
                    'link' => '/hizmetler',
                    'link_label' => 'Hizmetleri İncele',
                ],
            ],
            'faq_common' => [
                'sort_order' => 2,
                'payload' => [
                    'image_style' => 'background: linear-gradient(135deg, #ec4899 0%, #7c3aed 100%);',
                    'icon_class' => 'fa-question-circle',
                    'date_label' => '5 Şubat 2026',
                    'title' => 'Sıkça Sorulan Sorular',
                    'description' => 'Kurye hizmetlerimiz, fiyatlandırma, teslimat süreleri ve daha fazlası hakkında merak edilenler.',
                    'link' => '/sss',
                    'link_label' => 'Sorulara Bak',
                ],
            ],
        ];

        $this->upsertItems($sectionId, $items);
    }

    private function seedFeatureItems(int $sectionId): void
    {
        $items = [
            'feature_routing' => ['sort_order' => 1, 'payload' => ['icon' => '🚩', 'title' => 'Akilli Rotalama', 'description' => 'En hizli rota ile teslimat']],
            'feature_tracking' => ['sort_order' => 2, 'payload' => ['icon' => '📍', 'title' => 'Canli Takip', 'description' => 'Gonderinizi gercek zamanli izleyin']],
            'feature_matching' => ['sort_order' => 3, 'payload' => ['icon' => '⚡', 'title' => 'Hizli Eslestirme', 'description' => 'Saniyeler icinde en yakin kurye']],
            'feature_secure' => ['sort_order' => 4, 'payload' => ['icon' => '🔒', 'title' => 'Guvenli Teslimat', 'description' => '%99 basarili teslimat orani']],
        ];

        $this->upsertItems($sectionId, $items);
    }

    private function seedProcessItems(int $sectionId): void
    {
        $items = [
            'process_1' => ['sort_order' => 1, 'payload' => ['number' => '01', 'title' => 'Gonderi Bilgisi', 'description' => 'Telefon veya WhatsApp ile bize ulasin.']],
            'process_2' => ['sort_order' => 2, 'payload' => ['number' => '02', 'title' => 'Akilli Eslestirme', 'description' => 'En uygun kurye ve rota saniyeler icinde belirlenir.']],
            'process_3' => ['sort_order' => 3, 'payload' => ['number' => '03', 'title' => 'Hizli Teslimat', 'description' => 'Kurye yola cikar, siz anlik takip edersiniz.']],
        ];

        $this->upsertItems($sectionId, $items);
    }

    private function seedStatsItems(int $sectionId): void
    {
        $items = [
            'stat_1' => ['sort_order' => 1, 'payload' => ['count' => 52, 'suffix' => 'K+', 'label' => 'Mutlu Musteri']],
            'stat_2' => ['sort_order' => 2, 'payload' => ['count' => 150, 'suffix' => 'K+', 'label' => 'Tamamlanan Teslimat']],
            'stat_3' => ['sort_order' => 3, 'payload' => ['count' => 500, 'suffix' => '+', 'label' => 'Aktif Kurye']],
            'stat_4' => ['sort_order' => 4, 'payload' => ['count' => 99, 'suffix' => '%', 'label' => 'Memnuniyet Orani']],
        ];

        $this->upsertItems($sectionId, $items);
    }

    private function seedTestimonialItems(int $sectionId): void
    {
        $items = [
            'testimonial_1' => [
                'sort_order' => 1,
                'payload' => [
                    'avatar_text' => 'AY',
                    'avatar_style' => 'background:linear-gradient(135deg,#7c3aed,#a855f7);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;color:white;',
                    'stars' => 5,
                    'text' => 'E-ticaret gonderilerimizde hiz ve memnuniyet gozle gorulur sekilde artti.',
                    'author_name' => 'Ahmet Yilmaz',
                    'author_role' => 'E-Ticaret Isletmecisi',
                ],
            ],
            'testimonial_2' => [
                'sort_order' => 2,
                'payload' => [
                    'avatar_text' => 'EK',
                    'avatar_style' => 'background:linear-gradient(135deg,#22d3ee,#06b6d4);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;color:white;',
                    'stars' => 5,
                    'text' => 'Acil gonderilerimizde guvenilir ve surekli bir cozum sagladi.',
                    'author_name' => 'Dr. Elif Kaya',
                    'author_role' => 'Klinik Yoneticisi',
                ],
            ],
        ];

        $this->upsertItems($sectionId, $items);
    }

    private function seedMainCtaItems(int $sectionId): void
    {
        // Main CTA currently uses section payload only.
        $this->upsertItems($sectionId, []);
    }

    private function seedCorporateCtaItems(int $sectionId): void
    {
        $items = [
            'corp_benefit_1' => ['sort_order' => 1, 'payload' => ['icon_class' => 'fa-check-circle', 'text' => 'Öncelikli kurye ataması']],
            'corp_benefit_2' => ['sort_order' => 2, 'payload' => ['icon_class' => 'fa-check-circle', 'text' => 'Toplu gönderi indirimi']],
            'corp_benefit_3' => ['sort_order' => 3, 'payload' => ['icon_class' => 'fa-check-circle', 'text' => 'API entegrasyonu']],
            'corp_benefit_4' => ['sort_order' => 4, 'payload' => ['icon_class' => 'fa-check-circle', 'text' => 'Özel müşteri temsilcisi']],
        ];

        $this->upsertItems($sectionId, $items);
    }

    private function seedCourierCtaItems(int $sectionId): void
    {
        $items = [
            'courier_feature_1' => ['sort_order' => 1, 'payload' => ['icon_class' => 'fa-wallet', 'title' => 'Esnek Çalışma', 'subtitle' => 'İstediğin saatlerde çalış']],
            'courier_feature_2' => ['sort_order' => 2, 'payload' => ['icon_class' => 'fa-mobile-screen', 'title' => 'Akıllı Navigasyon', 'subtitle' => 'Akıllı rota önerileri']],
            'courier_feature_3' => ['sort_order' => 3, 'payload' => ['icon_class' => 'fa-bolt', 'title' => 'Hızlı Ödeme', 'subtitle' => 'Haftalık ödemeler']],
            'courier_feature_4' => ['sort_order' => 4, 'payload' => ['icon_class' => 'fa-bullseye', 'title' => 'Akıllı Görev Dağılımı', 'subtitle' => 'Yakınındaki siparişler öncelikli']],
        ];

        $this->upsertItems($sectionId, $items);
    }

    private function seedHeroItems(int $sectionId): void
    {
        // Reserved for future hero sub-block items.
        $this->upsertItems($sectionId, []);
    }

    private function upsertItems(int $sectionId, array $items): void
    {
        foreach ($items as $itemKey => $item) {
            LandingSectionItem::query()->updateOrCreate(
                [
                    'section_id' => $sectionId,
                    'item_key' => $itemKey,
                ],
                [
                    'sort_order' => $item['sort_order'] ?? 0,
                    'payload' => $item['payload'] ?? [],
                    'is_active' => true,
                ]
            );
        }
    }
}
