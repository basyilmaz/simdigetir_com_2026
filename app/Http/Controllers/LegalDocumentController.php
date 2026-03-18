<?php

namespace App\Http\Controllers;

use App\Models\LegalDocument;
use DOMDocument;
use DOMXPath;
use Illuminate\View\View;
use Illuminate\Support\Str;

class LegalDocumentController extends Controller
{
    public function show(string $slug): View
    {
        $document = $this->resolveDocument($slug);

        abort_if(! $document, 404);

        ['html' => $contentHtml, 'toc' => $tableOfContents] = $this->buildContentNavigation((string) $document->content);

        return view('landing.legal-document', [
            'document' => $document,
            'contentHtml' => $contentHtml,
            'tableOfContents' => $tableOfContents,
        ]);
    }

    protected function resolveDocument(string $slug): ?LegalDocument
    {
        try {
            $document = LegalDocument::query()
                ->where('slug', $slug)
                ->where('is_published', true)
                ->first();
        } catch (\Throwable) {
            $document = null;
        }

        if ($document) {
            return $document;
        }

        return $this->fallbackDocumentForSlug($slug);
    }

    protected function fallbackDocumentForSlug(string $slug): ?LegalDocument
    {
        $fallbackDocuments = [
            'kvkk' => [
                'title' => 'KVKK Aydinlatma Metni',
                'summary' => 'Yasal icerik gecici olarak statik fallback modunda gosteriliyor.',
                'content' => '<h2>Veri sorumlusu</h2><p>SimdiGetir, hizmet surecinde paylastiginiz temel kisisel verileri ilgili mevzuata uygun olarak isler.</p><h2>Isleme amaci</h2><p>Verileriniz siparis, destek, teklif ve iletisim sureclerini yurutmek icin kullanilir.</p><h2>Basvuru ve iletisim</h2><p>Haklariniz ve talepleriniz icin iletisim kanallarimiz uzerinden bize ulasabilirsiniz.</p>',
            ],
            'cerez-politikasi' => [
                'title' => 'Cerez Politikasi',
                'summary' => 'Cerez kullanim esaslari ve tarayici kontrol secenekleri.',
                'content' => '<h2>Cerez kullanimi</h2><p>Web sitemiz deneyim, guvenlik ve performans amaclariyla sinirli cerezler kullanabilir.</p><h2>Cerez tercihleri</h2><p>Tarayici ayarlarinizdan cerez tercihlerinizi yonetebilir veya cerezleri silebilirsiniz.</p><h2>Iletisim</h2><p>Cerez kullanimi ile ilgili sorulariniz icin iletisim sayfamiz uzerinden bize ulasabilirsiniz.</p>',
            ],
            'kullanim-kosullari' => [
                'title' => 'Kullanim Kosullari',
                'summary' => 'Web sitesi ve hizmet kullanimina dair temel kosullar.',
                'content' => '<h2>Hizmet kullanimi</h2><p>SimdiGetir yuzeylerini kullanirken guncel bilgilerle islem yapmaniz ve yasal kurallara uymaniz beklenir.</p><h2>Sorumluluk sinirlari</h2><p>Operasyonel durumlar, fiyatlama ve hizmet kapsami ilgili siparis akisi ve teyit adimlarina tabidir.</p><h2>Destek ve guncellemeler</h2><p>Kosullar gerekli oldugunda guncellenebilir; guncel metin yayina alindiginda bu sayfada gorunur.</p>',
            ],
        ];

        if (! isset($fallbackDocuments[$slug])) {
            return null;
        }

        return LegalDocument::make([
            'slug' => $slug,
            'title' => $fallbackDocuments[$slug]['title'],
            'summary' => $fallbackDocuments[$slug]['summary'],
            'content' => $fallbackDocuments[$slug]['content'],
            'version' => 1,
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    /**
     * @return array{html: string, toc: array<int, array{level: string, id: string, label: string}>}
     */
    protected function buildContentNavigation(string $html): array
    {
        $html = trim($html);
        if ($html === '' || ! class_exists(DOMDocument::class)) {
            return [
                'html' => $html,
                'toc' => [],
            ];
        }

        $previousState = libxml_use_internal_errors(true);

        try {
            $document = new DOMDocument('1.0', 'UTF-8');
            $loaded = $document->loadHTML(
                '<?xml encoding="utf-8" ?><div id="legal-content-root">'.$html.'</div>',
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
            );

            if (! $loaded) {
                return [
                    'html' => $html,
                    'toc' => [],
                ];
            }

            $xpath = new DOMXPath($document);
            $rootNode = $xpath->query('//*[@id="legal-content-root"]')->item(0);

            if (! $rootNode) {
                return [
                    'html' => $html,
                    'toc' => [],
                ];
            }

            $usedIds = [];
            $tableOfContents = [];
            $headingNodes = $xpath->query('.//h2 | .//h3', $rootNode);

            if ($headingNodes === false) {
                return [
                    'html' => $html,
                    'toc' => [],
                ];
            }

            foreach ($headingNodes as $index => $headingNode) {
                $label = trim((string) $headingNode->textContent);
                if ($label === '') {
                    continue;
                }

                $baseId = Str::slug($label);
                if ($baseId === '') {
                    $baseId = 'bolum-'.($index + 1);
                }

                $resolvedId = $baseId;
                $suffix = 2;

                while (isset($usedIds[$resolvedId])) {
                    $resolvedId = $baseId.'-'.$suffix;
                    $suffix++;
                }

                $usedIds[$resolvedId] = true;
                $headingNode->setAttribute('id', $resolvedId);

                $tableOfContents[] = [
                    'level' => strtolower($headingNode->nodeName),
                    'id' => $resolvedId,
                    'label' => $label,
                ];
            }

            $renderedHtml = '';
            foreach ($rootNode->childNodes as $childNode) {
                $renderedHtml .= (string) $document->saveHTML($childNode);
            }

            return [
                'html' => $renderedHtml !== '' ? $renderedHtml : $html,
                'toc' => $tableOfContents,
            ];
        } catch (\Throwable) {
            return [
                'html' => $html,
                'toc' => [],
            ];
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousState);
        }
    }
}
