<?php

namespace Database\Seeders;

use App\Models\FormDefinition;
use App\Models\LegalDocument;
use App\Support\FormDefinitionDefaults;
use Illuminate\Database\Seeder;

class Sprint2FoundationSeeder extends Seeder
{
    public function run(): void
    {
        $contactDefaults = FormDefinitionDefaults::byKey('contact');
        if (is_array($contactDefaults)) {
            FormDefinition::query()->updateOrCreate(['key' => 'contact'], $contactDefaults);
        }

        $corporateQuoteDefaults = FormDefinitionDefaults::byKey('corporate-quote');
        if (is_array($corporateQuoteDefaults)) {
            FormDefinition::query()->updateOrCreate(['key' => 'corporate-quote'], $corporateQuoteDefaults);
        }

        LegalDocument::query()->updateOrCreate(
            ['slug' => 'cerez-politikasi'],
            [
                'title' => 'Cerez Politikasi',
                'summary' => 'Cerez kullanimina iliskin bilgilendirme metni.',
                'content' => '<p>Bu sayfayi admin panelinden guncelleyebilirsiniz.</p>',
                'is_published' => true,
                'published_at' => now(),
            ]
        );

        LegalDocument::query()->updateOrCreate(
            ['slug' => 'kullanim-kosullari'],
            [
                'title' => 'Kullanim Kosullari',
                'summary' => 'Platform kullanim kosullarina iliskin bilgilendirme metni.',
                'content' => '<p>Bu sayfayi admin panelinden guncelleyebilirsiniz.</p>',
                'is_published' => true,
                'published_at' => now(),
            ]
        );
    }
}
