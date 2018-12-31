<?php
/**
 * This file is part of Lcobucci\JWT, a simple library to handle JWT and JWS
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\JWT\FunctionalTests;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 2.1.0
 */
class UnsignedTokenTest extends \PHPUnit_Framework_TestCase
{
    const CURRENT_TIME = 100000;

    /**
     * @test
     *
     * @covers Lcobucci\JWT\Builder
     * @covers Lcobucci\JWT\Token
     * @covers Lcobucci\JWT\Claim\Factory
     * @covers Lcobucci\JWT\Claim\Basic
     * @covers Lcobucci\JWT\Parsing\Encoder
     */
    public function builderCanGenerateAToken()
    {
        $user = (object) ['name' => 'testing', 'email' => 'testing@abc.com'];

        $token = (new Builder())->setId(1)
                              ->setAudience('http://client.abc.com')
                              ->setIssuer('http://api.abc.com')
                              ->setExpiration(self::CURRENT_TIME + 3000)
                              ->set('user', $user)
                              ->getToken();

        $this->assertAttributeEquals(null, 'signature', $token);
        $this->assertEquals('http://client.abc.com', $token->getClaim('aud'));
        $this->assertEquals('http://api.abc.com', $token->getClaim('iss'));
        $this->assertEquals(self::CURRENT_TIME + 3000, $token->getClaim('exp'));
        $this->assertEquals($user, $token->getClaim('user'));

        return $token;
    }

    /**
     * @test
     *
     * @depends builderCanGenerateAToken
     *
     * @covers Lcobucci\JWT\Builder
     * @covers Lcobucci\JWT\Parser
     * @covers Lcobucci\JWT\Token
     * @covers Lcobucci\JWT\Claim\Factory
     * @covers Lcobucci\JWT\Claim\Basic
     * @covers Lcobucci\JWT\Parsing\Encoder
     * @covers Lcobucci\JWT\Parsing\Decoder
     */
    public function parserCanReadAToken(Token $generated)
    {
        $read = (new Parser())->parse((string) $generated);

        $this->assertEquals($generated, $read);
        $this->assertEquals('testing', $read->getClaim('user')->name);
    }

    /**
     * @test
     *
     * @depends builderCanGenerateAToken
     *
     * @covers Lcobucci\JWT\Builder
     * @covers Lcobucci\JWT\Parser
     * @covers Lcobucci\JWT\Token
     * @covers Lcobucci\JWT\ValidationData
     * @covers Lcobucci\JWT\Claim\Factory
     * @covers Lcobucci\JWT\Claim\Basic
     * @covers Lcobucci\JWT\Claim\EqualsTo
     * @covers Lcobucci\JWT\Claim\GreaterOrEqualsTo
     * @covers Lcobucci\JWT\Parsing\Encoder
     * @covers Lcobucci\JWT\Parsing\Decoder
     */
    public function tokenValidationShouldReturnWhenEverythingIsFine(Token $generated)
    {
        $data = new ValidationData(self::CURRENT_TIME - 10);
        $data->setAudience('http://client.abc.com');
        $data->setIssuer('http://api.abc.com');

        $this->assertTrue($generated->validate($data));
    }

    /**
     * @test
     *
     * @dataProvider invalidValidationData
     *
     * @depends builderCanGenerateAToken
     *
     * @covers Lcobucci\JWT\Builder
     * @covers Lcobucci\JWT\Parser
     * @covers Lcobucci\JWT\Token
     * @covers Lcobucci\JWT\ValidationData
     * @covers Lcobucci\JWT\Claim\Factory
     * @covers Lcobucci\JWT\Claim\Basic
     * @covers Lcobucci\JWT\Claim\EqualsTo
     * @covers Lcobucci\JWT\Claim\GreaterOrEqualsTo
     * @covers Lcobucci\JWT\Parsing\Encoder
     * @covers Lcobucci\JWT\Parsing\Decoder
     */
    public function tokenValidationShouldReturnFalseWhenExpectedDataDontMatch(ValidationData $data, Token $generated)
    {
        $this->assertFalse($generated->validate($data));
    }

    public function invalidValidationData()
    {
        $expired = new ValidationData(self::CURRENT_TIME + 3020);
        $expired->setAudience('http://client.abc.com');
        $expired->setIssuer('http://api.abc.com');

        $invalidAudience = new ValidationData(self::CURRENT_TIME - 10);
        $invalidAudience->setAudience('http://cclient.abc.com');
        $invalidAudience->setIssuer('http://api.abc.com');

        $invalidIssuer = new ValidationData(self::CURRENT_TIME - 10);
        $invalidIssuer->setAudience('http://client.abc.com');
        $invalidIssuer->setIssuer('http://aapi.abc.com');

        return [[$expired], [$invalidAudience], [$invalidIssuer]];
    }
}
