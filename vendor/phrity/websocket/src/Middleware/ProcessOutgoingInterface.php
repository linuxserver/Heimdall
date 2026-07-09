<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Middleware;

use WebSocket\Connection;
use WebSocket\Message\Message;

/**
 * WebSocket\Middleware\ProcessOutgoingInterface interface.
 * Interface for outgoing middleware implementations.
 */
interface ProcessOutgoingInterface extends MiddlewareInterface
{
    /**
     * @template T of Message
     * @param T $message
     * @return T
     */
    public function processOutgoing(ProcessStack $stack, Connection $connection, Message $message): Message;
}
