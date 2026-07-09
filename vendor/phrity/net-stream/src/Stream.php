<?php

namespace Phrity\Net;

use InvalidArgumentException;
use Phrity\Util\ErrorHandler;
use Stringable;
use Throwable;

/**
 * Phrity\Net\Stream class.
 * @see https://www.php-fig.org/psr/psr-7/#34-psrhttpmessagestreaminterface
*/
class Stream implements StreamInterface, Stringable
{
    /** @var array<string> */
    private static array $readmodes = ['r', 'r+', 'w+', 'a+', 'x+', 'c+'];
    /** @var array<string> */
    private static array $writemodes = ['r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+'];

    /** @var resource|null */
    protected $stream;
    protected Context $context;
    protected ErrorHandler $handler;
    protected bool $readable = false;
    protected bool $writable = false;
    protected bool $seekable = false;

    /**
     * Create new stream wrapper instance
     * @param resource $stream A stream resource to wrap
     * @throws InvalidArgumentException If not a valid stream resource
     */
    public function __construct($stream)
    {
        $type = gettype($stream);
        if ($type !== 'resource') {
             throw new InvalidArgumentException("Invalid stream provided; got type '{$type}'.");
        }
        $rtype = get_resource_type($stream);
        if (!in_array($rtype, ['stream', 'persistent stream'])) {
             throw new InvalidArgumentException("Invalid stream provided; got resource type '{$rtype}'.");
        }
        $this->stream = $stream;
        $this->context = new Context($this->stream);
        $this->handler = new ErrorHandler();
        $this->evalStream();
    }


    // ---------- PSR-7 methods ---------------------------------------------------------------------------------------

    /**
     * Closes the stream and any underlying resources.
     * @return void
     */
    public function close(): void
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
        $this->stream = null;
        $this->evalStream();
    }

    /**
     * Separates any underlying resources from the stream.
     * After the stream has been detached, the stream is in an unusable state.
     * @return resource|null Underlying stream, if any
     */
    public function detach(): mixed
    {
        if (!isset($this->stream)) {
            return null;
        }
        $stream = $this->stream;
        $this->stream = null;
        $this->evalStream();
        return $stream;
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata(string|null $key = null): mixed
    {
        if (!isset($this->stream)) {
            return null;
        }
        $meta = stream_get_meta_data($this->stream);
        if (isset($key)) {
            return array_key_exists($key, $meta) ? $meta[$key] : null;
        }
        return $meta;
    }

    /**
     * Returns the current position of the file read/write pointer
     * @return int Position of the file pointer
     * @throws StreamException on error.
     */
    public function tell(): int
    {
        /** @throws StreamException */
        return $this->handler->with(function () {
            return ftell($this->getOpenResource());
        }, new StreamException(StreamException::FAIL_TELL));
    }

    /**
     * Returns true if the stream is at the end of the stream.
     * @return bool
     */
    public function eof(): bool
    {
        return empty($this->stream) || feof($this->stream);
    }

    /**
     * Read data from the stream.
     * @param int<1, max> $length Read up to $length bytes from the object and return them.
     * @return string Returns the data read from the stream, or an empty string.
     * @throws StreamException if an error occurs.
     */
    public function read(int $length): string
    {
        if ($length < 1) {
            throw new InvalidArgumentException("Must read minimum 1 byte");
        }
        $stream = $this->getOpenResource();
        if (!$this->readable) {
            throw new StreamException(StreamException::NOT_READABLE);
        }
        /** @throws StreamException */
        return $this->handler->with(function () use ($stream, $length) {
            return (string)fread($stream, $length);
        }, new StreamException(StreamException::FAIL_READ));
    }

    /**
     * Write data to the stream.
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws StreamException on failure.
     */
    public function write(string $string): int
    {
        $stream = $this->getOpenResource();
        if (!$this->writable) {
            throw new StreamException(StreamException::NOT_WRITABLE);
        }
        /** @throws StreamException */
        return $this->handler->with(function () use ($stream, $string) {
            return fwrite($stream, $string);
        }, new StreamException(StreamException::FAIL_WRITE));
    }

    /**
     * Get the size of the stream if known.
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize(): int|null
    {
        if (!is_resource($this->stream)) {
            return null;
        }
        $stats = fstat($this->stream);
        return $stats['size'] ?? null;
    }

    /**
     * Returns whether or not the stream is seekable.
     * @return bool
     */
    public function isSeekable(): bool
    {
        return $this->seekable;
    }

    /**
     * Seek to a position in the stream.
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated based on the seek offset.
     * @throws StreamException on failure.
     */
    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        $stream = $this->getOpenResource();
        if (!$this->seekable) {
            throw new StreamException(StreamException::NOT_SEEKABLE);
        }
        $result = fseek($stream, $offset, $whence);
        if ($result !== 0) {
            throw new StreamException(StreamException::FAIL_SEEK);
        }
    }

    /**
     * Seek to the beginning of the stream.
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     */
    public function rewind(): void
    {
        $this->seek(0);
    }

    /**
     * Returns whether or not the stream is writable.
     * @return bool
     */
    public function isWritable(): bool
    {
        return $this->writable;
    }

    /**
     * Returns whether or not the stream is readable.
     * @return bool
     */
    public function isReadable(): bool
    {
        return $this->readable;
    }

    /**
     * Returns the remaining contents in a string
     * @return string
     * @throws StreamException if unable to read.
     * @throws StreamException if error occurs while reading.
     */
    public function getContents(): string
    {
        $stream = $this->getOpenResource();
        if (!$this->readable) {
            throw new StreamException(StreamException::NOT_READABLE);
        }
        /** @throws StreamException */
        return $this->handler->with(function () use ($stream) {
            return stream_get_contents($stream);
        }, new StreamException(StreamException::FAIL_CONTENTS));
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     * @return string
     */
    public function __toString(): string
    {
        try {
            if ($this->isSeekable()) {
                $this->rewind();
            }
            return $this->getContents();
        } catch (Throwable $e) {
            trigger_error($e->getMessage(), E_USER_WARNING);
            return '';
        }
    }


    // ---------- StreamInterface methods -----------------------------------------------------------------------------

    /**
     * Return context for stream.
     * @return Context
     */
    public function getContext(): Context
    {
        return $this->context;
    }

    /**
     * Return underlying resource.
     * @return resource|null
     */
    public function getResource(): mixed
    {
        return $this->stream;
    }


    // ---------- Protected helper methods ----------------------------------------------------------------------------

    /**
     * Evaluate stream state.
     */
    protected function evalStream(): void
    {
        if ($this->stream && $meta = $this->getMetadata()) {
            $mode = substr($meta['mode'], 0, 2);
            $this->readable = in_array($mode, self::$readmodes);
            $this->writable = in_array($mode, self::$writemodes);
            $this->seekable = $meta['seekable'];
            return;
        }
        $this->readable = $this->writable = $this->seekable = false;
    }

    /**
     * Return underlying resource.
     * @return resource
     * @throws StreamException if closed.
     */
    protected function getOpenResource(): mixed
    {
        if (!is_resource($this->stream)) {
            throw new StreamException(StreamException::STREAM_DETACHED);
        }
        return $this->stream;
    }
}
