<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\SessionGuard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * The two ways in: a remembered token, or an email address and a password.
 *
 * This walks the whole shape of the sign-in flow end to end — token, login,
 * session, role page, logout — so each branch is known to work rather than
 * assumed.
 */
class AuthenticationFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The cookie Laravel issues for "keep me signed in": remember_<guard>_<hash>,
     * where the hash is of the guard class rather than the user model.
     */
    private function recallCookieName(): string
    {
        return 'remember_'.config('auth.defaults.guard').'_'.sha1(SessionGuard::class);
    }

    /**
     * Drop the resolved guard as well as the session, so the next request looks
     * like a different browser rather than the same signed-in one.
     */
    private function freshBrowser(): void
    {
        Auth::forgetGuards();
        $this->flushSession();
    }

    /*
    |--------------------------------------------------------------------------
    | Branch one: an existing cookie/session/token
    |--------------------------------------------------------------------------
    */

    public function test_keeping_the_session_signed_in_issues_a_recall_cookie(): void
    {
        User::factory()->staff()->create(['email' => 'ricky@goldengate.test']);

        $response = $this->post('/staff/login.php', [
            'email' => 'ricky@goldengate.test',
            'password' => 'password',
            'remember' => '1',
        ])->assertRedirect(route('admin.staff.home'));

        // This is the cookie branch of the flow: the token is handed to the
        // browser so a later visit can skip the password.
        $names = collect($response->headers->getCookies())->map(fn ($c) => $c->getName());

        $this->assertTrue(
            $names->contains($this->recallCookieName()),
            'expected a '.$this->recallCookieName().' cookie, got: '.$names->implode(', ')
        );
    }

    public function test_without_remembering_there_is_no_recall_cookie(): void
    {
        User::factory()->staff()->create(['email' => 'ricky@goldengate.test']);

        $response = $this->post('/staff/login.php', [
            'email' => 'ricky@goldengate.test',
            'password' => 'password',
        ])->assertRedirect(route('admin.staff.home'));

        $names = collect($response->headers->getCookies())->map(fn ($c) => $c->getName());

        $this->assertFalse($names->contains($this->recallCookieName()));
    }

    public function test_a_forged_token_is_refused(): void
    {
        User::factory()->staff()->create(['username' => 'ricky']);

        $this->freshBrowser();

        $this->withCookie($this->recallCookieName(), 'not-a-real-token')
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login.php'));

        $this->assertGuest();
    }

    /*
    |--------------------------------------------------------------------------
    | Branch two: an email address, then a password
    |--------------------------------------------------------------------------
    */

    public function test_the_email_address_identifies_the_account(): void
    {
        $user = User::factory()->staff()->create([
            'username' => 'ricky',
            'email' => 'ricky@goldengate.test',
        ]);

        $this->post('/staff/login.php', ['email' => 'ricky@goldengate.test', 'password' => 'password'])
            ->assertRedirect(route('admin.staff.home'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_a_username_alone_does_not_identify_the_account(): void
    {
        User::factory()->staff()->create([
            'username' => 'ricky',
            'email' => 'ricky@goldengate.test',
        ]);

        $this->post('/staff/login.php', ['email' => 'ricky', 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_the_email_is_matched_without_caring_about_case_or_surrounding_space(): void
    {
        $user = User::factory()->staff()->create(['email' => 'ricky@goldengate.test']);

        $this->post('/staff/login.php', ['email' => '  RICKY@goldengate.test ', 'password' => 'password'])
            ->assertRedirect(route('admin.staff.home'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_a_wrong_password_gets_in_nowhere(): void
    {
        User::factory()->staff()->create(['email' => 'ricky@goldengate.test']);

        $this->post('/staff/login.php', ['email' => 'ricky@goldengate.test', 'password' => 'wrong'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    /*
    |--------------------------------------------------------------------------
    | Branch three: no account yet
    |--------------------------------------------------------------------------
    */

    public function test_an_account_is_created_pending_and_only_opens_after_approval(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'username' => 'jennac',
            'name' => 'Jenna Cruz',
            'email' => 'jenna@goldengate.test',
            'role' => Role::Staff->value,
            'is_active' => '1',
        ])->assertRedirect(route('admin.users.index'));

        $created = User::query()->where('email', 'jenna@goldengate.test')->firstOrFail();

        // Pending: no password, and nobody has accepted it.
        $this->assertTrue($created->isPending());
        $this->assertFalse($created->hasPassword());

        // The link comes back once, for the person to set their own password.
        $link = session('setPasswordLink');
        $this->assertNotNull($link);
        $this->get(route('admin.users.index'))->assertSessionMissing('setPasswordLink');

        parse_str(parse_url($link, PHP_URL_QUERY) ?: '', $query);
        $token = $query['token'] ?? '';

        $this->freshBrowser();

        $this->post(route('set-password.store'), [
            'token' => $token,
            'password' => 'chicken-fried-rice',
            'password_confirmation' => 'chicken-fried-rice',
        ])->assertRedirect(route('staff.login.php'));

        // A password is set, but the account is still pending, so it opens nothing.
        $this->post('/staff/login.php', ['email' => 'jenna@goldengate.test', 'password' => 'chicken-fried-rice'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        // Accepted by a manager, and now it works. No "from" here on purpose: that
        // would leave a stored destination behind for the next person to sign
        // in, which is not what happens in a real sign-out.
        $this->actingAs(User::factory()->manager()->create())
            ->patch(route('admin.approvals.accept', $created))
            ->assertRedirect();

        $this->freshBrowser();

        $this->post('/staff/login.php', ['email' => 'jenna@goldengate.test', 'password' => 'chicken-fried-rice'])
            ->assertRedirect(route('admin.staff.home'));

        $this->assertAuthenticatedAs($created->fresh());
    }

    /*
    |--------------------------------------------------------------------------
    | Role lands each person on the right page
    |--------------------------------------------------------------------------
    */

    public function test_each_role_reaches_its_own_pages_and_no_others(): void
    {
        $admin = User::factory()->admin()->create();
        $manager = User::factory()->manager()->create();
        $staff = User::factory()->staff()->create();

        // Admin reaches users and the catalogue.
        $this->actingAs($admin)->get('/admin/users')->assertOk();
        $this->actingAs($admin)->get('/admin/categories')->assertOk();

        // Manager reaches the catalogue and stock, but not users.
        $this->actingAs($manager)->get('/admin/products')->assertOk();
        $this->actingAs($manager)->get('/admin/stock')->assertOk();
        $this->actingAs($manager)->get('/admin/users')->assertForbidden();

        // Staff reaches the dashboard, orders, stock and goods, and nothing else.
        $this->actingAs($staff)->get('/admin')->assertOk();
        $this->actingAs($staff)->get('/admin/orders')->assertOk();
        $this->actingAs($staff)->get('/admin/stock')->assertOk();
        $this->actingAs($staff)->get('/admin/products')->assertOk();
        $this->actingAs($staff)->get('/admin/users')->assertForbidden();
        $this->actingAs($staff)->get('/admin/categories')->assertForbidden();
        $this->actingAs($staff)->get('/admin/suppliers')->assertForbidden();
    }

    /*
    |--------------------------------------------------------------------------
    | Logout leaves nothing behind
    |--------------------------------------------------------------------------
    */

    public function test_logout_ends_the_session_and_cannot_be_replayed(): void
    {
        $user = User::factory()->staff()->create();

        $this->post('/staff/login.php', [
            'email' => $user->email,
            'password' => 'password',
            'remember' => '1',
        ]);

        $this->assertAuthenticated();
        $oldToken = $user->fresh()->remember_token;

        $this->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();

        // The token is replaced rather than left behind, so the cookie that was
        // handed out cannot be replayed after signing out.
        $this->assertNotSame($oldToken, $user->fresh()->remember_token);

        $this->freshBrowser();

        $this->withCookie($this->recallCookieName(), $oldToken)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login.php'));

        $this->assertGuest();
    }

    /*
    |--------------------------------------------------------------------------
    | A shopper never needs any of this
    |--------------------------------------------------------------------------
    */

    public function test_a_customer_browses_and_orders_without_an_account(): void
    {
        $product = Product::factory()->create(['stock' => 5]);

        foreach (['/catalog', '/services', '/information', '/settings', '/cart'] as $url) {
            $this->get($url)->assertOk();
        }

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
        $this->assertDatabaseCount('orders', 1);
    }
}
