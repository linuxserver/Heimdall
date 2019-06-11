<?php
/**
 * @see       https://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Diactoros;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createResponse(int $code = 200, string $reasonPhrase = '') : ResponseInterface
    {
        return (new Response())
            ->withStatus($code, $reasonPhrase);
    }
}
