<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Manager.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Manager;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Arr;
use InvalidArgumentException;

/**
 * This is the abstract manager class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
abstract class AbstractManager implements ManagerInterface
{
    /**
     * The config instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected Repository $config;

    /**
     * The active connection instances.
     *
     * @var array<string,object>
     */
    protected array $connections = [];

    /**
     * The custom connection resolvers.
     *
     * @var array<string,callable>
     */
    protected array $extensions = [];

    /**
     * Create a new manager instance.
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     *
     * @return void
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * Get a connection instance.
     *
     * @param string|null $name
     *
     * @throws \InvalidArgumentException
     *
     * @return object
     */
    public function connection(?string $name = null): object
    {
        $name = $name ?: $this->getDefaultConnection();

        if (!isset($this->connections[$name])) {
            $this->connections[$name] = $this->makeConnection($name);
        }

        return $this->connections[$name];
    }

    /**
     * Reconnect to the given connection.
     *
     * @param string|null $name
     *
     * @throws \InvalidArgumentException
     *
     * @return object
     */
    public function reconnect(?string $name = null): object
    {
        $name = $name ?: $this->getDefaultConnection();

        $this->disconnect($name);

        return $this->connection($name);
    }

    /**
     * Disconnect from the given connection.
     *
     * @param string|null $name
     *
     * @return void
     */
    public function disconnect(?string $name = null): void
    {
        $name = $name ?: $this->getDefaultConnection();

        unset($this->connections[$name]);
    }

    /**
     * Create the connection instance.
     *
     * @param array $config
     *
     * @throws \InvalidArgumentException
     *
     * @return object
     */
    abstract protected function createConnection(array $config): object;

    /**
     * Make the connection instance.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return object
     */
    protected function makeConnection(string $name): object
    {
        $config = $this->getConnectionConfig($name);

        if (isset($this->extensions[$name])) {
            return $this->extensions[$name]($config);
        }

        if ($driver = Arr::get($config, 'driver')) {
            if (isset($this->extensions[$driver])) {
                return $this->extensions[$driver]($config);
            }
        }

        return $this->createConnection($config);
    }

    /**
     * Get the configuration name.
     *
     * @return string
     */
    abstract protected function getConfigName(): string;

    /**
     * Get the configuration for a connection.
     *
     * @param string|null $name
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function getConnectionConfig(?string $name = null): array
    {
        $name = $name ?: $this->getDefaultConnection();

        return $this->getNamedConfig('connections', 'Connection', $name);
    }

    /**
     * Get the given named configuration.
     *
     * @param string $type
     * @param string $desc
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function getNamedConfig(string $type, string $desc, string $name): array
    {
        $data = $this->config->get($this->getConfigName().'.'.$type);

        if (!is_array($config = Arr::get($data, $name)) && !$config) {
            throw new InvalidArgumentException("$desc [$name] not configured.");
        }

        $config['name'] = $name;

        return $config;
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection(): string
    {
        return $this->config->get($this->getConfigName().'.default');
    }

    /**
     * Set the default connection name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setDefaultConnection(string $name): void
    {
        $this->config->set($this->getConfigName().'.default', $name);
    }

    /**
     * Register an extension connection resolver.
     *
     * @param string   $name
     * @param callable $resolver
     *
     * @return void
     */
    public function extend(string $name, callable $resolver): void
    {
        if ($resolver instanceof Closure) {
            $this->extensions[$name] = $resolver->bindTo($this, $this);
        } else {
            $this->extensions[$name] = $resolver;
        }
    }

    /**
     * Return all of the created connections.
     *
     * @return array<string,object>
     */
    public function getConnections(): array
    {
        return $this->connections;
    }

    /**
     * Get the config instance.
     *
     * @return \Illuminate\Contracts\Config\Repository
     */
    public function getConfig(): Repository
    {
        return $this->config;
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->connection()->$method(...$parameters);
    }
}
