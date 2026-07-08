<?php

namespace Http\Client\Common\Plugin\Tests\Cache\Generator;

use Http\Client\Common\Plugin\Cache\Generator\CacheKeyGenerator;
use Http\Client\Common\Plugin\Cache\Generator\SimpleGenerator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class SimpleGeneratorTest extends TestCase
{
    public function testInterface(): void
    {
        $generator = new SimpleGenerator();
        $this->assertInstanceOf(CacheKeyGenerator::class, $generator);
    }

    public function testGenerateCacheFromRequest(): void
    {
        $uri = $this->createMock(UriInterface::class);
        $uri->expects($this->once())->method('__toString')->willReturn('http://example.com/foo');

        $body = $this->createMock(StreamInterface::class);
        $body->expects($this->once())->method('__toString')->willReturn('bar');

        $request = $this->createMock(RequestInterface::class);
        $request->expects($this->once())->method('getMethod')->willReturn('GET');
        $request->expects($this->once())->method('getUri')->willReturn($uri);
        $request->expects($this->once())->method('getBody')->willReturn($body);

        $generator = new SimpleGenerator();

        $this->assertSame('GET http://example.com/foo bar', $generator->generate($request));
    }

    public function testGenerateCacheFromRequestWithNoBody(): void
    {
        $uri = $this->createMock(UriInterface::class);
        $uri->expects($this->once())->method('__toString')->willReturn('http://example.com/foo');

        $body = $this->createMock(StreamInterface::class);
        $body->expects($this->once())->method('__toString')->willReturn('');

        $request = $this->createMock(RequestInterface::class);
        $request->expects($this->once())->method('getMethod')->willReturn('GET');
        $request->expects($this->once())->method('getUri')->willReturn($uri);
        $request->expects($this->once())->method('getBody')->willReturn($body);

        $generator = new SimpleGenerator();

        $this->assertSame('GET http://example.com/foo', $generator->generate($request));
    }
}
