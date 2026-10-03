<?php

namespace Tests\Feature;

use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * Behind Railway's edge.
 *
 * The browser speaks HTTPS to the proxy and the proxy speaks plain HTTP to the
 * application. If the application does not believe the proxy, it believes every
 * request arrived insecurely and builds http:// links, and the session cookie
 * goes out without the secure flag that is the whole point of signing in over
 * HTTPS.
 *
 * These run as production, because that is the only environment where the
 * application is behind an edge: forcing https there is the whole point, and
 * doing it locally would rewrite a redirect to https://localhost, where nothing
 * is listening for TLS.
 */
class TrustedProxyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The site's own address is what decides whether https is forced, so that
        // is what is set here. The provider is then booted by hand, because boot()
        // has already run once by the time setUp() is entered — and re-booting is
        // also how the forcing is undone after the class finishes.
        $this->app['config']->set('app.url', 'https://ocproject-production.up.railway.app');

        (new AppServiceProvider($this->app))->boot();

        $this->assertSame(
            'https',
            parse_url($this->app['url']->to('/catalog'), PHP_URL_SCHEME),
            'this test is only meaningful with https forced on'
        );
    }

    protected function tearDown(): void
    {
        // The scheme is held statically on the URL generator, so it has to be put
        // back or every later test in the run inherits it.
        URL::forceScheme(null);

        parent::tearDown();
    }

    /**
     * A header as the proxy would send it.
     *
     * @return array<string, string>
     */
    private function header(string $name, string $value): array
    {
        return ['HTTP_'.str_replace('-', '_', $name) => $value];
    }

    /**
     * A request that genuinely arrives over plain HTTP, which is what the edge
     * forwards.
     */
    private function edgeRequest(string $method, string $uri, array $data = []): TestResponse
    {
        // The host is spelled out rather than built from app.url, because the
        // request has to arrive over plain http while the application still
        // believes it is serving https.
        $url = 'http://localhost:8000'.parse_url($uri, PHP_URL_PATH);

        return $method === 'POST' ? $this->post($url, $data) : $this->get($url);
    }

    public function test_a_forwarded_https_request_is_recognised_as_secure(): void
    {
        $response = $this->withHeaders($this->header('X-Forwarded-Proto', 'https'))
            ->get('/catalog');

        $response->assertOk();
        $this->assertTrue(
            $response->baseRequest->isSecure(),
            'the forwarded request should be recognised as HTTPS'
        );
    }

    public function test_the_edge_itself_forwards_plain_http(): void
    {
        // What the edge actually sends when the browser used https: the hop to
        // this application is not encrypted, which is why the header matters.
        $response = $this->edgeRequest('GET', '/catalog');

        $response->assertOk();
        $this->assertFalse(
            $response->baseRequest->isSecure(),
            'a hop with no forwarded header is plain HTTP'
        );
    }

    public function test_a_request_claiming_plain_http_still_gets_https_links(): void
    {
        // The forwarded header is believed for the request's own scheme, but
        // every URL generated is forced to https regardless. So even a header
        // claiming plain HTTP cannot make the app hand out an http:// link.
        $html = $this->withHeaders($this->header('X-Forwarded-Proto', 'http'))
            ->get('/catalog')
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('http://', $html);
    }

    public function test_a_forged_forwarded_header_cannot_produce_an_http_redirect(): void
    {
        $response = $this->withHeaders($this->header('X-Forwarded-Proto', 'http'))
            ->post('/login', [
                'email' => 'nobody@goldengate.test',
                'password' => 'wrong',
            ]);

        $this->assertTrue($response->isRedirect());

        // An http:// Location would take the browser off the secure origin and
        // bounce back through the edge, losing the session on the way.
        $this->assertStringStartsWith(
            'https://',
            (string) $response->headers->get('Location'),
            'the redirect after sign-in must stay on https'
        );
    }

    public function test_the_session_cookie_is_secure_when_the_browser_used_https(): void
    {
        $response = $this->withHeaders($this->header('X-Forwarded-Proto', 'https'))
            ->post('/login', [
                'email' => 'nobody@goldengate.test',
                'password' => 'wrong',
            ]);

        $cookies = collect($response->headers->getCookies())
            ->filter(fn ($cookie) => str_contains((string) $cookie->getName(), config('session.cookie')));

        $this->assertNotEmpty($cookies, 'expected a session cookie: the error is flashed into it');

        foreach ($cookies as $cookie) {
            $this->assertTrue(
                $cookie->isSecure(),
                'the session cookie must be marked secure on an HTTPS request'
            );
        }
    }

    public function test_the_session_cookie_is_left_alone_on_a_plain_hop(): void
    {
        // Marking it secure on the edge's own hop would make the app look
        // broken rather than safer, and buys nothing: the browser never sees
        // this hop.
        $response = $this->edgeRequest('POST', '/login', [
            'email' => 'nobody@goldengate.test',
            'password' => 'wrong',
        ]);

        $cookies = collect($response->headers->getCookies())
            ->filter(fn ($cookie) => str_contains((string) $cookie->getName(), config('session.cookie')));

        $this->assertNotEmpty($cookies);

        foreach ($cookies as $cookie) {
            $this->assertFalse($cookie->isSecure());
        }
    }

    public function test_a_real_sign_in_over_https_gets_a_secure_session(): void
    {
        // End to end: the whole point is that a person signing in over HTTPS
        // gets a cookie the browser will only send back over HTTPS.
        $user = User::factory()->admin()->create();

        $response = $this->withHeaders($this->header('X-Forwarded-Proto', 'https'))
            ->post('/admin/login.php', [
                'email' => $user->email,
                'password' => 'password',
            ]);

        $this->assertTrue($response->isRedirect());
        $this->assertStringStartsWith('https://', (string) $response->headers->get('Location'));

        $session = collect($response->headers->getCookies())
            ->first(fn ($cookie) => str_contains((string) $cookie->getName(), config('session.cookie')));

        $this->assertNotNull($session, 'a successful sign-in must set a session');
        $this->assertTrue($session->isSecure(), 'and it must be marked secure');

        $this->assertAuthenticatedAs($user);
    }

    public function test_the_health_endpoint_still_answers(): void
    {
        // Railway uses this to decide whether the service is up. It arrives
        // through the same edge as everything else.
        $this->withHeaders($this->header('X-Forwarded-Proto', 'https'))
            ->get('/up')
            ->assertOk();
    }
}
