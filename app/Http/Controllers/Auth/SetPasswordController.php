<?php

namespace App\Http\Controllers\Auth;

use App\Auth\PasswordLink;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * The step after an account is created: choosing a password.
 *
 * This is the only place a password is ever typed by the person who owns it.
 * The admin never sees or sets one.
 */
class SetPasswordController extends Controller
{
    public function __construct(private readonly PasswordLink $links) {}

    public function create(Request $request): View|RedirectResponse
    {
        $user = $this->links->resolve((string) $request->query('token', ''));

        if (! $user) {
            return redirect()
                ->route('login')
                ->with('error', 'That link has expired or has already been used. Ask for a new one.');
        }

        return view('auth.set-password', [
            'user' => $user,
            'token' => (string) $request->query('token'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'password.confirmed' => 'The two passwords do not match.',
        ]);

        $user = $this->links->resolve($data['token']);

        if (! $user) {
            return redirect()
                ->route('login')
                ->with('error', 'That link has expired or has already been used. Ask for a new one.');
        }

        $this->links->complete($user, $data['token'], $data['password']);

        return redirect()
            ->route($user->isAdmin() ? 'admin.login.php' : 'staff.login.php')
            ->with('status', 'Your password is set. Sign in with it now.');
    }
}
