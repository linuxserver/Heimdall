<?php
/**
 * This file is part of Lcobucci\JWT, a simple library to handle JWT and JWS
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\JWT\Parsing;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 0.1.0
 */
class DecoderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     *
     * @covers Lcobucci\JWT\Parsing\Decoder::jsonDecode
     */
    public function jsonDecodeMustReturnTheDecodedData()
    {
        $decoder = new Decoder();

        $this->assertEquals(
            (object) ['test' => 'test'],
            $decoder->jsonDecode('{"test":"test"}')
        );
    }

    /**
     * @test
     *
     * @covers Lcobucci\JWT\Parsing\Decoder::jsonDecode
     *
     * @expectedException \RuntimeException
     */
    public function jsonDecodeMustRaiseExceptionWhenAnErrorHasOccured()
    {
        $decoder = new Decoder();
        $decoder->jsonDecode('{"test":\'test\'}');
    }

    /**
     * @test
     *
     * @covers Lcobucci\JWT\Parsing\Decoder::base64UrlDecode
     */
    public function base64UrlDecodeMustReturnTheRightData()
    {
        $data = base64_decode('0MB2wKB+L3yvIdzeggmJ+5WOSLaRLTUPXbpzqUe0yuo=');

        $decoder = new Decoder();
        $this->assertEquals($data, $decoder->base64UrlDecode('0MB2wKB-L3yvIdzeggmJ-5WOSLaRLTUPXbpzqUe0yuo'));
    }
}
