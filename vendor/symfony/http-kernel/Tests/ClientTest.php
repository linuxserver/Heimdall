<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\HttpKernel\Tests\Fixtures\TestClient;

/**
 * @group time-sensitive
 */
class ClientTest extends TestCase
{
    public function testDoRequest()
    {
        $client = new Client(new TestHttpKernel());

        $client->request('GET', '/');
        $this->assertEquals('Request: /', $client->getResponse()->getContent(), '->doRequest() uses the request handler to make the request');
        $this->assertInstanceOf('Symfony\Component\BrowserKit\Request', $client->getInternalRequest());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $client->getRequest());
        $this->assertInstanceOf('Symfony\Component\BrowserKit\Response', $client->getInternalResponse());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $client->getResponse());

        $client->request('GET', 'http://www.example.com/');
        $this->assertEquals('Request: /', $client->getResponse()->getContent(), '->doRequest() uses the request handler to make the request');
        $this->assertEquals('www.example.com', $client->getRequest()->getHost(), '->doRequest() uses the request handler to make the request');

        $client->request('GET', 'http://www.example.com/?parameter=http://google.com');
        $this->assertEquals('http://www.example.com/?parameter='.urlencode('http://google.com'), $client->getRequest()->getUri(), '->doRequest() uses the request handler to make the request');
    }

    public function testGetScript()
    {
        $client = new TestClient(new TestHttpKernel());
        $client->insulate();
        $client->request('GET', '/');

        $this->assertEquals('Request: /', $client->getResponse()->getContent(), '->getScript() returns a script that uses the request handler to make the request');
    }

    public function testFilterResponseConvertsCookies()
    {
        $client = new Client(new TestHttpKernel());

        $r = new \ReflectionObject($client);
        $m = $r->getMethod('filterResponse');
        $m->setAccessible(true);

        $response = new Response();
        $response->headers->setCookie($cookie1 = new Cookie('foo', 'bar', \DateTime::createFromFormat('j-M-Y H:i:s T', '15-Feb-2009 20:00:00 GMT')->format('U'), '/foo', 'http://example.com', true, true, false, null));
        $domResponse = $m->invoke($client, $response);
        $this->assertSame((string) $cookie1, $domResponse->getHeader('Set-Cookie'));

        $response = new Response();
        $response->headers->setCookie($cookie1 = new Cookie('foo', 'bar', \DateTime::createFromFormat('j-M-Y H:i:s T', '15-Feb-2009 20:00:00 GMT')->format('U'), '/foo', 'http://example.com', true, true, false, null));
        $response->headers->setCookie($cookie2 = new Cookie('foo1', 'bar1', \DateTime::createFromFormat('j-M-Y H:i:s T', '15-Feb-2009 20:00:00 GMT')->format('U'), '/foo', 'http://example.com', true, true, false, null));
        $domResponse = $m->invoke($client, $response);
        $this->assertSame((string) $cookie1, $domResponse->getHeader('Set-Cookie'));
        $this->assertSame(array((string) $cookie1, (string) $cookie2), $domResponse->getHeader('Set-Cookie', false));
    }

    public function testFilterResponseSupportsStreamedResponses()
    {
        $client = new Client(new TestHttpKernel());

        $r = new \ReflectionObject($client);
        $m = $r->getMethod('filterResponse');
        $m->setAccessible(true);

        $response = new StreamedResponse(function () {
            echo 'foo';
        });

        $domResponse = $m->invoke($client, $response);
        $this->assertEquals('foo', $domResponse->getContent());
    }

    public function testUploadedFile()
    {
        $source = tempnam(sys_get_temp_dir(), 'source');
        file_put_contents($source, '1');
        $target = sys_get_temp_dir().'/sf.moved.file';
        @unlink($target);

        $kernel = new TestHttpKernel();
        $client = new Client($kernel);

        $files = array(
            array('tmp_name' => $source, 'name' => 'original', 'type' => 'mime/original', 'size' => null, 'error' => UPLOAD_ERR_OK),
            new UploadedFile($source, 'original', 'mime/original', UPLOAD_ERR_OK, true),
        );

        $file = null;
        foreach ($files as $file) {
            $client->request('POST', '/', array(), array('foo' => $file));

            $files = $client->getRequest()->files->all();

            $this->assertCount(1, $files);

            $file = $files['foo'];

            $this->assertEquals('original', $file->getClientOriginalName());
            $this->assertEquals('mime/original', $file->getClientMimeType());
            $this->assertEquals(1, $file->getSize());
        }

        $file->move(\dirname($target), basename($target));

        $this->assertFileExists($target);
        unlink($target);
    }

    public function testUploadedFileWhenNoFileSelected()
    {
        $kernel = new TestHttpKernel();
        $client = new Client($kernel);

        $file = array('tmp_name' => '', 'name' => '', 'type' => '', 'size' => 0, 'error' => UPLOAD_ERR_NO_FILE);

        $client->request('POST', '/', array(), array('foo' => $file));

        $files = $client->getRequest()->files->all();

        $this->assertCount(1, $files);
        $this->assertNull($files['foo']);
    }

    public function testUploadedFileWhenSizeExceedsUploadMaxFileSize()
    {
        $source = tempnam(sys_get_temp_dir(), 'source');

        $kernel = new TestHttpKernel();
        $client = new Client($kernel);

        $file = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
            ->setConstructorArgs(array($source, 'original', 'mime/original', UPLOAD_ERR_OK, true))
            ->setMethods(array('getSize', 'getClientSize'))
            ->getMock()
        ;
        /* should be modified when the getClientSize will be removed */
        $file->expects($this->any())
            ->method('getSize')
            ->will($this->returnValue(INF))
        ;
        $file->expects($this->any())
            ->method('getClientSize')
            ->will($this->returnValue(INF))
        ;

        $client->request('POST', '/', array(), array($file));

        $files = $client->getRequest()->files->all();

        $this->assertCount(1, $files);

        $file = $files[0];

        $this->assertFalse($file->isValid());
        $this->assertEquals(UPLOAD_ERR_INI_SIZE, $file->getError());
        $this->assertEquals('mime/original', $file->getClientMimeType());
        $this->assertEquals('original', $file->getClientOriginalName());
        $this->assertEquals(0, $file->getSize());

        unlink($source);
    }
}
