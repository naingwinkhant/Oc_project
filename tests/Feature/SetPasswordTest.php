<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * An account is created with an email, a username and a role. The password is
 * set afterwards by the person it belongs to, from a one-time link.
 */
class SetPasswordTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    private function createUser(array $attributes = []): TestResponse
    {
        return $this->actingAs($this->admin())->post(route('admin.users.store'), [
            'username' => 'jennac',
            'name' => 'Jenna Cruz',
            'email' => 'jenna@goldengate.test',
            'role' => 'staff',
            'is_active' => '1',
            ...$attributes,
        ]);
    }

    /**
     * Create an account and read the one-time link off the page the admin is
     * sent to, which is also the proof that the link is actually shown.
     */
    private function tokenFromCreatedAccount(array $attributes = []): string
    {
        $this->createUser($attributes)->assertRedirect(route('admin.users.index'));

        $html = $this->get(route('admin.users.index'))->assertOk()->getContent();

        $this->assertStringContainsString('Set-password link', $html);

        preg_match('/token=([A-Za-z0-9]+)/', $html, $matches);

        return $matches[1] ?? '';
    }

    public function test_the_create_form_asks_for_no_password(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.users.create'))
            ->assertOk()
            ->assertSee('Their password')
            ->assertDontSee('name="password"', false)
            ->assertDontSee('password_confirmation', false);
    }

    public function test_an_account_is_created_without_a_password(): void
    {
        $this->createUser()->assertRedirect(route('admin.users.index'));

        $user = User::query()->where('email', 'jenna@goldengate.test')->firstOrFail();

        $this->assertSame('jennac', $user->username);
        $this->assertSame('staff', $user->role->value);
        $this->assertNull($user->password, 'no password should be stored yet');
        $this->assertFalse($user->hasPassword());
    }

    public function test_a_set_password_link_is_handed_back_once(): void
    {
        $this->tokenFromCreatedAccount();

        // Shown on the page after creating, and gone on the one after that.
        $this->get(route('admin.users.index'))
            ->assertOk()
            ->assertSessionMissing('setPasswordLink');
    }

    public function test_only_a_hash_of_the_token_is_stored(): void
    {
        $plain = $this->tokenFromCreatedAccount();

        $stored = DB::table('password_reset_tokens')->value('token');

        $this->assertNotSame('', $plain);
        $this->assertNotSame($plain, $stored);
        $this->assertSame(hash('sha256', $plain), $stored);
    }

    public function test_the_new_person_sets_their_own_password(): void
    {
        $token = $this->tokenFromCreatedAccount();

        $this->app['auth']->forgetGuards();

        $this->get(route('set-password', ['token' => $token]))
            ->assertOk()
            ->assertSee('Choose a password')
            ->assertSee('Jenna Cruz');

        $this->post(route('set-password.store'), [
            'token' => $token,
            'password' => 'chicken-fried-rice',
            'password_confirmation' => 'chicken-fried-rice',
        ])->assertRedirect(route('staff.login.php'));

        $user = User::query()->where('email', 'jenna@goldengate.test')->firstOrFail();

        $this->assertTrue($user->hasPassword());
        $this->assertTrue(Hash::check('chicken-fried-rice', $user->password));
    }

    public function test_the_new_person_can_then_sign_in(): void
    {
        $token = $this->tokenFromCreatedAccount();
        $this->app['auth']->forgetGuards();

        $this->post(route('set-password.store'), [
            'token' => $token,
            'password' => 'chicken-fried-rice',
            'password_confirmation' => 'chicken-fried-rice',
        ]);

        $user = User::query()->where('email', 'jenna@goldengate.test')->firstOrFail();

        // Setting a password is not enough: an administrator or manager has to
        // accept the account first.
        $this->actingAs($this->admin())->patch(route('admin.approvals.accept', $user))->assertRedirect();
        $this->app['auth']->forgetGuards();

        $this->post(route('staff.login.php'), [
            'email' => 'jenna@goldengate.test',
            'password' => 'chicken-fried-rice',
        ])->assertRedirect(route('admin.staff.home'));

        $this->assertAuthenticated();
    }

    public function test_an_account_with_no_password_cannot_sign_in_at_all(): void
    {
        $this->tokenFromCreatedAccount();
        $this->app['auth']->forgetGuards();

        // The important case: a blank password must not be turned into a valid
        // one, so none of these may ever get in.
        foreach (['', ' ', 'password', 'chicken-fried-rice'] as $attempt) {
            $this->post(route('staff.login.php'), ['email' => 'jenna@goldengate.test', 'password' => $attempt])
                ->assertSessionHasErrors();

            $this->assertGuest();
        }

        // Accepted, so the only thing left in the way is the missing password.
        User::query()->where('email', 'jenna@goldengate.test')->firstOrFail()
            ->approve(User::factory()->admin()->create());

        $this->app['auth']->forgetGuards();

        // A wrong, non-blank password gets the specific reason.
        $this->post(route('staff.login.php'), ['email' => 'jenna@goldengate.test', 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();

        $this->assertNull(User::query()->where('email', 'jenna@goldengate.test')->value('password'));
    }

    public function test_a_link_cannot_be_used_twice(): void
    {
        $token = $this->tokenFromCreatedAccount();
        $this->app['auth']->forgetGuards();

        $this->post(route('set-password.store'), [
            'token' => $token,
            'password' => 'chicken-fried-rice',
            'password_confirmation' => 'chicken-fried-rice',
        ])->assertRedirect(route('staff.login.php'));

        $this->get(route('set-password', ['token' => $token]))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'jenna@goldengate.test']);
    }

    public function test_a_made_up_or_expired_link_is_refused(): void
    {
        $this->get(route('set-password', ['token' => 'not-a-real-token']))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');

        $token = $this->tokenFromCreatedAccount();
        $this->app['auth']->forgetGuards();

        // Age the link past an hour.
        DB::table('password_reset_tokens')->update([
            'created_at' => now()->subMinutes(61),
        ]);

        $this->get(route('set-password', ['token' => $token]))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');
    }

    public function test_the_passwords_must_match_and_be_long_enough(): void
    {
        $token = $this->tokenFromCreatedAccount();
        $this->app['auth']->forgetGuards();

        $this->post(route('set-password.store'), [
            'token' => $token,
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');

        $this->post(route('set-password.store'), [
            'token' => $token,
            'password' => 'long-enough-here',
            'password_confirmation' => 'different-one',
        ])->assertSessionHasErrors('password');

        $this->assertNull(User::query()->where('email', 'jenna@goldengate.test')->value('password'));
    }

    public function test_an_administrator_is_sent_to_their_own_door_afterwards(): void
    {
        $token = $this->tokenFromCreatedAccount(['role' => 'admin']);
        $this->app['auth']->forgetGuards();

        $this->post(route('set-password.store'), [
            'token' => $token,
            'password' => 'chicken-fried-rice',
            'password_confirmation' => 'chicken-fried-rice',
        ])->assertRedirect(route('admin.login.php'));
    }

    public function test_the_list_flags_an_account_that_has_no_password_yet(): void
    {
        $this->tokenFromCreatedAccount();
        User::factory()->create(['name' => 'Has One', 'email' => 'has@goldengate.test']);

        $this->actingAs($this->admin())
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('No password yet');
    }

    public function test_editing_an_account_leaves_its_password_alone(): void
    {
        $user = User::factory()->create(['name' => 'Before', 'email' => 'e@goldengate.test']);

        $this->actingAs($this->admin())
            ->put(route('admin.users.update', $user), [
                'username' => $user->username,
                'name' => 'After',
                'email' => $user->email,
                'role' => $user->role->value,
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertSame('After', $user->fresh()->name);
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_only_an_administrator_may_create_an_account(): void
    {
        foreach (['staff', 'manager'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->post(route('admin.users.store'), [
                    'username' => 'nope',
                    'name' => 'Nope',
                    'email' => 'nope@goldengate.test',
                    'role' => 'staff',
                ])
                ->assertForbidden();
        }

        $this->app['auth']->forgetGuards();

        $this->post(route('admin.users.store'), [
            'username' => 'nope',
            'name' => 'Nope',
            'email' => 'nope@goldengate.test',
            'role' => 'staff',
        ])->assertRedirect(route('admin.login.php'));

        $this->assertDatabaseMissing('users', ['email' => 'nope@goldengate.test']);
    }
}
