<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group time-sensitive
 */
class ResponseTest extends ResponseTestCase
{
    public function testCreate()
    {
        $response = Response::create('foo', 301, array('Foo' => 'bar'));

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals('bar', $response->headers->get('foo'));
    }

    public function testToString()
    {
        $response = new Response();
        $response = explode("\r\n", $response);
        $this->assertEquals('HTTP/1.0 200 OK', $response[0]);
        $this->assertEquals('Cache-Control: no-cache, private', $response[1]);
    }

    public function testClone()
    {
        $response = new Response();
        $responseClone = clone $response;
        $this->assertEquals($response, $responseClone);
    }

    public function testSendHeaders()
    {
        $response = new Response();
        $headers = $response->sendHeaders();
        $this->assertObjectHasAttribute('headers', $headers);
        $this->assertObjectHasAttribute('content', $headers);
        $this->assertObjectHasAttribute('version', $headers);
        $this->assertObjectHasAttribute('statusCode', $headers);
        $this->assertObjectHasAttribute('statusText', $headers);
        $this->assertObjectHasAttribute('charset', $headers);
    }

    public function testSend()
    {
        $response = new Response();
        $responseSend = $response->send();
        $this->assertObjectHasAttribute('headers', $responseSend);
        $this->assertObjectHasAttribute('content', $responseSend);
        $this->assertObjectHasAttribute('version', $responseSend);
        $this->assertObjectHasAttribute('statusCode', $responseSend);
        $this->assertObjectHasAttribute('statusText', $responseSend);
        $this->assertObjectHasAttribute('charset', $responseSend);
    }

    public function testGetCharset()
    {
        $response = new Response();
        $charsetOrigin = 'UTF-8';
        $response->setCharset($charsetOrigin);
        $charset = $response->getCharset();
        $this->assertEquals($charsetOrigin, $charset);
    }

    public function testIsCacheable()
    {
        $response = new Response();
        $this->assertFalse($response->isCacheable());
    }

    public function testIsCacheableWithErrorCode()
    {
        $response = new Response('', 500);
        $this->assertFalse($response->isCacheable());
    }

    public function testIsCacheableWithNoStoreDirective()
    {
        $response = new Response();
        $response->headers->set('cache-control', 'private');
        $this->assertFalse($response->isCacheable());
    }

    public function testIsCacheableWithSetTtl()
    {
        $response = new Response();
        $response->setTtl(10);
        $this->assertTrue($response->isCacheable());
    }

    public function testMustRevalidate()
    {
        $response = new Response();
        $this->assertFalse($response->mustRevalidate());
    }

    public function testMustRevalidateWithMustRevalidateCacheControlHeader()
    {
        $response = new Response();
        $response->headers->set('cache-control', 'must-revalidate');

        $this->assertTrue($response->mustRevalidate());
    }

    public function testMustRevalidateWithProxyRevalidateCacheControlHeader()
    {
        $response = new Response();
        $response->headers->set('cache-control', 'proxy-revalidate');

        $this->assertTrue($response->mustRevalidate());
    }

    public function testSetNotModified()
    {
        $response = new Response('foo');
        $modified = $response->setNotModified();
        $this->assertObjectHasAttribute('headers', $modified);
        $this->assertObjectHasAttribute('content', $modified);
        $this->assertObjectHasAttribute('version', $modified);
        $this->assertObjectHasAttribute('statusCode', $modified);
        $this->assertObjectHasAttribute('statusText', $modified);
        $this->assertObjectHasAttribute('charset', $modified);
        $this->assertEquals(304, $modified->getStatusCode());

        ob_start();
        $modified->sendContent();
        $string = ob_get_clean();
        $this->assertEmpty($string);
    }

    public function testIsSuccessful()
    {
        $response = new Response();
        $this->assertTrue($response->isSuccessful());
    }

    public function testIsNotModified()
    {
        $response = new Response();
        $modified = $response->isNotModified(new Request());
        $this->assertFalse($modified);
    }

    public function testIsNotModifiedNotSafe()
    {
        $request = Request::create('/homepage', 'POST');

        $response = new Response();
        $this->assertFalse($response->isNotModified($request));
    }

