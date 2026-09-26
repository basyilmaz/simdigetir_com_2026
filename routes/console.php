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
