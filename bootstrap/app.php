<?php

use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'active' => EnsureUserIsActive::class,
        ]);

        // Guests are sent to the sign-in page for whichever area they were
        // trying to reach, so /admin/... opens the admin door and /staff/... the
        // staff one, rather than dropping everybody on the generic page.
        $middleware->redirectGuestsTo(function (Request $request) {
            // Both the bare area and everything under it, so a deep link like
            // /admin/products still opens the admin door rather than the
            // generic one.
            if ($request->is('admin', 'admin/*')) {
                return route('admin.login.php');
            }

            if ($request->is('staff', 'staff/*')) {
                return route('staff.login.php');
            }

            return route('login');
        });

        // Somebody already signed in never sees a sign-in page: they go to the page
        // that belongs to their role.
        $middleware->redirectUsersTo(fn ($request) => $request->user()?->landingUrl()
            ?? route('login'));

        // Payment callbacks arrive from the provider, not the browser, and are
        // authenticated by the provider's HMAC signature instead of a CSRF token.
        $middleware->validateCsrfTokens(except: [
            'payments/callback/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
