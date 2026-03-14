<?php

namespace Tests\Feature;

use App\Models\Courier;
use App\Models\Order;
use App\Models\OrderAssignment;
use App\Models\PaymentTransaction;
use App\Models\User;
use Tests\TestCase;

class Sprint5FinanceSupportCorporateTest extends TestCase
{
    public function test_settlement_run_creates_wallet_entries_and_batch(): void
    {
        $courier = Courier::query()->create([
            'full_name' => 'Courier Settlement',
            'phone' => '05320000101',
            'status' => 'approved',
        ]);

        $order = Order::query()->create([
            'order_no' => 'ORD-S5-001',
            'state' => 'delivered',
            'payment_state' => 'succeeded',
            'pickup_address' => 'P',
            'dropoff_address' => 'D',
            'total_amount' => 1000,
            'currency' => 'TRY',
        ]);

        OrderAssignment::query()->create([
            'order_id' => $order->id,
            'courier_id' => $courier->id,
            'status' => 'completed',
            'assignment_type' => 'auto',
            'assigned_at' => now(),
            'accepted_at' => now(),
            'completed_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/finance/settlements/run', [
            'commission_rate_bps' => 1000,
        ]);

        $response->assertStatus(201)->assertJsonPath('success', true);
        $this->assertDatabaseHas('settlement_batches', [
            'id' => $response->json('data.id'),
            'net_amount' => 900,
        ]);
        $this->assertDatabaseHas('courier_wallet_entries', [
            'courier_id' => $courier->id,
            'order_id' => $order->id,
            'entry_type' => 'earning',
            'amount' => 1000,
        ]);
        $this->assertDatabaseHas('courier_wallet_entries', [
            'courier_id' => $courier->id,
            'order_id' => $order->id,
            'entry_type' => 'commission',
            'amount' => -100,
        ]);

        $wallet = $this->getJson('/api/v1/finance/couriers/'.$courier->id.'/wallet');
        $wallet->assertOk()->assertJsonPath('success', true)->assertJsonPath('data.balance', 900);
        $this->assertSame(2, (int) $wallet->json('data.entry_count'));
    }

    public function test_payment_reconcile_and_refund_flow(): void
    {
        $order = Order::query()->create([
            'order_no' => 'ORD-S5-002',
            'state' => 'paid',
            'payment_state' => 'succeeded',
            'pickup_address' => 'P',
            'dropoff_address' => 'D',
            'total_amount' => 2000,
            'currency' => 'TRY',
        ]);

        $tx = PaymentTransaction::query()->create([
            'order_id' => $order->id,
            'provider' => 'mockpay',
            'provider_reference' => 'PAY-S5-REFUND',
            'amount' => 2000,
            'currency' => 'TRY',
            'status' => 'succeeded',
            'processed_at' => now(),
        ]);

        $reconcile = $this->postJson('/api/v1/finance/payments/reconcile', [
            'payment_transaction_id' => $tx->id,
            'provider_status' => 'succeeded',
        ]);
        $reconcile->assertStatus(201)->assertJsonPath('success', true)->assertJsonPath('data.is_match', true);

        $refund = $this->postJson('/api/v1/finance/payments/'.$tx->id.'/refund', [
            'amount' => 500,
            'reason' => 'Customer complaint',
        ]);
        $refund->assertStatus(201)->assertJsonPath('success', true);
        $this->assertDatabaseHas('payment_refunds', [
            'payment_transaction_id' => $tx->id,
            'amount' => 500,
            'status' => 'succeeded',
        ]);
    }

    public function test_notification_templates_and_dispatch(): void
    {
        $this->postJson('/api/v1/notifications/templates/upsert', [
            'event_key' => 'order_delivered',
            'channel' => 'sms',
            'body' => 'Siparisiniz teslim edildi. No: {order_no}',
            'variables' => ['order_no'],
        ])->assertStatus(201);

        $this->postJson('/api/v1/notifications/templates/upsert', [
            'event_key' => 'order_delivered',
            'channel' => 'email',
            'subject' => 'Teslimat Tamamlandi',
            'body' => 'Merhaba, siparis {order_no} teslim edildi.',
            'variables' => ['order_no'],
        ])->assertStatus(201);

        $dispatch = $this->postJson('/api/v1/notifications/dispatch', [
            'event_key' => 'order_delivered',
            'targets' => [
                ['channel' => 'sms', 'target' => '05320000999'],
                ['channel' => 'email', 'target' => 'demo@example.com'],
            ],
            'context' => ['order_no' => 'ORD-S5-003'],
        ]);

        $dispatch->assertStatus(201)->assertJsonPath('success', true)->assertJsonPath('data.count', 2);
        $this->assertDatabaseHas('notification_dispatches', [
            'event_key' => 'order_delivered',
            'channel' => 'sms',
            'status' => 'sent',
        ]);
        $this->assertDatabaseHas('notification_dispatches', [
            'event_key' => 'order_delivered',
            'channel' => 'email',
            'status' => 'sent',
        ]);
    }

    public function test_corporate_account_and_support_ticket_flow(): void
    {
        $owner = User::factory()->create();
        $courier = Courier::query()->create([
            'full_name' => 'Courier Support',
            'phone' => '05320000111',
            'status' => 'approved',
        ]);
        $order = Order::query()->create([
            'order_no' => 'ORD-S5-004',
            'state' => 'delivered',
            'payment_state' => 'succeeded',
            'pickup_address' => 'P',
            'dropoff_address' => 'D',
            'total_amount' => 1800,
            'currency' => 'TRY',
            'customer_id' => $owner->id,
        ]);

        $account = $this->postJson('/api/v1/corporate/accounts', [
            'name' => 'Acme Logistics',
            'owner_user_id' => $owner->id,
            'invoice_email' => 'invoice@acme.test',
            'addresses' => [
                ['label' => 'HQ', 'address' => 'Istanbul', 'is_default' => true],
            ],
        ]);
        $account->assertStatus(201)->assertJsonPath('success', true);
        $this->assertDatabaseHas('corporate_accounts', ['name' => 'Acme Logistics']);
        $this->assertDatabaseHas('corporate_account_users', [
            'corporate_account_id' => $account->json('data.id'),
            'user_id' => $owner->id,
            'role' => 'owner',
        ]);

        $ticket = $this->postJson('/api/v1/support/tickets', [
            'order_id' => $order->id,
            'customer_id' => $owner->id,
            'courier_id' => $courier->id,
            'subject' => 'Gec teslimat bildirimi',
            'description' => 'Teslimat beklenenden gec geldi.',
        ]);
        $ticket->assertStatus(201)->assertJsonPath('success', true);

        $msg = $this->postJson('/api/v1/support/tickets/'.$ticket->json('data.id').'/messages', [
            'author_type' => 'customer',
            'author_id' => $owner->id,
            'message' => 'Detaylari paylastim.',
        ]);
        $msg->assertStatus(201)->assertJsonPath('success', true);

        $this->assertDatabaseHas('support_tickets', [
            'id' => $ticket->json('data.id'),
            'order_id' => $order->id,
            'status' => 'open',
        ]);
        $this->assertDatabaseHas('support_ticket_messages', [
            'support_ticket_id' => $ticket->json('data.id'),
            'author_type' => 'customer',
        ]);
    }
}
