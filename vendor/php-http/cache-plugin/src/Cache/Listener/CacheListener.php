<?php

namespace Http\Client\Common\Plugin\Cache\Listener;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Cache\CacheItemInterface;

/**
 * Called by the cache plugin with information on the cache status.
 * Provides an opportunity to update the response based on whether the cache was a hit or a miss, or
 * other cache-meta-data.
 *
 * @author Iain Connor <iainconnor@gmail.com>
 */
interface CacheListener
{
    /**
     * Called before the cache plugin returns the response, with information on whether that response came from cache.
     *
     * @param bool                    $fromCache Whether the `$response` was from the cache or not.
     *                                           Note that checking `$cacheItem->isHit()` is not sufficent to determine this.
     * @param CacheItemInterface|null $cacheItem
     *
     * @return ResponseInterface
     */
    public function onCacheResponse(RequestInterface $request, ResponseInterface $response, $fromCache, $cacheItem);
}
