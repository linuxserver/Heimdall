<?php
/**
 * Unit test class for the MemberVarSpacing sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\WhiteSpace;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the MemberVarSpacing sniff.
 *
 * @covers \PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\MemberVarSpacingSniff
 */
final class MemberVarSpacingUnitTest extends AbstractSniffUnitTest
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
        case 'MemberVarSpacingUnitTest.1.inc':
            return [
                4   => 1,
                7   => 1,
                20  => 1,
                30  => 1,
                35  => 1,
                44  => 1,
                50  => 1,
                73  => 1,
                86  => 1,
                107 => 1,
                115 => 1,
                151 => 1,
                160 => 1,
                165 => 1,
                177 => 1,
                186 => 1,
                200 => 1,
                209 => 1,
                211 => 1,
                224 => 1,
                229 => 1,
                241 => 1,
                246 => 1,
                252 => 1,
                254 => 1,
                261 => 1,
                275 => 1,
                276 => 1,
                288 => 1,
                292 => 1,
                333 => 1,
                343 => 1,
                345 => 1,
                346 => 1,
                355 => 1,
                357 => 1,
                366 => 1,
                377 => 1,
                378 => 1,
                379 => 1,
                380 => 1,
                384 => 1,
                394 => 1,
                396 => 1,
                403 => 1,
                412 => 1,
                415 => 1,
                416 => 1,
                420 => 1,
                427 => 1,
                437 => 1,
                445 => 1,
                449 => 1,
                456 => 1,
                457 => 1,
                460 => 1,
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
