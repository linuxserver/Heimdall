<?php
/**
 * This file is part of Lcobucci\JWT, a simple library to handle JWT and JWS
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\JWT\Signer;

use Lcobucci\JWT\Signature;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 0.1.0
 */
class BaseSignerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BaseSigner|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $signer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->signer = $this->getMockForAbstractClass(BaseSigner::class);

        $this->signer->method('getAlgorithmId')
                     ->willReturn('TEST123');
    }

    /**
     * @test
     *
     * @covers Lcobucci\JWT\Signer\BaseSigner::modifyHeader
     */
    public function modifyHeaderShouldChangeAlgorithm()
    {
        $headers = ['typ' => 'JWT'];

        $this->signer->modifyHeader($headers);

        $this->assertEquals($headers['typ'], 'JWT');
        $this->assertEquals($headers['alg'], 'TEST123');
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\Signature::__construct
     * @uses Lcobucci\JWT\Signer\Key
     *
     * @covers Lcobucci\JWT\Signer\BaseSigner::sign
     * @covers Lcobucci\JWT\Signer\BaseSigner::getKey
     */
    public function signMustReturnANewSignature()
    {
        $key = new Key('123');

        $this->signer->expects($this->once())
                     ->method('createHash')
                     ->with('test', $key)
                     ->willReturn('test');

        $this->assertEquals(new Signature('test'), $this->signer->sign('test', $key));
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\Signature::__construct
     * @uses Lcobucci\JWT\Signer\Key
     *
     * @covers Lcobucci\JWT\Signer\BaseSigner::sign
     * @covers Lcobucci\JWT\Signer\BaseSigner::getKey
     */
    public function signShouldConvertKeyWhenItsNotAnObject()
    {
        $this->signer->expects($this->once())
                     ->method('createHash')
                     ->with('test', new Key('123'))
                     ->willReturn('test');

        $this->assertEquals(new Signature('test'), $this->signer->sign('test', '123'));
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\Signature::__construct
     * @uses Lcobucci\JWT\Signer\Key
     *
     * @covers Lcobucci\JWT\Signer\BaseSigner::verify
     * @covers Lcobucci\JWT\Signer\BaseSigner::getKey
     */
    public function verifyShouldDelegateTheCallToAbstractMethod()
    {
        $key = new Key('123');

        $this->signer->expects($this->once())
                     ->method('doVerify')
                     ->with('test', 'test', $key)
                     ->willReturn(true);

        $this->assertTrue($this->signer->verify('test', 'test', $key));
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\Signature::__construct
     * @uses Lcobucci\JWT\Signer\Key
     *
     * @covers Lcobucci\JWT\Signer\BaseSigner::verify
     * @covers Lcobucci\JWT\Signer\BaseSigner::getKey
     */
    public function verifyShouldConvertKeyWhenItsNotAnObject()
    {
        $this->signer->expects($this->once())
                     ->method('doVerify')
                     ->with('test', 'test', new Key('123'))
                     ->willReturn(true);

        $this->assertTrue($this->signer->verify('test', 'test', '123'));
    }
}
