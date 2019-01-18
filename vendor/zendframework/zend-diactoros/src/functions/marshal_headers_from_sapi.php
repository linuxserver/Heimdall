<?php
/**
 * @see       https://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Diactoros;

use function array_key_exists;
use function strpos;
use function strtolower;
use function strtr;
use function substr;

/**
 * @param array $server Values obtained from the SAPI (generally `$_SERVER`).
 * @return array Header/value pairs
 */
function marshalHeadersFromSapi(array $server)
{
    $headers = [];
    foreach ($server as $key => $value) {
        // Apache prefixes environment variables with REDIRECT_
        // if they are added by rewrite rules
        if (strpos($key, 'REDIRECT_') === 0) {
            $key = substr($key, 9);

            // We will not overwrite existing variables with the
            // prefixed versions, though
            if (array_key_exists($key, $server)) {
                continue;
            }
        }

        if ($value && strpos($key, 'HTTP_') === 0) {
            $name = strtr(strtolower(substr($key, 5)), '_', '-');
            $headers[$name] = $value;
            continue;
        }

        if ($value && strpos($key, 'CONTENT_') === 0) {
            $name = 'content-' . strtolower(substr($key, 8));
            $headers[$name] = $value;
            continue;
        }
    }

    return $headers;
}
