<?php
/**
 * This file is part of Lcobucci\JWT, a simple library to handle JWT and JWS
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\JWT;

use Lcobucci\JWT\Claim\Factory as ClaimFactory;
use Lcobucci\JWT\Parsing\Decoder;
use RuntimeException;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 0.1.0
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Decoder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $decoder;

    /**
     * @var ClaimFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $claimFactory;

    /**
     * @var Claim|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $defaultClaim;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->decoder = $this->getMock(Decoder::class);
        $this->claimFactory = $this->getMock(ClaimFactory::class, [], [], '', false);
        $this->defaultClaim = $this->getMock(Claim::class);

        $this->claimFactory->expects($this->any())
                           ->method('create')
                           ->willReturn($this->defaultClaim);
    }

    /**
     * @return Parser
     */
    private function createParser()
    {
        return new Parser($this->decoder, $this->claimFactory);
    }

    /**
     * @test
     *
     * @covers Lcobucci\JWT\Parser::__construct
     */
    public function constructMustConfigureTheAttributes()
    {
        $parser = $this->createParser();

        $this->assertAttributeSame($this->decoder, 'decoder', $parser);
        $this->assertAttributeSame($this->claimFactory, 'claimFactory', $parser);
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\Parser::__construct
     *
     * @covers Lcobucci\JWT\Parser::parse
     * @covers Lcobucci\JWT\Parser::splitJwt
     *
     * @expectedException InvalidArgumentException
     */
    public function parseMustRaiseExceptionWhenJWSIsNotAString()
    {
        $parser = $this->createParser();
        $parser->parse(['asdasd']);
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\Parser::__construct
     *
     * @covers Lcobucci\JWT\Parser::parse
     * @covers Lcobucci\JWT\Parser::splitJwt
     *
     * @expectedException InvalidArgumentException
     */
    public function parseMustRaiseExceptionWhenJWSDontHaveThreeParts()
    {
        $parser = $this->createParser();
        $parser->parse('');
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\Parser::__construct
     *
     * @covers Lcobucci\JWT\Parser::parse
     * @covers Lcobucci\JWT\Parser::splitJwt
     * @covers Lcobucci\JWT\Parser::parseHeader
     *
     * @expectedException RuntimeException
     */
    public function parseMustRaiseExceptionWhenHeaderCannotBeDecoded()
    {
        $this->decoder->expects($this->any())
                      ->method('jsonDecode')
                      ->willThrowException(new RuntimeException());

        $parser = $this->createParser();
        $parser->parse('asdfad.asdfasdf.');
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\Parser::__construct
     *
     * @covers Lcobucci\JWT\Parser::parse
     * @covers Lcobucci\JWT\Parser::splitJwt
     * @covers Lcobucci\JWT\Parser::parseHeader
     *
     * @expectedException InvalidArgumentException
     */
    public function parseMustRaiseExceptionWhenHeaderIsFromAnEncryptedToken()
    {
        $this->decoder->expects($this->any())
                      ->method('jsonDecode')
                      ->willReturn(['enc' => 'AAA']);

        $parser = $this->createParser();
        $parser->parse('a.a.');
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\Parser::__construct
     * @uses Lcobucci\JWT\Token::__construct
     *
     * @covers Lcobucci\JWT\Parser::parse
     * @covers Lcobucci\JWT\Parser::splitJwt
     * @covers Lcobucci\JWT\Parser::parseHeader
     * @covers Lcobucci\JWT\Parser::parseClaims
     * @covers Lcobucci\JWT\Parser::parseSignature
     *
     */
    public function parseMustReturnANonSignedTokenWhenSignatureIsNotInformed()
    {
        $this->decoder->expects($this->at(1))
                      ->method('jsonDecode')
                      ->willReturn(['typ' => 'JWT', 'alg' => 'none']);

        $this->decoder->expects($this->at(3))
                      ->method('jsonDecode')
                      ->willReturn(['aud' => 'test']);

        $parser = $this->createParser();
        $token = $parser->parse('a.a.');

        $this->assertAttributeEquals(['typ' => 'JWT', 'alg' => 'none'], 'headers', $token);
        $this->assertAttributeEquals(['aud' => $this->defaultClaim], 'claims', $token);
        $this->assertAttributeEquals(null, 'signature', $token);
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\Parser::__construct
     * @uses Lcobucci\JWT\Token::__construct
     *
     * @covers Lcobucci\JWT\Parser::parse
     * @covers Lcobucci\JWT\Parser::splitJwt
     * @covers Lcobucci\JWT\Parser::parseHeader
     * @covers Lcobucci\JWT\Parser::parseClaims
     * @covers Lcobucci\JWT\Parser::parseSignature
     */
    public function parseShouldReplicateClaimValueOnHeaderWhenNeeded()
    {
        $this->decoder->expects($this->at(1))
                      ->method('jsonDecode')
                      ->willReturn(['typ' => 'JWT', 'alg' => 'none', 'aud' => 'test']);

        $this->decoder->expects($this->at(3))
                      ->method('jsonDecode')
                      ->willReturn(['aud' => 'test']);

        $parser = $this->createParser();
        $token = $parser->parse('a.a.');

        $this->assertAttributeEquals(
            ['typ' => 'JWT', 'alg' => 'none', 'aud' => $this->defaultClaim],
            'headers',
            $token
        );

        $this->assertAttributeEquals(['aud' => $this->defaultClaim], 'claims', $token);
        $this->assertAttributeEquals(null, 'signature', $token);
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\Parser::__construct
     * @uses Lcobucci\JWT\Token::__construct
     * @uses Lcobucci\JWT\Signature::__construct
     *
     * @covers Lcobucci\JWT\Parser::parse
     * @covers Lcobucci\JWT\Parser::splitJwt
     * @covers Lcobucci\JWT\Parser::parseHeader
     * @covers Lcobucci\JWT\Parser::parseClaims
     * @covers Lcobucci\JWT\Parser::parseSignature
     */
    public function parseMustReturnASignedTokenWhenSignatureIsInformed()
    {
        $this->decoder->expects($this->at(1))
                      ->method('jsonDecode')
                      ->willReturn(['typ' => 'JWT', 'alg' => 'HS256']);

        $this->decoder->expects($this->at(3))
                      ->method('jsonDecode')
                      ->willReturn(['aud' => 'test']);

        $this->decoder->expects($this->at(4))
                      ->method('base64UrlDecode')
                      ->willReturn('aaa');

        $parser = $this->createParser();
        $token = $parser->parse('a.a.a');

        $this->assertAttributeEquals(['typ' => 'JWT', 'alg' => 'HS256'], 'headers', $token);
        $this->assertAttributeEquals(['aud' => $this->defaultClaim], 'claims', $token);
        $this->assertAttributeEquals(new Signature('aaa'), 'signature', $token);
    }
}
