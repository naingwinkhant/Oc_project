<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Your account is not active.');
        }

        if ($roles !== [] && ! in_array($user->role->value, $roles, true)) {
            abort(403, 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
