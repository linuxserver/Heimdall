<?php

namespace Tests\Unit;

use App\Exceptions\BlockedUrlException;
use App\Helpers\SafeUrlFetcher;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SafeUrlFetcherTest extends TestCase
{
    private function fetcher(MockHandler $mock): SafeUrlFetcher
    {
        return new SafeUrlFetcher(HandlerStack::create($mock));
    }

    public static function privateAddresses(): array
    {
        return [
            'loopback' => ['127.0.0.1'],
            'loopback high' => ['127.255.255.254'],
            'rfc1918 10/8' => ['10.0.0.1'],
            'rfc1918 172.16/12' => ['172.16.5.5'],
            'rfc1918 192.168/16' => ['192.168.1.10'],
            'link-local metadata' => ['169.254.169.254'],
            'unspecified' => ['0.0.0.0'],
            'ipv6 loopback' => ['::1'],
            'ipv6 unspecified' => ['::'],
            'ipv6 link-local' => ['fe80::1'],
            'ipv6 ula' => ['fd00::1'],
            'ipv4-mapped loopback' => ['::ffff:127.0.0.1'],
            'ipv4-mapped rfc1918' => ['::ffff:192.168.0.1'],
            'ipv4-mapped hex form' => ['::ffff:7f00:1'],
            'not an ip' => ['localhost'],
        ];
    }

    #[DataProvider('privateAddresses')]
    public function test_private_and_reserved_addresses_are_not_public(string $ip): void
    {
        $this->assertFalse(SafeUrlFetcher::isPublicIp($ip), $ip);
    }

    public static function publicAddresses(): array
    {
        return [
            ['8.8.8.8'],
            ['93.184.216.34'],
            ['2606:4700:4700::1111'],
            ['::ffff:8.8.8.8'],
        ];
    }

    #[DataProvider('publicAddresses')]
    public function test_public_addresses_are_public(string $ip): void
    {
        $this->assertTrue(SafeUrlFetcher::isPublicIp($ip), $ip);
    }

    public function test_rejects_non_http_schemes(): void
    {
        $this->expectException(BlockedUrlException::class);
        $this->fetcher(new MockHandler())->assertAllowed('ftp://93.184.216.34/file');
    }

    public function test_rejects_private_host(): void
    {
        $this->expectException(BlockedUrlException::class);
        $this->fetcher(new MockHandler())->assertAllowed('http://169.254.169.254/latest/meta-data/');
    }

    public function test_rejects_bracketed_ipv6_loopback(): void
    {
        $this->expectException(BlockedUrlException::class);
        $this->fetcher(new MockHandler())->assertAllowed('http://[::1]:8080/');
    }

    public function test_pins_the_url_port(): void
    {
        $resolved = $this->fetcher(new MockHandler())->assertAllowed('https://93.184.216.34:8443/x');

        $this->assertSame(['host' => '93.184.216.34', 'port' => 8443, 'ip' => '93.184.216.34'], $resolved);
    }

    public function test_allow_internal_requests_skips_the_address_check(): void
    {
        config(['app.allow_internal_requests' => true]);

        $resolved = $this->fetcher(new MockHandler())->assertAllowed('http://192.168.1.10:8080/favicon.png');

        $this->assertSame(['host' => '192.168.1.10', 'port' => 8080, 'ip' => null], $resolved);
    }

    public function test_fetches_a_public_url(): void
    {
        $mock = new MockHandler([new Response(200, [], 'hello')]);

        $response = $this->fetcher($mock)->fetch('http://93.184.216.34/');

        $this->assertSame('hello', (string) $response->getBody());
        $this->assertSame('http://93.184.216.34/', (string) $mock->getLastRequest()->getUri());
    }

    public function test_follows_a_redirect_to_a_public_address(): void
    {
        $mock = new MockHandler([
            new Response(301, ['Location' => 'https://93.184.216.34/moved']),
            new Response(200, [], 'moved body'),
        ]);

        $response = $this->fetcher($mock)->fetch('http://93.184.216.34/');

        $this->assertSame('moved body', (string) $response->getBody());
        $this->assertSame('https://93.184.216.34/moved', (string) $mock->getLastRequest()->getUri());
        $this->assertCount(0, $mock);
    }

    public function test_resolves_a_relative_redirect(): void
    {
        $mock = new MockHandler([
            new Response(302, ['Location' => '/login']),
            new Response(200, [], 'login'),
        ]);

        $this->fetcher($mock)->fetch('http://93.184.216.34/app/');

        $this->assertSame('http://93.184.216.34/login', (string) $mock->getLastRequest()->getUri());
    }

    public function test_blocks_a_redirect_to_a_private_address(): void
    {
        $mock = new MockHandler([
            new Response(302, ['Location' => 'http://127.0.0.1:8080/internal']),
            new Response(200, [], 'internal body'),
        ]);

        try {
            $this->fetcher($mock)->fetch('http://93.184.216.34/');
            $this->fail('Expected BlockedUrlException');
        } catch (BlockedUrlException $e) {
            // The internal request must never have been sent.
            $this->assertCount(1, $mock);
            $this->assertSame('http://93.184.216.34/', (string) $mock->getLastRequest()->getUri());
        }
    }

    public function test_blocks_a_redirect_to_a_non_http_scheme(): void
    {
        $mock = new MockHandler([
            new Response(302, ['Location' => 'file:///etc/passwd']),
        ]);

        $this->expectException(BlockedUrlException::class);
        $this->fetcher($mock)->fetch('http://93.184.216.34/');
    }

    public function test_blocks_after_too_many_redirects(): void
    {
        $responses = [];
        for ($i = 0; $i <= SafeUrlFetcher::MAX_REDIRECTS + 1; $i++) {
            $responses[] = new Response(302, ['Location' => 'http://93.184.216.34/' . $i]);
        }
        $mock = new MockHandler($responses);

        $this->expectException(BlockedUrlException::class);
        $this->fetcher($mock)->fetch('http://93.184.216.34/');
    }

    public function test_redirect_without_location_is_returned_as_is(): void
    {
        $mock = new MockHandler([new Response(302, [], 'no location')]);

        $response = $this->fetcher($mock)->fetch('http://93.184.216.34/');

        $this->assertSame(302, $response->getStatusCode());
    }
}
