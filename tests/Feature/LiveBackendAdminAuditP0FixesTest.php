<?php

namespace Tests\Feature;

use App\Filament\Resources\LeadResource\Pages\ViewLead;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\Pages\ViewOrder;
use App\Filament\Resources\PaymentTransactionResource;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\User;
use Carbon\Carbon;
use Modules\Leads\Models\Lead;
use Tests\TestCase;

class LiveBackendAdminAuditP0FixesTest extends TestCase
{
    public function test_order_resource_formats_minor_unit_amounts_and_export_rows(): void
    {
        $order = new Order([
            'order_no' => 'ORD-100',
            'state' => 'paid',
            'payment_state' => 'succeeded',
            'total_amount' => 15000,
            'currency' => 'TRY',
        ]);
        $order->id = 10;
        $order->created_at = Carbon::parse('2026-03-15 09:30:00');

        $this->assertSame('150,00 ₺', OrderResource::formatAmount($order->total_amount, $order->currency));
        $this->assertSame(
            [10, 'ORD-100', 'paid', 'succeeded', '150,00 ₺', 'TRY', '2026-03-15 09:30:00'],
            OrderResource::exportRow($order)
        );
    }

    public function test_order_resource_resolves_customer_name_and_contact_with_legacy_fallbacks(): void
    {
        $customer = User::factory()->make([
            'name' => 'Ayse Admin',
            'email' => 'ayse@example.com',
            'phone' => '905551112233',
        ]);

        $orderWithCustomer = new Order([
            'pickup_name' => 'Legacy Sender',
            'pickup_phone' => '05550000000',
        ]);
        $orderWithCustomer->setRelation('customer', $customer);

        $legacyOrder = new Order([
            'pickup_name' => 'Legacy Sender',
            'pickup_phone' => '05550000000',
        ]);

        $this->assertSame('Ayse Admin', OrderResource::resolveCustomerName($orderWithCustomer));
        $this->assertSame('ayse@example.com', OrderResource::resolveCustomerContact($orderWithCustomer));
        $this->assertSame('Legacy Sender', OrderResource::resolveCustomerName($legacyOrder));
        $this->assertSame('05550000000', OrderResource::resolveCustomerContact($legacyOrder));
    }

    public function test_payment_transaction_resource_formats_amounts_and_resolves_pending_date_summary(): void
    {
        $payment = new PaymentTransaction([
            'provider' => 'mockpay',
            'provider_reference' => 'mock-7',
            'status' => 'pending',
            'amount' => 21990,
            'currency' => 'TRY',
            'order_id' => 12,
        ]);
        $payment->id = 7;
        $payment->created_at = Carbon::parse('2026-03-15 10:10:00');

        $this->assertSame('219,90 ₺', PaymentTransactionResource::formatAmount($payment->amount, $payment->currency));
        $this->assertSame('Bekliyor - Kayıt: 15.03.2026 10:10', PaymentTransactionResource::resolveProcessedAtSummary($payment));
        $this->assertSame('Henüz işlenmedi', PaymentTransactionResource::resolveProcessedAtMeta($payment));
        $this->assertSame(
            [7, 'mockpay', 'mock-7', 'pending', '219,90 ₺', 'TRY', 12, '2026-03-15 10:10:00'],
            PaymentTransactionResource::exportRow($payment)
        );
    }

    public function test_payment_transaction_resource_prefers_processed_at_when_available(): void
    {
        $payment = new PaymentTransaction([
            'status' => 'succeeded',
            'amount' => 10000,
            'currency' => 'TRY',
        ]);
        $payment->created_at = Carbon::parse('2026-03-15 09:00:00');
        $payment->processed_at = Carbon::parse('2026-03-15 09:05:00');

        $this->assertSame('İşlendi: 15.03.2026 09:05', PaymentTransactionResource::resolveProcessedAtSummary($payment));
        $this->assertNull(PaymentTransactionResource::resolveProcessedAtMeta($payment));
    }

    public function test_lead_timeline_display_items_are_sanitized_and_rendered_without_blank_rows(): void
    {
        $lead = new Lead([
            'type' => 'contact',
            'name' => 'Backend Audit Lead',
            'phone' => '05551112233',
            'status' => 'contacted',
            'source' => 'google',
            'campaign' => 'istanbul',
            'notes' => 'Musteri ile gorusuldu',
        ]);
        $lead->created_at = Carbon::parse('2026-03-15 08:00:00');
        $lead->updated_at = Carbon::parse('2026-03-15 08:30:00');

        $items = ViewLead::timelineItemsForDisplay($lead);
        $html = ViewLead::renderTimelineHtml($lead)->toHtml();

        $this->assertGreaterThanOrEqual(4, count($items));
        $this->assertSame(count($items), count(array_unique(array_map(fn (array $item): string => implode('|', $item), $items))));
        $this->assertStringContainsString('Talep olu', $html);
        $this->assertStringContainsString('Kaynak bilgisi', $html);
        $this->assertStringContainsString('Not eklendi', $html);
        $this->assertStringNotContainsString('<div class="mt-2 text-sm text-gray-600"></div>', $html);
    }

    public function test_order_view_page_exposes_manual_transition_action_in_header(): void
    {
        $order = new Order([
            'order_no' => 'ORD-VIEW-001',
            'state' => 'paid',
            'payment_state' => 'cash_on_delivery',
        ]);

        $page = new class extends ViewOrder
        {
            public function exposeHeaderActionsFor(Order $record): array
            {
                $this->record = $record;

                return $this->getHeaderActions();
            }
        };

        $actions = $page->exposeHeaderActionsFor($order);
        $actionNames = array_map(fn ($action): string => (string) $action->getName(), $actions);

        $this->assertContains('manual_transition', $actionNames);
        $this->assertArrayHasKey('assigned', OrderResource::orderStateOptions());
        $this->assertArrayHasKey('delivered', OrderResource::orderStateOptions());
        $this->assertCount(2, OrderResource::manualTransitionFormSchema());
    }
}
