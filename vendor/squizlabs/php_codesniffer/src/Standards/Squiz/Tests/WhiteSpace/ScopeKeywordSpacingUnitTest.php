<?php
/**
 * Unit test class for the ScopeKeywordSpacing sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\WhiteSpace;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the ScopeKeywordSpacing sniff.
 *
 * @covers \PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\ScopeKeywordSpacingSniff
 */
final class ScopeKeywordSpacingUnitTest extends AbstractSniffUnitTest
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
        case 'ScopeKeywordSpacingUnitTest.1.inc':
            return [
                7   => 2,
                8   => 1,
                13  => 1,
                14  => 1,
                15  => 1,
                17  => 2,
                26  => 1,
                28  => 1,
                29  => 1,
                64  => 1,
                67  => 1,
                71  => 1,
                103 => 1,
                106 => 1,
                111 => 1,
                119 => 1,
                121 => 1,
                127 => 2,
                134 => 2,
                138 => 2,
                140 => 3,
                145 => 1,
                149 => 1,
                152 => 1,
                155 => 1,
                158 => 1,
                162 => 1,
                163 => 1,
                166 => 1,
                167 => 1,
                179 => 1,
                186 => 1,
                187 => 1,
                188 => 1,
                193 => 2,
                197 => 1,
                198 => 3,
                199 => 2,
            ];

        case 'ScopeKeywordSpacingUnitTest.3.inc':
            return [6 => 1];

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
