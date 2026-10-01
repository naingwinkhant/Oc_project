<?php

namespace App\Http\Controllers\Auth;

use App\Cart\FavouriteService;
use App\Enums\Role;
use App\History\ViewHistoryService;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Notifications\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * The sign-in identifier is remembered between visits, so a username or an
     * email only has to be typed the first time. A password is never stored.
     */
    private const IDENTIFIER_COOKIE = 'last_identifier';

    private const IDENTIFIER_DAYS = 365;

    public function create(): View
    {
        return view('auth.login', [
            'heading' => 'Sign in to your account',
            'identifier' => $this->rememberedIdentifier(),
        ]);
    }

    /**
     * The admin door. Only an administrator may come through it.
     */
    public function createAdmin(): View
    {
        return view('auth.login', [
            'area' => 'admin',
            'heading' => 'Administrator sign in',
            'eyebrow' => 'Administrator access',
            'blurb' => 'This page is for administrators. Staff should use the staff sign-in page.',
            'identifier' => $this->rememberedIdentifier(),
        ]);
    }

    /**
     * The staff door. Staff and managers come through here; an administrator
     * is pointed back at their own page rather than quietly let in here.
     */
    public function createStaff(): View
    {
        return view('auth.login', [
            'area' => 'staff',
            'heading' => 'Staff sign in',
            'eyebrow' => 'Store team access',
            'blurb' => 'Use the username issued to you by your store manager.',
            'identifier' => $this->rememberedIdentifier(),
        ]);
    }

    /**
     * What the form should offer as a starting point: whatever was just typed,
     * otherwise whatever came back last time.
     */
    private function rememberedIdentifier(): string
    {
        return (string) request()->cookie(self::IDENTIFIER_COOKIE, '');
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->authenticate($request);
    }

    public function storeAdmin(Request $request): RedirectResponse
    {
        return $this->authenticate($request, [Role::Admin], 'admin');
    }

    public function storeStaff(Request $request): RedirectResponse
    {
        return $this->authenticate($request, [Role::Staff, Role::Manager], 'staff');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have been signed out.');
    }

    /**
     * Verify a credential and open a session for it.
     *
     * The password is checked with Hash::check, which is bcrypt's password_verify
     * behind the facade, and the lookup goes through Eloquent, so the username
     * is a bound parameter rather than string-concatenated SQL.
     *
     * When $roles is given the door is role-locked: a valid password for the
     * wrong role is refused before any session is created, so a staff member
     * cannot reach the admin area by guessing the other URL.
     *
     * @param  array<int, Role>  $roles
     */
    private function authenticate(Request $request, array $roles = [], string $area = ''): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:190'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Enter the email address on your account.',
        ]);

        $email = trim((string) $data['email']);
        $user = User::findByEmail($email);

        if (! $user) {
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        // Anything that is not accepted cannot get in, whatever password it
        // holds. Checked before hashing so a null password is never verified.
        if (! $user->isApproved()) {
            throw ValidationException::withMessages(['email' => $user->status->refusalMessage()]);
        }

        if (! Hash::check($data['password'], $user->password ?? '')) {
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'This account has been disabled. Contact an administrator.',
            ]);
        }

        if ($roles !== [] && ! in_array($user->role, $roles, true)) {
            $this->logRefused($user, $area);

            throw ValidationException::withMessages([
                'email' => $this->wrongDoorMessage($area, $user),
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        app(FavouriteService::class)->mergeOnLogin($user->id);
        app(ViewHistoryService::class)->mergeOnLogin($user->id);
        app(NotificationService::class)->mergeOnLogin($user->id);

        $user->forceFill(['last_login_at' => now()])->save();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'description' => $user->name.' signed in',
        ]);

        // Remember the address, never the password, so the next visit only has
        // one field filled in.
        cookie()->queue(cookie(self::IDENTIFIER_COOKIE, $email, self::IDENTIFIER_DAYS * 24 * 60));

        return redirect()->to($this->welcomeUrl($user));
    }

    /**
     * Where to send somebody after they sign in.
     */
    private function welcomeUrl(User $user): string
    {
        $fallback = $user->landingUrl();

        if (! app()->bound('request')) {
            return $fallback;
        }

        $intended = session()->pull('url.intended');

        if (! is_string($intended) || $intended === $fallback) {
            return $fallback;
        }

        $route = Route::getRoutes()->match(
            Request::create($intended, 'GET')
        )->getName();

        foreach (Route::getRoutes()->getByName($route)?->gatherMiddleware() ?? [] as $middleware) {
            if (! is_string($middleware) || ! str_starts_with($middleware, 'role:')) {
                continue;
            }

            $allowed = array_map('trim', explode(',', substr($middleware, 5)));

            if (! in_array($user->role->value, $allowed, true)) {
                return $fallback;
            }
        }

        return $intended;
    }

    /**
     * Say which door they should be using, without confirming anything about
     * the account beyond what they already typed.
     */
    private function wrongDoorMessage(string $area, User $user): string
    {
        if ($area === 'admin') {
            return 'That account is not an administrator. Use the staff sign-in page instead.';
        }

        if ($user->isAdmin()) {
            return 'Administrators sign in on the admin page.';
        }

        return 'That account cannot sign in here. Contact your administrator.';
    }

    /**
     * A wrong-role attempt is worth knowing about, so it is recorded against
     * the account that tried.
     */
    private function logRefused(User $user, string $area): void
    {
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'denied',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'description' => $user->name.' was refused at the '.$area.' sign-in page',
        ]);
    }
}
