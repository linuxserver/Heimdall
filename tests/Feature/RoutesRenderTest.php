<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Broad "the app still boots and renders on this Laravel version" safety net.
 *
 * Beyond the focused per-feature tests, this walks the main GET surface of the
 * app in one place and asserts every route returns its expected status with no
 * exception. A framework/PHP upgrade that broke view rendering, routing, the
 * auth scaffolding or the auth middleware would surface here as a 500 / wrong
 * status even if a more specific test was missing.
 */
class RoutesRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_core_get_routes_boot_without_error(): void
    {
        $this->seed();

        $routes = [
            '/' => 200,
            '/login' => 200,
            '/userselect' => 200,
            '/settings' => 200,
            '/items' => 200,
            '/items/create' => 200,
            '/tags' => 200,
            '/health' => 200,
            '/up' => 200,
        ];

        foreach ($routes as $uri => $expectedStatus) {
            $response = $this->get($uri);

            $this->assertSame(
                $expectedStatus,
                $response->getStatusCode(),
                "GET {$uri} returned {$response->getStatusCode()}, expected {$expectedStatus}."
            );
        }
    }

    public function test_home_redirects_guests_to_login(): void
    {
        $this->seed();

        // /home is behind the auth middleware; a guest must be redirected to
        // the login route (redirectGuestsTo in bootstrap/app.php).
        $response = $this->get('/home');

        $response->assertRedirect(route('login'));
    }

    public function test_search_redirects_to_the_provider(): void
    {
        $this->seed();

        $response = $this->get('/search?provider=google&q=heimdall');

        $response->assertStatus(302);
    }
}
