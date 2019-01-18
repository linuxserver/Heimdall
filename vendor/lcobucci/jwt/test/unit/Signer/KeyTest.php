<?php
/**
 * This file is part of Lcobucci\JWT, a simple library to handle JWT and JWS
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\JWT\Signer;

use org\bovigo\vfs\vfsStream;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 3.0.4
 */
class KeyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @before
     */
    public function configureRootDir()
    {
        vfsStream::setup(
            'root',
            null,
            [
                'test.pem' => 'testing',
                'emptyFolder' => []
            ]
        );
    }

    /**
     * @test
     *
     * @covers Lcobucci\JWT\Signer\Key::__construct
     * @covers Lcobucci\JWT\Signer\Key::setContent
     */
    public function constructShouldConfigureContentAndPassphrase()
    {
        $key = new Key('testing', 'test');

        $this->assertAttributeEquals('testing', 'content', $key);
        $this->assertAttributeEquals('test', 'passphrase', $key);
    }

    /**
     * @test
     *
     * @covers Lcobucci\JWT\Signer\Key::__construct
     * @covers Lcobucci\JWT\Signer\Key::setContent
     * @covers Lcobucci\JWT\Signer\Key::readFile
     */
    public function constructShouldBeAbleToConfigureContentFromFile()
    {
        $key = new Key('file://' . vfsStream::url('root/test.pem'));

        $this->assertAttributeEquals('testing', 'content', $key);
        $this->assertAttributeEquals(null, 'passphrase', $key);
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     *
     * @covers Lcobucci\JWT\Signer\Key::__construct
     * @covers Lcobucci\JWT\Signer\Key::setContent
     * @covers Lcobucci\JWT\Signer\Key::readFile
     */
    public function constructShouldRaiseExceptionWhenFileDoesNotExists()
    {
        new Key('file://' . vfsStream::url('root/test2.pem'));
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     *
     * @covers Lcobucci\JWT\Signer\Key::__construct
     * @covers Lcobucci\JWT\Signer\Key::setContent
     * @covers Lcobucci\JWT\Signer\Key::readFile
     */
    public function constructShouldRaiseExceptionWhenFileGetContentsFailed()
    {
        new Key('file://' . vfsStream::url('root/emptyFolder'));
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\Signer\Key::__construct
     * @uses Lcobucci\JWT\Signer\Key::setContent
     *
     * @covers Lcobucci\JWT\Signer\Key::getContent
     */
    public function getContentShouldReturnConfiguredData()
    {
        $key = new Key('testing', 'test');

        $this->assertEquals('testing', $key->getContent());
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\Signer\Key::__construct
     * @uses Lcobucci\JWT\Signer\Key::setContent
     *
     * @covers Lcobucci\JWT\Signer\Key::getPassphrase
     */
    public function getPassphraseShouldReturnConfiguredData()
    {
        $key = new Key('testing', 'test');

        $this->assertEquals('test', $key->getPassphrase());
    }
}
