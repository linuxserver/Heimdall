<?php
/*
 * This file is part of sebastian/environment.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SebastianBergmann\Environment;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Environment\Runtime
 */
final class RuntimeTest extends TestCase
{
    /**
     * @var \SebastianBergmann\Environment\Runtime
     */
    private $env;

    protected function setUp()/*: void*/
    {
        $this->env = new Runtime;
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testAbilityToCollectCodeCoverageCanBeAssessed()/*: void*/
    {
        $this->assertInternalType('boolean', $this->env->canCollectCodeCoverage());
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testBinaryCanBeRetrieved()/*: void*/
    {
        $this->assertInternalType('string', $this->env->getBinary());
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testCanBeDetected()/*: void*/
    {
        $this->assertInternalType('boolean', $this->env->isHHVM());
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testCanBeDetected2()/*: void*/
    {
        $this->assertInternalType('boolean', $this->env->isPHP());
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testXdebugCanBeDetected()/*: void*/
    {
        $this->assertInternalType('boolean', $this->env->hasXdebug());
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testNameAndVersionCanBeRetrieved()/*: void*/
    {
        $this->assertInternalType('string', $this->env->getNameWithVersion());
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testNameCanBeRetrieved()/*: void*/
    {
        $this->assertInternalType('string', $this->env->getName());
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testVersionCanBeRetrieved()/*: void*/
    {
        $this->assertInternalType('string', $this->env->getVersion());
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testVendorUrlCanBeRetrieved()/*: void*/
    {
        $this->assertInternalType('string', $this->env->getVendorUrl());
    }
}
