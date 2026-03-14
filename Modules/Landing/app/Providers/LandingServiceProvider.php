<?php

namespace Modules\Landing\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\Landing\Models\LandingPageSection;
use Modules\Landing\Models\LandingSectionItem;
use Modules\Landing\Observers\LandingPageSectionObserver;
use Modules\Landing\Observers\LandingSectionItemObserver;
use Modules\Landing\Services\LandingContentResolver;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class LandingServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Landing';

    protected string $nameLower = 'landing';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerViewComposers();
        $this->registerObservers();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->singleton(LandingContentResolver::class);
    }

    protected function registerViewComposers(): void
    {
        View::composer('landing.home', function ($view) {
            $content = app(LandingContentResolver::class)->resolveHomeContent();
            $view->with('landingContent', $content);
        });

        $standardPages = [
            'landing.about' => [
                'slug' => 'about',
                'defaults' => [
                    'meta_title' => 'Hakkımızda - SimdiGetir Hızlı ve Güvenilir Kurye',
                    'meta_description' => "SimdiGetir - 7/24 güvenilir ve hızlı teslimat ile İstanbul'un lider kurye şirketi.",
                    'meta_keywords' => 'simdigetir hakkında, kurye şirketi istanbul, güvenilir kurye firması, profesyonel kurye şirketi, moto kurye firması istanbul',
                    'hero_badge_text' => 'Teknoloji & İnovasyon',
                    'hero_title_html' => "Kuryenin <span class='gradient-text'>Geleceğini</span> İnşa Ediyoruz",
                    'hero_description_text' => "İstanbul'da hızlı ve güvenilir teslimat. Her gönderide daha hızlı, daha güvenli.",
                ],
            ],
            'landing.services' => [
                'slug' => 'services',
                'defaults' => [
                    'meta_title' => 'Hizmetlerimiz - SimdiGetir Profesyonel Kurye Hizmetleri',
                    'meta_description' => 'SimdiGetir profesyonel kurye hizmetleri. Motorlu kurye, acil kurye ve araçlı kurye hizmetleri ile 7/24 yanınızdayız.',
                    'meta_keywords' => 'motorlu kurye, moto kurye istanbul, acil kurye, araçlı kurye, hızlı teslimat, aynı gün teslim, kurye hizmeti fiyat, istanbul kurye',
                    'hero_badge_text' => 'Profesyonel Hizmetler',
                    'hero_title_html' => "Akıllı Kurye <span class='gradient-text'>Çözümleri</span>",
                    'hero_description_text' => 'Gönderinize en uygun hizmeti sunuyoruz. Hızlı, güvenilir, profesyonel.',
                ],
            ],
            'landing.contact' => [
                'slug' => 'contact',
                'defaults' => [
                    'meta_title' => 'İletişim - SimdiGetir',
                    'meta_description' => 'SimdiGetir kurye hizmeti ile iletişime geçin. 7/24 aktif müşteri desteği. Telefon: 0551 356 72 92',
                    'meta_keywords' => 'simdigetir iletişim, kurye telefon, moto kurye ara, kurye çağır istanbul, 7/24 kurye hattı',
                    'hero_badge_text' => 'Müşteri Servisi Aktif',
                    'hero_title_html' => "<span class='gradient-text'>7/24</span> Yanınızdayız",
                    'hero_description_text' => 'Uzman ekibimiz sorularınızı yanıtlamak için her zaman hazır.',
                    'contact_channels' => [
                        [
                            'title' => 'Telefon',
                            'value' => '+90 551 356 72 92',
                            'hint' => '7/24 Aktif Hat',
                            'icon_class' => 'fa-phone',
                            'link' => 'tel:+905513567292',
                            'card_class' => '',
                            'icon_wrapper_class' => '',
                            'target_blank' => false,
                        ],
                        [
                            'title' => 'WhatsApp',
                            'value' => 'Hızlı Mesaj',
                            'hint' => 'Anlık Yanıt',
                            'icon_class' => 'fa-whatsapp',
                            'link' => 'https://wa.me/905513567292',
                            'card_class' => 'contact-card-whatsapp',
                            'icon_wrapper_class' => 'contact-icon-whatsapp',
                            'target_blank' => true,
                        ],
                        [
                            'title' => 'E-posta',
                            'value' => 'webgetir@simdigetir.com',
                            'hint' => 'Kurumsal İletişim',
                            'icon_class' => 'fa-envelope',
                            'link' => 'mailto:webgetir@simdigetir.com',
                            'card_class' => '',
                            'icon_wrapper_class' => 'contact-icon-email',
                            'target_blank' => false,
                        ],
                        [
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
                ],
                'item_map' => [
                    'contact_channels' => 'contact_channels',
                ],
            ],
            'landing.faq' => [
                'slug' => 'faq',
                'defaults' => [
                    'meta_title' => 'Sıkça Sorulan Sorular - SimdiGetir',
                    'meta_description' => 'SimdiGetir kurye hizmetleri hakkında sıkça sorulan sorular. Merak ettiklerinizi anında öğrenin!',
                    'meta_keywords' => 'kurye sss, kurye sıkça sorulan sorular, moto kurye soru, kurye hizmeti bilgi, istanbul kurye bilgi',
                    'hero_badge_text' => 'Yardım Merkezi',
                    'hero_title_html' => "<span class='gradient-text'>Sıkça Sorulan</span> Sorular",
                    'hero_description_text' => 'Kurye hizmetlerimiz hakkında merak ettiklerinizi öğrenin. Sorunuz cevaplanmadıysa bize ulaşın!',
                    'faq_items' => [
                        [
                            'icon' => '📦',
                            'question' => 'SimdiGetir.com hangi kurye hizmetlerini sunmaktadır?',
                            'answer_html' => "<strong>Motorlu kurye</strong>, <strong>acil kurye</strong> ve <strong>araçlı kurye</strong> hizmetleri sunuyoruz. Gönderiniz için en uygun kurye tipini ve rotayı belirleriz.",
                        ],
                        [
                            'icon' => '⏰',
                            'question' => 'Çalışma saatleriniz nedir?',
                            'answer_html' => "<strong>7 gün 24 saat</strong> aktif hizmet veriyoruz! Gece veya gündüz fark etmeksizin, kurye hizmetlerimizden yararlanabilirsiniz.",
                        ],
                        [
                            'icon' => '📍',
                            'question' => 'Hangi bölgelerde kurye hizmeti sunuyorsunuz?',
                            'answer_html' => "<strong>İstanbul genelinde</strong> tüm ilçelere ve semtlere hizmet vermekteyiz. Akıllı rota optimizasyonumuz sayesinde en uzak noktalara bile hızlı teslimat sağlıyoruz.",
                        ],
                        [
                            'icon' => '⚡',
                            'question' => 'Acil gönderilerimi nasıl hızlı teslim edebilirsiniz?',
                            'answer_html' => "Acil gönderileriniz <strong>saniyeler içinde</strong> en yakın müsait kuryeye atanır. Akıllı rotalama ile trafik durumunu analiz eder ve en hızlı güzergahı belirler. En uzun mesafe gönderileri bile <strong>3 saat içinde</strong> teslim edilir.",
                        ],
                    ],
                ],
                'item_map' => [
                    'faq_items' => 'faq_items',
                ],
            ],
            'landing.corporate' => [
                'slug' => 'corporate',
                'defaults' => [
                    'meta_title' => 'Kurumsal Çözümler - SimdiGetir Kurye',
                    'meta_description' => 'E-ticaret ve kurumsal firmalar için özel teslimat çözümleri. API entegrasyonu, özel fiyatlandırma, öncelikli destek.',
                    'meta_keywords' => 'kurumsal kurye, firma kurye hizmeti, toplu gönderi, e-ticaret kurye, API kurye entegrasyonu, kurumsal teslimat, B2B kurye istanbul',
                    'hero_badge_text' => 'Kurumsal Çözümler',
                    'hero_title_html' => "İşletmeniz İçin<br><span class='gradient-text'>Özel Teslimat</span><br>Çözümleri",
                    'hero_description_text' => 'E-ticaret siteniz, mağazanız veya kurumunuz için ölçeklenebilir, güvenilir ve akıllı teslimat altyapısı.',
                ],
            ],
        ];

        foreach ($standardPages as $viewName => $definition) {
            View::composer($viewName, function ($view) use ($definition) {
                $content = app(LandingContentResolver::class)->resolveStandardPageContent(
                    $definition['slug'],
                    $definition['defaults'],
                    $definition['item_map'] ?? []
                );
                $view->with('landingContent', $content);
            });
        }
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $configPath = module_path($this->name, config('modules.paths.generator.config.path'));

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $config = str_replace($configPath.DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $config_key = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $config);
                    $segments = explode('.', $this->nameLower.'.'.$config_key);

                    // Remove duplicated adjacent segments
                    $normalized = [];
                    foreach ($segments as $segment) {
                        if (end($normalized) !== $segment) {
                            $normalized[] = $segment;
                        }
                    }

                    $key = ($config === 'config.php') ? $this->nameLower : implode('.', $normalized);

                    $this->publishes([$file->getPathname() => config_path($config)], 'config');
                    $this->merge_config_from($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Merge config from the given path recursively.
     */
    protected function merge_config_from(string $path, string $key): void
    {
        $existing = config($key, []);
        $module_config = require $path;

        config([$key => array_replace_recursive($existing, $module_config)]);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        Blade::componentNamespace(config('modules.namespace').'\\' . $this->name . '\\View\\Components', $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }

    private function registerObservers(): void
    {
        LandingPageSection::observe(LandingPageSectionObserver::class);
        LandingSectionItem::observe(LandingSectionItemObserver::class);
    }
}
