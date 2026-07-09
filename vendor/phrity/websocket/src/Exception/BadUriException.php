<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Exception;

/**
 * WebSocket\Exception\BadUriException class.
 * Thrown when invalid URI is provided.
 */
class BadUriException extends Exception
{
    public function __construct(string $message = 'Bad URI')
    {
        parent::__construct($message);
    }
}
