<?php
/**
 * This file is part of Lcobucci\JWT, a simple library to handle JWT and JWS
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\JWT\Claim;

use Lcobucci\JWT\ValidationData;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 2.0.0
 */
class EqualsToTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     *
     * @uses Lcobucci\JWT\Claim\Basic::__construct
     * @uses Lcobucci\JWT\Claim\Basic::getName
     * @uses Lcobucci\JWT\ValidationData::__construct
     * @uses Lcobucci\JWT\ValidationData::has
     * @uses Lcobucci\JWT\ValidationData::setCurrentTime
     *
     * @covers Lcobucci\JWT\Claim\EqualsTo::validate
     */
    public function validateShouldReturnTrueWhenValidationDontHaveTheClaim()
    {
        $claim = new EqualsTo('iss', 'test');

        $this->assertTrue($claim->validate(new ValidationData()));
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\Claim\Basic::__construct
     * @uses Lcobucci\JWT\Claim\Basic::getName
     * @uses Lcobucci\JWT\Claim\Basic::getValue
     * @uses Lcobucci\JWT\ValidationData::__construct
     * @uses Lcobucci\JWT\ValidationData::setIssuer
     * @uses Lcobucci\JWT\ValidationData::has
     * @uses Lcobucci\JWT\ValidationData::get
     * @uses Lcobucci\JWT\ValidationData::setCurrentTime
     *
     * @covers Lcobucci\JWT\Claim\EqualsTo::validate
     */
    public function validateShouldReturnTrueWhenValueIsEqualsToValidationData()
    {
        $claim = new EqualsTo('iss', 'test');

        $data = new ValidationData();
        $data->setIssuer('test');

        $this->assertTrue($claim->validate($data));
    }

    /**
     * @test
     *
     * @uses Lcobucci\JWT\Claim\Basic::__construct
     * @uses Lcobucci\JWT\Claim\Basic::getName
     * @uses Lcobucci\JWT\Claim\Basic::getValue
     * @uses Lcobucci\JWT\ValidationData::__construct
     * @uses Lcobucci\JWT\ValidationData::setIssuer
     * @uses Lcobucci\JWT\ValidationData::has
     * @uses Lcobucci\JWT\ValidationData::get
     * @uses Lcobucci\JWT\ValidationData::setCurrentTime
     *
     * @covers Lcobucci\JWT\Claim\EqualsTo::validate
     */
    public function validateShouldReturnFalseWhenValueIsNotEqualsToValidationData()
    {
        $claim = new EqualsTo('iss', 'test');

        $data = new ValidationData();
        $data->setIssuer('test1');

        $this->assertFalse($claim->validate($data));
    }
}
