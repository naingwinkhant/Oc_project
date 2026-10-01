<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * The email address only has to be typed once.
 *
 * Whichever door is used, the address that worked is remembered in a cookie and
 * offered again next time. A password is never stored.
 */
class RememberedIdentifierTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: string}>
     */
    public static function doors(): array
    {
        return [
            'staff' => ['/staff/login.php', '/staff/login.php', 'staff', 'admin.staff.home'],
            'admin' => ['/admin/login.php', '/admin/login.php', 'admin', 'admin.dashboard'],
            'general' => ['/login', '/login', 'staff', 'admin.staff.home'],
        ];
    }

    private function signIn(string $postPath, string $email, string $landsOn): TestResponse
    {
        return $this->post($postPath, ['email' => $email, 'password' => 'password'])
            ->assertRedirect(route($landsOn));
    }

    /**
     * @dataProvider doors
     */
    public function test_the_identifier_is_prefilled_after_a_successful_sign_in(string $getPath, string $postPath, string $role, string $landsOn): void
    {
        User::factory()->create([
            'username' => 'thein',
            'email' => 'thein@goldengate.test',
            'role' => $role,
        ]);

        $this->signIn($postPath, 'thein@goldengate.test', $landsOn)
            ->assertCookie('last_identifier', 'thein@goldengate.test');

        // Signing out first, because a signed-in visitor is redirected away
        // from the door rather than shown the form.
        $this->post('/logout');

        $this->withCookie('last_identifier', 'thein@goldengate.test')
            ->get($getPath)
            ->assertOk()
            ->assertSee('value="thein@goldengate.test"', false);
    }

    /**
     * @dataProvider doors
     */
    public function test_nothing_is_remembered_when_the_sign_in_fails(string $getPath, string $postPath, string $role): void
    {
        User::factory()->create([
            'username' => 'thein',
            'email' => 'thein@goldengate.test',
            'role' => $role,
        ]);

        $this->post($postPath, ['email' => 'thein@goldengate.test', 'password' => 'wrong'])
            ->assertSessionHasErrors('email')
            ->assertCookieMissing('last_identifier');
    }

    public function test_the_password_is_never_put_in_a_cookie(): void
    {
        User::factory()->staff()->create(['email' => 'thein@goldengate.test']);

        $response = $this->signIn('/staff/login.php', 'thein@goldengate.test', 'admin.staff.home');

        $cookies = collect($response->headers->getCookies())
            ->reject(fn ($cookie) => $cookie->getName() === config('session.cookie'))
            ->map(fn ($cookie) => $cookie->getName().'='.$cookie->getValue())
            ->implode('; ');

        $this->assertStringContainsString('last_identifier', $cookies);
        $this->assertStringNotContainsString('password', strtolower($cookies));
    }

    public function test_the_form_label_says_email(): void
    {
        $this->get('/staff/login.php')
            ->assertOk()
            ->assertSee('Email', false)
            ->assertDontSee('Username or email');
    }

    public function test_a_failed_attempt_is_shown_again_so_it_can_be_corrected(): void
    {
        User::factory()->staff()->create(['email' => 'thein@goldengate.test']);

        $this->from('/staff/login.php')
            ->post('/staff/login.php', ['email' => 'thein@goldengate.test', 'password' => 'wrong'])
            ->assertRedirect('/staff/login.php')
            ->assertSessionHasInput('email', 'thein@goldengate.test');
    }

    public function test_signing_out_keeps_the_identifier_for_next_time(): void
    {
        User::factory()->staff()->create(['email' => 'thein@goldengate.test']);

        $this->signIn('/staff/login.php', 'thein@goldengate.test', 'admin.staff.home');
        $this->post('/logout')
            ->assertRedirect(route('login'))
            ->assertCookie('last_identifier', 'thein@goldengate.test');

        // Still remembered, so the next visit only needs the password.
        $this->withCookie('last_identifier', 'thein@goldengate.test')
            ->get('/staff/login.php')
            ->assertOk()
            ->assertSee('value="thein@goldengate.test"', false);
    }

    public function test_a_returning_visitor_sees_it_in_the_email_field(): void
    {
        User::factory()->staff()->create(['email' => 'thein@goldengate.test']);

        $this->signIn('/staff/login.php', 'thein@goldengate.test', 'admin.staff.home');
        $this->post('/logout');

        $html = $this->withCookie('last_identifier', 'thein@goldengate.test')
            ->get('/staff/login.php')
            ->assertOk()
            ->getContent();

        // A starting point to overwrite, not a locked-in account.
        $this->assertMatchesRegularExpression(
            '/name="email"[^>]*value="thein@goldengate.test"/',
            $html
        );
    }
}
