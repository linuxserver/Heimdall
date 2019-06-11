<?php
/**
 * This file is part of Lcobucci\JWT, a simple library to handle JWT and JWS
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\JWT\Signer\Rsa;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 2.1.0
 */
class Sha512Test extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     *
     * @covers Lcobucci\JWT\Signer\Rsa\Sha512::getAlgorithmId
     */
    public function getAlgorithmIdMustBeCorrect()
    {
        $signer = new Sha512();

        $this->assertEquals('RS512', $signer->getAlgorithmId());
    }

    /**
     * @test
     *
     * @covers Lcobucci\JWT\Signer\Rsa\Sha512::getAlgorithm
     */
    public function getAlgorithmMustBeCorrect()
    {
        $signer = new Sha512();

        $this->assertEquals(OPENSSL_ALGO_SHA512, $signer->getAlgorithm());
    }
}
