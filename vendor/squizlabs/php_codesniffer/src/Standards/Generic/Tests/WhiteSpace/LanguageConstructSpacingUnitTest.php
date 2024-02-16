<?php
/**
 * Unit test class for the LanguageConstructSpacing sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2017 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\WhiteSpace;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the LanguageConstructSpacing sniff.
 *
 * @covers \PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\LanguageConstructSpacingSniff
 */
final class LanguageConstructSpacingUnitTest extends AbstractSniffUnitTest
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
        case 'LanguageConstructSpacingUnitTest.1.inc':
            return [
                3  => 1,
                5  => 1,
                8  => 1,
                10 => 1,
                13 => 1,
                15 => 1,
                18 => 1,
                20 => 1,
                23 => 1,
                25 => 1,
                28 => 1,
                30 => 1,
                33 => 1,
                36 => 1,
                39 => 1,
                40 => 1,
                43 => 1,
                44 => 1,
                45 => 1,
                46 => 1,
                48 => 1,
                52 => 1,
                55 => 1,
                56 => 1,
                57 => 2,
                60 => 1,
                63 => 1,
                65 => 1,
                73 => 1,
                75 => 1,
                77 => 1,
                81 => 1,
                83 => 1,
                85 => 1,
                86 => 1,
                90 => 1,
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
