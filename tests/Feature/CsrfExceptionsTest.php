<?php

namespace Tests\Feature;

use App\Http\Controllers\ItemController;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Route;
use ReflectionClass;
use Tests\TestCase;

/**
 * Guards the CSRF configuration wired up in bootstrap/app.php.
 *
 * Laravel's request-forgery middleware self-disables while running unit tests,
 * so we cannot observe CSRF at request time. Instead we assert the *configuration*
 * directly: the three excluded URIs really are registered on the framework's
 * PreventRequestForgery middleware, and the routes those exceptions cover still
 * resolve to the expected controller actions. Either check would fail if a
 * framework upgrade renamed / deprecated the exception API (validateCsrfTokens()
 * now proxies preventRequestForgery()) or changed how the except list is stored,
 * or if the AJAX routes were dropped.
 */
class CsrfExceptionsTest extends TestCase
{
    /**
     * @return string[]
     */
    private function csrfExceptUris(): array
    {
        // The except list is stored in the protected static $neverVerify
        // property that Middleware::validateCsrfTokens(except: [...]) feeds.
        $reflection = new ReflectionClass(PreventRequestForgery::class);
        $property = $reflection->getProperty('neverVerify');
        $property->setAccessible(true);

        return (array) $property->getValue();
    }

    public function test_ajax_routes_are_registered_as_csrf_exceptions(): void
    {
        $except = $this->csrfExceptUris();

        $this->assertContains('order', $except);
        $this->assertContains('appload', $except);
        $this->assertContains('test_config', $except);
    }

    public function test_csrf_excepted_routes_resolve_to_the_expected_actions(): void
    {
        $expected = [
            'items.order' => ['order', 'setOrder'],
            'appload' => ['appload', 'appload'],
            'test_config' => ['test_config', 'testConfig'],
        ];

        foreach ($expected as $name => [$uri, $method]) {
            $route = Route::getRoutes()->getByName($name);

            $this->assertNotNull($route, "Route [{$name}] is not registered.");
            $this->assertSame($uri, $route->uri());
            $this->assertContains('POST', $route->methods());
            $this->assertSame(ItemController::class . '@' . $method, $route->getActionName());
        }
    }
}
