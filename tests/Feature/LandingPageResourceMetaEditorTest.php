<?php

namespace Tests\Feature;

use Illuminate\Validation\ValidationException;
use Modules\Landing\Filament\Resources\LandingPageResource;
use Tests\TestCase;

class LandingPageResourceMetaEditorTest extends TestCase
{
    public function test_normalize_meta_editor_fields_merges_editor_inputs_into_meta(): void
    {
        $data = [
            'meta' => [
                'meta_title' => 'Sample Title',
                'custom_key' => 'custom-value',
            ],
            'structured_data_json' => '{"@context":"https://schema.org","@type":"Organization","name":"Schema Name"}',
            'service_schema_items_editor' => [
                [
                    'name' => 'Service A',
                    'description' => 'Desc A',
                    'serviceType' => 'Type A',
                    'url' => 'https://example.com/a',
                ],
                [
                    'name' => '   ',
                    'description' => 'Should be ignored',
                ],
            ],
        ];

        $normalized = LandingPageResource::normalizeMetaEditorFields($data);

        $this->assertArrayNotHasKey('structured_data_json', $normalized);
        $this->assertArrayNotHasKey('service_schema_items_editor', $normalized);
        $this->assertSame('custom-value', $normalized['meta']['custom_key']);
        $this->assertSame('Schema Name', $normalized['meta']['structured_data']['name']);
        $this->assertCount(1, $normalized['meta']['service_schema_items']);
        $this->assertSame('Service A', $normalized['meta']['service_schema_items'][0]['name']);
    }

    public function test_normalize_meta_editor_fields_throws_validation_exception_for_invalid_json(): void
    {
        $this->expectException(ValidationException::class);

        LandingPageResource::normalizeMetaEditorFields([
            'meta' => [],
            'structured_data_json' => '{invalid-json',
            'service_schema_items_editor' => [],
        ]);
    }
}

