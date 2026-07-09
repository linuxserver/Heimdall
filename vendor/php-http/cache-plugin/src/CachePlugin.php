<?php

namespace Http\Client\Common\Plugin;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Allow for caching a response with a PSR-6 compatible caching engine.
 *
 * It can follow the RFC-7234 caching specification or use a fixed cache lifetime.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class CachePlugin extends AbstractCachePlugin
{
    /**
     * This method will setup the cachePlugin in client cache mode. When using the client cache mode the plugin will
     * cache responses with `private` cache directive.
     *
     * @param mixed[] $config For all possible config options see the constructor docs
     *
     * @return CachePlugin
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
     * @return CachePlugin
     */
    public static function serverCache(CacheItemPoolInterface $pool, StreamFactoryInterface $streamFactory, array $config = [])
    {
        return new self($pool, $streamFactory, $config);
    }
}
