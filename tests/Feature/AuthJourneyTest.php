<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\Role;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * The whole journey in one place, walked the way a person walks it.
 *
 * Nothing is mocked. Every step is a real request against a real session, so
 * the order the checks run in matters: a later step depends on the state an
 * earlier one left behind. The point is to catch the case where each piece
 * passes on its own but the path between them does not join up.
 */
class AuthJourneyTest extends TestCase
{
    use RefreshDatabase;

    private int $step = 0;

    private function passing(): void
    {
        $this->step++;
    }

    /*
    |--------------------------------------------------------------------------
    | A stranger arrives with no account
    |--------------------------------------------------------------------------
    */

    public function test_the_journey_from_stranger_to_signed_in_staff_member(): void
    {
        // They land on the shop with nothing, and can already browse and order.
        $product = Product::factory()->create(['stock' => 5]);

        $this->get('/catalog')->assertOk();
        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 2])
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
        $this->passing();

        // No account, so they follow the Create Account link on the staff door.
        $this->get(route('staff.login.php'))
            ->assertOk()
            ->assertSee(route('register'), false);

        $this->get(route('register'))->assertOk();
        $this->passing();

        // They fill it in.
        $this->post(route('register.store'), [
            'name' => 'Jenna Cruz',
            'email' => 'jenna@goldengate.test',
            'password' => 'chicken-fried-rice',
            'password_confirmation' => 'chicken-fried-rice',
        ])->assertRedirect(route('staff.login.php'))
            ->assertSessionHas('status');

        $jenna = User::query()->where('email', 'jenna@goldengate.test')->firstOrFail();

        $this->assertSame(Role::Staff, $jenna->role);
        $this->assertTrue($jenna->isPending());
        $this->assertTrue(Hash::check('chicken-fried-rice', $jenna->password));
        $this->passing();

        /*
        |------------------------------------------------------------------
        | While they wait, they cannot get in anywhere
        |------------------------------------------------------------------
        */

        $this->post(route('staff.login.php'), [
            'email' => 'jenna@goldengate.test',
            'password' => 'chicken-fried-rice',
        ])->assertSessionHasErrors(['email' => AccountStatus::Pending->refusalMessage()]);

        $this->assertGuest();
        $this->passing();

        // The right password on the wrong door is refused just the same.
        $this->post(route('admin.login.php'), [
            'email' => 'jenna@goldengate.test',
            'password' => 'chicken-fried-rice',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->passing();

        // Their role pages are shut, and the admin area sends them to a door.
        $this->get(route('admin.staff.home'))->assertRedirect(route('admin.login.php'));
        $this->get(route('admin.manager.home'))->assertRedirect(route('admin.login.php'));
        $this->get(route('account.home'))->assertRedirect(route('login'));
        $this->passing();

        // Nor can they accept themselves.
        $this->patch(route('admin.approvals.accept', $jenna))
            ->assertRedirect(route('admin.login.php'));
        $this->assertTrue($jenna->fresh()->isPending());
        $this->passing();

        /*
        |------------------------------------------------------------------
        | An administrator looks at the queue
        |------------------------------------------------------------------
        */

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.approvals.index'))
            ->assertOk()
            ->assertSee('Jenna Cruz')
            ->assertSee(route('admin.approvals.accept', $jenna), false)
            ->assertSee(route('admin.approvals.reject', $jenna), false);
        $this->passing();

        // Staff never reach that queue at all.
        $this->actingAs(User::factory()->staff()->create())
            ->get(route('admin.approvals.index'))
            ->assertForbidden();
        $this->passing();

        // They are accepted.
        $this->actingAs($admin)
            ->from(route('admin.approvals.index'))
            ->patch(route('admin.approvals.accept', $jenna))
            ->assertRedirect(route('admin.approvals.index'))
            ->assertSessionHas('success');

        $this->assertTrue($jenna->fresh()->isApproved());
        $this->passing();

        /*
        |------------------------------------------------------------------
        | Now the same password works
        |------------------------------------------------------------------
        */

        $this->app['auth']->forgetGuards();

        $this->post(route('staff.login.php'), [
            'email' => 'jenna@goldengate.test',
            'password' => 'chicken-fried-rice',
        ])->assertRedirect(route('admin.staff.home'));

        $this->assertAuthenticatedAs($jenna->fresh());
        $this->passing();

        // Their own page opens, and the shop's does not.
        $this->get(route('admin.staff.home'))->assertOk();
        $this->get(route('admin.users.index'))->assertForbidden();
        $this->get(route('account.home'))->assertForbidden();
        $this->passing();

        // The shopper's guest order is still there and was never touched.
        $this->assertDatabaseCount('orders', 1);
        $this->assertSame(1, Order::query()->where('email', 'aung@example.com')->count());
        $this->passing();

        /*
        |------------------------------------------------------------------
        | Out again, and back in without the form
        |------------------------------------------------------------------
        */

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();

        $this->get(route('admin.staff.home'))->assertRedirect(route('admin.login.php'));
        $this->passing();

        // The address is remembered, so only the password is needed.
        $this->withCookie('last_identifier', 'jenna@goldengate.test')
            ->get(route('staff.login.php'))
            ->assertOk()
            ->assertSee('value="jenna@goldengate.test"', false);
        $this->passing();
    }

    public function test_a_turned_down_registration_is_never_opened(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Nyein',
            'email' => 'nyein@goldengate.test',
            'password' => 'chicken-fried-rice',
            'password_confirmation' => 'chicken-fried-rice',
        ]);

        $nyein = User::query()->where('email', 'nyein@goldengate.test')->firstOrFail();

        $this->actingAs(User::factory()->admin()->create())
            ->from(route('admin.approvals.index'))
            ->patch(route('admin.approvals.reject', $nyein))
            ->assertSessionHas('success');

        $this->assertTrue($nyein->fresh()->isRejected());
        $this->passing();

        $this->app['auth']->forgetGuards();

        // The password is now worthless: the account itself is closed.
        $this->post(route('staff.login.php'), [
            'email' => 'nyein@goldengate.test',
            'password' => 'chicken-fried-rice',
        ])->assertSessionHasErrors(['email' => AccountStatus::Rejected->refusalMessage()]);

        $this->assertGuest();
        $this->passing();

        // Re-registering with the same address is refused too.
        $this->post(route('register.store'), [
            'name' => 'Nyein Again',
            'email' => 'nyein@goldengate.test',
            'password' => 'chicken-fried-rice',
            'password_confirmation' => 'chicken-fried-rice',
        ])->assertSessionHasErrors('email');

        // The one registration is still the one registration: nothing new.
        $this->assertSame(1, User::query()->where('email', 'nyein@goldengate.test')->count());
        $this->assertSame(0, User::query()->where('name', 'Nyein Again')->count());
        $this->passing();
    }

    public function test_a_manager_can_work_the_queue_without_being_an_administrator(): void
    {
        $manager = User::factory()->manager()->create();

        // They land on their own page, not the administrator's dashboard.
        $this->post(route('staff.login.php'), [
            'email' => $manager->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.manager.home'));

        $this->actingAs($manager)->get(route('admin.manager.home'))->assertOk();
        $this->passing();

        // They cannot touch user administration...
        $this->get(route('admin.users.index'))->assertForbidden();

        // ...but the queue is shared with them.
        $jenna = User::factory()->pending()->staff()->create([
            'email' => 'jenna@goldengate.test',
            'password' => Hash::make('chicken-fried-rice'),
        ]);

        $this->actingAs($manager)->patch(route('admin.approvals.accept', $jenna));
        $this->assertTrue($jenna->fresh()->isApproved());
        $this->passing();

        // Once accepted, that person signs in and reaches only the staff page.
        $this->post(route('logout'));
        $this->app['auth']->forgetGuards();
        $this->flushSession();

        $this->post(route('staff.login.php'), [
            'email' => 'jenna@goldengate.test',
            'password' => 'chicken-fried-rice',
        ])->assertRedirect(route('admin.staff.home'));

        $this->get(route('admin.manager.home'))->assertForbidden();
        $this->passing();
    }

    public function test_a_customer_account_reaches_its_own_orders_and_nothing_else(): void
    {
        $customer = User::factory()->customer()->create([
            'email' => 'shopper@goldengate.test',
            'password' => Hash::make('chicken-fried-rice'),
        ]);

        // Two guest orders, placed with the real checkout so the rows are genuine:
        // one with the customer's own address, one with somebody else's.
        foreach (['shopper@goldengate.test', 'someone-else@goldengate.test'] as $email) {
            $product = Product::factory()->create([
                'stock' => 5,
                'price' => 2_000,
                'sale_price' => null,
            ]);

            $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1])
                ->assertRedirect();

            $this->post(route('checkout.store'), [
                'customer_name' => 'Guest Shopper',
                'phone' => '09 380 000 00',
                'email' => $email,
                'delivery_address' => 'No. 12, Baho Road, Kamayut',
                'township' => 'Kamayut',
                'payment_gateway' => 'sandbox',
            ])->assertSessionHasNoErrors();
        }

        $this->assertDatabaseCount('orders', 2);
        $this->passing();

        $this->app['auth']->forgetGuards();

        $this->post(route('login'), [
            'email' => 'shopper@goldengate.test',
            'password' => 'chicken-fried-rice',
        ])->assertRedirect(route('account.home'));

        $this->get(route('account.home'))
            ->assertOk()
            ->assertSee('shopper@goldengate.test');
        $this->passing();

        // They see their own order and nobody else's.
        $this->assertSame(
            1,
            Order::query()->where('email', 'shopper@goldengate.test')->count()
        );

        $this->get(route('account.home'))->assertDontSee('someone-else@goldengate.test');
        $this->passing();

        // No part of the shop is open to them.
        $this->get(route('admin.staff.home'))->assertForbidden();
        $this->get(route('admin.manager.home'))->assertForbidden();
        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('admin.users.index'))->assertForbidden();
        $this->passing();
    }
}