    public function testIsNotModifiedLastModified()
    {
        $before = 'Sun, 25 Aug 2013 18:32:31 GMT';
        $modified = 'Sun, 25 Aug 2013 18:33:31 GMT';
        $after = 'Sun, 25 Aug 2013 19:33:31 GMT';

        $request = new Request();
        $request->headers->set('If-Modified-Since', $modified);

        $response = new Response();

        $response->headers->set('Last-Modified', $modified);
        $this->assertTrue($response->isNotModified($request));

        $response->headers->set('Last-Modified', $before);
        $this->assertTrue($response->isNotModified($request));

        $response->headers->set('Last-Modified', $after);
        $this->assertFalse($response->isNotModified($request));

        $response->headers->set('Last-Modified', '');
        $this->assertFalse($response->isNotModified($request));
    }

    public function testIsNotModifiedEtag()
    {
        $etagOne = 'randomly_generated_etag';
        $etagTwo = 'randomly_generated_etag_2';

        $request = new Request();
        $request->headers->set('if_none_match', sprintf('%s, %s, %s', $etagOne, $etagTwo, 'etagThree'));

        $response = new Response();

        $response->headers->set('ETag', $etagOne);
        $this->assertTrue($response->isNotModified($request));

        $response->headers->set('ETag', $etagTwo);
        $this->assertTrue($response->isNotModified($request));

        $response->headers->set('ETag', '');
        $this->assertFalse($response->isNotModified($request));
    }

    public function testIsNotModifiedLastModifiedAndEtag()
    {
        $before = 'Sun, 25 Aug 2013 18:32:31 GMT';
        $modified = 'Sun, 25 Aug 2013 18:33:31 GMT';
        $after = 'Sun, 25 Aug 2013 19:33:31 GMT';
        $etag = 'randomly_generated_etag';

        $request = new Request();
        $request->headers->set('if_none_match', sprintf('%s, %s', $etag, 'etagThree'));
        $request->headers->set('If-Modified-Since', $modified);

        $response = new Response();

        $response->headers->set('ETag', $etag);
        $response->headers->set('Last-Modified', $after);
        $this->assertFalse($response->isNotModified($request));

        $response->headers->set('ETag', 'non-existent-etag');
        $response->headers->set('Last-Modified', $before);
        $this->assertFalse($response->isNotModified($request));

        $response->headers->set('ETag', $etag);
        $response->headers->set('Last-Modified', $modified);
        $this->assertTrue($response->isNotModified($request));
    }

    public function testIsNotModifiedIfModifiedSinceAndEtagWithoutLastModified()
    {
        $modified = 'Sun, 25 Aug 2013 18:33:31 GMT';
        $etag = 'randomly_generated_etag';

        $request = new Request();
        $request->headers->set('if_none_match', sprintf('%s, %s', $etag, 'etagThree'));
        $request->headers->set('If-Modified-Since', $modified);

        $response = new Response();

        $response->headers->set('ETag', $etag);
        $this->assertTrue($response->isNotModified($request));

        $response->headers->set('ETag', 'non-existent-etag');
        $this->assertFalse($response->isNotModified($request));
    }

    public function testIsValidateable()
    {
        $response = new Response('', 200, array('Last-Modified' => $this->createDateTimeOneHourAgo()->format(DATE_RFC2822)));
        $this->assertTrue($response->isValidateable(), '->isValidateable() returns true if Last-Modified is present');

        $response = new Response('', 200, array('ETag' => '"12345"'));
        $this->assertTrue($response->isValidateable(), '->isValidateable() returns true if ETag is present');

        $response = new Response();
        $this->assertFalse($response->isValidateable(), '->isValidateable() returns false when no validator is present');
    }

