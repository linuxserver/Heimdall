<?php
/**
 * @see       https://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Diactoros;

use function preg_match_all;
use function urldecode;

/**
 * Parse a cookie header according to RFC 6265.
 *
 * PHP will replace special characters in cookie names, which results in other cookies not being available due to
 * overwriting. Thus, the server request should take the cookies from the request header instead.
 *
 * @param string $cookieHeader A string cookie header value.
 * @return array key/value cookie pairs.
 */
function parseCookieHeader($cookieHeader)
{
    preg_match_all('(
        (?:^\\n?[ \t]*|;[ ])
        (?P<name>[!#$%&\'*+-.0-9A-Z^_`a-z|~]+)
        =
        (?P<DQUOTE>"?)
            (?P<value>[\x21\x23-\x2b\x2d-\x3a\x3c-\x5b\x5d-\x7e]*)
        (?P=DQUOTE)
        (?=\\n?[ \t]*$|;[ ])
    )x', $cookieHeader, $matches, PREG_SET_ORDER);

    $cookies = [];

    foreach ($matches as $match) {
        $cookies[$match['name']] = urldecode($match['value']);
    }

    return $cookies;
}
