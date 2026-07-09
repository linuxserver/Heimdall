<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Middleware;

use Psr\Http\Message\MessageInterface;
use Stringable;
use WebSocket\Connection;
use WebSocket\Http\HttpHandler;
use WebSocket\Trait\StringableTrait;

/**
 * WebSocket\Middleware\ProcessHttpStack class.
 * Worker stack for HTTP middleware implementations.
 */
class ProcessHttpStack implements Stringable
{
    use StringableTrait;

    private Connection $connection;
    private HttpHandler $httpHandler;
    /** @var array<ProcessHttpIncomingInterface|ProcessHttpOutgoingInterface> $processors */
    private array $processors;

    /**
     * Create ProcessStack.
     * @param Connection $connection
     * @param HttpHandler $httpHandler
     * @param array<ProcessHttpIncomingInterface|ProcessHttpOutgoingInterface> $processors
     */
    public function __construct(Connection $connection, HttpHandler $httpHandler, array $processors)
    {
        $this->connection = $connection;
        $this->httpHandler = $httpHandler;
        $this->processors = $processors;
    }

    /**
     * Process middleware for incoming http message.
     * @return MessageInterface
     */
    public function handleHttpIncoming(): MessageInterface
    {
        /** @var ProcessHttpIncomingInterface|null $processor */
        $processor = array_shift($this->processors);
        if ($processor) {
            return $processor->processHttpIncoming($this, $this->connection);
        }
        return $this->httpHandler->pull();
    }

    /**
     * Process middleware for outgoing http message.
     * @param MessageInterface $message
     * @return MessageInterface
     */
    public function handleHttpOutgoing(MessageInterface $message): MessageInterface
    {
        /** @var ProcessHttpOutgoingInterface|null $processor */
        $processor = array_shift($this->processors);
        if ($processor) {
            return $processor->processHttpOutgoing($this, $this->connection, $message);
        }
        return $this->httpHandler->push($message);
    }
}
