<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Message;

/**
 * WebSocket\Message\Binary class.
 * A Binary WebSocket message.
 */
class Binary extends Message
{
    protected string $opcode = 'binary';

    public function isCompressed(): bool
    {
        return $this->compress;
    }

    public function setCompress(bool $compress): void
    {
        $this->compress = $compress;
    }
}
