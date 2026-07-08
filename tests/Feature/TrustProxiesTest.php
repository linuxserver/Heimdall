<?php

namespace Tests\Feature;

use App\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Tests\TestCase;

class TrustProxiesTest extends TestCase
{
    /**
     * Remove any TRUSTED_PROXIES override and reset Symfony's static trusted
     * proxy/host state so tests do not leak into one another.
     */
    protected function tearDown(): void
    {
        putenv('TRUSTED_PROXIES');
        unset($_ENV['TRUSTED_PROXIES'], $_SERVER['TRUSTED_PROXIES']);

        Request::setTrustedProxies([], Request::HEADER_X_FORWARDED_FOR);
        Request::setTrustedHosts([]);

        parent::tearDown();
    }

    private function setTrustedProxiesEnv(string $value): void
    {
        putenv('TRUSTED_PROXIES='.$value);
        $_ENV['TRUSTED_PROXIES'] = $value;
        $_SERVER['TRUSTED_PROXIES'] = $value;
    }

    private function readProtected(object $object, string $property): mixed
    {
        return (fn () => $this->{$property})->call($object);
    }

    public function test_x_forwarded_host_header_is_ignored(): void
    {
        $request = Request::create('http://localhost/', 'GET');
        $request->server->set('REMOTE_ADDR', '10.0.0.1');
        $request->headers->set('X-Forwarded-Host', 'evil.com');
        $request->server->set('HTTP_X_FORWARDED_HOST', 'evil.com');

        (new TrustProxies())->handle($request, fn ($req) => $req);

        $this->assertSame('localhost', $request->getHost());
        $this->assertNotSame('evil.com', $request->getHost());
    }

    public function test_headers_bitmask_excludes_forwarded_host(): void
    {
        $headers = $this->readProtected(new TrustProxies(), 'headers');

        $this->assertSame(0, $headers & Request::HEADER_X_FORWARDED_HOST);
        $this->assertNotSame(0, $headers & Request::HEADER_X_FORWARDED_FOR);
        $this->assertNotSame(0, $headers & Request::HEADER_X_FORWARDED_PORT);
        $this->assertNotSame(0, $headers & Request::HEADER_X_FORWARDED_PROTO);
        $this->assertNotSame(0, $headers & Request::HEADER_X_FORWARDED_AWS_ELB);
    }

    public function test_default_trusted_proxies_when_env_unset(): void
    {
        putenv('TRUSTED_PROXIES');
        unset($_ENV['TRUSTED_PROXIES'], $_SERVER['TRUSTED_PROXIES']);

        $proxies = $this->readProtected(new TrustProxies(), 'proxies');

        $this->assertSame(
            ['192.168.0.0/16', '172.16.0.0/12', '10.0.0.0/8', '127.0.0.1'],
            $proxies
        );
    }

    public function test_trusted_proxies_can_be_configured_via_env(): void
    {
        $this->setTrustedProxiesEnv('203.0.113.5, 198.51.100.0/24');

        $proxies = $this->readProtected(new TrustProxies(), 'proxies');

        $this->assertSame(['203.0.113.5', '198.51.100.0/24'], $proxies);
    }

    public function test_trusted_proxies_supports_wildcard(): void
    {
        $this->setTrustedProxiesEnv('*');

        $proxies = $this->readProtected(new TrustProxies(), 'proxies');

        $this->assertSame('*', $proxies);
    }

    public function test_wildcard_proxy_trusts_calling_ip_for_forwarded_headers(): void
    {
        $this->setTrustedProxiesEnv('*');

        $request = Request::create('http://localhost/', 'GET');
        $request->server->set('REMOTE_ADDR', '203.0.113.9');
        $request->headers->set('X-Forwarded-Proto', 'https');
        $request->server->set('HTTP_X_FORWARDED_PROTO', 'https');

        (new TrustProxies())->handle($request, fn ($req) => $req);

        // Proto is honored (proxy trusted) but host is still not taken from headers.
        $this->assertTrue($request->isSecure());
        $this->assertSame('localhost', $request->getHost());
    }
}
