<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Settings\Models\Setting;

class SettingsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Marketing
            [
                'key' => 'marketing.gtm_head',
                'value' => '',
                'group' => 'marketing',
            ],
            [
                'key' => 'marketing.gtm_body',
                'value' => '',
                'group' => 'marketing',
            ],
            [
                'key' => 'marketing.ga4_id',
                'value' => '',
                'group' => 'marketing',
            ],
            
            // Contact
            [
                'key' => 'contact.phone',
                'value' => '+90 212 XXX XX XX',
                'group' => 'contact',
            ],
            [
                'key' => 'contact.whatsapp',
                'value' => '905321234567',
                'group' => 'contact',
            ],
            [
                'key' => 'contact.email',
                'value' => 'info@simdigetir.com',
                'group' => 'contact',
            ],
            [
                'key' => 'contact.address',
                'value' => 'İstanbul, Türkiye',
                'group' => 'contact',
            ],
            
            // Social
            [
                'key' => 'social.instagram',
                'value' => '',
                'group' => 'social',
            ],
            [
                'key' => 'social.linkedin',
                'value' => '',
                'group' => 'social',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
