<?php

namespace Phrity\Net;

use Countable;
use ErrorException;
use InvalidArgumentException;
use Iterator;
use Phrity\Util\ErrorHandler;

/**
 * StreamCollection class.
 * @implements Iterator<string, Stream>
 */
class StreamCollection implements Countable, Iterator
{
    protected ErrorHandler $handler;
    /** @var array<string, StreamInterface|StreamContainerInterface> */
    private array $streams = [];

    /**
     * Create new stream collection instance.
     */
    public function __construct()
    {
        $this->handler = new ErrorHandler();
    }


    // ---------- Collectors and selectors ----------------------------------------------------------------------------

    /**
     * Attach stream to collection.
     * @param StreamInterface|StreamContainerInterface $attach Stream to attach.
     * @param string|null $key Definable name of stream.
     * @return string Name of stream.
     * @throws StreamException If already attached.
     */
    public function attach(StreamInterface|StreamContainerInterface $attach, string|null $key = null): string
    {
        if ($key && array_key_exists($key, $this->streams)) {
            throw new StreamException(StreamException::COLLECT_KEY_CONFLICT, ['key' => $key]);
        }
        $key = $key ?: $this->createKey();
        $this->streams[$key] = $attach;
        return $key;
    }

    /**
     * Detach stream from collection.
     * @param StreamInterface|StreamContainerInterface|string $detach Stream or name of stream to detach.
     * @return bool If a stream was detached.
     */
    public function detach(StreamInterface|StreamContainerInterface|string $detach): bool
    {
        if (is_string($detach)) {
            if (array_key_exists($detach, $this->streams)) {
                unset($this->streams[$detach]);
                return true;
            }
        }
        if ($detach instanceof Stream) {
            foreach ($this->streams as $key => $stream) {
                if ($stream === $detach) {
                    unset($this->streams[$key]);
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Collect all readable streams into new collection.
     * @return self New collection instance.
     */
    public function getReadable(): self
    {
        $readables = new self();
        foreach ($this->streams as $key => $stream) {
            if ($this->getStream($stream)->isReadable()) {
                $readables->attach($stream, $key);
            }
        }
        return $readables;
    }

    /**
     * Collect all writable streams into new collection.
     * @return self New collection instance.
     */
    public function getWritable(): self
    {
        $writables = new self();
        foreach ($this->streams as $key => $stream) {
            if ($this->getStream($stream)->isWritable()) {
                $writables->attach($stream, $key);
            }
        }
        return $writables;
    }

    /**
     * Wait for redable content in stream collection.
     * @param int<0, max>|float $timeout Timeout in seconds.
     * @return self New collection instance.
     * @throws InvalidArgumentException If invalid timeout.
     */
    public function waitRead(int|float $timeout = 60): self
    {
        if ($timeout < 0) {
            throw new InvalidArgumentException("Timeout must be 0 or more.");
        }
        $seconds = intval($timeout);
        $microseconds = intval(round($timeout - $seconds, 6) * 1000000);

        $read = [];
        foreach ($this->streams as $key => $stream) {
            if ($this->getStream($stream)->isReadable()) {
                $read[$key] = $this->getStream($stream)->getResource();
            }
        }
        if (empty($read)) {
            return new self(); // Nothing to select
        }

        $changed = $this->handler->with(function () use ($read, $seconds, $microseconds) {
            $write = $oob = [];
            /** @phpstan-ignore argument.type */
            stream_select($read, $write, $oob, $seconds, $microseconds);
            return $read;
        }, function (ErrorException $error) {
            return []; // Ignore, but don't use result
        });

        $ready = new self();
        foreach ($changed as $key => $resource) {
            $ready->attach($this->streams[$key], $key);
        }
        return $ready;
    }


    // ---------- Countable interface implementation ------------------------------------------------------------------

    /**
     * Count contained streams.
     * @return int Number of streams in collection.
     */
    public function count(): int
    {
        return count($this->streams);
    }


    // ---------- Iterator interface implementation -------------------------------------------------------------------

    /**
     * Return the current stream.
     * @return StreamInterface|StreamContainerInterface|null Current stream.
     */
    public function current(): StreamInterface|StreamContainerInterface|null
    {
        return current($this->streams) ?: null;
    }

    /**
     * Return the key of the current stream.
     * @return string Current key.
     */
    public function key(): string|null
    {
        return key($this->streams);
    }

    /**
     * Move forward to next stream.
     */
    public function next(): void
    {
        next($this->streams);
    }

    /**
     * Rewind the Iterator to the first stream.
     */
    public function rewind(): void
    {
        reset($this->streams);
    }

    /**
     * Checks if current position is valid.
     * @return bool True if valid.
     */
    public function valid(): bool
    {
        return array_key_exists(key($this->streams) ?? -1, $this->streams);
    }


    // ---------- Protected helper methods ----------------------------------------------------------------------------

    /**
     * Create unique key.
     * @return string Unique key.
     */
    protected function createKey(): string
    {
        do {
            $key = bin2hex(random_bytes(16));
        } while (array_key_exists($key, $this->streams));
        return $key;
    }

    protected function getStream(StreamInterface|StreamContainerInterface $stream): StreamInterface
    {
        return $stream instanceof StreamInterface ? $stream : $stream->getStream();
    }
}
