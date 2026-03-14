<?php

namespace Tests\Feature;

use App\Models\AdminAuditLog;
use Modules\Landing\Models\LandingPage;
use Modules\Landing\Models\LandingPageSection;
use Modules\Settings\Models\Setting;
use Tests\TestCase;

class AdminAuditLogTest extends TestCase
{
    public function test_it_logs_create_update_delete_for_landing_models(): void
    {
        $page = LandingPage::create([
            'slug' => 'home',
            'title' => 'Home',
            'is_active' => true,
        ]);

        $section = LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'hero',
            'type' => 'hero',
            'sort_order' => 1,
            'is_active' => true,
            'payload' => ['hero_badge_text' => 'A'],
        ]);

        $section->update([
            'payload' => ['hero_badge_text' => 'B'],
        ]);

        $section->delete();

        $logs = AdminAuditLog::query()
            ->where('auditable_type', LandingPageSection::class)
            ->where('auditable_id', (string) $section->id)
            ->pluck('event')
            ->all();

        $this->assertContains('created', $logs);
        $this->assertContains('updated', $logs);
        $this->assertContains('deleted', $logs);
    }

    public function test_it_logs_settings_mutations(): void
    {
        $setting = Setting::setValue('audit.test.key', 'one', 'test');
        Setting::setValue('audit.test.key', 'two', 'test');
        $setting->delete();

        $logs = AdminAuditLog::query()
            ->where('auditable_type', Setting::class)
            ->where('auditable_id', (string) $setting->id)
            ->pluck('event')
            ->all();

        $this->assertContains('created', $logs);
        $this->assertContains('updated', $logs);
        $this->assertContains('deleted', $logs);
    }
}

