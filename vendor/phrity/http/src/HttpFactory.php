<?php

namespace Phrity\Http;

use BadMethodCallException;
use Psr\Http\Message\{
    RequestFactoryInterface,
    ResponseFactoryInterface,
    ServerRequestFactoryInterface,
    StreamFactoryInterface,
    UploadedFileFactoryInterface,
    UriFactoryInterface,
    RequestInterface,
    ResponseInterface,
    ServerRequestInterface,
    StreamInterface,
    UploadedFileInterface,
    UriInterface,
};

/**
 * Phrity\Http\HttpFactory
 */
class HttpFactory implements
    RequestFactoryInterface,
    ResponseFactoryInterface,
    ServerRequestFactoryInterface,
    StreamFactoryInterface,
    UploadedFileFactoryInterface,
    UriFactoryInterface
{
    public function __construct(
        private RequestFactoryInterface|null $requestFactory = null,
        private ResponseFactoryInterface|null $responseFactory = null,
        private ServerRequestFactoryInterface|null $serverRequestFactory = null,
        private StreamFactoryInterface|null $streamFactory = null,
        private UploadedFileFactoryInterface|null $uploadedFileFactory = null,
        private UriFactoryInterface|null $uriFactory = null,
    ) {
    }

    /**
     * @param string $method
     * @param UriInterface|string $uri
     */
    public function createRequest(string $method, mixed $uri): RequestInterface
    {
        if (is_null($this->requestFactory)) {
            throw new BadMethodCallException('HttpFactory.createRequest not implemented.');
        }
        return $this->requestFactory->createRequest($method, $uri);
    }

    /**
     * @param int $code
     * @param string $reasonPhrase
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        if (is_null($this->responseFactory)) {
            throw new BadMethodCallException('HttpFactory.createResponse not implemented.');
        }
        return $this->responseFactory->createResponse($code, $reasonPhrase);
    }

    /**
     * @param string $method
     * @param UriInterface|string $uri
     * @param array<string, mixed> $serverParams
     */
    public function createServerRequest(string $method, mixed $uri, array $serverParams = []): ServerRequestInterface
    {
        if (is_null($this->serverRequestFactory)) {
            throw new BadMethodCallException('HttpFactory.createServerRequest not implemented.');
        }
        return $this->serverRequestFactory->createServerRequest($method, $uri);
    }

    /**
     * @param string $content
     */
    public function createStream(string $content = ''): StreamInterface
    {
        if (is_null($this->streamFactory)) {
            throw new BadMethodCallException('HttpFactory.createStream not implemented.');
        }
        return $this->streamFactory->createStream($content);
    }

    /**
     * @param string $filename
     * @param string $mode
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        if (is_null($this->streamFactory)) {
            throw new BadMethodCallException('HttpFactory.createStreamFromFile not implemented.');
        }
        return $this->streamFactory->createStreamFromFile($filename, $mode);
    }

    /**
     * @param resource $resource
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        if (is_null($this->streamFactory)) {
            throw new BadMethodCallException('HttpFactory.createStreamFromResource not implemented.');
        }
        return $this->streamFactory->createStreamFromResource($resource);
    }

    /**
     * @param StreamInterface $stream
     * @param int $size
     * @param int $error
     * @param string $clientFilename
     * @param string $clientMediaType
     */
    public function createUploadedFile(
        StreamInterface $stream,
        int|null $size = null,
        int $error = UPLOAD_ERR_OK,
        string|null $clientFilename = null,
        string|null $clientMediaType = null
    ): UploadedFileInterface {
        if (is_null($this->uploadedFileFactory)) {
            throw new BadMethodCallException('HttpFactory.createUploadedFile not implemented.');
        }
        return $this->uploadedFileFactory->createUploadedFile(
            $stream,
            $size,
            $error,
            $clientFilename,
            $clientMediaType
        );
    }

    /**
     * @param string $uri The URI to parse.
     */
    public function createUri(string $uri = ''): UriInterface
    {
        if (is_null($this->uriFactory)) {
            throw new BadMethodCallException('HttpFactory.createUri not implemented.');
        }
        return $this->uriFactory->createUri($uri);
    }

    public static function create(object ...$implementations): self
    {
        $created = new self();
        foreach ($implementations as $implementation) {
            if ($implementation instanceof RequestFactoryInterface) {
                $created->requestFactory = $implementation;
            }
            if ($implementation instanceof ResponseFactoryInterface) {
                $created->responseFactory = $implementation;
            }
            if ($implementation instanceof ServerRequestFactoryInterface) {
                $created->serverRequestFactory = $implementation;
            }
            if ($implementation instanceof StreamFactoryInterface) {
                $created->streamFactory = $implementation;
            }
            if ($implementation instanceof UploadedFileFactoryInterface) {
                $created->uploadedFileFactory = $implementation;
            }
            if ($implementation instanceof UriFactoryInterface) {
                $created->uriFactory = $implementation;
            }
        }
        return $created;
    }
}
