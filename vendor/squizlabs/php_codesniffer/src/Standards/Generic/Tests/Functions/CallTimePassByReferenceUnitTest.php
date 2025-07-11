<?php
/**
 * Unit test class for the CallTimePassByReference sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\Functions;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the CallTimePassByReference sniff.
 *
 * @covers \PHP_CodeSniffer\Standards\Generic\Sniffs\Functions\CallTimePassByReferenceSniff
 */
final class CallTimePassByReferenceUnitTest extends AbstractSniffUnitTest
{


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @param string $testFile The name of the test file to process.
     *
     * @return array<int, int>
     */
    public function getErrorList($testFile='CallTimePassByReferenceUnitTest.1.inc')
    {
        switch ($testFile) {
        case 'CallTimePassByReferenceUnitTest.1.inc':
            return [
                9  => 1,
                12 => 1,
                15 => 1,
                18 => 2,
                23 => 1,
                30 => 1,
                41 => 1,
                50 => 1,
                51 => 1,
                54 => 1,
                62 => 1,
                63 => 1,
                64 => 1,
            ];

        default:
            return [];
        }//end switch

    }//end getErrorList()


    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array<int, int>
     */
    public function getWarningList()
    {
        return [];

    }//end getWarningList()


}//end class
