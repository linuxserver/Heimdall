<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Exception;

/**
 * WebSocket\Exception\ConnectionClosedException class.
 * Connection is unexpectedly closed exception.
 */
class ConnectionClosedException extends Exception implements ConnectionLevelInterface
{
    public function __construct()
    {
        parent::__construct('Connection has unexpectedly closed');
    }
}
