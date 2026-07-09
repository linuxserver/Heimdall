<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Exception;

use Throwable;

/**
 * WebSocket\Exception\ExceptionInterface interface.
 * Root interface for internal exceptions.
 */
interface ExceptionInterface extends Throwable
{
    public function getMessage(): string;
}
