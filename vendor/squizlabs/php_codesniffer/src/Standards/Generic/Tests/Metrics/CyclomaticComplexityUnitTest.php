<?php
/**
 * Unit test class for the CyclomaticComplexity sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\Metrics;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the CyclomaticComplexity sniff.
 *
 * @covers \PHP_CodeSniffer\Standards\Generic\Sniffs\Metrics\CyclomaticComplexitySniff
 */
final class CyclomaticComplexityUnitTest extends AbstractSniffUnitTest
{


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @param string $testFile The name of the file being tested.
     *
     * @return array<int, int>
     */
    public function getErrorList($testFile='')
    {
        switch ($testFile) {
        case 'CyclomaticComplexityUnitTest.1.inc':
            return [118 => 1];
        default:
            return [];
        }

    }//end getErrorList()


    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @param string $testFile The name of the file being tested.
     *
     * @return array<int, int>
     */
    public function getWarningList($testFile='')
    {
        switch ($testFile) {
        case 'CyclomaticComplexityUnitTest.1.inc':
            return [
                45  => 1,
                72  => 1,
                189 => 1,
                237 => 1,
                285 => 1,
                333 => 1,
                381 => 1,
                417 => 1,
                445 => 1,
            ];
        default:
            return [];
        }

    }//end getWarningList()


}//end class
