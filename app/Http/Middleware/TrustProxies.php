<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * Set TRUSTED_PROXIES in your .env file to the IP address(es) of your reverse proxy.
     * Use '*' to trust all proxies (not recommended for production).
     * Use comma-separated values for multiple proxies (e.g., "192.168.1.10,192.168.1.11").
     *
     * @var array|string|null
     */
    protected $proxies;

    /**
     * The current proxy header mappings.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO | Request::HEADER_X_FORWARDED_AWS_ELB;

    /**
     * Default trusted proxies (private IP ranges for backwards compatibility).
     */
    private const DEFAULT_PROXIES = ['192.168.0.0/16', '172.16.0.0/12', '10.0.0.0/8', '127.0.0.1'];

    /**
     * Bootstrap the middleware.
     */
    public function __construct()
    {
        $proxies = env('TRUSTED_PROXIES');

        if ($proxies === null) {
            // Default to private IP ranges for backwards compatibility
            $this->proxies = self::DEFAULT_PROXIES;
        } elseif ($proxies === '*') {
            $this->proxies = '*';
        } elseif ($proxies === '') {
            // Explicitly set to empty = trust no proxies
            $this->proxies = [];
        } else {
            $this->proxies = array_map('trim', explode(',', $proxies));
        }
    }
}
