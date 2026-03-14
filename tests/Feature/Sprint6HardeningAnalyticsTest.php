<?php

namespace Tests\Feature;

use App\Models\AdminAuditLog;
use App\Models\CorporateAccount;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Tests\TestCase;

class Sprint6HardeningAnalyticsTest extends TestCase
{
    public function test_quote_endpoint_is_rate_limited(): void
    {
        $lastStatus = 200;
        for ($i = 0; $i < 31; $i++) {
            $response = $this->postJson('/api/v1/quotes', [
                'base_amount' => 1000,
                'zone' => 'A',
                'hour' => 10,
                'currency' => 'TRY',
            ]);
            $lastStatus = $response->getStatusCode();
        }

        $this->assertSame(429, $lastStatus);
    }

    public function test_admin_audit_log_masks_sensitive_values(): void
    {
        $account = CorporateAccount::query()->create([
            'name' => 'Mask Test',
            'slug' => 'mask-test',
            'tax_no' => '1234567890',
            'invoice_email' => 'finance@example.test',
            'billing_address' => 'Istanbul',
            'status' => 'active',
        ]);

        $log = AdminAuditLog::query()
            ->where('auditable_type', CorporateAccount::class)
            ->where('auditable_id', (string) $account->id)
            ->latest('id')
            ->firstOrFail();

        $this->assertNotSame('1234567890', (string) ($log->new_values['tax_no'] ?? ''));
        $this->assertStringContainsString('*', (string) ($log->new_values['tax_no'] ?? ''));
        $this->assertNotSame('finance@example.test', (string) ($log->new_values['invoice_email'] ?? ''));
    }

    public function test_kpi_and_ops_endpoints_return_operational_data(): void
    {
        $order = Order::query()->create([
            'order_no' => 'ORD-S6-001',
            'state' => 'delivered',
            'payment_state' => 'succeeded',
            'pickup_address' => 'P',
            'dropoff_address' => 'D',
            'total_amount' => 1400,
            'currency' => 'TRY',
        ]);

        PaymentTransaction::query()->create([
            'order_id' => $order->id,
            'provider' => 'mockpay',
            'provider_reference' => 'PAY-S6-001',
            'amount' => 1400,
            'currency' => 'TRY',
            'status' => 'succeeded',
            'processed_at' => now(),
        ]);

        $kpi = $this->getJson('/api/v1/kpi/overview');
        $kpi->assertOk()->assertJsonPath('success', true);
        $this->assertNotNull($kpi->json('data.orders_total'));
        $this->assertNotNull($kpi->json('data.gross_revenue'));

        $health = $this->getJson('/api/v1/ops/health');
        $health->assertOk()->assertJsonPath('success', true);
        $this->assertSame('ok', $health->json('data.status'));
    }
}

