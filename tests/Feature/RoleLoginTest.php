<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleLoginTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | The two doors
    |--------------------------------------------------------------------------
    */

    public function test_both_sign_in_pages_render(): void
    {
        $this->get('/admin/login.php')
            ->assertOk()
            ->assertSee('Administrator sign in')
            ->assertSee('Sign in as administrator');

        $this->get('/staff/login.php')
            ->assertOk()
            ->assertSee('Staff sign in')
            ->assertSee('Sign in as staff');
    }

    public function test_each_page_posts_to_itself(): void
    {
        $this->get('/admin/login.php')->assertOk()->assertSee('/admin/login.php', false);
        $this->get('/staff/login.php')->assertOk()->assertSee('/staff/login.php', false);
    }

    public function test_an_administrator_can_sign_in_at_the_admin_door(): void
    {
        $user = User::factory()->admin()->create(['email' => 'boss@goldengate.test']);

        $this->post('/admin/login.php', ['email' => 'boss@goldengate.test', 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_staff_and_managers_can_sign_in_at_the_staff_door(): void
    {
        foreach ([Role::Staff, Role::Manager] as $role) {
            $email = 'user'.$role->value.'@goldengate.test';
            $user = User::factory()->create(['role' => $role, 'email' => $email]);

            $this->post('/staff/login.php', ['email' => $email, 'password' => 'password'])
                ->assertRedirect(route($role->homeRoute()));

            $this->assertAuthenticatedAs($user);
            $this->post('/logout');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | A door only opens for its own role
    |--------------------------------------------------------------------------
    */

    public function test_staff_are_refused_at_the_admin_door(): void
    {
        $user = User::factory()->staff()->create(['email' => 'jo@goldengate.test']);

        $this->post('/admin/login.php', ['email' => 'jo@goldengate.test', 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'denied',
        ]);
    }

    public function test_an_administrator_is_refused_at_the_staff_door(): void
    {
        $user = User::factory()->admin()->create(['email' => 'boss@goldengate.test']);

        $this->post('/staff/login.php', ['email' => 'boss@goldengate.test', 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'denied',
        ]);
    }

    public function test_a_wrong_password_is_refused_at_either_door(): void
    {
        User::factory()->admin()->create(['email' => 'boss@goldengate.test']);

        $this->post('/admin/login.php', ['email' => 'boss@goldengate.test', 'password' => 'wrong'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_a_disabled_account_is_refused_at_either_door(): void
    {
        User::factory()->admin()->create(['email' => 'boss@goldengate.test', 'is_active' => false]);

        $this->post('/admin/login.php', ['email' => 'boss@goldengate.test', 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_an_already_signed_in_person_cannot_reach_the_doors(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get('/admin/login.php')->assertRedirect(route('admin.dashboard'));
        $this->get('/staff/login.php')->assertRedirect(route('admin.dashboard'));
    }

    public function test_the_email_is_matched_case_insensitively(): void
    {
        $user = User::factory()->admin()->create([
            'username' => 'theboss',
            'name' => 'The Boss',
            'email' => 'boss@goldengate.test',
        ]);

        $this->post('/admin/login.php', ['email' => 'BOSS@goldengate.test', 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    /*
    |--------------------------------------------------------------------------
    | Server-side protection, not just hidden buttons
    |--------------------------------------------------------------------------
    */

    public function test_the_admin_area_redirects_a_guest_to_a_sign_in_page(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login.php'));
        $this->get('/admin/products')->assertRedirect(route('admin.login.php'));
    }

    public function test_a_staff_member_cannot_open_the_admin_only_pages(): void
    {
        $this->actingAs(User::factory()->staff()->create())
            ->get('/admin/users')
            ->assertForbidden();
    }

    public function test_a_manager_cannot_open_the_admin_only_pages_either(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->get('/admin/users')
            ->assertForbidden();
    }

    public function test_a_manager_may_open_the_catalog_write_pages(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->get('/admin/products')
            ->assertOk();
    }

    public function test_signing_out_ends_the_session(): void
    {
        $this->actingAs(User::factory()->staff()->create());
        $this->get('/admin')->assertOk();

        // Signing out lands on the neutral door, not either area's.
        $this->post('/logout')->assertRedirect(route('login'));

        $this->assertGuest();
        $this->get('/admin')->assertRedirect(route('admin.login.php'));
    }

    /*
    |--------------------------------------------------------------------------
    | Shoppers are never asked to sign in
    |--------------------------------------------------------------------------
    */

    public function test_the_public_site_offers_no_customer_sign_in(): void
    {
        foreach (['/catalog', '/services', '/information', '/settings', '/cart'] as $url) {
            $html = $this->get($url)->assertOk()->getContent();

            $this->assertStringNotContainsString(route('login'), $html, $url.' must not link to a sign-in page');
            $this->assertStringNotContainsString(route('account.home'), $html, $url.' must not push a shopper account');
        }
    }

    public function test_the_public_site_still_points_at_the_staff_door_for_the_team(): void
    {
        $this->get('/catalog')
            ->assertOk()
            ->assertSee('Staff sign in')
            ->assertSee('/staff/login.php', false);
    }

    public function test_a_guest_can_still_browse_and_order_end_to_end(): void
    {
        $product = Product::factory()->create(['stock' => 3]);

        $this->get('/catalog')->assertOk();
        $this->get('/cart')->assertOk();

        $this->post('/cart', ['product_id' => $product->id, 'quantity' => 2])->assertRedirect();

        $this->post('/checkout', [
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
}
