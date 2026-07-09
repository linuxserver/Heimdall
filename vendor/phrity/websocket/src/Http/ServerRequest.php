<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Http;

use Nyholm\Psr7\ServerRequest as NyholmServerRequest;
use Psr\Http\Message\{
    ServerRequestInterface,
    UriInterface
};

/**
 * WebSocket\Http\ServerRequest class.
 * Only used for handshake procedure.
 * @deprecated To be removed in v4, use Nyholm directly instead
 * @phpstan-ignore class.extendsFinalByPhpDoc
 */
class ServerRequest extends NyholmServerRequest implements ServerRequestInterface
{
    public function __construct(string $method = 'GET', UriInterface|string $uri = '')
    {
        parent::__construct($method, $uri);
    }
}
