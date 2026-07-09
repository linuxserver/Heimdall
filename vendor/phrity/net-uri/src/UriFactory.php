<?php

/**
 * File for Net\UriFactory class.
 * @package Phrity > Net > Uri
 * @see https://www.rfc-editor.org/rfc/rfc3986
 * @see https://www.php-fig.org/psr/psr-17/#26-urifactoryinterface
 */

namespace Phrity\Net;

use InvalidArgumentException;
use Psr\Http\Message\{
    UriFactoryInterface,
    UriInterface
};

/**
 * Net\UriFactory class.
 */
class UriFactory implements UriFactoryInterface
{
    // ---------- PSR-7 methods ---------------------------------------------------------------------------------------

    /**
     * Create a new URI.
     * @param string $uri The URI to parse.
     * @throws InvalidArgumentException If the given URI cannot be parsed
     */
    public function createUri(string $uri = ''): UriInterface
    {
        return new Uri($uri);
    }


    // ---------- Extensions ------------------------------------------------------------------------------------------

    /**
     * Create a new URI from existing.
     * @param UriInterface $uri A URI instance to create from.
     * @throws InvalidArgumentException If the given URI cannot be parsed
     */
    public function createUriFromInterface(UriInterface $uri): UriInterface
    {
        return new Uri($uri->__toString());
    }
}
