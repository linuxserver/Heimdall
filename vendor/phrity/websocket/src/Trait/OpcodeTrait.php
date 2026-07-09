<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Trait;

/**
 * WebSocket\Trait\OpcodeTrait trait.
 * Opcode number/name list.
 */
trait OpcodeTrait
{
    /** @var array<string, int> $opcodes */
    private static array $opcodes = [
        'continuation' => 0,
        'text'         => 1,
        'binary'       => 2,
        'close'        => 8,
        'ping'         => 9,
        'pong'         => 10,
    ];
}
