<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket;

use InvalidArgumentException;
use Phrity\Net\Context;
use Psr\Log\{
    LoggerAwareInterface,
    LoggerInterface,
    NullLogger,
};
use Stringable;
use WebSocket\Trait\StringableTrait;

/**
 * WebSocket\Connection class.
 * A client/server connection, wrapping socket stream.
 */
class Configuration implements LoggerAwareInterface, Stringable
{
    use StringableTrait;

    private LoggerInterface $logger;
    private Context $context;
    /** @var int<0, max>|float $timeout */
    private int|float $timeout;
    /** @var int<1, max> $frameSize */
    private int $frameSize;
    private bool $persistent;
    /** @var int<1, max>|null $maxConnections */
    private int|null $maxConnections;


    /* ---------- Magic methods ------------------------------------------------------------------------------------ */

    /**
     * @param LoggerInterface|null $logger
     * @param Context|null $context
     * @param int<0, max>|float $timeout
     * @param int<1, max> $frameSize
     * @param bool $persistent
     * @param int<1, max>|null $maxConnections
     */
    public function __construct(
        LoggerInterface|null $logger = null,
        Context|null $context = null,
        int|float|null $timeout = null,
        int|null $frameSize = null,
        bool|null $persistent = null,
        int|null $maxConnections = null,
    ) {
        $this->setLogger($logger ?? new NullLogger());
        $this->setContext($context ?? new Context());
        $this->setTimeout($timeout ?? 60);
        $this->setFrameSize($frameSize ?? 4096);
        $this->setPersistent($persistent ?? false);
        $this->setMaxConnections($maxConnections);
    }

    public function __toString(): string
    {
        return $this->stringable('');
    }


    /* ---------- Logger methods ----------------------------------------------------------------------------------- */

    /**
     * @return LoggerInterface $logger
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }


    /* ---------- Context methods ---------------------------------------------------------------------------------- */

    /**
     * @return Context
     */
    public function getContext(): Context
    {
        return $this->context;
    }

    /**
     * @param Context $context Context or options as array
     */
    public function setContext(Context $context): void
    {
        $this->context = $context;
    }


    /* ---------- Timeout methods ---------------------------------------------------------------------------------- */

    /**
     * @return int<0, max>|float Timeout in seconds
     */
    public function getTimeout(): int|float
    {
        return $this->timeout;
    }

    /**
     * @param int<0, max>|float $timeout Timeout in seconds
     * @throws InvalidArgumentException If invalid timeout provided
     */
    public function setTimeout(int|float $timeout): void
    {
        if ($timeout < 0) {
            throw new InvalidArgumentException("Invalid timeout '{$timeout}' provided");
        }
        $this->timeout = $timeout;
    }


    /* ---------- FrameSize methods -------------------------------------------------------------------------------- */

    /**
     * @return int<1, max> Frame size in bytes
     */
    public function getFrameSize(): int
    {
        return $this->frameSize;
    }

    /**
     * @param int<1, max> $frameSize Max frame payload size in bytes
     * @throws InvalidArgumentException If invalid frameSize provided
     */
    public function setFrameSize(int $frameSize): void
    {
        if ($frameSize < 1) {
            throw new InvalidArgumentException("Invalid frameSize '{$frameSize}' provided");
        }
        $this->frameSize = $frameSize;
    }


    /* ---------- Persistent methods (Client only) ----------------------------------------------------------------- */

    /**
     * @return bool $persistent
     */
    public function isPersistent(): bool
    {
        return $this->persistent;
    }

    /**
     * @param bool $persistent True for persistent connection
     */
    public function setPersistent(bool $persistent): void
    {
        $this->persistent = $persistent;
    }


    /* ---------- Max connections (Server only) -------------------------------------------------------------------- */

    /**
     * @return int<1, max> Max connections allowed
     */
    public function getMaxConnections(): int|null
    {
        return $this->maxConnections;
    }

    /**
     * @param int<1, max>|null $maxConnections
     * @throws InvalidArgumentException If invalid number provided
     */
    public function setMaxConnections(int|null $maxConnections): void
    {
        if ($maxConnections !== null && $maxConnections < 1) {
            throw new InvalidArgumentException("Invalid maxConnections '{$maxConnections}' provided");
        }
        $this->maxConnections = $maxConnections;
    }
}
