<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Frame;

use Stringable;
use WebSocket\Trait\StringableTrait;

/**
 * WebSocket\Frame\Frame class.
 * Represent a single frame sent or received as part of websocket message.
 */
class Frame implements Stringable
{
    use StringableTrait;

    private string $opcode;
    private string $payload;
    private bool $final;
    private bool $rsv1;
    private bool $rsv2;
    private bool $rsv3;

    public function __construct(
        string $opcode,
        string $payload,
        bool $final,
        bool $rsv1 = false,
        bool $rsv2 = false,
        bool $rsv3 = false
    ) {
        $this->opcode = $opcode;
        $this->payload = $payload;
        $this->final = $final;
        $this->rsv1 = $rsv1;
        $this->rsv2 = $rsv2;
        $this->rsv3 = $rsv3;
    }

    public function isFinal(): bool
    {
        return $this->final;
    }

    public function getRsv1(): bool
    {
        return $this->rsv1;
    }

    public function setRsv1(bool $rsv1): void
    {
        $this->rsv1 = $rsv1;
    }

    public function getRsv2(): bool
    {
        return $this->rsv2;
    }

    public function getRsv3(): bool
    {
        return $this->rsv3;
    }

    public function isContinuation(): bool
    {
        return $this->opcode === 'continuation';
    }

    public function getOpcode(): string
    {
        return $this->opcode;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    public function getPayloadLength(): int
    {
        return strlen($this->payload);
    }

    public function __toString(): string
    {
        return $this->stringable('%s', $this->opcode);
    }
}
