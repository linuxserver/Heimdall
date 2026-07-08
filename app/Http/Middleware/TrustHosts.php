<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * Get the host patterns that should be trusted.
     *
     * The allow-list is read from the TRUSTED_HOSTS env var (comma-separated
     * hostnames). When it is unset/empty an empty array is returned so that NO
     * host restriction is applied, preserving Heimdall's historic behaviour of
     * running on arbitrary hosts. When set, only the listed hosts (and their
     * subdomains) are accepted; any other Host header is rejected by Symfony
     * with a SuspiciousOperationException (HTTP 400).
     *
     * @return array
     */
    public function hosts()
    {
        $trustedHosts = env('TRUSTED_HOSTS');

        if ($trustedHosts === null || trim((string) $trustedHosts) === '') {
            return [];
        }

        $hosts = [];

        foreach (explode(',', (string) $trustedHosts) as $host) {
            $host = trim($host);

            if ($host !== '') {
                $hosts[] = '^(.+\.)?'.preg_quote($host).'$';
            }
        }

        return $hosts;
    }
}
