<?php

namespace Http\Client\Common\Plugin;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Allow for caching only ETag-backed responses with a PSR-6 compatible caching engine.
 *
 * Cached responses are always revalidated with the origin before their cached body is returned.
 */
final class EtagCachePlugin extends AbstractCachePlugin
{
    /**
     * This method will setup the cachePlugin in client cache mode. When using the client cache mode the plugin will
     * cache responses with `private` cache directive.
     *
     * @param mixed[] $config For all possible config options see the constructor docs
     *
     * @return EtagCachePlugin
     */
    public static function clientCache(CacheItemPoolInterface $pool, StreamFactoryInterface $streamFactory, array $config = [])
    {
        return new self($pool, $streamFactory, self::prepareClientCacheConfig($config));
    }

    /**
     * This method will setup the cachePlugin in server cache mode. This is the default caching behavior it refuses to
     * cache responses with the `private`or `no-cache` directives.
     *
     * @param mixed[] $config For all possible config options see the constructor docs
     *
     * @return EtagCachePlugin
     */
    public static function serverCache(CacheItemPoolInterface $pool, StreamFactoryInterface $streamFactory, array $config = [])
    {
        return new self($pool, $streamFactory, $config);
    }

    /**
     * @return int
     */
    protected static function calculateResponseExpiresAt(?int $maxAge)
    {
        return 0;
    }

    protected function isCacheable(ResponseInterface $response)
    {
        return parent::isCacheable($response) && self::responseHasETag($response);
    }

    /**
     * @param mixed[] $data
     */
    protected static function shouldUseCachedResponse(array $data): bool
    {
        return false;
    }

    protected static function withCacheValidationHeaders(RequestInterface $request, CacheItemInterface $cacheItem): RequestInterface
    {
        if ($etag = self::getETag($cacheItem)) {
            $request = $request->withHeader('If-None-Match', $etag);
        }

        return $request;
    }

    protected static function canUseCacheItemForNotModifiedResponse(CacheItemInterface $cacheItem): bool
    {
        return $cacheItem->isHit() && null !== self::getETag($cacheItem);
    }
}
