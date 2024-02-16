<?php

declare(strict_types=1);

/*
 * This file is part of Bounded Cache.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\BoundedCache;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

/**
 * This is the bounded cache class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class BoundedCache implements BoundedCacheInterface
{
    /**
     * The underlying cache instance.
     */
    private CacheInterface $cache;

    /**
     * The minimum cache lifetime.
     */
    private int $min;

    /**
     * The maximum cache lifetime.
     */
    private int $max;

    /**
     * Create a bounded cache instance.
     *
     * @return void
     */
    public function __construct(CacheInterface $cache, int $min, int $max)
    {
        $this->cache = $cache;
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * Get the minimum cache lifetime.
     */
    public function getMinimumLifetime(): int
    {
        return $this->min;
    }

    /**
     * Get the maximum cache lifetime.
     */
    public function getMaximumLifetime(): int
    {
        return $this->max;
    }

    /**
     * Fetches a value from the cache.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->cache->get($key, $default);
    }

    /**
     * Persists data in the cache, uniquely referenced by a key.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return bool
     */
    public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool
    {
        return $this->cache->set($key, $value, $this->computeTtl($ttl));
    }

    /**
     * Delete an item from the cache by its unique key.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function delete(string $key): bool
    {
        return $this->cache->delete($key);
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool
     */
    public function clear(): bool
    {
        return $this->cache->clear();
    }

    /**
     * Obtains multiple cache items by their unique keys.
     *
     * @param iterable<string> $keys
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return iterable<string, mixed>
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        return $this->cache->getMultiple($keys, $default);
    }

    /**
     * Persists a set of key => value pairs in the cache.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
        return $this->cache->setMultiple($values, $this->computeTtl($ttl));
    }

    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param iterable<string> $keys
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function deleteMultiple(iterable $keys): bool
    {
        return $this->cache->deleteMultiple($keys);
    }

    /**
     * Determines whether an item is present in the cache.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function has(string $key): bool
    {
        return $this->cache->has($key);
    }

    /**
     * Computes the TTL to use.
     */
    private function computeTtl(null|int|DateInterval $ttl): int
    {
        return TtlHelper::computeTtl($this->min, $this->max, $ttl);
    }
}
