<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Exception;

use Psr\Http\Message\ResponseInterface;

/**
 * WebSocket\Exception\HandshakeException class.
 * Exception during handshake
 */
class HandshakeException extends Exception implements ConnectionLevelInterface
{
    private ResponseInterface $response;

    public function __construct(string $message, ResponseInterface $response)
    {
        parent::__construct($message);
        $this->response = $response;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
