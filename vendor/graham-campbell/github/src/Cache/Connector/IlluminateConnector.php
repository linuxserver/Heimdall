<?php

declare(strict_types=1);

/*
 * This file is part of Laravel GitHub.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\GitHub\Cache\Connector;

use GrahamCampbell\BoundedCache\BoundedCache;
use GrahamCampbell\BoundedCache\BoundedCacheInterface;
use GrahamCampbell\Manager\ConnectorInterface;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Arr;
use InvalidArgumentException;

/**
 * This is the illuminate connector class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class IlluminateConnector implements ConnectorInterface
{
    /**
     * The minimum cache lifetime of 12 hours.
     *
     * @var int
     */
    private const MIN_CACHE_LIFETIME = 43200;

    /**
     * The maximum cache lifetime of 48 hours.
     *
     * @var int
     */
    private const MAX_CACHE_LIFETIME = 172800;

    /**
     * The cache factory instance.
     *
     * @var \Illuminate\Contracts\Cache\Factory|null
     */
    private ?Factory $cache;

    /**
     * Create a new illuminate connector instance.
     *
     * @param \Illuminate\Contracts\Cache\Factory|null $cache
     *
     * @return void
     */
    public function __construct(Factory $cache = null)
    {
        $this->cache = $cache;
    }

    /**
     * Establish a cache connection.
     *
     * @param array $config
     *
     * @throws \InvalidArgumentException
     *
     * @return \GrahamCampbell\BoundedCache\BoundedCacheInterface
     */
    public function connect(array $config): BoundedCacheInterface
    {
        $repository = $this->getRepository($config);

        return self::getBoundedCache($repository, $config);
    }

    /**
     * Get the cache repository.
     *
     * @param array $config
     *
     * @throws \InvalidArgumentException
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    private function getRepository(array $config): Repository
    {
        if (!$this->cache) {
            throw new InvalidArgumentException('Illuminate caching support not available.');
        }

        $name = Arr::get($config, 'connector');

        return $this->cache->store($name);
    }

    /**
     * Get the bounded cache instance.
     *
     * @param \Illuminate\Contracts\Cache\Repository $repository
     * @param array                                  $config
     *
     * @return \GrahamCampbell\BoundedCache\BoundedCacheInterface
     */
    private static function getBoundedCache(Repository $repository, array $config): BoundedCacheInterface
    {
        $min = Arr::get($config, 'min', self::MIN_CACHE_LIFETIME);
        $max = Arr::get($config, 'max', self::MAX_CACHE_LIFETIME);

        return new BoundedCache($repository, $min, $max);
    }
}
