<?php

namespace App\Http\Controllers;

use App\Enums\AccountStatus;
use App\Enums\Role;
use App\Models\ActivityLog;
use App\Models\User;
use App\Notifications\TeamAlertService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Create Account, for somebody who does not have one yet.
 *
 * A registration is always a staff account in a waiting state. It is not a way
 * into the shop: it cannot sign in until an administrator or manager accepts
 * it, and the password is hashed before it is ever stored.
 */
class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'email.unique' => 'You already have an account with that email address.',
            'password.confirmed' => 'The two passwords do not match.',
        ]);

        // Checked here as well as by the rule, so the message can say what to do
        // next rather than just that the field is taken.
        $existing = User::query()
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($data['email'])])
            ->first();

        if ($existing) {
            return redirect()
                ->route('staff.login.php')
                ->with('email', $data['email'])
                ->withErrors([
                    'email' => $existing->isRejected()
                        ? 'That email address has an account which was turned down. Contact an administrator.'
                        : 'You already have an account with that email address. Sign in instead.',
                ]);
        }

        $user = User::create([
            'username' => $this->uniqueUsername($data['email']),
            'name' => $data['name'],
            'email' => $data['email'],
            // Hashed here rather than trusted from the form.
            'password' => Hash::make($data['password']),
            'role' => Role::Staff,
            'is_active' => true,
            'status' => AccountStatus::Pending,
        ]);

        ActivityLog::create([
            'user_id' => null,
            'action' => 'created',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'description' => $user->name.' registered and is waiting to be accepted',
        ]);

        // The same queue the public form reaches, so an administrator knows
        // somebody is waiting without having to be told out of band.
        app(TeamAlertService::class)->accountRegistered($user);

        return redirect()
            ->route('staff.login.php')
            ->with('status', 'Your account is created. It is waiting to be accepted, and you will be able to sign in once it is.');
    }

    /**
     * Sign-in is by email, so the username is only an internal handle. Derive one
     * from the address and keep adding digits until it is free.
     */
    private function uniqueUsername(string $email): string
    {
        $base = Str::of(Str::before($email, '@'))
            ->lower()
            ->replaceMatches('/[^a-z0-9._-]/', '')
            ->limit(30, '')
            ->value();

        $base = $base !== '' ? $base : 'member';

        $username = $base;
        $suffix = 1;

        while (User::query()->where('username', $username)->exists()) {
            $username = $base.'-'.$suffix++;
        }

        return $username;
    }
}
