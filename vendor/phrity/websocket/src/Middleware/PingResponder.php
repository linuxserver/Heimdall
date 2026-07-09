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
    Pong,
    Message
};
use WebSocket\Trait\{
    ConfigurationTrait,
    StringableTrait,
};

/**
 * WebSocket\Middleware\PingResponder class.
 * Responds on incoming ping messages.
 */
class PingResponder implements LoggerAwareInterface, ProcessIncomingInterface, Stringable
{
    use ConfigurationTrait;
    use StringableTrait;

    public function __construct()
    {
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

    public function processIncoming(ProcessStack $stack, Connection $connection): Message
    {
        $message = $stack->handleIncoming();
        if ($message instanceof Ping && $connection->isWritable()) {
            $connection->send(new Pong($message->getContent()));
        }
        return $message;
    }
}
