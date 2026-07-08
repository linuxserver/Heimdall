<?php

namespace Http\Client\Common\Plugin\Tests\Cache;

use Http\Client\Common\Plugin;
use Http\Client\Common\Plugin\Cache\Generator\SimpleGenerator;
use Http\Client\Common\Plugin\CachePlugin;
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
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class CachePluginTest extends TestCase
{
    private function createPlugin(CacheItemPoolInterface $pool, StreamFactoryInterface $streamFactory, array $config = []): CachePlugin
    {
        $defaults = [
            'default_ttl' => 60,
            'cache_lifetime' => 1000,
        ];

        return new CachePlugin($pool, $streamFactory, array_merge($defaults, $config));
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

    public function testCacheResponses(): void
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
        $request->method('getMethod')->willReturn('GET');
        $request->method('getUri')->willReturn($uri);
        $request->method('getBody')->willReturn($stream);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($stream);
        $response->method('getHeader')->willReturn([]);
        $response->expects($this->once())->method('withBody')->with($stream)->willReturnSelf();

        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')->willReturn(false);
        $item->expects($this->once())->method('expiresAfter')->with(1060)->willReturnSelf();
        $item->expects($this->once())->method('set')->with($this->cacheItemConstraint([
            'response' => $response,
            'body' => $httpBody,
            'expiresAt' => 0,
            'createdAt' => 0,
            'etag' => [],
        ]))->willReturnSelf();

        $pool = $this->createMock(CacheItemPoolInterface::class);
        $pool->expects($this->once())->method('getItem')->with($this->anything())->willReturn($item);
        $pool->expects($this->once())->method('save')->with($item);

        $plugin = $this->createPlugin($pool, $streamFactory);
        $result = $plugin->handleRequest($request, $this->createFulfilledNext($response), function () {
        })->wait();

        self::assertSame($response, $result);
    }

    public function testDoNotStoreFailedResponses(): void
    {
        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')->willReturn(false);

        $pool = $this->createMock(CacheItemPoolInterface::class);
        $pool->expects($this->once())->method('getItem')->with($this->anything())->willReturn($item);
        $pool->expects($this->never())->method('save');

        $uri = $this->createMock(UriInterface::class);
        $uri->method('__toString')->willReturn('https://example.com/');

        $requestBody = $this->createMock(StreamInterface::class);
        $requestBody->method('__toString')->willReturn('body');

        $request = $this->createMock(RequestInterface::class);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getUri')->willReturn($uri);
        $request->method('getBody')->willReturn($requestBody);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(400);

        $plugin = $this->createPlugin($pool, $this->createMock(StreamFactoryInterface::class));

        $result = $plugin->handleRequest($request, $this->createFulfilledNext($response), function () {
        })->wait();

        self::assertSame($response, $result);
    }

    public function testDoNotStorePostRequestsByDefault(): void
    {
        $pool = $this->createMock(CacheItemPoolInterface::class);
        $pool->expects($this->never())->method('getItem');

        $request = $this->createMock(RequestInterface::class);
        $request->method('getMethod')->willReturn('POST');

        $response = $this->createMock(ResponseInterface::class);

        $plugin = $this->createPlugin($pool, $this->createMock(StreamFactoryInterface::class));

        $result = $plugin->handleRequest($request, $this->createFulfilledNext($response), function () {
        })->wait();

        self::assertSame($response, $result);
    }

    public function testStorePostRequestsWhenAllowed(): void
    {
        $httpBody = 'hello=world';

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
        $request->method('getMethod')->willReturn('POST');
        $request->method('getUri')->willReturn($uri);
        $request->method('getBody')->willReturn($stream);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($stream);
        $response->method('getHeader')->willReturn([]);
        $response->expects($this->once())->method('withBody')->with($stream)->willReturnSelf();

        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')->willReturn(false);
        $item->expects($this->once())->method('expiresAfter')->with(1060)->willReturnSelf();
        $item->expects($this->once())->method('set')->with($this->cacheItemConstraint([
            'response' => $response,
            'body' => $httpBody,
            'expiresAt' => 0,
            'createdAt' => 0,
            'etag' => [],
        ]))->willReturnSelf();

        $pool = $this->createMock(CacheItemPoolInterface::class);
        $pool->expects($this->once())->method('getItem')->willReturn($item);
        $pool->expects($this->once())->method('save')->with($item);

        $plugin = $this->createPlugin($pool, $streamFactory, [
            'methods' => ['GET', 'HEAD', 'POST'],
        ]);

        $result = $plugin->handleRequest($request, $this->createFulfilledNext($response), function () {
        })->wait();

        self::assertSame($response, $result);
    }

    /**
     * @dataProvider invalidMethodProvider
     */
    public function testDoNotAllowInvalidRequestMethods(array $methods): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->createPlugin(
            $this->createMock(CacheItemPoolInterface::class),
            $this->createMock(StreamFactoryInterface::class),
            [
                'methods' => $methods,
            ]
        );
    }

    public function invalidMethodProvider(): array
    {
        return [
            [['GET', 'HEAD', 'POST ']],
            [['GET', 'HEAD"', 'POST']],
            [['GET', 'head', 'POST']],
        ];
    }

    public function testCalculateAgeFromResponse(): void
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
        $request->method('getMethod')->willReturn('GET');
        $request->method('getUri')->willReturn($uri);
        $request->method('getBody')->willReturn($stream);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($stream);
        $response->method('getHeader')->willReturnCallback(function ($header) {
            if ('Cache-Control' === $header) {
                return ['max-age=40'];
            }

            if ('Age' === $header) {
                return ['15'];
            }

            return [];
        });
        $response->expects($this->once())->method('withBody')->with($stream)->willReturnSelf();

        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')->willReturn(false);
        $item->expects($this->once())->method('set')->with($this->cacheItemConstraint([
            'response' => $response,
            'body' => $httpBody,
            'expiresAt' => 0,
            'createdAt' => 0,
            'etag' => [],
        ]))->willReturnSelf();
        $item->expects($this->once())->method('expiresAfter')->with(1025)->willReturnSelf();

        $pool = $this->createMock(CacheItemPoolInterface::class);
        $pool->method('getItem')->willReturn($item);
        $pool->expects($this->once())->method('save')->with($item);

        $plugin = $this->createPlugin($pool, $streamFactory);

        $result = $plugin->handleRequest($request, $this->createFulfilledNext($response), function () {
        })->wait();

        self::assertSame($response, $result);
    }

    public function testSaveEtag(): void
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

    public function testAddEtagAndModifiedSinceToRequest(): void
    {
        $httpBody = 'body';

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
        $request->expects($this->exactly(2))
            ->method('withHeader')
            ->withConsecutive(
                ['If-Modified-Since', 'Thursday, 01-Jan-70 01:18:31 GMT'],
                ['If-None-Match', 'foo_etag']
            )
            ->willReturnSelf();

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(304);

        $item = $this->createMock(CacheItemInterface::class);
        $item->expects($this->exactly(2))->method('isHit')->willReturnOnConsecutiveCalls(true, false);
        $item->method('get')->willReturn([
            'response' => $response,
            'body' => $httpBody,
            'expiresAt' => 0,
            'createdAt' => 4711,
            'etag' => ['foo_etag'],
        ]);

        $pool = $this->createMock(CacheItemPoolInterface::class);
        $pool->expects($this->once())->method('getItem')->willReturn($item);

        $plugin = $this->createPlugin($pool, $streamFactory);

        $result = $plugin->handleRequest($request, $this->createFulfilledNext($response), function () {
        })->wait();

        self::assertSame($response, $result);
    }

    public function testServeCachedResponse(): void
    {
        $httpBody = 'body';

        $stream = $this->createMock(StreamInterface::class);

        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $streamFactory->expects($this->once())->method('createStream')->with($httpBody)->willReturn($stream);

        $uri = $this->createMock(UriInterface::class);
        $uri->method('__toString')->willReturn('https://example.com/');

        $requestBody = $this->createMock(StreamInterface::class);
        $requestBody->method('__toString')->willReturn('');

        $request = $this->createMock(RequestInterface::class);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getUri')->willReturn($uri);
        $request->method('getBody')->willReturn($requestBody);

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('withBody')->with($stream)->willReturnSelf();

        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')->willReturn(true);
        $item->method('get')->willReturn([
            'response' => $response,
            'body' => $httpBody,
            'expiresAt' => time() + 1000000,
            'createdAt' => 4711,
            'etag' => [],
        ]);

        $pool = $this->createMock(CacheItemPoolInterface::class);
        $pool->expects($this->once())->method('getItem')->willReturn($item);

        $plugin = $this->createPlugin($pool, $streamFactory);

        $result = $plugin->handleRequest($request, $this->createFulfilledNext($response), function () {
        })->wait();

        self::assertSame($response, $result);
    }

    public function testServeAndResaveExpiredResponse(): void
    {
        $httpBody = 'body';

        $uri = $this->createMock(UriInterface::class);
        $uri->method('__toString')->willReturn('https://example.com/');

        $requestStream = $this->createMock(StreamInterface::class);
        $requestStream->method('__toString')->willReturn('');

        $request = $this->createMock(RequestInterface::class);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getUri')->willReturn($uri);
        $request->method('getBody')->willReturn($requestStream);
        $request->method('withHeader')->willReturnSelf();

        $stream = $this->createMock(StreamInterface::class);
        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $streamFactory->expects($this->once())->method('createStream')->with($httpBody)->willReturn($stream);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(304);
        $response->method('getHeader')->willReturn([]);
        $response->expects($this->once())->method('withBody')->with($stream)->willReturnSelf();

        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')->willReturn(true);
        $item->expects($this->once())->method('expiresAfter')->with(1060)->willReturnSelf();
        $item->method('get')->willReturn([
            'response' => $response,
            'body' => $httpBody,
            'expiresAt' => 0,
            'createdAt' => 4711,
            'etag' => ['foo_etag'],
        ]);
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

    public function testCachePrivateResponsesWhenAllowed(): void
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
        $request->method('getMethod')->willReturn('GET');
        $request->method('getUri')->willReturn($uri);
        $request->method('getBody')->willReturn($stream);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($stream);
        $response->method('getHeader')->willReturnCallback(function ($header) {
            if ('Cache-Control' === $header) {
                return ['private'];
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
            'etag' => [],
        ]))->willReturnSelf();

        $pool = $this->createMock(CacheItemPoolInterface::class);
        $pool->expects($this->once())->method('getItem')->willReturn($item);
        $pool->expects($this->once())->method('save')->with($item);

        $plugin = CachePlugin::clientCache($pool, $streamFactory, [
            'default_ttl' => 60,
            'cache_lifetime' => 1000,
        ]);

        $result = $plugin->handleRequest($request, $this->createFulfilledNext($response), function () {
        })->wait();

        self::assertSame($response, $result);
    }

    public function testDoNotStoreResponsesOfRequestsToBlacklistedPaths(): void
    {
        $httpBody = 'body';

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn($httpBody);
        $stream->method('isSeekable')->willReturn(true);

        $streamFactory = $this->createMock(StreamFactoryInterface::class);

        $uri = $this->createMock(UriInterface::class);
        $uri->method('__toString')->willReturn('https://example.com/foo');

        $request = $this->createMock(RequestInterface::class);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getUri')->willReturn($uri);
        $request->method('getBody')->willReturn($stream);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($stream);
        $response->method('getHeader')->willReturn([]);

        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')->willReturn(false);
        $item->expects($this->never())->method('set');

        $pool = $this->createMock(CacheItemPoolInterface::class);
        $pool->expects($this->once())->method('getItem')->willReturn($item);
        $pool->expects($this->never())->method('save');

        $plugin = CachePlugin::clientCache($pool, $streamFactory, [
            'default_ttl' => 60,
            'cache_lifetime' => 1000,
            'blacklisted_paths' => ['@/foo@'],
        ]);

        $result = $plugin->handleRequest($request, $this->createFulfilledNext($response), function () {
        })->wait();

        self::assertSame($response, $result);
    }

    public function testStoreResponsesOfRequestsNotInBlacklistedPaths(): void
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
        $request->method('getMethod')->willReturn('GET');
        $request->method('getUri')->willReturn($uri);
        $request->method('getBody')->willReturn($stream);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($stream);
        $response->method('getHeader')->willReturn([]);
        $response->expects($this->once())->method('withBody')->with($stream)->willReturnSelf();

        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')->willReturn(false);
        $item->expects($this->once())->method('expiresAfter')->with(1060)->willReturnSelf();
        $item->expects($this->once())->method('set')->with($this->cacheItemConstraint([
            'response' => $response,
            'body' => $httpBody,
            'expiresAt' => 0,
            'createdAt' => 0,
            'etag' => [],
        ]))->willReturnSelf();

        $pool = $this->createMock(CacheItemPoolInterface::class);
        $pool->expects($this->once())->method('getItem')->willReturn($item);
        $pool->expects($this->once())->method('save')->with($item);

        $plugin = CachePlugin::clientCache($pool, $streamFactory, [
            'default_ttl' => 60,
            'cache_lifetime' => 1000,
            'blacklisted_paths' => ['@/foo@'],
        ]);

        $result = $plugin->handleRequest($request, $this->createFulfilledNext($response), function () {
        })->wait();

        self::assertSame($response, $result);
    }

    public function testCustomCacheKeyGenerator(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())->method('rewind');
        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $streamFactory->expects($this->once())->method('createStream')->willReturn($stream);

        $request = $this->createMock(RequestInterface::class);
        $request->method('getMethod')->willReturn('GET');

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('withBody')->willReturnSelf();

        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')->willReturn(true);
        $item->method('get')->willReturn([
            'response' => $response,
            'body' => 'body',
            'expiresAt' => null,
            'createdAt' => 0,
            'etag' => [],
        ]);

        $pool = $this->createMock(CacheItemPoolInterface::class);
        $pool->expects($this->once())->method('getItem')->willReturn($item);

        $generator = $this->createMock(SimpleGenerator::class);
        $generator->expects($this->once())->method('generate')->with($request)->willReturn('foo');

        $plugin = CachePlugin::clientCache($pool, $streamFactory, [
            'cache_key_generator' => $generator,
        ]);

        $result = $plugin->handleRequest($request, $this->createFulfilledNext($response), function () {
        })->wait();

        self::assertSame($response, $result);
    }
}
