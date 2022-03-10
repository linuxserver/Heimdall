<?php

namespace Http\Client\Common\Plugin\Cache\Generator;

use Psr\Http\Message\RequestInterface;

/**
 * An interface for generate a cache key.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
interface CacheKeyGenerator
{
    /**
     * Generate a cache key from a Request.
     *
     * @return string
     */
    public function generate(RequestInterface $request);
}
