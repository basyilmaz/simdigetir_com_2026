<?php

namespace Tests\Feature;

use App\Filament\Resources\FormSubmissionResource;
use App\Models\FormDefinition;
use App\Models\FormSubmission;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FormSubmissionWorkflowEnhancementsTest extends TestCase
{
    public function test_form_submissions_table_contains_triage_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('form_submissions', [
            'assigned_to',
            'follow_up_at',
            'internal_note',
        ]));
    }

    public function test_form_submission_can_be_assigned_to_user_with_follow_up_and_note(): void
    {
        $owner = User::factory()->create();
        $form = FormDefinition::query()->create([
            'key' => 'workflow-form',
            'title' => 'Workflow Form',
        ]);

        $submission = FormSubmission::query()->create([
            'form_definition_id' => $form->id,
            'payload' => ['name' => 'Test User'],
            'status' => 'received',
            'assigned_to' => $owner->id,
            'follow_up_at' => now()->addHour(),
            'internal_note' => 'Öncelikli geri dönüş yapılacak.',
        ]);

        $submission->refresh();

        $this->assertSame($owner->id, $submission->assigned_to);
        $this->assertSame($owner->id, $submission->assignedUser?->id);
        $this->assertNotNull($submission->follow_up_at);
        $this->assertSame('Öncelikli geri dönüş yapılacak.', $submission->internal_note);
    }

    public function test_form_submission_resource_global_search_includes_internal_note(): void
    {
        $this->assertContains('internal_note', FormSubmissionResource::getGloballySearchableAttributes());
    }
}