    public function testGetDate()
    {
        $oneHourAgo = $this->createDateTimeOneHourAgo();
        $response = new Response('', 200, array('Date' => $oneHourAgo->format(DATE_RFC2822)));
        $date = $response->getDate();
        $this->assertEquals($oneHourAgo->getTimestamp(), $date->getTimestamp(), '->getDate() returns the Date header if present');

        $response = new Response();
        $date = $response->getDate();
        $this->assertEquals(time(), $date->getTimestamp(), '->getDate() returns the current Date if no Date header present');

        $response = new Response('', 200, array('Date' => $this->createDateTimeOneHourAgo()->format(DATE_RFC2822)));
        $now = $this->createDateTimeNow();
        $response->headers->set('Date', $now->format(DATE_RFC2822));
        $date = $response->getDate();
        $this->assertEquals($now->getTimestamp(), $date->getTimestamp(), '->getDate() returns the date when the header has been modified');

        $response = new Response('', 200);
        $now = $this->createDateTimeNow();
        $response->headers->remove('Date');
        $date = $response->getDate();
        $this->assertEquals($now->getTimestamp(), $date->getTimestamp(), '->getDate() returns the current Date when the header has previously been removed');
    }

    public function testGetMaxAge()
    {
        $response = new Response();
        $response->headers->set('Cache-Control', 's-maxage=600, max-age=0');
        $this->assertEquals(600, $response->getMaxAge(), '->getMaxAge() uses s-maxage cache control directive when present');

        $response = new Response();
        $response->headers->set('Cache-Control', 'max-age=600');
        $this->assertEquals(600, $response->getMaxAge(), '->getMaxAge() falls back to max-age when no s-maxage directive present');

        $response = new Response();
        $response->headers->set('Cache-Control', 'must-revalidate');
        $response->headers->set('Expires', $this->createDateTimeOneHourLater()->format(DATE_RFC2822));
        $this->assertEquals(3600, $response->getMaxAge(), '->getMaxAge() falls back to Expires when no max-age or s-maxage directive present');

        $response = new Response();
        $response->headers->set('Cache-Control', 'must-revalidate');
        $response->headers->set('Expires', -1);
        $this->assertLessThanOrEqual(time() - 2 * 86400, $response->getExpires()->format('U'));

        $response = new Response();
        $this->assertNull($response->getMaxAge(), '->getMaxAge() returns null if no freshness information available');
    }

    public function testSetSharedMaxAge()
    {
        $response = new Response();
        $response->setSharedMaxAge(20);

        $cacheControl = $response->headers->get('Cache-Control');
        $this->assertEquals('public, s-maxage=20', $cacheControl);
    }

    public function testIsPrivate()
    {
        $response = new Response();
        $response->headers->set('Cache-Control', 'max-age=100');
        $response->setPrivate();
        $this->assertEquals(100, $response->headers->getCacheControlDirective('max-age'), '->isPrivate() adds the private Cache-Control directive when set to true');
        $this->assertTrue($response->headers->getCacheControlDirective('private'), '->isPrivate() adds the private Cache-Control directive when set to true');

        $response = new Response();
        $response->headers->set('Cache-Control', 'public, max-age=100');
        $response->setPrivate();
        $this->assertEquals(100, $response->headers->getCacheControlDirective('max-age'), '->isPrivate() adds the private Cache-Control directive when set to true');
        $this->assertTrue($response->headers->getCacheControlDirective('private'), '->isPrivate() adds the private Cache-Control directive when set to true');
        $this->assertFalse($response->headers->hasCacheControlDirective('public'), '->isPrivate() removes the public Cache-Control directive');
    }

    public function testExpire()
    {
        $response = new Response();
        $response->headers->set('Cache-Control', 'max-age=100');
        $response->expire();
        $this->assertEquals(100, $response->headers->get('Age'), '->expire() sets the Age to max-age when present');

        $response = new Response();
        $response->headers->set('Cache-Control', 'max-age=100, s-maxage=500');
        $response->expire();
        $this->assertEquals(500, $response->headers->get('Age'), '->expire() sets the Age to s-maxage when both max-age and s-maxage are present');

        $response = new Response();
        $response->headers->set('Cache-Control', 'max-age=5, s-maxage=500');
        $response->headers->set('Age', '1000');
        $response->expire();
        $this->assertEquals(1000, $response->headers->get('Age'), '->expire() does nothing when the response is already stale/expired');

        $response = new Response();
        $response->expire();
        $this->assertFalse($response->headers->has('Age'), '->expire() does nothing when the response does not include freshness information');

        $response = new Response();
        $response->headers->set('Expires', -1);
        $response->expire();
        $this->assertNull($response->headers->get('Age'), '->expire() does not set the Age when the response is expired');

        $response = new Response();
        $response->headers->set('Expires', date(DATE_RFC2822, time() + 600));
        $response->expire();
        $this->assertNull($response->headers->get('Expires'), '->expire() removes the Expires header when the response is fresh');
    }

