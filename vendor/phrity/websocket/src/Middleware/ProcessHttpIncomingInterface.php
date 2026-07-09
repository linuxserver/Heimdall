<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Middleware;

use WebSocket\Connection;
use Psr\Http\Message\MessageInterface;

/**
 * WebSocket\Middleware\ProcessHttpIncomingInterface interface.
 * Interface for incoming middleware implementations.
 */
interface ProcessHttpIncomingInterface extends MiddlewareInterface
{
    public function processHttpIncoming(ProcessHttpStack $stack, Connection $connection): MessageInterface;
}
