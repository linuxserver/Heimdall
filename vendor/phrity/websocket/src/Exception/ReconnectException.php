<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Exception;

use Phrity\Net\Uri;

/**
 * WebSocket\Exception\ReconnectException class.
 * Reconnect requested.
 */
class ReconnectException extends Exception
{
    private Uri|null $uri;

    public function __construct(Uri|null $uri = null)
    {
        $this->uri = $uri;
        parent::__construct("Reconnect requested" . ($uri ? ": {$uri}" : ''));
    }

    public function getUri(): Uri|null
    {
        return $this->uri;
    }
}
