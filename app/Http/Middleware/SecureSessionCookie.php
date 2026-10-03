<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Marks the session cookie as secure on any request that really arrived over
 * HTTPS.
 *
 * Whether a connection is encrypted is a property of the request, not something
 * that can be settled once at boot: the same deployment may be reached over
 * HTTPS from the public internet and over plain HTTP from a health check inside
 * the platform's own network. Deciding per request means the cookie is marked
 * secure for real users and left alone for an internal probe, so marking it
 * unconditionally would only make the app look broken rather than safer.
 *
 * Trusting the request here also depends on TrustProxies having run first, so
 * that a request forwarded by Railway's edge is recognised as HTTPS rather than
 * as plain HTTP with a header claiming otherwise.
 */
class SecureSessionCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        config(['session.secure' => $request->isSecure()]);

        return $next($request);
    }
}
