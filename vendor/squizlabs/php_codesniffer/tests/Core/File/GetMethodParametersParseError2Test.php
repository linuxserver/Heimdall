<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File::getMethodParameters method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019-2024 PHPCSStandards Contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\File;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

/**
 * Tests for the \PHP_CodeSniffer\Files\File::getMethodParameters method.
 *
 * @covers \PHP_CodeSniffer\Files\File::getMethodParameters
 */
final class GetMethodParametersParseError2Test extends AbstractMethodUnitTest
{


    /**
     * Test receiving an empty array when encountering a specific parse error.
     *
     * @return void
     */
    public function testParseError()
    {
        $target = $this->getTargetToken('/* testParseError */', [T_FUNCTION, T_CLOSURE, T_FN]);
        $result = self::$phpcsFile->getMethodParameters($target);

        $this->assertSame([], $result);

    }//end testParseError()


}//end class
