<?php

namespace Http\Message\StreamFactory;

use Http\Message\StreamFactory;
use Psr\Http\Message\StreamInterface;
use Slim\Http\Stream;

/**
 * Creates Slim 3 streams.
 *
 * @author Mika Tuupola <tuupola@appelsiini.net>
 */
final class SlimStreamFactory implements StreamFactory
{
    /**
     * {@inheritdoc}
     */
    public function createStream($body = null)
    {
        if ($body instanceof StreamInterface) {
            return $body;
        }

        if (is_resource($body)) {
            return new Stream($body);
        }

        $resource = fopen('php://memory', 'r+');
        $stream = new Stream($resource);
        if (null !== $body && '' !== $body) {
            $stream->write((string) $body);
        }

        return $stream;
    }
}
