<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Exception;

/**
 * WebSocket\Exception\ConnectionFailureException class.
 * Unspecified error on connection.
 */
class ConnectionFailureException extends Exception implements ConnectionLevelInterface
{
    public function __construct(string|null $message = null)
    {
        parent::__construct($message ?? 'Connection error');
    }
}
