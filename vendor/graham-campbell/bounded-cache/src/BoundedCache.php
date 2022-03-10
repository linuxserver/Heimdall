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
     *
     * @var \Psr\SimpleCache\CacheInterface
     */
    private $cache;

    /**
     * The minimum cache lifetime.
     *
     * @var int
     */
    private $min;

    /**
     * The maximum cache lifetime.
     *
     * @var int
     */
    private $max;

    /**
     * Create a bounded cache instance.
     *
     * @param \Psr\SimpleCache\CacheInterface $cache
     * @param int                             $min
     * @param int                             $max
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
     *
     * @return int
     */
    public function getMinimumLifetime()
    {
        return $this->min;
    }

    /**
     * Get the maximum cache lfetime.
     *
     * @return int
     */
    public function getMaximumLifetime()
    {
        return $this->max;
    }

    /**
     * Fetches a value from the cache.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->cache->get($key, $default);
    }

    /**
     * Persists data in the cache, uniquely referenced by a key.
     *
     * @param string                 $key
     * @param mixed                  $value
     * @param null|int|\DateInterval $ttl
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return bool
     */
    public function set($key, $value, $ttl = null)
    {
        return $this->cache->set($key, $value, $this->computeTtl($ttl));
    }

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $key
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return bool
     */
    public function delete($key)
    {
        return $this->cache->delete($key);
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool
     */
    public function clear()
    {
        return $this->cache->clear();
    }

    /**
     * Obtains multiple cache items by their unique keys.
     *
     * @param iterable $keys
     * @param mixed    $default
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return iterable
     */
    public function getMultiple($keys, $default = null)
    {
        return $this->cache->getMultiple($keys, $default);
    }

    /**
     * Persists a set of key => value pairs in the cache.
     *
     * @param iterable               $values
     * @param null|int|\DateInterval $ttl
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return bool
     */
    public function setMultiple($values, $ttl = null)
    {
        return $this->cache->setMultiple($values, $this->computeTtl($ttl));
    }

    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param iterable $keys
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return bool
     */
    public function deleteMultiple($keys)
    {
        return $this->cache->deleteMultiple($keys);
    }

    /**
     * Determines whether an item is present in the cache.
     *
     * @param string $key
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return bool
     */
    public function has($key)
    {
        return $this->cache->has($key);
    }

    /**
     * Computes the TTL to use.
     *
     * @param null|int|\DateInterval $ttl
     *
     * @return int
     */
    private function computeTtl($ttl)
    {
        return TtlHelper::computeTtl($this->min, $this->max, $ttl);
    }
}
