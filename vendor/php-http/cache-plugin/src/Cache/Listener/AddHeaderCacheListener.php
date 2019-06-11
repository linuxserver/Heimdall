<?php

namespace Http\Client\Common\Plugin\Cache\Listener;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Cache\CacheItemInterface;

/**
 * Adds a header indicating if the response came from cache.
 *
 * @author Iain Connor <iain.connor@priceline.com>
 */
class AddHeaderCacheListener implements CacheListener
{
    /** @var string */
    private $headerName;

    /**
     * @param string $headerName
     */
    public function __construct($headerName = 'X-Cache')
    {
        $this->headerName = $headerName;
    }

    /**
     * Called before the cache plugin returns the response, with information on whether that response came from cache.
     *
     * @param RequestInterface        $request
     * @param ResponseInterface       $response
     * @param bool                    $fromCache Whether the `$response` was from the cache or not.
     *                                           Note that checking `$cacheItem->isHit()` is not sufficent to determine this.
     * @param CacheItemInterface|null $cacheItem
     *
     * @return ResponseInterface
     */
    public function onCacheResponse(RequestInterface $request, ResponseInterface $response, $fromCache, $cacheItem)
    {
        return $response->withHeader($this->headerName, $fromCache ? 'HIT' : 'MISS');
    }
}
