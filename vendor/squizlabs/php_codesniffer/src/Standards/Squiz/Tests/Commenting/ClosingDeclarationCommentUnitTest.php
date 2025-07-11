<?php
/**
 * Unit test class for the ClosingDeclarationComment sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\Commenting;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the ClosingDeclarationComment sniff.
 *
 * @covers \PHP_CodeSniffer\Standards\Squiz\Sniffs\Commenting\ClosingDeclarationCommentSniff
 */
final class ClosingDeclarationCommentUnitTest extends AbstractSniffUnitTest
{


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @param string $testFile The name of the test file being tested.
     *
     * @return array<int, int>
     */
    public function getErrorList($testFile='')
    {
        switch ($testFile) {
        case 'ClosingDeclarationCommentUnitTest.1.inc':
            return [
                13  => 1,
                17  => 1,
                31  => 1,
                41  => 1,
                59  => 1,
                63  => 1,
                67  => 1,
                79  => 1,
                83  => 1,
                89  => 1,
                92  => 1,
                98  => 1,
                101 => 1,
                106 => 1,
                110 => 1,
                124 => 1,
            ];

        case 'ClosingDeclarationCommentUnitTest.4.inc':
            return [8 => 1];

        case 'ClosingDeclarationCommentUnitTest.5.inc':
            return [11 => 1];

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
     * @param string $testFile The name of the test file being tested.
     *
     * @return array<int, int>
     */
    public function getWarningList($testFile='')
    {
        switch ($testFile) {
        case 'ClosingDeclarationCommentUnitTest.1.inc':
            return [71 => 1];

        case 'ClosingDeclarationCommentUnitTest.2.inc':
        case 'ClosingDeclarationCommentUnitTest.3.inc':
            return [7 => 1];

        default:
            return [];
        }

    }//end getWarningList()


}//end class
