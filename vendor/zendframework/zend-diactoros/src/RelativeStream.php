<?php
/**
 * @see       https://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2015-2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Diactoros;

use Psr\Http\Message\StreamInterface;

use const SEEK_SET;

/**
 * Class RelativeStream
 *
 * Wrapper for default Stream class, representing subpart (starting from given offset) of initial stream.
 * It can be used to avoid copying full stream, conserving memory.
 * @example see Zend\Diactoros\AbstractSerializer::splitStream()
 */
final class RelativeStream implements StreamInterface
{
    /**
     * @var StreamInterface
     */
    private $decoratedStream;

    /**
     * @var int
     */
    private $offset;

    /**
     * Class constructor
     *
     * @param StreamInterface $decoratedStream
     * @param int $offset
     */
    public function __construct(StreamInterface $decoratedStream, ?int $offset)
    {
        $this->decoratedStream = $decoratedStream;
        $this->offset = (int) $offset;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString() : string
    {
        if ($this->isSeekable()) {
            $this->seek(0);
        }
        return $this->getContents();
    }

    /**
     * {@inheritdoc}
     */
    public function close() : void
    {
        $this->decoratedStream->close();
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {
        return $this->decoratedStream->detach();
    }

    /**
     * {@inheritdoc}
     */
    public function getSize() : int
    {
        return $this->decoratedStream->getSize() - $this->offset;
    }

    /**
     * {@inheritdoc}
     */
    public function tell() : int
    {
        return $this->decoratedStream->tell() - $this->offset;
    }

    /**
     * {@inheritdoc}
     */
    public function eof() : bool
    {
        return $this->decoratedStream->eof();
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable() : bool
    {
        return $this->decoratedStream->isSeekable();
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = SEEK_SET) : void
    {
        if ($whence == SEEK_SET) {
            $this->decoratedStream->seek($offset + $this->offset, $whence);
            return;
        }
        $this->decoratedStream->seek($offset, $whence);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind() : void
    {
        $this->seek(0);
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable() : bool
    {
        return $this->decoratedStream->isWritable();
    }

    /**
     * {@inheritdoc}
     */
    public function write($string) : int
    {
        if ($this->tell() < 0) {
            throw new Exception\InvalidStreamPointerPositionException();
        }
        return $this->decoratedStream->write($string);
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable() : bool
    {
        return $this->decoratedStream->isReadable();
    }

    /**
     * {@inheritdoc}
     */
    public function read($length) : string
    {
        if ($this->tell() < 0) {
            throw new Exception\InvalidStreamPointerPositionException();
        }
        return $this->decoratedStream->read($length);
    }

    /**
     * {@inheritdoc}
     */
    public function getContents() : string
    {
        if ($this->tell() < 0) {
            throw new Exception\InvalidStreamPointerPositionException();
        }
        return $this->decoratedStream->getContents();
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null)
    {
        return $this->decoratedStream->getMetadata($key);
    }
}
