<?php

namespace App\Http\Controllers;

use App\Models\LegalDocument;
use App\Models\SitemapEntry;
use Illuminate\Http\Response;
use Modules\Landing\Models\LandingPage;

class SitemapController extends Controller
{
    /**
     * Dinamik XML Sitemap üreteci
     */
    public function index(): Response
    {
        $locations = config('istanbul-locations');
        $baseUrl = rtrim(config('app.url', 'https://simdigetir.com'), '/');
        // lastmod "her gün bugün" olmaz (Google o zaman lastmod'a güvenmeyi bırakır).
        $view = fn (string $name) => resource_path("views/landing/{$name}.blade.php");
        $locationsFile = config_path('istanbul-locations.php');
        $staticSources = [
            '/' => [$view('home')],
            '/hakkimizda' => [$view('about')],
            '/hizmetler' => [$view('services')],
            '/kurumsal' => [$view('corporate')],
            '/kurye-basvuru' => [$view('courier-apply')],
            '/iletisim' => [$view('contact')],
            '/sss' => [$view('faq')],
            '/kvkk' => [$view('kvkk')],
            '/kurye' => [$view('location-index'), $locationsFile],
        ];
        $districtLastmod = $this->fileLastmod([$view('location-district'), $locationsFile]);
        $neighborhoodLastmod = $this->fileLastmod([$view('location-neighborhood'), $locationsFile]);
        $this->seen = [];

        $staticDefaults = [
            ['url' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => '/hakkimizda', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/hizmetler', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['url' => '/kurumsal', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/kurye-basvuru', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['url' => '/iletisim', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['url' => '/sss', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['url' => '/kvkk', 'priority' => '0.3', 'changefreq' => 'yearly'],
            ['url' => '/kurye', 'priority' => '0.9', 'changefreq' => 'weekly'],
        ];

        $entryOverrides = SitemapEntry::query()->get()->keyBy('path');
        $staticPages = [];
        foreach ($staticDefaults as $default) {
            $path = $default['url'];
            $override = $entryOverrides->get($path);

            if ($override && ! $override->is_active) {
                continue;
            }

            $staticPages[] = [
                'url' => $path,
                'priority' => (string) ($override?->priority ?? $default['priority']),
                'changefreq' => (string) ($override?->changefreq ?? $default['changefreq']),
                'lastmod' => $override?->lastmod_at?->format('Y-m-d') ?? $this->fileLastmod($staticSources[$path] ?? []),
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Statik sayfalar
        foreach ($staticPages as $page) {
            $xml .= $this->urlEntry($baseUrl . $page['url'], $page['lastmod'], $page['changefreq'], $page['priority']);
        }

        // CMS landing pages (SEO fields controlled per page)
        $slugMap = [
            'home' => '/',
            'about' => '/hakkimizda',
            'services' => '/hizmetler',
            'contact' => '/iletisim',
            'faq' => '/sss',
            'corporate' => '/kurumsal',
            'courier-apply' => '/kurye-basvuru',
        ];

        $landingPages = LandingPage::query()->where('is_active', true)->get();
        foreach ($landingPages as $page) {
            $meta = is_array($page->meta) ? $page->meta : [];
            if (array_key_exists('sitemap_is_active', $meta) && ! (bool) $meta['sitemap_is_active']) {
                continue;
            }

            $path = $slugMap[$page->slug] ?? null;
            if ($path === null) {
                continue;
            }

            $override = $entryOverrides->get($path);
            if ($override && ! $override->is_active) {
                continue;
            }

            $priority = (string) ($override?->priority ?? ($meta['sitemap_priority'] ?? '0.8'));
            $changefreq = (string) ($override?->changefreq ?? ($meta['sitemap_changefreq'] ?? 'weekly'));
            $lastmod = $override?->lastmod_at?->format('Y-m-d') ?? $page->updated_at?->format('Y-m-d');

            $xml .= $this->urlEntry($baseUrl.$path, $lastmod, $changefreq, $priority);
        }

        // Published legal pages
        $legalPages = LegalDocument::query()->where('is_published', true)->get();
        foreach ($legalPages as $legal) {
            $path = '/'.ltrim($legal->slug, '/');
            $override = $entryOverrides->get($path);
            if ($override && ! $override->is_active) {
                continue;
            }

            $xml .= $this->urlEntry(
                $baseUrl.$path,
                $override?->lastmod_at?->format('Y-m-d') ?? $legal->updated_at?->format('Y-m-d'),
                (string) ($override?->changefreq ?? 'yearly'),
                (string) ($override?->priority ?? '0.3')
            );
        }

        // İlçe sayfaları
        foreach ($locations as $districtSlug => $district) {
            $xml .= $this->urlEntry(
                $baseUrl . '/kurye/' . $districtSlug,
                $districtLastmod,
                'weekly',
                '0.8'
            );

            // Mahalle sayfaları
            foreach ($district['neighborhoods'] ?? [] as $nSlug => $nName) {
                $xml .= $this->urlEntry(
                    $baseUrl . '/kurye/' . $districtSlug . '/' . $nSlug,
                    $neighborhoodLastmod,
                    'monthly',
                    '0.6'
                );
            }
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /** @var array<string, true> Aynı URL ikinci kez yazılmaz (statik liste + CMS sayfası çakışması). */
    private array $seen = [];

    private function urlEntry(string $loc, ?string $lastmod, string $changefreq, string $priority): string
    {
        if (isset($this->seen[$loc])) {
            return '';
        }
        $this->seen[$loc] = true;

        return "  <url>\n" .
               "    <loc>{$loc}</loc>\n" .
               ($lastmod ? "    <lastmod>{$lastmod}</lastmod>\n" : '') .
               "    <changefreq>{$changefreq}</changefreq>\n" .
               "    <priority>{$priority}</priority>\n" .
               "  </url>\n";
    }

    /**
     * Dosyaların en son değişim tarihi. Deploy `git reset` yalnız değişen dosyalara dokunduğu için
     * bu tarih gerçek içerik değişimini izler; bilinmiyorsa null (lastmod yazılmaz).
     *
     * @param  array<int, string>  $files
     */
    private function fileLastmod(array $files): ?string
    {
        $times = array_filter(array_map(fn (string $f) => is_file($f) ? filemtime($f) : false, $files));

        return $times ? date('Y-m-d', max($times)) : null;
    }
}
