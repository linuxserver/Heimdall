<?php
/**
 * @see       https://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Diactoros;

use function preg_match;

/**
 * Return HTTP protocol version (X.Y) as discovered within a `$_SERVER` array.
 *
 * @throws Exception\UnrecognizedProtocolVersionException if the
 *     $server['SERVER_PROTOCOL'] value is malformed.
 */
function marshalProtocolVersionFromSapi(array $server) : string
{
    if (! isset($server['SERVER_PROTOCOL'])) {
        return '1.1';
    }

    if (! preg_match('#^(HTTP/)?(?P<version>[1-9]\d*(?:\.\d)?)$#', $server['SERVER_PROTOCOL'], $matches)) {
        throw Exception\UnrecognizedProtocolVersionException::forVersion(
            (string) $server['SERVER_PROTOCOL']
        );
    }

    return $matches['version'];
}
