<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Middleware;

use Stringable;
use WebSocket\Connection;
use WebSocket\Trait\StringableTrait;

/**
 * WebSocket\Middleware\ProcessTickStack class.
 * Worker stack for HTTP middleware implementations.
 */
class ProcessTickStack implements Stringable
{
    use StringableTrait;

    private Connection $connection;
    /** @var array<ProcessTickInterface> $processors */
    private array $processors;

    /**
     * Create ProcessStack.
     * @param Connection $connection
     * @param array<ProcessTickInterface> $processors
     */
    public function __construct(Connection $connection, array $processors)
    {
        $this->connection = $connection;
        $this->processors = $processors;
    }

    /**
     * Process middleware for tick.
     */
    public function handleTick(): void
    {
        $processor = array_shift($this->processors);
        if ($processor) {
            $processor->processTick($this, $this->connection);
        }
    }
}
