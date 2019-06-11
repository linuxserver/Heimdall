<?php

declare(strict_types=1);

/*
 * This file is part of Cache Plugin.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\CachePlugin;

use Exception;
use Http\Client\Common\Plugin;
use Http\Client\Common\Plugin\Cache\Generator\CacheKeyGenerator;
use Http\Client\Common\Plugin\Cache\Generator\HeaderCacheKeyGenerator;
use Http\Client\Common\Plugin\Exception\RewindStreamException;
use Http\Client\Common\Plugin\VersionBridgePlugin;
use Http\Message\StreamFactory;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * This is the response cache plugin class.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Graham Campbell <graham@alt-three.com>
 */
class CachePlugin implements Plugin
{
    use VersionBridgePlugin;

    /**
     * The cache item pool instance.
     *
     * @var \Psr\Cache\CacheItemPoolInterface
     */
    protected $pool;

    /**
     * The steam factory instance.
     *
     * @var \Http\Message\StreamFactory
     */
    protected $streamFactory;

    /**
     * The cache key generator instance.
     *
     * @var \Http\Client\Common\Plugin\Cache\Generator\CacheKeyGenerator
     */
    protected $generator;

    /**
     * The cache lifetime in seconds.
     *
     * @var int
     */
    protected $lifetime;

    /**
     * Create a new cache plugin.
     *
     * @param \Psr\Cache\CacheItemPoolInterface                                 $pool
     * @param \Http\Message\StreamFactory                                       $streamFactory
     * @param \Http\Client\Common\Plugin\Cache\Generator\CacheKeyGenerator|null $generator
     * @param int|null                                                          $lifetime
     *
     * @return void
     */
    public function __construct(CacheItemPoolInterface $pool, StreamFactory $streamFactory, CacheKeyGenerator $generator = null, int $lifetime = null)
    {
        $this->pool = $pool;
        $this->streamFactory = $streamFactory;
        $this->generator = $generator ?: new HeaderCacheKeyGenerator(['Authorization', 'Cookie', 'Accept', 'Content-type']);
        $this->lifetime = $lifetime ?: 3600 * 48;
    }

    /**
     * Handle the request and return the response coming from the next callable.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param callable                           $next
     * @param callable                           $first
     *
     * @return \Http\Promise\Promise
     */
    protected function doHandleRequest(RequestInterface $request, callable $next, callable $first)
    {
        $method = strtoupper($request->getMethod());
        // If the request not is cachable, move to $next
        if (!in_array($method, ['GET', 'HEAD'], true)) {
            return $next($request);
        }

        $cacheItem = $this->createCacheItem($request);

        if ($cacheItem->isHit() && ($etag = $this->getETag($cacheItem))) {
            $request = $request->withHeader('If-None-Match', $etag);
        }

        return $next($request)->then(function (ResponseInterface $response) use ($cacheItem) {
            if (304 === $response->getStatusCode()) {
                if (!$cacheItem->isHit()) {
                    // We do not have the item in cache. This plugin did not
                    // add If-None-Match headers. Return the response.
                    return $response;
                }

                // The cached response we have is still valid
                $cacheItem->set($cacheItem->get())->expiresAfter($this->lifetime);
                $this->pool->save($cacheItem);

                return $this->createResponseFromCacheItem($cacheItem);
            }

            if ($this->isCacheable($response)) {
                $bodyStream = $response->getBody();
                $body = $bodyStream->__toString();
                if ($bodyStream->isSeekable()) {
                    $bodyStream->rewind();
                } else {
                    $response = $response->withBody($this->streamFactory->createStream($body));
                }

                $cacheItem
                    ->expiresAfter($this->lifetime)
                    ->set([
                        'response' => $response,
                        'body'     => $body,
                        'etag'     => $response->getHeader('ETag'),
                    ]);
                $this->pool->save($cacheItem);
            }

            return $response;
        });
    }

    /**
     * Create a cache item for a request.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return \Psr\Cache\CacheItemInterface
     */
    protected function createCacheItem(RequestInterface $request)
    {
        $key = sha1($this->generator->generate($request));

        return $this->pool->getItem($key);
    }

    /**
     * Verify that we can cache this response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return bool
     */
    protected function isCacheable(ResponseInterface $response)
    {
        if (!in_array($response->getStatusCode(), [200, 203, 300, 301, 302, 404, 410])) {
            return false;
        }

        return !$this->getCacheControlDirective($response, 'no-cache');
    }

    /**
     * Get the value of a parameter in the cache control header.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param string                              $name
     *
     * @return bool|string
     */
    protected function getCacheControlDirective(ResponseInterface $response, string $name)
    {
        foreach ($response->getHeader('Cache-Control') as $header) {
            if (preg_match(sprintf('|%s=?([0-9]+)?|i', $name), $header, $matches)) {
                // return the value for $name if it exists
                if (isset($matches[1])) {
                    return $matches[1];
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Create a response from a cache item.
     *
     * @param \Psr\Cache\CacheItemInterface $cacheItem
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function createResponseFromCacheItem(CacheItemInterface $cacheItem)
    {
        $data = $cacheItem->get();

        $response = $data['response'];
        $stream = $this->streamFactory->createStream($data['body']);

        try {
            $stream->rewind();
        } catch (Exception $e) {
            throw new RewindStreamException('Cannot rewind stream.', 0, $e);
        }

        $response = $response->withBody($stream);

        return $response;
    }

    /**
     * Get the ETag from the cached response.
     *
     * @param \Psr\Cache\CacheItemInterface $cacheItem
     *
     * @return string|null
     */
    protected function getETag(CacheItemInterface $cacheItem)
    {
        $data = $cacheItem->get();

        foreach ($data['etag'] as $etag) {
            if (!empty($etag)) {
                return $etag;
            }
        }
    }
}
