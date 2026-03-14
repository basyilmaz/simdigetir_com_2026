<?php

namespace Tests\Feature;

use App\Filament\Resources\FormSubmissionResource;
use App\Filament\Resources\LeadResource;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\PaymentTransactionResource;
use App\Models\FormSubmission;
use App\Models\User;
use App\Support\CsvExporter;
use Database\Seeders\RolePermissionSeeder;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class AdminPhase4ResourceEnhancementsTest extends TestCase
{
    public function test_resources_define_global_search_attributes(): void
    {
        $this->assertContains('name', LeadResource::getGloballySearchableAttributes());
        $this->assertContains('order_no', OrderResource::getGloballySearchableAttributes());
        $this->assertContains('provider_reference', PaymentTransactionResource::getGloballySearchableAttributes());
        $this->assertContains('status', FormSubmissionResource::getGloballySearchableAttributes());
    }

    public function test_form_submission_resource_is_authorized_for_admin_role(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->assertTrue($admin->can('viewAny', FormSubmission::class));
    }

    public function test_csv_exporter_streams_rows_with_headers(): void
    {
        $response = CsvExporter::download(
            filename: 'sample.csv',
            headers: ['ID', 'Ad'],
            rows: [
                [1, 'Ali'],
                [2, 'Ayşe'],
            ]
        );

        $this->assertInstanceOf(StreamedResponse::class, $response);

        ob_start();
        $response->sendContent();
        $content = (string) ob_get_clean();

        $this->assertStringContainsString('ID;Ad', $content);
        $this->assertStringContainsString('1;Ali', $content);
        $this->assertStringContainsString('2;Ayşe', $content);
    }
}
