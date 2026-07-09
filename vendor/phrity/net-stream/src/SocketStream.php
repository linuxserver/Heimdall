<?php

namespace Phrity\Net;

use InvalidArgumentException;

/**
 * SocketStream class.
 */
class SocketStream extends Stream
{
    // ---------- Configuration ---------------------------------------------------------------------------------------

    /**
     * If stream is connected.
     * @return bool
     */
    public function isConnected(): bool
    {
        return is_resource($this->stream) && ($this->readable || $this->writable);
    }

    /**
     * Get name of remote socket, or null if not connected.
     * @return string|null
     */
    public function getRemoteName(): string|null
    {
        return is_resource($this->stream) ? (stream_socket_get_name($this->stream, true) ?: null) : null;
    }

    /**
     * Get name of local socket, or null if not connected.
     * @return string|null
     */
    public function getLocalName(): string|null
    {
        return is_resource($this->stream) ? (stream_socket_get_name($this->stream, false) ?: null) : null;
    }

    /**
     * Get type of stream resoucre.
     * @return string
     */
    public function getResourceType(): string
    {
        return $this->stream ? get_resource_type($this->stream) : '';
    }

    /**
     * If stream is in blocking mode.
     * @return bool|null
     */
    public function isBlocking(): bool|null
    {
        return $this->getMetadata('blocked');
    }

    /**
     * Toggle blocking/non-blocking mode.
     * @param bool $enable Blocking mode to set.
     * @return bool If operation was succesful.
     * @throws StreamException if stream is closed.
     */
    public function setBlocking(bool $enable): bool
    {
        if (!isset($this->stream)) {
            throw new StreamException(StreamException::STREAM_DETACHED);
        }
        return stream_set_blocking($this->stream, $enable);
    }

    /**
     * If socket stream has unread content.
     * @return bool If there is content to read.
     * @throws StreamException if stream is unselectable.
     */
    public function hasContents(): bool
    {
        if (!is_resource($this->stream)) {
            return false;
        }
        /** @throws StreamException */
        return $this->handler->with(function () {
            $read = [$this->getOpenResource()];
            $write = $oob = [];
            return stream_select($read, $write, $oob, 0, 0) > 0;
        }, new StreamException(StreamException::FAIL_SELECT));
    }

    /**
     * Set timeout period on a stream.
     * @param int<0, max>|float $timeout Seconds to be set.
     * @param int|null $microseconds Microseconds to be set - deprecated
     * @return bool If operation was succesful.
     * @throws InvalidArgumentException if invalid timeout.
     * @throws StreamException if stream is closed.
     */
    public function setTimeout(int|float $timeout, int|null $microseconds = null): bool
    {
        // @deprecated Setting $microseconds is deprecated, use float value on $timeout instead
        // @todo Add deprecation warning
        if ($timeout < 0) {
            throw new InvalidArgumentException("Timeout must be 0 or more.");
        }
        if (!isset($this->stream)) {
            throw new StreamException(StreamException::STREAM_DETACHED);
        }
        $seconds = intval($timeout);
        $microseconds = $microseconds ?? intval(round($timeout - $seconds, 6) * 1000000);
        return stream_set_timeout($this->stream, $seconds, $microseconds);
    }


    // ---------- Operations ------------------------------------------------------------------------------------------

    /**
     * Read line from the stream.
     * @param int<0, max> $length Read up to $length bytes from the object and return them.
     * @return string|null Returns the data read from the stream, or null of eof.
     * @throws StreamException if an error occurs.
     */
    public function readLine(int $length): string|null
    {
        $stream = $this->getOpenResource();
        if (!$this->readable) {
            throw new StreamException(StreamException::NOT_READABLE);
        }
        /** @throws StreamException */
        return $this->handler->with(function () use ($stream, $length) {
            $result = fgets($stream, $length);
            return $result === false ? null : $result;
        }, new StreamException(StreamException::FAIL_GETS));
    }

    /**
     * Closes the stream for further reading.
     * @return void
     */
    public function closeRead(): void
    {
        if (is_resource($this->stream)) {
            if ($this->readable && $this->writable) {
                stream_socket_shutdown($this->stream, STREAM_SHUT_RD);
                $this->evalStream();
            } elseif (!$this->writable) {
                $this->close();
            }
        }
        $this->readable = false;
    }
    /**
     * Closes the stream for further writing.
     * @return void
     */
    public function closeWrite(): void
    {
        if ($this->readable && $this->writable) {
            stream_socket_shutdown($this->getOpenResource(), STREAM_SHUT_WR);
            $this->evalStream();
        } elseif (!$this->readable) {
            $this->close();
        }
        $this->writable = false;
    }
}
