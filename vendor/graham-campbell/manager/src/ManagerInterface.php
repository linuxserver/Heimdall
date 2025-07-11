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

/**
 * This is the manager interface.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
interface ManagerInterface
{
    /**
     * Get a connection instance.
     *
     * @param string|null $name
     *
     * @throws \InvalidArgumentException
     *
     * @return object
     */
    public function connection(?string $name = null): object;

    /**
     * Reconnect to the given connection.
     *
     * @param string|null $name
     *
     * @throws \InvalidArgumentException
     *
     * @return object
     */
    public function reconnect(?string $name = null): object;

    /**
     * Disconnect from the given connection.
     *
     * @param string|null $name
     *
     * @return void
     */
    public function disconnect(?string $name = null): void;

    /**
     * Get the configuration for a connection.
     *
     * @param string|null $name
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function getConnectionConfig(?string $name = null): array;

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection(): string;

    /**
     * Set the default connection name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setDefaultConnection(string $name): void;

    /**
     * Register an extension connection resolver.
     *
     * @param string   $name
     * @param callable $resolver
     *
     * @return void
     */
    public function extend(string $name, callable $resolver): void;

    /**
     * Return all of the created connections.
     *
     * @return array<string,object>
     */
    public function getConnections(): array;
}
