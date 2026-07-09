<?php

namespace Phrity\Net;

use Phrity\Util\Interpolator\InterpolatorTrait;
use RuntimeException;
use Throwable;

/**
 * StreamException class.
 */
class StreamException extends RuntimeException
{
    use InterpolatorTrait;

    // Stream errors
    public const STREAM_DETACHED = 1000;
    public const NOT_READABLE = 1010;
    public const NOT_WRITABLE = 1011;
    public const NOT_SEEKABLE = 1012;
    public const FAIL_READ = 1020;
    public const FAIL_WRITE = 1021;
    public const FAIL_SEEK = 1022;
    public const FAIL_TELL = 1023;
    public const FAIL_CONTENTS = 1024;
    public const FAIL_GETS = 1025;
    public const FAIL_SELECT = 1026;

    // Client errors
    public const CLIENT_CONNECT_ERR = 2000;

    // Server errors
    public const SCHEME_TRANSPORT = 3000;
    public const SCHEME_HANDLER = 3001;
    public const SERVER_SOCKET_ERR = 3010;
    public const SERVER_CLOSED = 3011;
    public const SERVER_ACCEPT_ERR = 3012;

    // Collection errors
    public const COLLECT_KEY_CONFLICT = 4000;
    public const COLLECT_SELECT_ERR = 4001;

    // Context errors
    public const CONTEXT_SET_ERR = 5000;

    /** @var array<int, string> */
    private static array $messages = [
        self::STREAM_DETACHED => 'Stream is detached.',
        self::NOT_READABLE => 'Stream is not readable.',
        self::NOT_WRITABLE => 'Stream is not writable.',
        self::NOT_SEEKABLE => 'Stream is not seekable.',
        self::FAIL_READ => 'Failed read() on stream.',
        self::FAIL_WRITE => 'Failed write() on stream.',
        self::FAIL_SEEK => 'Failed seek() on stream.',
        self::FAIL_TELL => 'Failed tell() on stream.',
        self::FAIL_CONTENTS => 'Failed getContents() on stream.',
        self::FAIL_GETS => 'Failed gets() on stream.',
        self::FAIL_SELECT => 'Failed select() on stream.',
        self::CLIENT_CONNECT_ERR => 'Client could not connect to "{uri}".',
        self::SCHEME_TRANSPORT => 'Scheme "{scheme}" is not supported.',
        self::SCHEME_HANDLER => 'Could not handle scheme "{scheme}".',
        self::SERVER_SOCKET_ERR => 'Could not create socket for "{uri}".',
        self::SERVER_CLOSED => 'Server is closed.',
        self::SERVER_ACCEPT_ERR => 'Could not accept on socket.',
        self::COLLECT_KEY_CONFLICT => 'Stream with name "{key}" already attached.',
        self::COLLECT_SELECT_ERR => 'Failed to select streams for reading.',
        self::CONTEXT_SET_ERR => 'Failed to set option/param on context.',
    ];

    /**
     * Create exception.
     * @param int $code Error code
     * @param array<string, mixed> $data Additional data
     * @param Throwable|null $previous Previous exception
     */
    public function __construct(int $code, array $data = [], Throwable|null $previous = null)
    {
        $message = self::$messages[$code];
        $message = $this->interpolate($message, $data);
        if ($previous) {
            $message .= " ({$previous->getMessage()})";
        }
        parent::__construct($message, $code, $previous);
    }
}
