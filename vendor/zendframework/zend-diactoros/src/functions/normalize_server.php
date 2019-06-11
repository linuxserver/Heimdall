<?php
/**
 * @see       https://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Diactoros;

use function is_callable;

/**
 * Marshal the $_SERVER array
 *
 * Pre-processes and returns the $_SERVER superglobal. In particularly, it
 * attempts to detect the Authorization header, which is often not aggregated
 * correctly under various SAPI/httpd combinations.
 *
 * @param null|callable $apacheRequestHeaderCallback Callback that can be used to
 *     retrieve Apache request headers. This defaults to
 *     `apache_request_headers` under the Apache mod_php.
 * @return array Either $server verbatim, or with an added HTTP_AUTHORIZATION header.
 */
function normalizeServer(array $server, callable $apacheRequestHeaderCallback = null) : array
{
    if (null === $apacheRequestHeaderCallback && is_callable('apache_request_headers')) {
        $apacheRequestHeaderCallback = 'apache_request_headers';
    }

    // If the HTTP_AUTHORIZATION value is already set, or the callback is not
    // callable, we return verbatim
    if (isset($server['HTTP_AUTHORIZATION'])
        || ! is_callable($apacheRequestHeaderCallback)
    ) {
        return $server;
    }

    $apacheRequestHeaders = $apacheRequestHeaderCallback();
    if (isset($apacheRequestHeaders['Authorization'])) {
        $server['HTTP_AUTHORIZATION'] = $apacheRequestHeaders['Authorization'];
        return $server;
    }

    if (isset($apacheRequestHeaders['authorization'])) {
        $server['HTTP_AUTHORIZATION'] = $apacheRequestHeaders['authorization'];
        return $server;
    }

    return $server;
}
