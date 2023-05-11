<?php

namespace Http\Message\Encoding;

use Clue\StreamFilter as Filter;
use Http\Message\Decorator\StreamDecorator;
use Psr\Http\Message\StreamInterface;

/**
 * A filtered stream has a filter for filtering output and a filter for filtering input made to a underlying stream.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
abstract class FilteredStream implements StreamInterface
{
    use StreamDecorator {
        rewind as private doRewind;
        seek as private doSeek;
    }
    const BUFFER_SIZE = 8192;

    /**
     * @var callable
     */
    protected $readFilterCallback;

    /**
     * @var resource
     *
     * @deprecated since version 1.5, will be removed in 2.0
     */
    protected $readFilter;

    /**
     * @var callable
     *
     * @deprecated since version 1.5, will be removed in 2.0
     */
    protected $writeFilterCallback;

    /**
     * @var resource
     *
     * @deprecated since version 1.5, will be removed in 2.0
     */
    protected $writeFilter;

    /**
     * Internal buffer.
     *
     * @var string
     */
    protected $buffer = '';

    /**
     * @param mixed|null $readFilterOptions
     * @param mixed|null $writeFilterOptions deprecated since 1.5, will be removed in 2.0
     */
    public function __construct(StreamInterface $stream, $readFilterOptions = null, $writeFilterOptions = null)
    {
        if (null !== $readFilterOptions) {
            $this->readFilterCallback = Filter\fun($this->readFilter(), $readFilterOptions);
        } else {
            $this->readFilterCallback = Filter\fun($this->readFilter());
        }

        if (null !== $writeFilterOptions) {
            $this->writeFilterCallback = Filter\fun($this->writeFilter(), $writeFilterOptions);

            @trigger_error('The $writeFilterOptions argument is deprecated since version 1.5 and will be removed in 2.0.', E_USER_DEPRECATED);
        } else {
            $this->writeFilterCallback = Filter\fun($this->writeFilter());
        }

        $this->stream = $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        if (strlen($this->buffer) >= $length) {
            $read = substr($this->buffer, 0, $length);
            $this->buffer = substr($this->buffer, $length);

            return $read;
        }

        if ($this->stream->eof()) {
            $buffer = $this->buffer;
            $this->buffer = '';

            return $buffer;
        }

        $read = $this->buffer;
        $this->buffer = '';
        $this->fill();

        return $read.$this->read($length - strlen($read));
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        return $this->stream->eof() && '' === $this->buffer;
    }

    /**
     * Buffer is filled by reading underlying stream.
     *
     * Callback is reading once more even if the stream is ended.
     * This allow to get last data in the PHP buffer otherwise this
     * bug is present : https://bugs.php.net/bug.php?id=48725
     */
    protected function fill()
    {
        $readFilterCallback = $this->readFilterCallback;
        $this->buffer .= $readFilterCallback($this->stream->read(self::BUFFER_SIZE));

        if ($this->stream->eof()) {
            $this->buffer .= $readFilterCallback();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        $buffer = '';

        while (!$this->eof()) {
            $buf = $this->read(self::BUFFER_SIZE);
            // Using a loose equality here to match on '' and false.
            if (null == $buf) {
                break;
            }

            $buffer .= $buf;
        }

        return $buffer;
    }

    /**
     * Always returns null because we can't tell the size of a stream when we filter.
     */
    public function getSize()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getContents();
    }

    /**
     * Filtered streams are not seekable.
     *
     * We would need to buffer and process everything to allow seeking.
     */
    public function isSeekable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        @trigger_error('Filtered streams are not seekable. This method will start raising an exception in the next major version', E_USER_DEPRECATED);
        $this->doRewind();
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        @trigger_error('Filtered streams are not seekable. This method will start raising an exception in the next major version', E_USER_DEPRECATED);
        $this->doSeek($offset, $whence);
    }

    /**
     * Returns the read filter name.
     *
     * @return string
     *
     * @deprecated since version 1.5, will be removed in 2.0
     */
    public function getReadFilter()
    {
        @trigger_error('The '.__CLASS__.'::'.__METHOD__.' method is deprecated since version 1.5 and will be removed in 2.0.', E_USER_DEPRECATED);

        return $this->readFilter();
    }

    /**
     * Returns the write filter name.
     *
     * @return string
     */
    abstract protected function readFilter();

    /**
     * Returns the write filter name.
     *
     * @return string
     *
     * @deprecated since version 1.5, will be removed in 2.0
     */
    public function getWriteFilter()
    {
        @trigger_error('The '.__CLASS__.'::'.__METHOD__.' method is deprecated since version 1.5 and will be removed in 2.0.', E_USER_DEPRECATED);

        return $this->writeFilter();
    }

    /**
     * Returns the write filter name.
     *
     * @return string
     */
    abstract protected function writeFilter();
}
