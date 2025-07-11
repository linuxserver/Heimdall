<?php
/**
 * Test case with helper methods for tests for the Ruleset class.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Ruleset;

use PHPUnit\Framework\TestCase;

abstract class AbstractRulesetTestCase extends TestCase
{

    /**
     * The fully qualified name of the PHPCS runtime exception class.
     *
     * @var string
     */
    const RUNTIME_EXCEPTION = 'PHP_CodeSniffer\Exceptions\RuntimeException';


    /**
     * Asserts that an object has a specified property in a PHPUnit cross-version compatible manner.
     *
     * @param string $propertyName The name of the property.
     * @param object $object       The object on which to check whether the property exists.
     * @param string $message      Optional failure message to display.
     *
     * @return void
     */
    protected function assertXObjectHasProperty($propertyName, $object, $message='')
    {
        if (method_exists($this, 'assertObjectHasProperty') === true) {
            $this->assertObjectHasProperty($propertyName, $object, $message);
        } else {
            // PHPUnit < 9.6.11.
            $this->assertObjectHasAttribute($propertyName, $object, $message);
        }

    }//end assertXObjectHasProperty()


    /**
     * Asserts that an object does not have a specified property
     * in a PHPUnit cross-version compatible manner.
     *
     * @param string $propertyName The name of the property.
     * @param object $object       The object on which to check whether the property exists.
     * @param string $message      Optional failure message to display.
     *
     * @return void
     */
    protected function assertXObjectNotHasProperty($propertyName, $object, $message='')
    {
        if (method_exists($this, 'assertObjectNotHasProperty') === true) {
            $this->assertObjectNotHasProperty($propertyName, $object, $message);
        } else {
            // PHPUnit < 9.6.11.
            $this->assertObjectNotHasAttribute($propertyName, $object, $message);
        }

    }//end assertXObjectNotHasProperty()


    /**
     * Helper method to tell PHPUnit to expect a PHPCS RuntimeException with a certain message
     * in a PHPUnit cross-version compatible manner.
     *
     * @param string $message The expected exception message.
     *
     * @return void
     */
    protected function expectRuntimeExceptionMessage($message)
    {
        if (method_exists($this, 'expectException') === true) {
            // PHPUnit 5+.
            $this->expectException(self::RUNTIME_EXCEPTION);
            $this->expectExceptionMessage($message);
        } else {
            // PHPUnit 4.
            $this->setExpectedException(self::RUNTIME_EXCEPTION, $message);
        }

    }//end expectRuntimeExceptionMessage()


    /**
     * Helper method to tell PHPUnit to expect a PHPCS RuntimeException which matches a regex patten
     * in a PHPUnit cross-version compatible manner.
     *
     * @param string $regex The regex which should match.
     *
     * @return void
     */
    protected function expectRuntimeExceptionRegex($regex)
    {
        if (method_exists($this, 'expectExceptionMessageMatches') === true) {
            $this->expectException(self::RUNTIME_EXCEPTION);
            $this->expectExceptionMessageMatches($regex);
        } else if (method_exists($this, 'expectExceptionMessageRegExp') === true) {
            // PHPUnit < 8.4.0.
            $this->expectException(self::RUNTIME_EXCEPTION);
            $this->expectExceptionMessageRegExp($regex);
        } else {
            // PHPUnit < 5.2.0.
            $this->setExpectedExceptionRegExp(self::RUNTIME_EXCEPTION, $regex);
        }

    }//end expectRuntimeExceptionRegex()


}//end class
