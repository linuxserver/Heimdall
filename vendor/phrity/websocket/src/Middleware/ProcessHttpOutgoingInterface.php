<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Middleware;

use WebSocket\Connection;
use Psr\Http\Message\MessageInterface;

/**
 * WebSocket\Middleware\ProcessHttpOutgoingInterface interface.
 * Interface for outgoing middleware implementations.
 */
interface ProcessHttpOutgoingInterface extends MiddlewareInterface
{
    public function processHttpOutgoing(
        ProcessHttpStack $stack,
        Connection $connection,
        MessageInterface $message
    ): MessageInterface;
}
