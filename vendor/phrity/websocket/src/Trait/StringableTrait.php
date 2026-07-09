<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Trait;

/**
 * WebSocket\Trait\StringableTrait trait.
 * Stringable helper.
 */
trait StringableTrait
{
    public function __toString(): string
    {
        return get_class($this);
    }

    protected function stringable(string $format, mixed ...$values): string
    {
        return sprintf("%s({$format})", get_class($this), ...$values);
    }
}
