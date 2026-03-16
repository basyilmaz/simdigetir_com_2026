<?php

namespace Tests\Feature;

use App\Filament\Resources\LeadResource;
use App\Filament\Widgets\StatsOverview;
use App\Models\FormDefinition;
use Modules\Leads\Models\Lead;
use Tests\TestCase;

class LiveBackendAdminAuditP1LeadAndDashboardTest extends TestCase
{
    public function test_public_lead_api_persists_company_name_for_corporate_quotes(): void
    {
        $response = $this->postJson('/api/leads', [
            'type' => 'corporate_quote',
            'name' => 'Kurumsal Test',
            'company_name' => 'Castintech Lojistik',
            'phone' => '05551112233',
            'email' => 'kurumsal@example.com',
            'page_url' => 'https://simdigetir.test/kurumsal',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('leads', [
            'type' => 'corporate_quote',
            'name' => 'Kurumsal Test',
            'company_name' => 'Castintech Lojistik',
        ]);
    }

    public function test_form_submission_target_lead_persists_company_name_when_schema_allows_it(): void
    {
        $definition = FormDefinition::query()->create([
            'key' => 'corporate-contact',
            'title' => 'Corporate Contact',
            'schema' => [
                'fields' => [
                    ['name' => 'type', 'type' => 'string', 'required' => true, 'max' => 40],
                    ['name' => 'name', 'type' => 'string', 'required' => true, 'max' => 120],
                    ['name' => 'company_name', 'type' => 'string', 'required' => false, 'max' => 255],
                    ['name' => 'phone', 'type' => 'string', 'required' => true, 'max' => 30],
                ],
            ],
            'target_type' => 'lead',
            'rate_limit_per_minute' => 10,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/forms/corporate-contact/submit', [
            'type' => 'corporate_quote',
            'name' => 'Corporate Lead',
            'company_name' => 'SimdiGetir Kurumsal',
            'phone' => '05550001122',
            'page_url' => 'https://simdigetir.test/kurumsal',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('form_submissions', [
            'form_definition_id' => $definition->id,
            'status' => 'forwarded_to_leads',
        ]);
        $this->assertDatabaseHas('leads', [
            'type' => 'corporate_quote',
            'name' => 'Corporate Lead',
            'company_name' => 'SimdiGetir Kurumsal',
        ]);
    }

    public function test_form_submission_target_lead_keeps_company_name_even_when_schema_omits_company_field(): void
    {
        FormDefinition::query()->create([
            'key' => 'corporate-contact-minimal',
            'title' => 'Corporate Contact Minimal',
            'schema' => [
                'fields' => [
                    ['name' => 'type', 'type' => 'string', 'required' => true, 'max' => 40],
                    ['name' => 'name', 'type' => 'string', 'required' => true, 'max' => 120],
                    ['name' => 'phone', 'type' => 'string', 'required' => true, 'max' => 30],
                ],
            ],
            'target_type' => 'lead',
            'rate_limit_per_minute' => 10,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/forms/corporate-contact-minimal/submit', [
            'type' => 'corporate_quote',
            'name' => 'Corporate Lead Minimal',
            'company_name' => 'Castintech',
            'phone' => '05550009988',
            'page_url' => 'https://simdigetir.test/kurumsal',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('leads', [
            'type' => 'corporate_quote',
            'name' => 'Corporate Lead Minimal',
            'company_name' => 'Castintech',
        ]);
    }

    public function test_lead_resource_company_display_is_operationally_clear(): void
    {
        $corporateLead = new Lead([
            'type' => 'corporate_quote',
            'name' => 'Corporate Lead',
            'company_name' => null,
        ]);
        $contactLead = new Lead([
            'type' => 'contact',
            'name' => 'Contact Lead',
            'company_name' => null,
        ]);
        $namedCorporateLead = new Lead([
            'type' => 'corporate_quote',
            'name' => 'Named Corporate Lead',
            'company_name' => 'Castintech',
        ]);

        $this->assertSame('Eksik', LeadResource::resolveCompanyDisplay($corporateLead));
        $this->assertSame('-', LeadResource::resolveCompanyDisplay($contactLead));
        $this->assertSame('Castintech', LeadResource::resolveCompanyDisplay($namedCorporateLead));
        $this->assertSame(
            [null, 'corporate_quote', 'Named Corporate Lead', 'Castintech', null, null, null, null],
            LeadResource::exportRow($namedCorporateLead)
        );
    }

    public function test_stats_overview_clarifies_today_vs_new_lead_backlog_semantics(): void
    {
        Lead::query()->insert([
            [
                'type' => 'contact',
                'name' => 'Today Lead',
                'phone' => '05550000001',
                'status' => 'won',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'contact',
                'name' => 'New Lead 1',
                'phone' => '055512341',
                'status' => 'new',
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
            [
                'type' => 'contact',
                'name' => 'New Lead 2',
                'phone' => '055512342',
                'status' => 'new',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'type' => 'contact',
                'name' => 'New Lead 3',
                'phone' => '055512343',
                'status' => 'new',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
        ]);

        $widget = new class extends StatsOverview
        {
            public function exposeStats(): array
            {
                return $this->getStats();
            }
        };

        $stats = collect($widget->exposeStats());
        $todayLeadStat = $stats->first(fn ($stat): bool => (string) $stat->getLabel() === 'Bugün Gelen Talepler');

        $this->assertNotNull($todayLeadStat);
        $this->assertSame('1', (string) $todayLeadStat->getValue());
        $this->assertStringContainsString('Bekleyen yeni talepler: 3', (string) $todayLeadStat->getDescription());
    }
}
