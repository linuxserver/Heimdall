<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Runtime;

/**
 * WebSocket\Runtime\IdentityInterface interface.
 */
interface IdentityInterface
{
    /**
     * @return non-empty-string
     */
    public function getIdentity(): string;
}
