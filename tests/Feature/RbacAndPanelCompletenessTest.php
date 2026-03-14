<?php

namespace Tests\Feature;

use App\Models\Courier;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class RbacAndPanelCompletenessTest extends TestCase
{
    public function test_role_permission_seeder_creates_expected_roles(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $expectedRoles = ['super-admin', 'admin', 'operations', 'finance', 'courier', 'support'];
        foreach ($expectedRoles as $roleName) {
            $this->assertTrue(Role::query()->where('name', $roleName)->exists(), 'Missing role: '.$roleName);
        }
    }

    public function test_courier_and_customer_panel_routes_render(): void
    {
        $courier = Courier::query()->create([
            'full_name' => 'Courier Panel',
            'phone' => '05320000222',
            'status' => 'approved',
        ]);

        $customer = User::factory()->create();
        Order::query()->create([
            'customer_id' => $customer->id,
            'order_no' => 'ORD-PANEL-001',
            'state' => 'paid',
            'payment_state' => 'pending',
            'pickup_address' => 'P',
            'dropoff_address' => 'D',
            'total_amount' => 900,
            'currency' => 'TRY',
        ]);

        $this->get('/panel/courier/'.$courier->id)->assertOk()->assertSee('Kurye Paneli');
        $this->get('/panel/customer/'.$customer->id)->assertOk()->assertSee('Musteri Paneli');
        $this->get('/kurye-panel?courier_id='.$courier->id)->assertOk()->assertSee('Kurye Paneli');
        $this->get('/musteri-panel?user_id='.$customer->id)->assertOk()->assertSee('Musteri Paneli');
    }
}
