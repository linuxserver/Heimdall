<?php
/**
 * @see       https://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Diactoros;

/**
 * Retrieve the request method from the SAPI parameters.
 */
function marshalMethodFromSapi(array $server) : string
{
    return isset($server['REQUEST_METHOD']) ? $server['REQUEST_METHOD'] : 'GET';
}
