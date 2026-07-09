<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Trait;

use WebSocket\Message\{
    Binary,
    Close,
    Ping,
    Pong,
    Text
};

/**
 * WebSocket\Trait\SendMethodsTrait trait.
 * Convenience send methods.
 */
trait SendMethodsTrait
{
    /**
     * Send text message.
     * @param string $message Content as string.
     * @return Text instance
     */
    public function text(string $message): Text
    {
        return $this->send(new Text($message));
    }

    /**
     * Send binary message.
     * @param string $message Content as binary string.
     * @return Binary instance
     */
    public function binary(string $message): Binary
    {
        return $this->send(new Binary($message));
    }

    /**
     * Send ping.
     * @param string $message Optional text as string.
     * @return Ping instance
     */
    public function ping(string $message = ''): Ping
    {
        return $this->send(new Ping($message));
    }

    /**
     * Send unsolicited pong.
     * @param string $message Optional text as string.
     * @return Pong instance
     */
    public function pong(string $message = ''): Pong
    {
        return $this->send(new Pong($message));
    }

    /**
     * Tell the socket to close.
     * @param integer $status  http://tools.ietf.org/html/rfc6455#section-7.4
     * @param string  $message A closing message, max 125 bytes.
     * @return Close instance
     */
    public function close(int $status = 1000, string $message = 'ttfn'): Close
    {
        return $this->send(new Close($status, $message));
    }
}
