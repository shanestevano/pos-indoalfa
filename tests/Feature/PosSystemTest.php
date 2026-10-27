<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Tests\TestCase;

class PosSystemTest extends TestCase
{
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Point of Sale');
    }

    public function test_cashier_quick_login_and_access_pos(): void
    {
        $cashier = User::where('employee_code', 'KSR001')->first();
        $this->actingAs($cashier);

        $response = $this->get('/pos');
        $response->assertStatus(200);
        $response->assertSee('Terminal Kasir');
    }

    public function test_warehouse_quick_login_and_access_warehouse(): void
    {
        $warehouse = User::where('employee_code', 'GDG001')->first();
        $this->actingAs($warehouse);

        $response = $this->get('/warehouse');
        $response->assertStatus(200);
        $response->assertSee('Modul Gudang');
    }

    public function test_admin_quick_login_and_access_dashboard(): void
    {
        $admin = User::where('employee_code', 'ADM001')->first();
        $this->actingAs($admin);

        $response = $this->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Finansial');
    }

    public function test_products_exist_in_database(): void
    {
        $count = Product::count();
        $this->assertGreaterThan(0, $count);
    }
}
