<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Middleware;

use WebSocket\Connection;
use WebSocket\Message\Message;

/**
 * WebSocket\Middleware\ProcessIncomingInterface interface.
 * Interface for incoming middleware implementations.
 */
interface ProcessIncomingInterface extends MiddlewareInterface
{
    public function processIncoming(ProcessStack $stack, Connection $connection): Message;
}
