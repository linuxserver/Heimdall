<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Http;

use Nyholm\Psr7\Request as NyholmRequest;
use Psr\Http\Message\{
    RequestInterface,
    UriInterface
};

/**
 * WebSocket\Http\Request class.
 * Only used for handshake procedure.
 * @deprecated To be removed in v4, use Nyholm directly instead
 * @phpstan-ignore class.extendsFinalByPhpDoc
 */
class Request extends NyholmRequest implements RequestInterface
{
    public function __construct(string $method = 'GET', UriInterface|string $uri = '')
    {
        parent::__construct($method, $uri);
    }
}
