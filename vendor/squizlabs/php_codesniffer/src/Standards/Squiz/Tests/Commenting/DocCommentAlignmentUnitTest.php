<?php
/**
 * Unit test class for the DocCommentAlignment sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\Commenting;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the DocCommentAlignment sniff.
 *
 * @covers \PHP_CodeSniffer\Standards\Squiz\Sniffs\Commenting\DocCommentAlignmentSniff
 */
final class DocCommentAlignmentUnitTest extends AbstractSniffUnitTest
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
        $errors = [
            3  => 1,
            11 => 1,
            17 => 1,
            18 => 1,
            19 => 1,
            23 => 2,
            24 => 1,
            25 => 2,
            26 => 1,
            32 => 1,
            33 => 1,
            38 => 1,
            39 => 1,
        ];

        if ($testFile === 'DocCommentAlignmentUnitTest.inc') {
            $errors[75] = 1;
            $errors[83] = 1;
            $errors[84] = 1;
            $errors[90] = 1;
            $errors[91] = 1;
            $errors[95] = 1;
            $errors[96] = 1;

            $errors[106] = 1;
            $errors[107] = 1;
            $errors[111] = 2;
            $errors[112] = 1;
            $errors[113] = 1;
            $errors[114] = 1;

            $errors[120] = 1;
            $errors[121] = 1;
            $errors[125] = 1;
            $errors[126] = 1;
        }//end if

        return $errors;

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
