<?php

namespace Http\Message\StreamFactory;

use Http\Message\StreamFactory;

/**
 * Creates Guzzle streams.
 *
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
 */
final class GuzzleStreamFactory implements StreamFactory
{
    /**
     * {@inheritdoc}
     */
    public function createStream($body = null)
    {
        return \GuzzleHttp\Psr7\stream_for($body);
    }
}
