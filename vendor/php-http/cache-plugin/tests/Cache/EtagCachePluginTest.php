<?php

namespace Http\Client\Common\Plugin\Tests\Cache;

use Http\Client\Common\Plugin;
use Http\Client\Common\Plugin\EtagCachePlugin;
use Http\Promise\FulfilledPromise;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class EtagCachePluginTest extends TestCase
{
    private function createPlugin(CacheItemPoolInterface $pool, StreamFactoryInterface $streamFactory, array $config = []): EtagCachePlugin
    {
        $defaults = [
            'default_ttl' => 60,
            'cache_lifetime' => 1000,
        ];

        return new EtagCachePlugin($pool, $streamFactory, array_merge($defaults, $config));
    }

    private function cacheItemConstraint(array $expected): Callback
    {
        return $this->callback(function ($actual) use ($expected) {
            if (!is_array($actual)) {
                return false;
            }

            foreach ($expected as $key => $value) {
                if (!array_key_exists($key, $actual)) {
                    return false;
                }

                if (in_array($key, ['expiresAt', 'createdAt'], true)) {
                    continue;
                }

                if ($actual[$key] !== $value) {
                    return false;
                }
            }

            return true;
        });
    }

    private function createFulfilledNext(ResponseInterface $response): callable
    {
        return function (RequestInterface $request) use ($response) {
            return new FulfilledPromise($response);
        };
    }

    public function testInterface(): void
    {
        $plugin = $this->createPlugin(
            $this->createMock(CacheItemPoolInterface::class),
            $this->createMock(StreamFactoryInterface::class)
        );

        self::assertInstanceOf(Plugin::class, $plugin);
    }

    public function testDoesNotCacheResponseWithoutEtag(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn('');

        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $streamFactory->expects($this->never())->method('createStream');

        $uri = $this->createMock(UriInterface::class);
        $uri->method('__toString')->willReturn('https://example.com/');

        $request = $this->createMock(RequestInterface::class);
        $request->method('getBody')->willReturn($stream);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getUri')->willReturn($uri);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getHeader')->willReturn([]);

        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')->willReturn(false);
        $item->expects($this->never())->method('set');

        $pool = $this->createMock(CacheItemPoolInterface::class);
        $pool->expects($this->once())->method('getItem')->willReturn($item);
        $pool->expects($this->never())->method('save');

        $plugin = $this->createPlugin($pool, $streamFactory);

        $result = $plugin->handleRequest($request, $this->createFulfilledNext($response), function () {
        })->wait();

        self::assertSame($response, $result);
    }

    public function testCachesResponseWithEtag(): void
    {
        $httpBody = 'body';

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn($httpBody);
        $stream->method('isSeekable')->willReturn(true);
        $stream->expects($this->once())->method('rewind');
        $stream->expects($this->once())->method('detach');

        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $streamFactory->expects($this->once())->method('createStream')->with($httpBody)->willReturn($stream);

        $uri = $this->createMock(UriInterface::class);
        $uri->method('__toString')->willReturn('https://example.com/');

        $request = $this->createMock(RequestInterface::class);
        $request->method('getBody')->willReturn($stream);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getUri')->willReturn($uri);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($stream);
        $response->method('getHeader')->willReturnCallback(function ($header) {
            if ('ETag' === $header) {
                return ['foo_etag'];
            }

            return [];
        });
        $response->expects($this->once())->method('withBody')->with($stream)->willReturnSelf();

        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')->willReturn(false);
        $item->expects($this->once())->method('expiresAfter')->with(1060)->willReturnSelf();
        $item->expects($this->once())->method('set')->with($this->cacheItemConstraint([
            'response' => $response,
            'body' => $httpBody,
            'expiresAt' => 0,
            'createdAt' => 0,
            'etag' => ['foo_etag'],
        ]))->willReturnSelf();

        $pool = $this->createMock(CacheItemPoolInterface::class);
        $pool->expects($this->once())->method('getItem')->willReturn($item);
        $pool->expects($this->once())->method('save')->with($item);

        $plugin = $this->createPlugin($pool, $streamFactory);

        $result = $plugin->handleRequest($request, $this->createFulfilledNext($response), function () {
        })->wait();

        self::assertSame($response, $result);
    }

    public function testAlwaysRevalidatesCachedResponse(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn('');

        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $streamFactory->expects($this->never())->method('createStream');

        $uri = $this->createMock(UriInterface::class);
        $uri->method('__toString')->willReturn('https://example.com/');

        $request = $this->createMock(RequestInterface::class);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getUri')->willReturn($uri);
        $request->method('getBody')->willReturn($stream);
        $request->expects($this->once())->method('withHeader')->with('If-None-Match', 'foo_etag')->willReturnSelf();

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getHeader')->willReturn([]);

        $cachedResponse = $this->createMock(ResponseInterface::class);

        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')->willReturn(true);
        $item->method('get')->willReturn([
            'response' => $cachedResponse,
            'body' => 'cached',
            'expiresAt' => time() + 1000000,
            'createdAt' => 4711,
            'etag' => ['foo_etag'],
        ]);
        $item->expects($this->never())->method('set');

        $pool = $this->createMock(CacheItemPoolInterface::class);
        $pool->expects($this->once())->method('getItem')->willReturn($item);
        $pool->expects($this->never())->method('save');

        $plugin = $this->createPlugin($pool, $streamFactory);

        $result = $plugin->handleRequest($request, $this->createFulfilledNext($response), function () {
        })->wait();

        self::assertSame($response, $result);
    }

    public function testServesCachedResponseAfterNotModified(): void
    {
        $httpBody = 'body';

        $requestBody = $this->createMock(StreamInterface::class);
        $requestBody->method('__toString')->willReturn('');

        $cachedBody = $this->createMock(StreamInterface::class);
        $cachedBody->expects($this->once())->method('rewind');

        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $streamFactory->expects($this->once())->method('createStream')->with($httpBody)->willReturn($cachedBody);

        $uri = $this->createMock(UriInterface::class);
        $uri->method('__toString')->willReturn('https://example.com/');

        $request = $this->createMock(RequestInterface::class);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getUri')->willReturn($uri);
        $request->method('getBody')->willReturn($requestBody);
        $request->expects($this->once())->method('withHeader')->with('If-None-Match', 'foo_etag')->willReturnSelf();

        $notModifiedResponse = $this->createMock(ResponseInterface::class);
        $notModifiedResponse->method('getStatusCode')->willReturn(304);
        $notModifiedResponse->method('getHeader')->willReturn([]);

        $cachedResponse = $this->createMock(ResponseInterface::class);
        $cachedResponse->expects($this->once())->method('withBody')->with($cachedBody)->willReturnSelf();

        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')->willReturn(true);
        $item->method('get')->willReturn([
            'response' => $cachedResponse,
            'body' => $httpBody,
            'expiresAt' => 0,
            'createdAt' => 4711,
            'etag' => ['foo_etag'],
        ]);
        $item->expects($this->once())->method('expiresAfter')->with(1060)->willReturnSelf();
        $item->expects($this->once())->method('set')->with($this->cacheItemConstraint([
            'response' => $cachedResponse,
            'body' => $httpBody,
            'expiresAt' => 0,
            'createdAt' => 0,
            'etag' => ['foo_etag'],
        ]))->willReturnSelf();

        $pool = $this->createMock(CacheItemPoolInterface::class);
        $pool->expects($this->once())->method('getItem')->willReturn($item);
        $pool->expects($this->once())->method('save')->with($item);

        $plugin = $this->createPlugin($pool, $streamFactory);

        $result = $plugin->handleRequest($request, $this->createFulfilledNext($notModifiedResponse), function () {
        })->wait();

        self::assertSame($cachedResponse, $result);
    }

    public function testIgnoresCachedResponseWithoutEtag(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn('');

        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $streamFactory->expects($this->never())->method('createStream');

        $uri = $this->createMock(UriInterface::class);
        $uri->method('__toString')->willReturn('https://example.com/');

        $request = $this->createMock(RequestInterface::class);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getUri')->willReturn($uri);
        $request->method('getBody')->willReturn($stream);
        $request->expects($this->never())->method('withHeader');

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getHeader')->willReturn([]);

        $cachedResponse = $this->createMock(ResponseInterface::class);

        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')->willReturn(true);
        $item->method('get')->willReturn([
            'response' => $cachedResponse,
            'body' => 'cached',
            'expiresAt' => time() + 1000000,
            'createdAt' => 4711,
            'etag' => [],
        ]);
        $item->expects($this->never())->method('set');

        $pool = $this->createMock(CacheItemPoolInterface::class);
        $pool->expects($this->once())->method('getItem')->willReturn($item);
        $pool->expects($this->never())->method('save');

        $plugin = $this->createPlugin($pool, $streamFactory);

        $result = $plugin->handleRequest($request, $this->createFulfilledNext($response), function () {
        })->wait();

        self::assertSame($response, $result);
    }
}
