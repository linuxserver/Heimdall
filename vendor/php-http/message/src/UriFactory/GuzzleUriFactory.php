<?php

namespace Http\Message\UriFactory;

use GuzzleHttp\Psr7;
use Http\Message\UriFactory;

/**
 * Creates Guzzle URI.
 *
 * @author David de Boer <david@ddeboer.nl>
 */
final class GuzzleUriFactory implements UriFactory
{
    /**
     * {@inheritdoc}
     */
    public function createUri($uri)
    {
        return Psr7\uri_for($uri);
    }
}
