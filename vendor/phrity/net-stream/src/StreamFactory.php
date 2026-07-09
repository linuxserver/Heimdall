<?php

namespace Phrity\Net;

use InvalidArgumentException;
use Phrity\Util\ErrorHandler;
use Psr\Http\Message\{
    StreamFactoryInterface,
    UriInterface
};
use RuntimeException;

/**
 * StreamFactory class.
 * @see https://www.php-fig.org/psr/psr-17/#24-streamfactoryinterface
 */
class StreamFactory implements StreamFactoryInterface
{
    /** @var array<string> */
    private static array $modes = ['r', 'r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+', 'e'];

    private ErrorHandler $handler;

    /**
     * Create new stream wrapper instance.
     */
    public function __construct()
    {
        $this->handler = new ErrorHandler();
    }


    // ---------- PSR-17 methods --------------------------------------------------------------------------------------

    /**
     * Create a new stream from a string.
     * @param string $content String content with which to populate the stream.
     * @return Stream A stream instance.
     */
    public function createStream(string $content = ''): Stream
    {
        $resource = $this->createResource('php://temp', 'r+');
        fwrite($resource, $content);
        return $this->createStreamFromResource($resource);
    }

    /**
     * Create a stream from an existing file.
     * @param string $filename The filename or stream URI to use as basis of stream.
     * @param string $mode The mode with which to open the underlying filename/stream.
     * @throws InvalidArgumentException If the mode is invalid.
     * @return Stream A stream instance.
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): Stream
    {
        if (!in_array($mode, self::$modes)) {
            throw new InvalidArgumentException("Invalid mode '{$mode}'.");
        }
        $resource = $this->createResource($filename, $mode);
        return $this->createStreamFromResource($resource);
    }

    /**
     * Create a new stream from an existing resource.
     * The stream MUST be readable and may be writable.
     * @param resource $resource The PHP resource to use as the basis for the stream.
     * @return Stream A stream instance.
     */
    public function createStreamFromResource($resource): Stream
    {
        return new Stream($resource);
    }


    // ---------- Extensions ------------------------------------------------------------------------------------------

    /**
     * Create a new socket client.
     * @param UriInterface $uri The URI to connect to.
     * @return SocketClient A socket client instance.
     */
    public function createSocketClient(UriInterface $uri, Context|null $context = null): SocketClient
    {
        return new SocketClient($uri, $context);
    }

    /**
     * Create a new socket server.
     * @param UriInterface $uri The URI to create server on.
     * @return SocketServer A socket server instance.
     */
    public function createSocketServer(UriInterface $uri, Context|null $context = null): SocketServer
    {
        return new SocketServer($uri, $context);
    }

    /**
     * Create a new ocket stream from an existing resource.
     * The stream MUST be readable and may be writable.
     * @param resource $resource The PHP resource to use as the basis for the stream.
     * @return SocketStream A socket stream instance.
     */
    public function createSocketStreamFromResource($resource): SocketStream
    {
        return new SocketStream($resource);
    }

    /**
     * Create a new stream collection.
     * @return StreamCollection A stream collection.
     */
    public function createStreamCollection(): StreamCollection
    {
        return new StreamCollection();
    }


    // ---------- Helpers ---------------------------------------------------------------------------------------------

    /**
     * @return resource
     * @throws RuntimeException If fails to open resource
     */
    private function createResource(string $filename, string $mode)
    {
        /** @throws RuntimeException */
        return $this->handler->with(function () use ($filename, $mode) {
            /** @var resource $resource */
            $resource = fopen($filename, $mode);
            return $resource;
        }, new RuntimeException("Could not open '{$filename}'."));
    }
}
