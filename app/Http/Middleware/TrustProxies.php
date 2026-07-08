<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The default trusted proxies used when the TRUSTED_PROXIES env var is unset.
     *
     * @var array
     */
    protected $defaultProxies = ['192.168.0.0/16', '172.16.0.0/12', '10.0.0.0/8', '127.0.0.1'];

    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * The current proxy header mappings.
     *
     * Note: Request::HEADER_X_FORWARDED_HOST is intentionally NOT trusted to
     * prevent Host header injection / open redirects (CVE-2025-50578). A spoofed
     * X-Forwarded-Host header must never influence getHost()/url()/asset().
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO | Request::HEADER_X_FORWARDED_AWS_ELB;

    /**
     * Create a new middleware instance.
     *
     * The set of trusted proxies is read from the TRUSTED_PROXIES env var
     * (comma-separated CIDRs/IPs). When unset it falls back to the historic
     * default list. The special value "*" trusts all proxies.
     *
     * @return void
     */
    public function __construct()
    {
        $trustedProxies = env('TRUSTED_PROXIES');

        if ($trustedProxies === null || trim((string) $trustedProxies) === '') {
            $this->proxies = $this->defaultProxies;
        } elseif (trim((string) $trustedProxies) === '*') {
            $this->proxies = '*';
        } else {
            $this->proxies = array_values(array_filter(
                array_map('trim', explode(',', (string) $trustedProxies)),
                fn ($proxy) => $proxy !== ''
            ));
        }
    }
}
