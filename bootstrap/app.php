<?php

use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\SecureSessionCookie;
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

        // Whether a connection is encrypted is a property of the request, not
        // something that can be settled once at boot: the same deployment is
        // reached over HTTPS from the internet and over plain HTTP from a probe
        // inside the platform's own network. Marking the cookie per request is
        // correct for real users and harmless for the probe.
        $middleware->append(SecureSessionCookie::class);

        // Behind a proxy — Railway in production — the browser speaks HTTPS to
        // the edge and the edge speaks plain HTTP to this application. Laravel
        // only believes the X-Forwarded-Proto header for a proxy it trusts, so
        // without this it believes every request arrived insecurely and builds
        // http:// redirects, which bounce off the secure origin and lose the
        // session on the way back.
        //
        // Every address is trusted because the edge is the only way in: this
        // application is not published to the internet directly. Nothing
        // security-relevant rests on it either — AppServiceProvider already
        // forces https on every generated URL, so a spoofed header cannot make
        // the app emit an http:// link even if it claimed to.
        $middleware->trustProxies(at: '*', headers: Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_AWS_ELB
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
