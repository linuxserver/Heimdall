<?php
/**
 * This file is part of Lcobucci\JWT, a simple library to handle JWT and JWS
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\JWT\Signer\Ecdsa;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 2.1.0
 */
class Sha384Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     *
     * @uses Lcobucci\JWT\Signer\Ecdsa
     * @uses Lcobucci\JWT\Signer\Ecdsa\KeyParser
     *
     * @covers Lcobucci\JWT\Signer\Ecdsa\Sha384::getAlgorithmId
     */
    public function getAlgorithmIdMustBeCorrect()
    {
        $signer = new Sha384();

        $this->assertEquals('ES384', $signer->getAlgorithmId());
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\Signer\Ecdsa
     * @uses Lcobucci\JWT\Signer\Ecdsa\KeyParser
     *
     * @covers Lcobucci\JWT\Signer\Ecdsa\Sha384::getAlgorithm
     */
    public function getAlgorithmMustBeCorrect()
    {
        $signer = new Sha384();

        $this->assertEquals('sha384', $signer->getAlgorithm());
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\Signer\Ecdsa
     * @uses Lcobucci\JWT\Signer\Ecdsa\KeyParser
     *
     * @covers Lcobucci\JWT\Signer\Ecdsa\Sha384::getSignatureLength
     */
    public function getSignatureLengthMustBeCorrect()
    {
        $signer = new Sha384();

        $this->assertEquals(96, $signer->getSignatureLength());
    }
}
