<?php

namespace Tests\Feature;

use App\Http\Middleware\TrustHosts;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Tests\TestCase;

class TrustHostsTest extends TestCase
{
    /**
     * Remove any TRUSTED_HOSTS override and reset Symfony's static trusted host
     * state so tests do not leak into one another.
     */
    protected function tearDown(): void
    {
        putenv('TRUSTED_HOSTS');
        unset($_ENV['TRUSTED_HOSTS'], $_SERVER['TRUSTED_HOSTS']);

        Request::setTrustedHosts([]);

        parent::tearDown();
    }

    private function setTrustedHostsEnv(string $value): void
    {
        putenv('TRUSTED_HOSTS='.$value);
        $_ENV['TRUSTED_HOSTS'] = $value;
        $_SERVER['TRUSTED_HOSTS'] = $value;
    }

    private function makeMiddleware(): TrustHosts
    {
        return $this->app->make(TrustHosts::class);
    }

    public function test_hosts_is_empty_when_env_unset(): void
    {
        putenv('TRUSTED_HOSTS');
        unset($_ENV['TRUSTED_HOSTS'], $_SERVER['TRUSTED_HOSTS']);

        $this->assertSame([], $this->makeMiddleware()->hosts());
    }

    public function test_arbitrary_host_is_accepted_when_env_unset(): void
    {
        putenv('TRUSTED_HOSTS');
        unset($_ENV['TRUSTED_HOSTS'], $_SERVER['TRUSTED_HOSTS']);

        // No trusted host patterns configured -> getHost() must not throw.
        Request::setTrustedHosts(array_filter($this->makeMiddleware()->hosts()));

        $request = Request::create('http://anything.example/', 'GET');

        $this->assertSame('anything.example', $request->getHost());
    }

    public function test_hosts_contains_pattern_matching_configured_host(): void
    {
        $this->setTrustedHostsEnv('example.com');

        $hosts = $this->makeMiddleware()->hosts();

        $this->assertNotEmpty($hosts);
        $this->assertCount(1, $hosts);
        // Symfony wraps each pattern as {pattern}i before matching.
        $this->assertSame(1, preg_match('{'.$hosts[0].'}i', 'example.com'));
        $this->assertSame(0, preg_match('{'.$hosts[0].'}i', 'evil.com'));
    }

    public function test_configured_host_is_accepted_and_others_rejected(): void
    {
        $this->setTrustedHostsEnv('example.com');

        Request::setTrustedHosts($this->makeMiddleware()->hosts());

        $accepted = Request::create('http://example.com/', 'GET');
        $this->assertSame('example.com', $accepted->getHost());

        $this->expectException(SuspiciousOperationException::class);

        Request::create('http://evil.com/', 'GET')->getHost();
    }

    public function test_multiple_hosts_can_be_configured(): void
    {
        $this->setTrustedHostsEnv('example.com, dash.example.org');

        $hosts = $this->makeMiddleware()->hosts();

        $this->assertCount(2, $hosts);

        Request::setTrustedHosts($hosts);

        $this->assertSame('example.com', Request::create('http://example.com/', 'GET')->getHost());
        $this->assertSame('dash.example.org', Request::create('http://dash.example.org/', 'GET')->getHost());
    }

    public function test_custom_trust_hosts_middleware_is_registered_globally(): void
    {
        $globalMiddleware = $this->app->make(Kernel::class)->getGlobalMiddleware();

        $this->assertContains(TrustHosts::class, $globalMiddleware);
        $this->assertNotContains(\Illuminate\Http\Middleware\TrustHosts::class, $globalMiddleware);
    }

    public function test_handle_enforces_trusted_hosts_even_in_local_environment(): void
    {
        // The app runs as APP_ENV=local under the test runner; the parent
        // middleware would skip enforcement entirely. Confirm handle() still
        // applies the allow-list once TRUSTED_HOSTS is configured.
        $this->setTrustedHostsEnv('example.com');

        $request = Request::create('http://example.com/', 'GET');

        $reachedNext = false;
        $this->makeMiddleware()->handle($request, function ($req) use (&$reachedNext) {
            $reachedNext = true;

            return $req;
        });

        $this->assertTrue($reachedNext);

        // The configured host is now accepted and any other Host is rejected.
        $this->assertSame('example.com', Request::create('http://example.com/', 'GET')->getHost());

        $this->expectException(SuspiciousOperationException::class);
        Request::create('http://evil.com/', 'GET')->getHost();
    }

    public function test_handle_does_not_restrict_hosts_when_env_unset(): void
    {
        putenv('TRUSTED_HOSTS');
        unset($_ENV['TRUSTED_HOSTS'], $_SERVER['TRUSTED_HOSTS']);

        $request = Request::create('http://anything.example/', 'GET');

        $this->makeMiddleware()->handle($request, fn ($req) => $req);

        // No allow-list configured -> arbitrary hosts still accepted.
        $this->assertSame('anything.example', Request::create('http://anything.example/', 'GET')->getHost());
    }
}
