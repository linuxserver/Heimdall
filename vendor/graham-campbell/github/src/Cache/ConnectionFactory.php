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

namespace GrahamCampbell\GitHub\Cache;

use GrahamCampbell\BoundedCache\BoundedCacheInterface;
use GrahamCampbell\Manager\ConnectorInterface;
use Illuminate\Contracts\Cache\Factory;
use InvalidArgumentException;

/**
 * This is the cache connection factory class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class ConnectionFactory
{
    /**
     * The cache factory instance.
     *
     * @var \Illuminate\Contracts\Cache\Factory|null
     */
    private ?Factory $cache;

    /**
     * Create a new connection factory instance.
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
    public function make(array $config): BoundedCacheInterface
    {
        return $this->createConnector($config)->connect($config);
    }

    /**
     * Create a connector instance based on the configuration.
     *
     * @param array $config
     *
     * @throws \InvalidArgumentException
     *
     * @return \GrahamCampbell\Manager\ConnectorInterface
     */
    public function createConnector(array $config): ConnectorInterface
    {
        if (!isset($config['driver'])) {
            throw new InvalidArgumentException('A driver must be specified.');
        }

        switch ($config['driver']) {
            case 'illuminate':
                return new Connector\IlluminateConnector($this->cache);
        }

        throw new InvalidArgumentException("Unsupported driver [{$config['driver']}].");
    }
}
