<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Exception;

/**
 * WebSocket\Exception\CloseException class.
 * Connection should close
 */
class CloseException extends Exception
{
    protected int|null $status;
    protected string $content;

    public function __construct(int|null $status = null, string $content = '')
    {
        $this->status = $status;
        parent::__construct($content);
    }

    public function getCloseStatus(): int
    {
        return $this->status ?? 1000;
    }
}
