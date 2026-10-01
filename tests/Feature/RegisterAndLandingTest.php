<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\Role;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Create Account, and the four landing pages.
 *
 * A registration is always a waiting staff account, and none of the four role
 * pages is a rename of another: each one refuses everybody who is not its role.
 */
class RegisterAndLandingTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Creating an account
    |--------------------------------------------------------------------------
    */

    public function test_the_registration_form_renders_for_a_guest(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Create account');
    }

    public function test_a_registration_becomes_a_waiting_staff_account(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Jenna Cruz',
            'email' => 'jenna@goldengate.test',
            'password' => 'chicken-fried-rice',
            'password_confirmation' => 'chicken-fried-rice',
        ])->assertRedirect(route('staff.login.php'))
            ->assertSessionHas('status');

        $user = User::query()->where('email', 'jenna@goldengate.test')->firstOrFail();

        $this->assertSame(Role::Staff, $user->role);
        $this->assertSame(AccountStatus::Pending, $user->status);
        $this->assertTrue($user->isPending());
        $this->assertFalse($user->isApproved());
    }

    public function test_a_registration_cannot_pick_its_own_role(): void
    {
        // Even if somebody edits the form to send an administrator or a customer
        // role, the account is written as waiting staff.
        $this->post(route('register.store'), [
            'name' => 'Sneaky',
            'email' => 'sneaky@goldengate.test',
            'password' => 'chicken-fried-rice',
            'password_confirmation' => 'chicken-fried-rice',
            'role' => 'admin',
            'status' => 'approved',
        ]);

        $user = User::query()->where('email', 'sneaky@goldengate.test')->firstOrFail();

        $this->assertSame(Role::Staff, $user->role);
        $this->assertSame(AccountStatus::Pending, $user->status);
    }

    public function test_the_password_is_hashed_and_never_stored_as_written(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Jenna Cruz',
            'email' => 'jenna@goldengate.test',
            'password' => 'chicken-fried-rice',
            'password_confirmation' => 'chicken-fried-rice',
        ]);

        $user = User::query()->where('email', 'jenna@goldengate.test')->firstOrFail();

        $this->assertNotSame('chicken-fried-rice', $user->password);
        $this->assertTrue(Hash::check('chicken-fried-rice', $user->password));
    }

    public function test_the_email_has_to_be_unique_regardless_of_case(): void
    {
        User::factory()->create(['email' => 'taken@goldengate.test']);

        $this->post(route('register.store'), [
            'name' => 'Impostor',
            'email' => 'TAKEN@goldengate.test',
            'password' => 'chicken-fried-rice',
            'password_confirmation' => 'chicken-fried-rice',
        ])->assertSessionHasErrors('email');

        $this->assertSame(1, User::query()->whereRaw('LOWER(email) = ?', ['taken@goldengate.test'])->count());
    }

    public function test_a_turned_down_address_cannot_register_again(): void
    {
        User::factory()->rejected()->create(['email' => 'nope@goldengate.test']);

        $this->post(route('register.store'), [
            'name' => 'Trying Again',
            'email' => 'nope@goldengate.test',
            'password' => 'chicken-fried-rice',
            'password_confirmation' => 'chicken-fried-rice',
        ])->assertSessionHasErrors('email');

        $this->assertSame(1, User::query()->count());
    }

    public function test_the_registration_needs_a_name_an_email_and_a_matching_password(): void
    {
        $this->post(route('register.store'), [])
            ->assertSessionHasErrors(['name', 'email', 'password']);

        $this->post(route('register.store'), [
            'name' => 'Jenna',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'different',
        ])->assertSessionHasErrors(['email', 'password']);

        $this->assertSame(0, User::query()->count());
    }

    public function test_two_people_with_the_same_local_part_get_different_usernames(): void
    {
        foreach (['first@goldengate.test', 'second@goldengate.test'] as $index => $email) {
            $this->post(route('register.store'), [
                'name' => 'Person '.$index,
                'email' => $email,
                'password' => 'chicken-fried-rice',
                'password_confirmation' => 'chicken-fried-rice',
            ]);
        }

        $usernames = User::query()->orderBy('id')->pluck('username');

        $this->assertCount(2, $usernames);
        $this->assertNotSame($usernames[0], $usernames[1]);
    }

    /*
    |--------------------------------------------------------------------------
    | Each role has its own page, and only its own
    |--------------------------------------------------------------------------
    */

    public function test_the_manager_page_is_the_managers_only(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->get(route('admin.manager.home'))
            ->assertOk();

        foreach ([Role::Admin, Role::Staff, Role::Customer] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->get(route('admin.manager.home'))
                ->assertForbidden();
        }
    }

    public function test_the_staff_page_is_the_staff_only(): void
    {
        $this->actingAs(User::factory()->staff()->create())
            ->get(route('admin.staff.home'))
            ->assertOk();

        foreach ([Role::Admin, Role::Manager, Role::Customer] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->get(route('admin.staff.home'))
                ->assertForbidden();
        }
    }

    public function test_the_customer_page_is_the_customers_only(): void
    {
        $this->actingAs(User::factory()->customer()->create())
            ->get(route('account.home'))
            ->assertOk();

        foreach ([Role::Admin, Role::Manager, Role::Staff] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->get(route('account.home'))
                ->assertForbidden();
        }
    }

    public function test_a_guest_is_sent_to_a_sign_in_page_from_every_landing_page(): void
    {
        $this->get(route('admin.manager.home'))->assertRedirect(route('admin.login.php'));
        $this->get(route('admin.staff.home'))->assertRedirect(route('admin.login.php'));
        $this->get(route('account.home'))->assertRedirect(route('login'));
    }

    public function test_the_manager_page_counts_the_accounts_waiting(): void
    {
        User::factory()->pending()->count(3)->create();

        $this->actingAs(User::factory()->manager()->create())
            ->get(route('admin.manager.home'))
            ->assertOk()
            ->assertSee(route('admin.approvals.index'), false);
    }

    /*
    |--------------------------------------------------------------------------
    | A shopper never has to be here
    |--------------------------------------------------------------------------
    */

    public function test_a_guest_can_still_order_without_any_account(): void
    {
        $product = Product::factory()->create(['stock' => 4]);

        $this->get('/catalog')->assertOk();

        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 2])
            ->assertRedirect();

        $this->get('/cart')->assertOk();

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
}
