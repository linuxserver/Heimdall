<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Middleware;

use Psr\Log\{
    LoggerInterface,
    LoggerAwareInterface,
};
use Stringable;
use WebSocket\{
    Configuration,
    Connection,
};
use WebSocket\Message\{
    Ping,
    Message
};
use WebSocket\Trait\{
    ConfigurationTrait,
    StringableTrait,
};

/**
 * WebSocket\Middleware\PingInterval class.
 * Handles close procedure.
 */
class PingInterval implements LoggerAwareInterface, ProcessOutgoingInterface, ProcessTickInterface, Stringable
{
    use ConfigurationTrait;
    use StringableTrait;

    private const SCOPE = 'ping-interval';

    private int|float|null $interval;

    public function __construct(int|float|null $interval = null)
    {
        $this->interval = $interval;
        $this->initConfiguration();
    }

    /**
     * Set logger.
     * @param LoggerInterface $logger
     * @deprecated Will be removed in future version, retrieved from Configuration instead
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->configuration->setLogger($logger);
    }

    public function processOutgoing(ProcessStack $stack, Connection $connection, Message $message): Message
    {
        $this->setNext($connection); // Update timestamp for next ping
        return $stack->handleOutgoing($message);
    }

    public function processTick(ProcessTickStack $stack, Connection $connection): void
    {
        // Push if time exceeds timestamp for next ping
        if ($connection->isWritable() && microtime(true) >= $this->getNext($connection)) {
            $this->configuration->getLogger()->debug("[{scope}] Auto-pushing ping", [
                'scope' => self::SCOPE,
                'connection' => $connection->getIdentity(),
            ]);
            $connection->send(new Ping());
            $this->setNext($connection); // Update timestamp for next ping
        }
        $stack->handleTick();
    }

    private function getNext(Connection $connection): float
    {
        return $connection->getMeta('pingInterval.next') ?? $this->setNext($connection);
    }

    private function setNext(Connection $connection): float
    {
        $next = microtime(true) + ($this->interval ?? $connection->getTimeout());
        $connection->setMeta('pingInterval.next', $next);
        return $next;
    }
}
