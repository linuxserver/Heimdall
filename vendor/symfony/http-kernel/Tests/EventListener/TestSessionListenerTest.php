<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\EventListener\SessionListener;
use Symfony\Component\HttpKernel\EventListener\TestSessionListener;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * SessionListenerTest.
 *
 * Tests SessionListener.
 *
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 */
class TestSessionListenerTest extends TestCase
{
    /**
     * @var TestSessionListener
     */
    private $listener;

    /**
     * @var SessionInterface
     */
    private $session;

    protected function setUp()
    {
        $this->listener = $this->getMockForAbstractClass('Symfony\Component\HttpKernel\EventListener\AbstractTestSessionListener');
        $this->session = $this->getSession();
        $this->listener->expects($this->any())
             ->method('getSession')
             ->will($this->returnValue($this->session));
    }

    public function testShouldSaveMasterRequestSession()
    {
        $this->sessionHasBeenStarted();
        $this->sessionMustBeSaved();

        $this->filterResponse(new Request());
    }

    public function testShouldNotSaveSubRequestSession()
    {
        $this->sessionMustNotBeSaved();

        $this->filterResponse(new Request(), HttpKernelInterface::SUB_REQUEST);
    }

    public function testDoesNotDeleteCookieIfUsingSessionLifetime()
    {
        $this->sessionHasBeenStarted();

        @ini_set('session.cookie_lifetime', 0);

        $response = $this->filterResponse(new Request(), HttpKernelInterface::MASTER_REQUEST);
        $cookies = $response->headers->getCookies();

        $this->assertEquals(0, reset($cookies)->getExpiresTime());
    }

    /**
     * @requires function \Symfony\Component\HttpFoundation\Session\Session::isEmpty
     */
    public function testEmptySessionDoesNotSendCookie()
    {
        $this->sessionHasBeenStarted();
        $this->sessionIsEmpty();

        $response = $this->filterResponse(new Request(), HttpKernelInterface::MASTER_REQUEST);

        $this->assertSame(array(), $response->headers->getCookies());
    }

    public function testEmptySessionWithNewSessionIdDoesSendCookie()
    {
        $this->sessionHasBeenStarted();
        $this->sessionIsEmpty();
        $this->fixSessionId('456');

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernelInterface')->getMock();
        $request = Request::create('/', 'GET', array(), array('MOCKSESSID' => '123'));
        $event = new GetResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        $response = $this->filterResponse(new Request(), HttpKernelInterface::MASTER_REQUEST);

        $this->assertNotEmpty($response->headers->getCookies());
    }

    /**
     * @dataProvider anotherCookieProvider
     */
    public function testSessionWithNewSessionIdAndNewCookieDoesNotSendAnotherCookie($existing, array $expected)
    {
        $this->sessionHasBeenStarted();
        $this->sessionIsEmpty();
        $this->fixSessionId('456');

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernelInterface')->getMock();
        $request = Request::create('/', 'GET', array(), array('MOCKSESSID' => '123'));
        $event = new GetResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        $response = new Response('', 200, array('Set-Cookie' => $existing));

        $response = $this->filterResponse(new Request(), HttpKernelInterface::MASTER_REQUEST, $response);

        $this->assertSame($expected, $response->headers->get('Set-Cookie', null, false));
    }

    public function anotherCookieProvider()
    {
        return array(
            'same' => array('MOCKSESSID=789; path=/', array('MOCKSESSID=789; path=/')),
            'different domain' => array('MOCKSESSID=789; path=/; domain=example.com', array('MOCKSESSID=789; path=/; domain=example.com', 'MOCKSESSID=456; path=/')),
            'different path' => array('MOCKSESSID=789; path=/foo', array('MOCKSESSID=789; path=/foo', 'MOCKSESSID=456; path=/')),
        );
    }

    public function testUnstartedSessionIsNotSave()
    {
        $this->sessionHasNotBeenStarted();
        $this->sessionMustNotBeSaved();

        $this->filterResponse(new Request());
    }

    public function testDoesNotThrowIfRequestDoesNotHaveASession()
    {
        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernelInterface')->getMock();
        $event = new FilterResponseEvent($kernel, new Request(), HttpKernelInterface::MASTER_REQUEST, new Response());

        $this->listener->onKernelResponse($event);

        $this->assertTrue(true);
    }

    private function filterResponse(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, Response $response = null)
    {
        $request->setSession($this->session);
        $response = $response ?: new Response();
        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernelInterface')->getMock();
        $event = new FilterResponseEvent($kernel, $request, $type, $response);

        $this->listener->onKernelResponse($event);

        $this->assertSame($response, $event->getResponse());

        return $response;
    }

    private function sessionMustNotBeSaved()
    {
        $this->session->expects($this->never())
            ->method('save');
    }

    private function sessionMustBeSaved()
    {
        $this->session->expects($this->once())
            ->method('save');
    }

    private function sessionHasBeenStarted()
    {
        $this->session->expects($this->once())
            ->method('isStarted')
            ->will($this->returnValue(true));
    }

    private function sessionHasNotBeenStarted()
    {
        $this->session->expects($this->once())
            ->method('isStarted')
            ->will($this->returnValue(false));
    }

    private function sessionIsEmpty()
    {
        $this->session->expects($this->once())
            ->method('isEmpty')
            ->will($this->returnValue(true));
    }

    private function fixSessionId($sessionId)
    {
        $this->session->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($sessionId));
    }

    private function getSession()
    {
        $mock = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\Session')
            ->disableOriginalConstructor()
            ->getMock();

        // set return value for getName()
        $mock->expects($this->any())->method('getName')->will($this->returnValue('MOCKSESSID'));

        return $mock;
    }
}
