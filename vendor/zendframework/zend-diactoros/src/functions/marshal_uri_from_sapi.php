<?php
/**
 * @see       https://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Diactoros;

use function array_change_key_case;
use function array_key_exists;
use function explode;
use function implode;
use function is_array;
use function ltrim;
use function preg_match;
use function preg_replace;
use function strlen;
use function strpos;
use function strtolower;
use function substr;

/**
 * Marshal a Uri instance based on the values presnt in the $_SERVER array and headers.
 *
 * @param array $server SAPI parameters
 * @param array $headers HTTP request headers
 * @return Uri
 */
function marshalUriFromSapi(array $server, array $headers)
{
    /**
     * Retrieve a header value from an array of headers using a case-insensitive lookup.
     *
     * @param string $name
     * @param array $headers Key/value header pairs
     * @param mixed $default Default value to return if header not found
     * @return mixed
     */
    $getHeaderFromArray = function ($name, array $headers, $default = null) {
        $header  = strtolower($name);
        $headers = array_change_key_case($headers, CASE_LOWER);
        if (array_key_exists($header, $headers)) {
            $value = is_array($headers[$header]) ? implode(', ', $headers[$header]) : $headers[$header];
            return $value;
        }

        return $default;
    };

    /**
     * Marshal the host and port from HTTP headers and/or the PHP environment.
     *
     * @param array $headers
     * @param array $server
     * @return array Array of two items, host and port, in that order (can be
     *     passed to a list() operation).
     */
    $marshalHostAndPort = function (array $headers, array $server) use ($getHeaderFromArray) {
        /**
        * @param string|array $host
        * @return array Array of two items, host and port, in that order (can be
        *     passed to a list() operation).
        */
        $marshalHostAndPortFromHeader = function ($host) {
            if (is_array($host)) {
                $host = implode(', ', $host);
            }

            $port = null;

            // works for regname, IPv4 & IPv6
            if (preg_match('|\:(\d+)$|', $host, $matches)) {
                $host = substr($host, 0, -1 * (strlen($matches[1]) + 1));
                $port = (int) $matches[1];
            }

            return [$host, $port];
        };

        /**
        * @param array $server
        * @param string $host
        * @param null|int $port
        * @return array Array of two items, host and port, in that order (can be
        *     passed to a list() operation).
        */
        $marshalIpv6HostAndPort = function (array $server, $host, $port) {
            $host = '[' . $server['SERVER_ADDR'] . ']';
            $port = $port ?: 80;
            if ($port . ']' === substr($host, strrpos($host, ':') + 1)) {
                // The last digit of the IPv6-Address has been taken as port
                // Unset the port so the default port can be used
                $port = null;
            }
            return [$host, $port];
        };

        static $defaults = ['', null];

        if ($getHeaderFromArray('host', $headers, false)) {
            return $marshalHostAndPortFromHeader($getHeaderFromArray('host', $headers));
        }

        if (! isset($server['SERVER_NAME'])) {
            return $defaults;
        }

        $host = $server['SERVER_NAME'];
        $port = isset($server['SERVER_PORT']) ? (int) $server['SERVER_PORT'] : null;

        if (! isset($server['SERVER_ADDR'])
            || ! preg_match('/^\[[0-9a-fA-F\:]+\]$/', $host)
        ) {
            return [$host, $port];
        }

        // Misinterpreted IPv6-Address
        // Reported for Safari on Windows
        return $marshalIpv6HostAndPort($server, $host, $port);
    };

    /**
     * Detect the path for the request
     *
     * Looks at a variety of criteria in order to attempt to autodetect the base
     * request path, including:
     *
     * - IIS7 UrlRewrite environment
     * - REQUEST_URI
     * - ORIG_PATH_INFO
     *
     * From ZF2's Zend\Http\PhpEnvironment\Request class
     * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
     * @license   http://framework.zend.com/license/new-bsd New BSD License
     *
     * @param array $server SAPI environment array (typically `$_SERVER`)
     * @return string Discovered path
     */
    $marshalRequestPath = function (array $server) {
        // IIS7 with URL Rewrite: make sure we get the unencoded url
        // (double slash problem).
        $iisUrlRewritten = array_key_exists('IIS_WasUrlRewritten', $server) ? $server['IIS_WasUrlRewritten'] : null;
        $unencodedUrl    = array_key_exists('UNENCODED_URL', $server) ? $server['UNENCODED_URL'] : '';
        if ('1' === $iisUrlRewritten && ! empty($unencodedUrl)) {
            return $unencodedUrl;
        }

        $requestUri = array_key_exists('REQUEST_URI', $server) ? $server['REQUEST_URI'] : null;

        if ($requestUri !== null) {
            return preg_replace('#^[^/:]+://[^/]+#', '', $requestUri);
        }

        $origPathInfo = array_key_exists('ORIG_PATH_INFO', $server) ? $server['ORIG_PATH_INFO'] : null;
        if (empty($origPathInfo)) {
            return '/';
        }

        return $origPathInfo;
    };

    $uri = new Uri('');

    // URI scheme
    $scheme = 'http';
    if (array_key_exists('HTTPS', $server)) {
        $https = $server['HTTPS'];
    } elseif (array_key_exists('https', $server)) {
        $https = $server['https'];
    } else {
        $https = false;
    }
    if (($https && 'off' !== strtolower($https))
        || strtolower($getHeaderFromArray('x-forwarded-proto', $headers, false)) === 'https'
    ) {
        $scheme = 'https';
    }
    $uri = $uri->withScheme($scheme);

    // Set the host
    list($host, $port) = $marshalHostAndPort($headers, $server);
    if (! empty($host)) {
        $uri = $uri->withHost($host);
        if (! empty($port)) {
            $uri = $uri->withPort($port);
        }
    }

    // URI path
    $path = $marshalRequestPath($server);

    // Strip query string
    $path = explode('?', $path, 2)[0];

    // URI query
    $query = '';
    if (isset($server['QUERY_STRING'])) {
        $query = ltrim($server['QUERY_STRING'], '?');
    }

    // URI fragment
    $fragment = '';
    if (strpos($path, '#') !== false) {
        list($path, $fragment) = explode('#', $path, 2);
    }

    return $uri
        ->withPath($path)
        ->withFragment($fragment)
        ->withQuery($query);
}
