<?php
/**
 * @see       https://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2015-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Diactoros;

use Psr\Http\Message\StreamInterface;

use function array_key_exists;

use const SEEK_SET;

/**
 * Implementation of PSR HTTP streams
 */
class CallbackStream implements StreamInterface
{
    /**
     * @var callable|null
     */
    protected $callback;

    /**
     * @param callable $callback
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(callable $callback)
    {
        $this->attach($callback);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString() : string
    {
        return $this->getContents();
    }

    /**
     * {@inheritdoc}
     */
    public function close() : void
    {
        $this->callback = null;
    }

    /**
     * {@inheritdoc}
     */
    public function detach() : ?callable
    {
        $callback = $this->callback;
        $this->callback = null;
        return $callback;
    }

    /**
     * Attach a new callback to the instance.
     */
    public function attach(callable $callback) : void
    {
        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize() : ?int
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function tell() : int
    {
        throw Exception\UntellableStreamException::forCallbackStream();
    }

    /**
     * {@inheritdoc}
     */
    public function eof() : bool
    {
        return empty($this->callback);
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable() : bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        throw Exception\UnseekableStreamException::forCallbackStream();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind() : void
    {
        throw Exception\UnrewindableStreamException::forCallbackStream();
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable() : bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function write($string) : void
    {
        throw Exception\UnwritableStreamException::forCallbackStream();
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable() : bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length) : string
    {
        throw Exception\UnreadableStreamException::forCallbackStream();
    }

    /**
     * {@inheritdoc}
     */
    public function getContents() : string
    {
        $callback = $this->detach();
        $contents = $callback ? $callback() : '';
        return (string) $contents;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null)
    {
        $metadata = [
            'eof' => $this->eof(),
            'stream_type' => 'callback',
            'seekable' => false
        ];

        if (null === $key) {
            return $metadata;
        }

        if (! array_key_exists($key, $metadata)) {
            return null;
        }

        return $metadata[$key];
    }
}
