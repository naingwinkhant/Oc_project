<?php

namespace App\Http\Controllers\Auth;

use App\Cart\FavouriteService;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:40'],
            'password' => ['required', 'string'],
        ]);

        $identifier = trim((string) $credentials['username']);
        $user = User::findForLogin($identifier);

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'username' => 'This account has been disabled. Contact an administrator.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        app(FavouriteService::class)->mergeOnLogin($user->id);

        $user->forceFill(['last_login_at' => now()])->save();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'description' => $user->name.' signed in',
        ]);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have been signed out.');
    }

    public function createAccount(): View
    {
        return view('auth.register');
    }

    public function storeAccount(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => $this->usernameRules(),
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^0?9[0-9\s-]{7,13}$/'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'username.regex' => 'Usernames may only contain letters, numbers, dots, dashes and underscores.',
            'phone.regex' => 'Enter a Myanmar mobile number, for example 09 380 000 00.',
        ]);

        $user = User::create([
            ...$data,
            'username' => Str::lower(trim($data['username'])),
            'password' => Hash::make($data['password']),
            'role' => Role::Staff,
            'is_active' => true,
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'created',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'description' => 'Account self-registered as '.$user->username,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        app(FavouriteService::class)->mergeOnLogin($user->id);

        return redirect()->route('admin.dashboard')->with('status', 'Welcome aboard, '.$user->name.'!');
    }

    /**
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    private function usernameRules(): array
    {
        return [
            'required',
            'string',
            'min:3',
            'max:40',
            'regex:/^[A-Za-z0-9._-]+$/',
            Rule::unique('users', 'username'),
        ];
    }
}
