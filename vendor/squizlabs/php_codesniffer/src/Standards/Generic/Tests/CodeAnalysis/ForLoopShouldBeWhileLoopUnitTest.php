<?php
/**
 * Unit test class for the ForLoopShouldBeWhileLoop sniff.
 *
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2007-2014 Manuel Pichler. All rights reserved.
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\CodeAnalysis;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the ForLoopShouldBeWhileLoop sniff.
 *
 * @covers \PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\ForLoopShouldBeWhileLoopSniff
 */
final class ForLoopShouldBeWhileLoopUnitTest extends AbstractSniffUnitTest
{


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @return array<int, int>
     */
    public function getErrorList()
    {
        return [];

    }//end getErrorList()


    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @param string $testFile The name of the test file being tested.
     *
     * @return array<int, int>
     */
    public function getWarningList($testFile='')
    {
        switch ($testFile) {
        case 'ForLoopShouldBeWhileLoopUnitTest.1.inc':
            return [
                6  => 1,
                10 => 1,
                34 => 1,
            ];
        default:
            return [];
        }

    }//end getWarningList()


}//end class
