<?php

namespace Http\Client\Common\Plugin\Cache\Generator;

use Psr\Http\Message\RequestInterface;

/**
 * Generate a cache key by using HTTP headers.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class HeaderCacheKeyGenerator implements CacheKeyGenerator
{
    /**
     * The header names we should take into account when creating the cache key.
     *
     * @var array
     */
    private $headerNames;

    /**
     * @param $headerNames
     */
    public function __construct(array $headerNames)
    {
        $this->headerNames = $headerNames;
    }

    public function generate(RequestInterface $request)
    {
        $concatenatedHeaders = [];
        foreach ($this->headerNames as $headerName) {
            $concatenatedHeaders[] = sprintf(' %s:"%s"', $headerName, $request->getHeaderLine($headerName));
        }

        return $request->getMethod().' '.$request->getUri().implode('', $concatenatedHeaders).' '.$request->getBody();
    }
}
