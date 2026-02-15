<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Dinamik XML Sitemap üreteci
     */
    public function index(): Response
    {
        $locations = config('istanbul-locations');
        $baseUrl = rtrim(config('app.url', 'https://simdigetir.com'), '/');

        // Statik sayfalar
        $staticPages = [
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

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $lastmod = date('Y-m-d');

        // Statik sayfalar
        foreach ($staticPages as $page) {
            $xml .= $this->urlEntry($baseUrl . $page['url'], $lastmod, $page['changefreq'], $page['priority']);
        }

        // İlçe sayfaları
        foreach ($locations as $districtSlug => $district) {
            $xml .= $this->urlEntry(
                $baseUrl . '/kurye/' . $districtSlug,
                $lastmod,
                'weekly',
                '0.8'
            );

            // Mahalle sayfaları
            foreach ($district['neighborhoods'] ?? [] as $nSlug => $nName) {
                $xml .= $this->urlEntry(
                    $baseUrl . '/kurye/' . $districtSlug . '/' . $nSlug,
                    $lastmod,
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

    private function urlEntry(string $loc, string $lastmod, string $changefreq, string $priority): string
    {
        return "  <url>\n" .
               "    <loc>{$loc}</loc>\n" .
               "    <lastmod>{$lastmod}</lastmod>\n" .
               "    <changefreq>{$changefreq}</changefreq>\n" .
               "    <priority>{$priority}</priority>\n" .
               "  </url>\n";
    }
}
