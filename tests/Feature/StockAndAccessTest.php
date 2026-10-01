<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockAndAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_in_increases_balance_and_logs_movement(): void
    {
        $manager = User::factory()->manager()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $this->actingAs($manager)
            ->post(route('admin.stock.store'), [
                'product_id' => $product->id,
                'type' => 'in',
                'quantity' => 25,
                'reason' => 'Delivery',
            ])
            ->assertSessionHas('success');

        $product->refresh();

        $this->assertSame(35, $product->stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 25,
            'balance_after' => 35,
        ]);
    }

    public function test_stock_out_never_drops_below_zero(): void
    {
        $manager = User::factory()->manager()->create();
        $product = Product::factory()->create(['stock' => 3]);

        $this->actingAs($manager)->post(route('admin.stock.store'), [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 10,
        ]);

        $this->assertSame(0, $product->fresh()->stock);
    }

    public function test_low_stock_page_lists_items_at_or_below_threshold(): void
    {
        $manager = User::factory()->manager()->create();
        Product::factory()->lowStock()->create(['name' => 'Nearly Gone Eggs']);
        Product::factory()->outOfStock()->create(['name' => 'Sold Out Milk']);
        Product::factory()->create(['name' => 'Plenty in Stock', 'stock' => 300, 'min_stock' => 10]);

        $this->actingAs($manager)
            ->get(route('admin.stock.low'))
            ->assertOk()
            ->assertSee('Nearly Gone Eggs')
            ->assertSee('Sold Out Milk')
            ->assertDontSee('Plenty in Stock');
    }

    public function test_staff_can_view_stock_but_not_record_it(): void
    {
        Product::factory()->create();

        $this->actingAs(User::factory()->staff()->create())
            ->get(route('admin.stock.index'))
            ->assertOk();

        $this->actingAs(User::factory()->staff()->create())
            ->post(route('admin.stock.store'), [
                'product_id' => Product::first()->id,
                'type' => 'in',
                'quantity' => 5,
            ])
            ->assertForbidden();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login.php'));
        $this->get(route('admin.products.index'))->assertRedirect(route('admin.login.php'));
    }

    public function test_disabled_account_cannot_sign_in(): void
    {
        User::factory()->disabled()->create(['email' => 'off@supermarket.test']);

        $this->post(route('login'), [
            'email' => 'off@supermarket.test',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_only_routes_are_closed_to_managers(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_administrator_can_change_roles_and_disable_users(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee($staff->email);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $staff), [
                'username' => $staff->username,
                'name' => $staff->name,
                'email' => $staff->email,
                'role' => Role::Manager->value,
                'password' => '',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertSame(Role::Manager, $staff->fresh()->role);
    }

    public function test_admin_cannot_demote_their_own_account(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => Role::Staff->value,
            ])
            ->assertSessionHasErrors('role');
    }

    public function test_there_is_no_public_sign_up(): void
    {
        // Customers order as guests; team accounts come from the dashboard.
        $this->get('/register')->assertNotFound();
        $this->post('/register', [
            'username' => 'newhire',
            'name' => 'New Hire',
            'email' => 'newhire@supermarket.test',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ])->assertNotFound();

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'newhire@supermarket.test']);
    }

    public function test_a_guest_can_browse_and_order_without_an_account(): void
    {
        $product = Product::factory()->create(['stock' => 5]);

        $this->get(route('catalog.index'))->assertOk();
        $this->get(route('services'))->assertOk();
        $this->get(route('information'))->assertOk();
        $this->get(route('settings'))->assertOk();

        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1])
            ->assertRedirect();

        $this->post(route('checkout.store'), [
            'customer_name' => 'Aung Kyaw',
            'phone' => '09 380 000 00',
            'email' => 'aung@example.com',
            'delivery_address' => 'No. 12, Baho Road, Kamayut',
            'township' => 'Kamayut',
            'payment_gateway' => 'sandbox',
        ])->assertSessionHasNoErrors();

        $this->assertGuest();
        $this->assertDatabaseCount('orders', 1);
    }

    public function test_sign_in_records_last_login(): void
    {
        $user = User::factory()->create(['email' => 'staff@supermarket.test']);

        $this->post(route('login'), [
            'email' => 'staff@supermarket.test',
            'password' => 'password',
        ])->assertRedirect(route($user->role->homeRoute()));

        $this->assertNotNull($user->fresh()->last_login_at);
        $this->assertAuthenticatedAs($user);
    }

    public function test_dashboard_renders_for_every_role(): void
    {
        Product::factory()->count(2)->create();

        foreach ([Role::Admin, Role::Manager, Role::Staff] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->get(route('admin.dashboard'))
                ->assertOk();
        }
    }
}