    public function testGetTtl()
    {
        $response = new Response();
        $this->assertNull($response->getTtl(), '->getTtl() returns null when no Expires or Cache-Control headers are present');

        $response = new Response();
        $response->headers->set('Expires', $this->createDateTimeOneHourLater()->format(DATE_RFC2822));
        $this->assertEquals(3600, $response->getTtl(), '->getTtl() uses the Expires header when no max-age is present');

        $response = new Response();
        $response->headers->set('Expires', $this->createDateTimeOneHourAgo()->format(DATE_RFC2822));
        $this->assertLessThan(0, $response->getTtl(), '->getTtl() returns negative values when Expires is in past');

        $response = new Response();
        $response->headers->set('Expires', $response->getDate()->format(DATE_RFC2822));
        $response->headers->set('Age', 0);
        $this->assertSame(0, $response->getTtl(), '->getTtl() correctly handles zero');

        $response = new Response();
        $response->headers->set('Cache-Control', 'max-age=60');
        $this->assertEquals(60, $response->getTtl(), '->getTtl() uses Cache-Control max-age when present');
    }

    public function testSetClientTtl()
    {
        $response = new Response();
        $response->setClientTtl(10);

        $this->assertEquals($response->getMaxAge(), $response->getAge() + 10);
    }

    public function testGetSetProtocolVersion()
    {
        $response = new Response();

        $this->assertEquals('1.0', $response->getProtocolVersion());

        $response->setProtocolVersion('1.1');

        $this->assertEquals('1.1', $response->getProtocolVersion());
    }

    public function testGetVary()
    {
        $response = new Response();
        $this->assertEquals(array(), $response->getVary(), '->getVary() returns an empty array if no Vary header is present');

        $response = new Response();
        $response->headers->set('Vary', 'Accept-Language');
        $this->assertEquals(array('Accept-Language'), $response->getVary(), '->getVary() parses a single header name value');

        $response = new Response();
        $response->headers->set('Vary', 'Accept-Language User-Agent    X-Foo');
        $this->assertEquals(array('Accept-Language', 'User-Agent', 'X-Foo'), $response->getVary(), '->getVary() parses multiple header name values separated by spaces');

        $response = new Response();
        $response->headers->set('Vary', 'Accept-Language,User-Agent,    X-Foo');
        $this->assertEquals(array('Accept-Language', 'User-Agent', 'X-Foo'), $response->getVary(), '->getVary() parses multiple header name values separated by commas');

        $vary = array('Accept-Language', 'User-Agent', 'X-foo');

        $response = new Response();
        $response->headers->set('Vary', $vary);
        $this->assertEquals($vary, $response->getVary(), '->getVary() parses multiple header name values in arrays');

        $response = new Response();
        $response->headers->set('Vary', 'Accept-Language, User-Agent, X-foo');
        $this->assertEquals($vary, $response->getVary(), '->getVary() parses multiple header name values in arrays');
    }

    public function testSetVary()
    {
        $response = new Response();
        $response->setVary('Accept-Language');
        $this->assertEquals(array('Accept-Language'), $response->getVary());

        $response->setVary('Accept-Language, User-Agent');
        $this->assertEquals(array('Accept-Language', 'User-Agent'), $response->getVary(), '->setVary() replace the vary header by default');

        $response->setVary('X-Foo', false);
        $this->assertEquals(array('Accept-Language', 'User-Agent', 'X-Foo'), $response->getVary(), '->setVary() doesn\'t wipe out earlier Vary headers if replace is set to false');
    }

    public function testDefaultContentType()
    {
        $headerMock = $this->getMockBuilder('Symfony\Component\HttpFoundation\ResponseHeaderBag')->setMethods(array('set'))->getMock();
        $headerMock->expects($this->at(0))
            ->method('set')
            ->with('Content-Type', 'text/html');
        $headerMock->expects($this->at(1))
            ->method('set')
            ->with('Content-Type', 'text/html; charset=UTF-8');

        $response = new Response('foo');
        $response->headers = $headerMock;

        $response->prepare(new Request());
    }

