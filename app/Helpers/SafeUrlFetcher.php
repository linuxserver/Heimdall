<?php

namespace App\Helpers;

use App\Exceptions\BlockedUrlException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

/**
 * Fetches caller-supplied URLs server-side without letting them reach
 * internal services.
 *
 * Every hop, including each redirect target, goes through the same guard:
 * the scheme must be http(s), the host must resolve, and every resolved
 * address must be public. The resolved address is pinned via CURLOPT_RESOLVE
 * so the connection goes to the address that was checked. Redirects are not
 * delegated to Guzzle; they are followed here one at a time so the guard
 * runs again for each Location.
 *
 * Set ALLOW_INTERNAL_REQUESTS=true to disable the address check for installs
 * that deliberately point Heimdall at LAN services.
 */
class SafeUrlFetcher
{
    public const MAX_REDIRECTS = 5;

    private ?HandlerStack $handler;

    public function __construct(?HandlerStack $handler = null)
    {
        $this->handler = $handler;
    }

    /**
     * Fetch $url with GET, following up to MAX_REDIRECTS redirects and
     * re-checking each target. Returns null on a transport failure.
     *
     * @throws BlockedUrlException when the URL or any redirect target is refused
     */
    public function fetch(string $url, array $clientOptions = [], array $requestOptions = []): ?ResponseInterface
    {
        $clientOptions = array_merge([
            'http_errors' => false,
            'timeout' => 15,
            'connect_timeout' => 15,
            'verify' => false,
        ], $clientOptions);

        // Redirects are handled below so every hop is checked.
        $clientOptions['allow_redirects'] = false;

        if ($this->handler !== null) {
            $clientOptions['handler'] = $this->handler;
        }

        $current = $url;

        for ($hop = 0; $hop <= self::MAX_REDIRECTS; $hop++) {
            $resolved = $this->assertAllowed($current);

            $options = $clientOptions;
            if ($resolved['ip'] !== null) {
                $options['curl'][CURLOPT_RESOLVE] = [
                    sprintf('%s:%d:%s', $resolved['host'], $resolved['port'], $resolved['ip']),
                ];
            }

            try {
                $response = (new Client($options))->request('GET', $current, $requestOptions);
            } catch (ConnectException $e) {
                Log::warning('Outbound request failed to connect.', ['url' => $current, 'error' => $e->getMessage()]);
                return null;
            } catch (GuzzleException $e) {
                Log::error('Outbound request failed: ' . $e->getMessage(), ['url' => $current]);
                return null;
            }

            $location = $this->redirectTarget($current, $response);
            if ($location === null) {
                return $response;
            }

            $current = $location;
        }

        throw new BlockedUrlException('Too many redirects while fetching ' . $url);
    }

    /**
     * Validate a URL against the guard and resolve its host.
     *
     * @return array{host: string, port: int, ip: string|null} ip is null when
     *         internal requests are allowed and pinning is skipped
     * @throws BlockedUrlException
     */
    public function assertAllowed(string $url): array
    {
        $parts = parse_url($url);
        if ($parts === false) {
            throw new BlockedUrlException('URL could not be parsed.');
        }

        $scheme = strtolower($parts['scheme'] ?? '');
        if (!in_array($scheme, ['http', 'https'], true)) {
            throw new BlockedUrlException('Only http and https URLs can be fetched.');
        }

        $host = $parts['host'] ?? '';
        if ($host === '') {
            throw new BlockedUrlException('URL has no host.');
        }

        // IPv6 literals arrive bracketed from parse_url.
        $host = trim($host, '[]');
        $port = (int) ($parts['port'] ?? ($scheme === 'https' ? 443 : 80));

        if (config('app.allow_internal_requests', false)) {
            return ['host' => $host, 'port' => $port, 'ip' => null];
        }

        $ips = $this->resolve($host);
        if ($ips === []) {
            throw new BlockedUrlException('Host could not be resolved: ' . $host);
        }

        foreach ($ips as $ip) {
            if (!self::isPublicIp($ip)) {
                Log::warning('Blocked access to private or reserved IPs.', ['ip' => $ip, 'host' => $host]);
                throw new BlockedUrlException('Access to private or reserved IPs is not allowed.');
            }
        }

        return ['host' => $host, 'port' => $port, 'ip' => $ips[0]];
    }

    /**
     * True when the address is neither private (RFC 1918, ULA) nor reserved
     * (loopback, link-local, unspecified, ...). IPv4-mapped IPv6 addresses
     * are checked as their embedded IPv4 address.
     */
    public static function isPublicIp(string $ip): bool
    {
        if (str_contains($ip, ':')) {
            $packed = @inet_pton($ip);
            if ($packed === false) {
                return false;
            }
            if (substr($packed, 0, 12) === "\0\0\0\0\0\0\0\0\0\0\xff\xff") {
                $ip = inet_ntop(substr($packed, 12));
            }
        }

        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }

    /**
     * All A and AAAA addresses for a host, or the host itself when it is
     * already an IP literal.
     *
     * @return string[]
     */
    private function resolve(string $host): array
    {
        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            return [$host];
        }

        $records = @dns_get_record($host, DNS_A + DNS_AAAA) ?: [];
        $ips = [];
        foreach ($records as $record) {
            $ip = $record['ip'] ?? $record['ipv6'] ?? null;
            if ($ip !== null) {
                $ips[] = $ip;
            }
        }

        if ($ips === []) {
            // dns_get_record can come back empty on hosts resolved only via
            // /etc/hosts; fall back to the resolver library.
            $fallback = gethostbyname($host);
            if ($fallback !== $host) {
                $ips[] = $fallback;
            }
        }

        return array_values(array_unique($ips));
    }

    /**
     * Absolute redirect target for a 3xx response, or null when the response
     * is not a redirect.
     */
    private function redirectTarget(string $current, ResponseInterface $response): ?string
    {
        if (!in_array($response->getStatusCode(), [301, 302, 303, 307, 308], true)) {
            return null;
        }

        $location = $response->getHeaderLine('Location');
        if ($location === '') {
            return null;
        }

        return (string) UriResolver::resolve(new Uri($current), new Uri($location));
    }
}
