<?php

namespace Database\Seeders;

use App\Models\FormDefinition;
use App\Models\LegalDocument;
use Illuminate\Database\Seeder;

class Sprint2FoundationSeeder extends Seeder
{
    public function run(): void
    {
        FormDefinition::query()->updateOrCreate(
            ['key' => 'contact'],
            [
                'title' => 'Iletisim Formu',
                'description' => 'Landing iletisim sayfasi formu',
                'schema' => [
                    'fields' => [
                        ['name' => 'type', 'type' => 'string', 'required' => false, 'max' => 50],
                        ['name' => 'name', 'type' => 'string', 'required' => true, 'max' => 120],
                        ['name' => 'phone', 'type' => 'string', 'required' => true, 'max' => 30],
                        ['name' => 'email', 'type' => 'email', 'required' => false, 'max' => 120],
                        ['name' => 'subject', 'type' => 'string', 'required' => false, 'max' => 120],
                        ['name' => 'message', 'type' => 'string', 'required' => true, 'max' => 1500],
                    ],
                ],
                'target_type' => 'lead',
                'success_message' => 'Mesajiniz alindi. En kisa surede donus yapacagiz.',
                'rate_limit_per_minute' => 10,
                'is_active' => true,
            ]
        );

        FormDefinition::query()->updateOrCreate(
            ['key' => 'corporate-quote'],
            [
                'title' => 'Kurumsal Teklif Formu',
                'description' => 'Landing kurumsal teklif formu',
                'schema' => [
                    'fields' => [
                        ['name' => 'type', 'type' => 'string', 'required' => false, 'max' => 50],
                        ['name' => 'name', 'type' => 'string', 'required' => true, 'max' => 120],
                        ['name' => 'company_name', 'type' => 'string', 'required' => true, 'max' => 120],
                        ['name' => 'phone', 'type' => 'string', 'required' => true, 'max' => 30],
                        ['name' => 'email', 'type' => 'email', 'required' => false, 'max' => 120],
                        ['name' => 'message', 'type' => 'string', 'required' => false, 'max' => 1500],
                        ['name' => 'sector', 'type' => 'string', 'required' => false, 'max' => 120],
                    ],
                ],
                'target_type' => 'lead',
                'success_message' => 'Talebiniz alindi. En kisa surede iletisime gececegiz.',
                'rate_limit_per_minute' => 10,
                'is_active' => true,
            ]
        );

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