    public function testContentTypeCharset()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/css');

        // force fixContentType() to be called
        $response->prepare(new Request());

        $this->assertEquals('text/css; charset=UTF-8', $response->headers->get('Content-Type'));
    }

    public function testPrepareDoesNothingIfContentTypeIsSet()
    {
        $response = new Response('foo');
        $response->headers->set('Content-Type', 'text/plain');

        $response->prepare(new Request());

        $this->assertEquals('text/plain; charset=UTF-8', $response->headers->get('content-type'));
    }

    public function testPrepareDoesNothingIfRequestFormatIsNotDefined()
    {
        $response = new Response('foo');

        $response->prepare(new Request());

        $this->assertEquals('text/html; charset=UTF-8', $response->headers->get('content-type'));
    }

    public function testPrepareSetContentType()
    {
        $response = new Response('foo');
        $request = Request::create('/');
        $request->setRequestFormat('json');

        $response->prepare($request);

        $this->assertEquals('application/json', $response->headers->get('content-type'));
    }

    public function testPrepareRemovesContentForHeadRequests()
    {
        $response = new Response('foo');
        $request = Request::create('/', 'HEAD');

        $length = 12345;
        $response->headers->set('Content-Length', $length);
        $response->prepare($request);

        $this->assertEquals('', $response->getContent());
        $this->assertEquals($length, $response->headers->get('Content-Length'), 'Content-Length should be as if it was GET; see RFC2616 14.13');
    }

    public function testPrepareRemovesContentForInformationalResponse()
    {
        $response = new Response('foo');
        $request = Request::create('/');

        $response->setContent('content');
        $response->setStatusCode(101);
        $response->prepare($request);
        $this->assertEquals('', $response->getContent());
        $this->assertFalse($response->headers->has('Content-Type'));
        $this->assertFalse($response->headers->has('Content-Type'));

        $response->setContent('content');
        $response->setStatusCode(304);
        $response->prepare($request);
        $this->assertEquals('', $response->getContent());
        $this->assertFalse($response->headers->has('Content-Type'));
        $this->assertFalse($response->headers->has('Content-Length'));
    }

    public function testPrepareRemovesContentLength()
    {
        $response = new Response('foo');
        $request = Request::create('/');

        $response->headers->set('Content-Length', 12345);
        $response->prepare($request);
        $this->assertEquals(12345, $response->headers->get('Content-Length'));

        $response->headers->set('Transfer-Encoding', 'chunked');
        $response->prepare($request);
        $this->assertFalse($response->headers->has('Content-Length'));
    }

    public function testPrepareSetsPragmaOnHttp10Only()
    {
        $request = Request::create('/', 'GET');
        $request->server->set('SERVER_PROTOCOL', 'HTTP/1.0');

        $response = new Response('foo');
        $response->prepare($request);
        $this->assertEquals('no-cache', $response->headers->get('pragma'));
        $this->assertEquals('-1', $response->headers->get('expires'));

        $request->server->set('SERVER_PROTOCOL', 'HTTP/1.1');
        $response = new Response('foo');
        $response->prepare($request);
        $this->assertFalse($response->headers->has('pragma'));
        $this->assertFalse($response->headers->has('expires'));
    }

    public function testPrepareSetsCookiesSecure()
    {
        $cookie = Cookie::create('foo', 'bar');

        $response = new Response('foo');
        $response->headers->setCookie($cookie);

        $request = Request::create('/', 'GET');
        $response->prepare($request);

        $this->assertFalse($cookie->isSecure());

        $request = Request::create('https://localhost/', 'GET');
        $response->prepare($request);

        $this->assertTrue($cookie->isSecure());
    }

    public function testSetCache()
    {
        $response = new Response();
        //array('etag', 'last_modified', 'max_age', 's_maxage', 'private', 'public')
        try {
            $response->setCache(array('wrong option' => 'value'));
            $this->fail('->setCache() throws an InvalidArgumentException if an option is not supported');
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e, '->setCache() throws an InvalidArgumentException if an option is not supported');
            $this->assertContains('"wrong option"', $e->getMessage());
        }

        $options = array('etag' => '"whatever"');
        $response->setCache($options);
        $this->assertEquals($response->getEtag(), '"whatever"');

        $now = $this->createDateTimeNow();
        $options = array('last_modified' => $now);
        $response->setCache($options);
        $this->assertEquals($response->getLastModified()->getTimestamp(), $now->getTimestamp());

        $options = array('max_age' => 100);
        $response->setCache($options);
        $this->assertEquals($response->getMaxAge(), 100);

        $options = array('s_maxage' => 200);
        $response->setCache($options);
        $this->assertEquals($response->getMaxAge(), 200);

        $this->assertTrue($response->headers->hasCacheControlDirective('public'));
        $this->assertFalse($response->headers->hasCacheControlDirective('private'));

        $response->setCache(array('public' => true));
        $this->assertTrue($response->headers->hasCacheControlDirective('public'));
        $this->assertFalse($response->headers->hasCacheControlDirective('private'));

        $response->setCache(array('public' => false));
        $this->assertFalse($response->headers->hasCacheControlDirective('public'));
        $this->assertTrue($response->headers->hasCacheControlDirective('private'));

        $response->setCache(array('private' => true));
        $this->assertFalse($response->headers->hasCacheControlDirective('public'));
        $this->assertTrue($response->headers->hasCacheControlDirective('private'));

        $response->setCache(array('private' => false));
        $this->assertTrue($response->headers->hasCacheControlDirective('public'));
        $this->assertFalse($response->headers->hasCacheControlDirective('private'));

        $response->setCache(array('immutable' => true));
        $this->assertTrue($response->headers->hasCacheControlDirective('immutable'));

        $response->setCache(array('immutable' => false));
        $this->assertFalse($response->headers->hasCacheControlDirective('immutable'));
    }

    public function testSendContent()
    {
        $response = new Response('test response rendering', 200);

        ob_start();
        $response->sendContent();
        $string = ob_get_clean();
        $this->assertContains('test response rendering', $string);
    }

    public function testSetPublic()
    {
        $response = new Response();
        $response->setPublic();

        $this->assertTrue($response->headers->hasCacheControlDirective('public'));
        $this->assertFalse($response->headers->hasCacheControlDirective('private'));
    }

    public function testSetImmutable()
    {
        $response = new Response();
        $response->setImmutable();

        $this->assertTrue($response->headers->hasCacheControlDirective('immutable'));
    }

    public function testIsImmutable()
    {
        $response = new Response();
        $response->setImmutable();

        $this->assertTrue($response->isImmutable());
    }

    public function testSetDate()
    {
        $response = new Response();
        $response->setDate(\DateTime::createFromFormat(\DateTime::ATOM, '2013-01-26T09:21:56+0100', new \DateTimeZone('Europe/Berlin')));

        $this->assertEquals('2013-01-26T08:21:56+00:00', $response->getDate()->format(\DateTime::ATOM));
    }

    public function testSetDateWithImmutable()
    {
        $response = new Response();
        $response->setDate(\DateTimeImmutable::createFromFormat(\DateTime::ATOM, '2013-01-26T09:21:56+0100', new \DateTimeZone('Europe/Berlin')));

        $this->assertEquals('2013-01-26T08:21:56+00:00', $response->getDate()->format(\DateTime::ATOM));
    }

    public function testSetExpires()
    {
        $response = new Response();
        $response->setExpires(null);

        $this->assertNull($response->getExpires(), '->setExpires() remove the header when passed null');

        $now = $this->createDateTimeNow();
        $response->setExpires($now);

        $this->assertEquals($response->getExpires()->getTimestamp(), $now->getTimestamp());
    }

    public function testSetExpiresWithImmutable()
    {
        $response = new Response();

        $now = $this->createDateTimeImmutableNow();
        $response->setExpires($now);

        $this->assertEquals($response->getExpires()->getTimestamp(), $now->getTimestamp());
    }

    public function testSetLastModified()
    {
        $response = new Response();
        $response->setLastModified($this->createDateTimeNow());
        $this->assertNotNull($response->getLastModified());

        $response->setLastModified(null);
        $this->assertNull($response->getLastModified());
    }

    public function testSetLastModifiedWithImmutable()
    {
        $response = new Response();
        $response->setLastModified($this->createDateTimeImmutableNow());
        $this->assertNotNull($response->getLastModified());

        $response->setLastModified(null);
        $this->assertNull($response->getLastModified());
    }

    public function testIsInvalid()
    {
        $response = new Response();

        try {
            $response->setStatusCode(99);
            $this->fail();
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue($response->isInvalid());
        }

        try {
            $response->setStatusCode(650);
            $this->fail();
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue($response->isInvalid());
        }

        $response = new Response('', 200);
        $this->assertFalse($response->isInvalid());
    }

    /**
     * @dataProvider getStatusCodeFixtures
     */
    public function testSetStatusCode($code, $text, $expectedText)
    {
        $response = new Response();

        $response->setStatusCode($code, $text);

        $statusText = new \ReflectionProperty($response, 'statusText');
        $statusText->setAccessible(true);

        $this->assertEquals($expectedText, $statusText->getValue($response));
    }

    public function getStatusCodeFixtures()
    {
        return array(
            array('200', null, 'OK'),
            array('200', false, ''),
            array('200', 'foo', 'foo'),
            array('199', null, 'unknown status'),
            array('199', false, ''),
            array('199', 'foo', 'foo'),
        );
    }

    public function testIsInformational()
    {
        $response = new Response('', 100);
        $this->assertTrue($response->isInformational());

        $response = new Response('', 200);
        $this->assertFalse($response->isInformational());
    }

    public function testIsRedirectRedirection()
    {
        foreach (array(301, 302, 303, 307) as $code) {
            $response = new Response('', $code);
            $this->assertTrue($response->isRedirection());
            $this->assertTrue($response->isRedirect());
        }

        $response = new Response('', 304);
        $this->assertTrue($response->isRedirection());
        $this->assertFalse($response->isRedirect());

        $response = new Response('', 200);
        $this->assertFalse($response->isRedirection());
        $this->assertFalse($response->isRedirect());

        $response = new Response('', 404);
        $this->assertFalse($response->isRedirection());
        $this->assertFalse($response->isRedirect());

        $response = new Response('', 301, array('Location' => '/good-uri'));
        $this->assertFalse($response->isRedirect('/bad-uri'));
        $this->assertTrue($response->isRedirect('/good-uri'));
    }

    public function testIsNotFound()
    {
        $response = new Response('', 404);
        $this->assertTrue($response->isNotFound());

        $response = new Response('', 200);
        $this->assertFalse($response->isNotFound());
    }

    public function testIsEmpty()
    {
        foreach (array(204, 304) as $code) {
            $response = new Response('', $code);
            $this->assertTrue($response->isEmpty());
        }

        $response = new Response('', 200);
        $this->assertFalse($response->isEmpty());
    }

    public function testIsForbidden()
    {
        $response = new Response('', 403);
        $this->assertTrue($response->isForbidden());

        $response = new Response('', 200);
        $this->assertFalse($response->isForbidden());
    }

    public function testIsOk()
    {
        $response = new Response('', 200);
        $this->assertTrue($response->isOk());

        $response = new Response('', 404);
        $this->assertFalse($response->isOk());
    }

    public function testIsServerOrClientError()
    {
        $response = new Response('', 404);
        $this->assertTrue($response->isClientError());
        $this->assertFalse($response->isServerError());

        $response = new Response('', 500);
        $this->assertFalse($response->isClientError());
        $this->assertTrue($response->isServerError());
    }

    public function testHasVary()
    {
        $response = new Response();
        $this->assertFalse($response->hasVary());

        $response->setVary('User-Agent');
        $this->assertTrue($response->hasVary());
    }

    public function testSetEtag()
    {
        $response = new Response('', 200, array('ETag' => '"12345"'));
        $response->setEtag();

        $this->assertNull($response->headers->get('Etag'), '->setEtag() removes Etags when call with null');
    }

    /**
     * @dataProvider validContentProvider
     */
    public function testSetContent($content)
    {
        $response = new Response();
        $response->setContent($content);
        $this->assertEquals((string) $content, $response->getContent());
    }

    /**
     * @expectedException \UnexpectedValueException
     * @dataProvider invalidContentProvider
     */
    public function testSetContentInvalid($content)
    {
        $response = new Response();
        $response->setContent($content);
    }

    public function testSettersAreChainable()
    {
        $response = new Response();

        $setters = array(
            'setProtocolVersion' => '1.0',
            'setCharset' => 'UTF-8',
            'setPublic' => null,
            'setPrivate' => null,
            'setDate' => $this->createDateTimeNow(),
            'expire' => null,
            'setMaxAge' => 1,
            'setSharedMaxAge' => 1,
            'setTtl' => 1,
            'setClientTtl' => 1,
        );

        foreach ($setters as $setter => $arg) {
            $this->assertEquals($response, $response->{$setter}($arg));
        }
    }

    public function testNoDeprecationsAreTriggered()
    {
        new DefaultResponse();
        $this->getMockBuilder(Response::class)->getMock();

        // we just need to ensure that subclasses of Response can be created without any deprecations
        // being triggered if the subclass does not override any final methods
        $this->addToAssertionCount(1);
    }

    public function validContentProvider()
    {
        return array(
            'obj' => array(new StringableObject()),
            'string' => array('Foo'),
            'int' => array(2),
        );
    }

    public function invalidContentProvider()
    {
        return array(
            'obj' => array(new \stdClass()),
            'array' => array(array()),
            'bool' => array(true, '1'),
        );
    }

    protected function createDateTimeOneHourAgo()
    {
        return $this->createDateTimeNow()->sub(new \DateInterval('PT1H'));
    }

    protected function createDateTimeOneHourLater()
    {
        return $this->createDateTimeNow()->add(new \DateInterval('PT1H'));
    }

    protected function createDateTimeNow()
    {
        $date = new \DateTime();

        return $date->setTimestamp(time());
    }

    protected function createDateTimeImmutableNow()
    {
        $date = new \DateTimeImmutable();

        return $date->setTimestamp(time());
    }

    protected function provideResponse()
    {
        return new Response();
    }

    /**
     * @see       http://github.com/zendframework/zend-diactoros for the canonical source repository
     *
     * @author    Fábio Pacheco
     * @copyright Copyright (c) 2015-2016 Zend Technologies USA Inc. (http://www.zend.com)
     * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
     */
    public function ianaCodesReasonPhrasesProvider()
    {
        if (!\in_array('https', stream_get_wrappers(), true)) {
            $this->markTestSkipped('The "https" wrapper is not available');
        }

        $ianaHttpStatusCodes = new \DOMDocument();

        libxml_set_streams_context(stream_context_create(array(
            'http' => array(
                'method' => 'GET',
                'timeout' => 30,
            ),
        )));

        $ianaHttpStatusCodes->load('https://www.iana.org/assignments/http-status-codes/http-status-codes.xml');
        if (!$ianaHttpStatusCodes->relaxNGValidate(__DIR__.'/schema/http-status-codes.rng')) {
            self::fail('Invalid IANA\'s HTTP status code list.');
        }

        $ianaCodesReasonPhrases = array();

        $xpath = new \DOMXPath($ianaHttpStatusCodes);
        $xpath->registerNamespace('ns', 'http://www.iana.org/assignments');

        $records = $xpath->query('//ns:record');
        foreach ($records as $record) {
            $value = $xpath->query('.//ns:value', $record)->item(0)->nodeValue;
            $description = $xpath->query('.//ns:description', $record)->item(0)->nodeValue;

            if (\in_array($description, array('Unassigned', '(Unused)'), true)) {
                continue;
            }

            if (preg_match('/^([0-9]+)\s*\-\s*([0-9]+)$/', $value, $matches)) {
                for ($value = $matches[1]; $value <= $matches[2]; ++$value) {
                    $ianaCodesReasonPhrases[] = array($value, $description);
                }
            } else {
                $ianaCodesReasonPhrases[] = array($value, $description);
            }
        }

        return $ianaCodesReasonPhrases;
    }

    /**
     * @dataProvider ianaCodesReasonPhrasesProvider
     */
    public function testReasonPhraseDefaultsAgainstIana($code, $reasonPhrase)
    {
        $this->assertEquals($reasonPhrase, Response::$statusTexts[$code]);
    }
}

class StringableObject
{
    public function __toString()
    {
        return 'Foo';
    }
}

class DefaultResponse extends Response
{
}
