<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Onaylı yasal metni repodaki dosyadan yayınlar (resources/legal/<slug>.html).
// Sürüm geçmişi LegalDocumentObserver tarafından tutulur; içerik aynıysa sürüm artmaz.
Artisan::command('legal:publish {slug}', function (string $slug) {
    $documents = [
        'cerez-politikasi' => [
            'title' => 'Çerez Politikası',
            'summary' => "simdigetir.com'da kullanılan çerezler, amaçları ve tercihlerinizi nasıl değiştirebileceğiniz.",
        ],
    ];

    if (! isset($documents[$slug])) {
        $this->error("Tanımsız belge: {$slug}");

        return 1;
    }

    $path = resource_path("legal/{$slug}.html");
    if (! is_file($path)) {
        $this->error("Dosya yok: {$path}");

        return 1;
    }

    $content = trim(preg_replace('/<!--.*?-->/s', '', file_get_contents($path)));

    $document = \App\Models\LegalDocument::query()->updateOrCreate(
        ['slug' => $slug],
        $documents[$slug] + ['content' => $content, 'is_published' => true],
    );

    $this->info("{$slug} yayında — sürüm {$document->version}");

    return 0;
})->purpose('Onaylı yasal metni resources/legal dosyasından yayınlar');

// IndexNow (Bing, Yandex): sitemap'teki URL'leri tek istekte bildirir. Anahtar dosyası public/<anahtar>.txt.
// Değişmeyen URL'yi tekrar bildirmek önerilmediği için deploy'a bağlı değil; büyük içerik değişikliğinden sonra elle çalıştırılır.
Artisan::command('seo:indexnow {--dry-run : Yalnız listeyi göster, gönderme}', function () {
    $key = '7e5c755ba185174c6edf63cae475906a';
    $host = 'simdigetir.com';

    if (! is_file(public_path("{$key}.txt"))) {
        $this->error("Anahtar dosyası yok: public/{$key}.txt");

        return 1;
    }

    $xml = app(\App\Http\Controllers\SitemapController::class)->index()->getContent();
    preg_match_all('#<loc>([^<]+)</loc>#', $xml, $m);
    $urls = array_values(array_unique(array_map(
        fn (string $u) => preg_replace('#^https?://[^/]+#', "https://{$host}", $u),
        $m[1]
    )));

    $this->info(count($urls).' URL');
    if ($this->option('dry-run')) {
        return 0;
    }

    $response = \Illuminate\Support\Facades\Http::timeout(30)->post('https://api.indexnow.org/indexnow', [
        'host' => $host,
        'key' => $key,
        'keyLocation' => "https://{$host}/{$key}.txt",
        'urlList' => $urls,
    ]);

    $this->info('IndexNow yanıtı: HTTP '.$response->status());

    return in_array($response->status(), [200, 202], true) ? 0 : 1;
})->purpose('Sitemap URL\'lerini IndexNow ile Bing/Yandex\'e bildirir');
