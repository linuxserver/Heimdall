<?php

namespace Github\HttpClient\Plugin;

use Http\Client\Common\Plugin;
use Psr\Http\Message\RequestInterface;

/**
 * Prepend the URI with a string.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class PathPrepend implements Plugin
{
    private $path;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first)
    {
        $currentPath = $request->getUri()->getPath();
        if (strpos($currentPath, $this->path) !== 0) {
            $uri = $request->getUri()->withPath($this->path.$currentPath);
            $request = $request->withUri($uri);
        }

        return $next($request);
    }
}
