<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Http;

use Nyholm\Psr7\Response as NyholmResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Phrity\WebSocket\Http\Response class.
 * Only used for handshake procedure.
 * @deprecated To be removed in v4, use Nyholm directly instead
 * @phpstan-ignore class.extendsFinalByPhpDoc
 */
class Response extends NyholmResponse implements ResponseInterface
{
    public function __construct(int $code = 200, string $reasonPhrase = '')
    {
        parent::__construct($code, reason: $reasonPhrase);
    }
}
