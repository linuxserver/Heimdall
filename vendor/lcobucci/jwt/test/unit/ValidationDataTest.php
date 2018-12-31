<?php
/**
 * This file is part of Lcobucci\JWT, a simple library to handle JWT and JWS
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\JWT;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 2.0.0
 */
class ValidationDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     *
     * @covers Lcobucci\JWT\ValidationData::__construct
     */
    public function constructorShouldConfigureTheItems()
    {
        $expected = $this->createExpectedData();
        $data = new ValidationData(1);

        $this->assertAttributeSame($expected, 'items', $data);
    }

    /**
     * @test
     *
     * @dataProvider claimValues
     *
     * @uses Lcobucci\JWT\ValidationData::__construct
     *
     * @covers Lcobucci\JWT\ValidationData::setId
     */
    public function setIdShouldChangeTheId($id)
    {
        $expected = $this->createExpectedData($id);
        $data = new ValidationData(1);
        $data->setId($id);

        $this->assertAttributeSame($expected, 'items', $data);
    }

    /**
     * @test
     *
     * @dataProvider claimValues
     *
     * @uses Lcobucci\JWT\ValidationData::__construct
     *
     * @covers Lcobucci\JWT\ValidationData::setIssuer
     */
    public function setIssuerShouldChangeTheIssuer($iss)
    {
        $expected = $this->createExpectedData(null, null, $iss);
        $data = new ValidationData(1);
        $data->setIssuer($iss);

        $this->assertAttributeSame($expected, 'items', $data);
    }

    /**
     * @test
     *
     * @dataProvider claimValues
     *
     * @uses Lcobucci\JWT\ValidationData::__construct
     *
     * @covers Lcobucci\JWT\ValidationData::setAudience
     */
    public function setAudienceShouldChangeTheAudience($aud)
    {
        $expected = $this->createExpectedData(null, null, null, $aud);
        $data = new ValidationData(1);
        $data->setAudience($aud);

        $this->assertAttributeSame($expected, 'items', $data);
    }

    /**
     * @test
     *
     * @dataProvider claimValues
     *
     * @uses Lcobucci\JWT\ValidationData::__construct
     *
     * @covers Lcobucci\JWT\ValidationData::setSubject
     */
    public function setSubjectShouldChangeTheSubject($sub)
    {
        $expected = $this->createExpectedData(null, $sub);
        $data = new ValidationData(1);
        $data->setSubject($sub);

        $this->assertAttributeSame($expected, 'items', $data);
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\ValidationData::__construct
     *
     * @covers Lcobucci\JWT\ValidationData::setCurrentTime
     */
    public function setCurrentTimeShouldChangeTheTimeBasedValues()
    {
        $expected = $this->createExpectedData(null, null, null, null, 2);
        $data = new ValidationData(1);
        $data->setCurrentTime(2);

        $this->assertAttributeSame($expected, 'items', $data);
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\ValidationData::__construct
     *
     * @covers Lcobucci\JWT\ValidationData::has
     */
    public function hasShouldReturnTrueWhenItemIsNotEmpty()
    {
        $data = new ValidationData(1);

        $this->assertTrue($data->has('iat'));
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\ValidationData::__construct
     *
     * @covers Lcobucci\JWT\ValidationData::has
     */
    public function hasShouldReturnFalseWhenItemIsEmpty()
    {
        $data = new ValidationData(1);

        $this->assertFalse($data->has('jti'));
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\ValidationData::__construct
     *
     * @covers Lcobucci\JWT\ValidationData::has
     */
    public function hasShouldReturnFalseWhenItemIsNotDefined()
    {
        $data = new ValidationData(1);

        $this->assertFalse($data->has('test'));
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\ValidationData::__construct
     *
     * @covers Lcobucci\JWT\ValidationData::get
     */
    public function getShouldReturnTheItemValue()
    {
        $data = new ValidationData(1);

        $this->assertEquals(1, $data->get('iat'));
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\ValidationData::__construct
     *
     * @covers Lcobucci\JWT\ValidationData::get
     */
    public function getShouldReturnNullWhenItemIsNotDefined()
    {
        $data = new ValidationData(1);

        $this->assertNull($data->get('test'));
    }

    /**
     * @return array
     */
    public function claimValues()
    {
        return [
            [1],
            ['test']
        ];
    }

    /**
     * @param string $id
     * @param string $sub
     * @param string $iss
     * @param string $aud
     * @param int $time
     *
     * @return array
     */
    private function createExpectedData(
        $id = null,
        $sub = null,
        $iss = null,
        $aud = null,
        $time = 1
    ) {
        return [
            'jti' => $id !== null ? (string) $id : null,
            'iss' => $iss !== null ? (string) $iss : null,
            'aud' => $aud !== null ? (string) $aud : null,
            'sub' => $sub !== null ? (string) $sub : null,
            'iat' => $time,
            'nbf' => $time,
            'exp' => $time
        ];
    }
}
