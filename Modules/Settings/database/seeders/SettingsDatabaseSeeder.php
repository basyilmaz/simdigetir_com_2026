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
            ['key' => 'marketing.gtm_head', 'value' => '', 'group' => 'marketing'],
            ['key' => 'marketing.gtm_body', 'value' => '', 'group' => 'marketing'],
            ['key' => 'marketing.ga4_id', 'value' => '', 'group' => 'marketing'],
            ['key' => 'marketing.meta_pixel_id', 'value' => '1657531168735846', 'group' => 'marketing'],

            // Contact
            ['key' => 'contact.phone', 'value' => '+90 551 356 72 92', 'group' => 'contact'],
            ['key' => 'contact.whatsapp', 'value' => '905513567292', 'group' => 'contact'],
            ['key' => 'contact.email', 'value' => 'info@simdigetir.com', 'group' => 'contact'],
            ['key' => 'contact.address', 'value' => 'Istanbul, Turkiye', 'group' => 'contact'],

            // Business
            ['key' => 'business.hours_label', 'value' => '7/24 Aktif Hizmet', 'group' => 'business'],
            ['key' => 'business.hours_weekdays', 'value' => 'Pzt-Cum 00:00 - 23:59', 'group' => 'business'],
            ['key' => 'business.hours_weekend', 'value' => 'Cts-Paz 00:00 - 23:59', 'group' => 'business'],

            // Brand
            ['key' => 'brand.logo_url', 'value' => '', 'group' => 'brand'],
            ['key' => 'brand.logo_alt', 'value' => 'SimdiGetir', 'group' => 'brand'],
            ['key' => 'brand.logo_height_sm', 'value' => 28, 'group' => 'brand'],
            ['key' => 'brand.logo_height_md', 'value' => 36, 'group' => 'brand'],
            ['key' => 'brand.logo_height_lg', 'value' => 44, 'group' => 'brand'],

            // Social
            ['key' => 'social.facebook', 'value' => '', 'group' => 'social'],
            ['key' => 'social.instagram', 'value' => '', 'group' => 'social'],
            ['key' => 'social.twitter', 'value' => '', 'group' => 'social'],
            ['key' => 'social.linkedin', 'value' => '', 'group' => 'social'],
            ['key' => 'social.youtube', 'value' => '', 'group' => 'social'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
