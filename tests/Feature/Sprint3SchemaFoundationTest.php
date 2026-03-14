<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class Sprint3SchemaFoundationTest extends TestCase
{
    public function test_sprint3_core_tables_and_columns_exist(): void
    {
        $this->assertTrue(Schema::hasTable('orders'));
        $this->assertTrue(Schema::hasColumns('orders', [
            'order_no',
            'state',
            'payment_state',
            'total_amount',
            'price_breakdown',
        ]));

        $this->assertTrue(Schema::hasTable('order_packages'));
        $this->assertTrue(Schema::hasColumns('order_packages', [
            'order_id',
            'package_type',
            'quantity',
        ]));

        $this->assertTrue(Schema::hasTable('order_state_logs'));
        $this->assertTrue(Schema::hasColumns('order_state_logs', [
            'order_id',
            'from_state',
            'to_state',
            'reason',
        ]));

        $this->assertTrue(Schema::hasTable('pricing_rules'));
        $this->assertTrue(Schema::hasColumns('pricing_rules', [
            'rule_type',
            'priority',
            'conditions',
            'effect',
        ]));

        $this->assertTrue(Schema::hasTable('pricing_quotes'));
        $this->assertTrue(Schema::hasColumns('pricing_quotes', [
            'quote_no',
            'request_snapshot',
            'resolved_rules',
            'total_amount',
            'expires_at',
        ]));

        $this->assertTrue(Schema::hasTable('payment_transactions'));
        $this->assertTrue(Schema::hasColumns('payment_transactions', [
            'provider',
            'provider_reference',
            'amount',
            'status',
            'callback_payload',
        ]));
    }
}

