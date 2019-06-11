<?php
/**
 * @see       https://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2015-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Diactoros\Response;

use Psr\Http\Message\UriInterface;
use Zend\Diactoros\Exception;
use Zend\Diactoros\Response;

use function get_class;
use function gettype;
use function is_object;
use function is_string;
use function sprintf;

/**
 * Produce a redirect response.
 */
class RedirectResponse extends Response
{
    /**
     * Create a redirect response.
     *
     * Produces a redirect response with a Location header and the given status
     * (302 by default).
     *
     * Note: this method overwrites the `location` $headers value.
     *
     * @param string|UriInterface $uri URI for the Location header.
     * @param int $status Integer status code for the redirect; 302 by default.
     * @param array $headers Array of headers to use at initialization.
     */
    public function __construct($uri, int $status = 302, array $headers = [])
    {
        if (! is_string($uri) && ! $uri instanceof UriInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Uri provided to %s MUST be a string or Psr\Http\Message\UriInterface instance; received "%s"',
                __CLASS__,
                (is_object($uri) ? get_class($uri) : gettype($uri))
            ));
        }

        $headers['location'] = [(string) $uri];
        parent::__construct('php://temp', $status, $headers);
    }
}
