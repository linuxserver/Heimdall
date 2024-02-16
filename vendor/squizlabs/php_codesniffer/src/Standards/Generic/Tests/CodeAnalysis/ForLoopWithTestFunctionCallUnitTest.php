<?php
/**
 * Unit test class for the ForLoopWithTestFunctionCall sniff.
 *
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2007-2014 Manuel Pichler. All rights reserved.
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\CodeAnalysis;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the ForLoopWithTestFunctionCall sniff.
 *
 * @covers \PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\ForLoopWithTestFunctionCallSniff
 */
final class ForLoopWithTestFunctionCallUnitTest extends AbstractSniffUnitTest
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
        case 'ForLoopWithTestFunctionCallUnitTest.1.inc':
            return [
                4  => 1,
                13 => 1,
                17 => 1,
                21 => 1,
                26 => 1,
                35 => 1,
                39 => 1,
                43 => 1,
                47 => 1,
                52 => 1,
                58 => 1,
                66 => 1,
                72 => 1,
                81 => 1,
            ];
        default:
            return [];
        }//end switch

    }//end getWarningList()


}//end class
