<?php

namespace Http\Client\Common\Plugin\Tests\Cache\Generator;

use Http\Client\Common\Plugin\Cache\Generator\CacheKeyGenerator;
use Http\Client\Common\Plugin\Cache\Generator\HeaderCacheKeyGenerator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class HeaderCacheKeyGeneratorTest extends TestCase
{
    public function testInterface(): void
    {
        $this->assertInstanceOf(CacheKeyGenerator::class, new HeaderCacheKeyGenerator(['Authorization', 'Content-Type']));
    }

    public function testGenerateCacheFromRequest(): void
    {
        $uri = $this->createMock(UriInterface::class);
        $uri->expects($this->once())->method('__toString')->willReturn('http://example.com/foo');

        $body = $this->createMock(StreamInterface::class);
        $body->expects($this->once())->method('__toString')->willReturn('');

        $request = $this->createMock(RequestInterface::class);
        $request->expects($this->once())->method('getMethod')->willReturn('GET');
        $request->expects($this->once())->method('getUri')->willReturn($uri);
        $request->expects($this->exactly(2))->method('getHeaderLine')->willReturnMap([
            ['Authorization', 'bar'],
            ['Content-Type', 'application/baz'],
        ]);
        $request->expects($this->once())->method('getBody')->willReturn($body);

        $generator = new HeaderCacheKeyGenerator(['Authorization', 'Content-Type']);

        $this->assertSame(
            'GET http://example.com/foo Authorization:"bar" Content-Type:"application/baz" ',
            $generator->generate($request)
        );
    }
}
