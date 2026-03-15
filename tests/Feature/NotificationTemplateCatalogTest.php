<?php

namespace Tests\Feature;

use App\Domain\Notifications\Support\NotificationTemplateCatalog;
use Tests\TestCase;

class NotificationTemplateCatalogTest extends TestCase
{
    public function test_bank_transfer_template_catalog_exposes_instruction_placeholder(): void
    {
        $definition = NotificationTemplateCatalog::definition('orders.payment_pending_bank_transfer');

        $this->assertNotNull($definition);
        $this->assertArrayHasKey('bank_transfer_instruction', $definition['variables']);
        $this->assertStringContainsString(
            '{bank_transfer_instruction}',
            (string) $definition['default_body']
        );
    }
}
