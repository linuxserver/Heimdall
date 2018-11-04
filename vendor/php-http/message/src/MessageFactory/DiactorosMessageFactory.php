<?php

namespace Http\Message\MessageFactory;

use Http\Message\StreamFactory\DiactorosStreamFactory;
use Http\Message\MessageFactory;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response;

/**
 * Creates Diactoros messages.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
final class DiactorosMessageFactory implements MessageFactory
{
    /**
     * @var DiactorosStreamFactory
     */
    private $streamFactory;

    public function __construct()
    {
        $this->streamFactory = new DiactorosStreamFactory();
    }

    /**
     * {@inheritdoc}
     */
    public function createRequest(
        $method,
        $uri,
        array $headers = [],
        $body = null,
        $protocolVersion = '1.1'
    ) {
        return (new Request(
            $uri,
            $method,
            $this->streamFactory->createStream($body),
            $headers
        ))->withProtocolVersion($protocolVersion);
    }

    /**
     * {@inheritdoc}
     */
    public function createResponse(
        $statusCode = 200,
        $reasonPhrase = null,
        array $headers = [],
        $body = null,
        $protocolVersion = '1.1'
    ) {
        return (new Response(
            $this->streamFactory->createStream($body),
            $statusCode,
            $headers
        ))->withProtocolVersion($protocolVersion);
    }
}
