<?php
/**
 * Unit test class for the FunctionDeclarationArgumentSpacing sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\Functions;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the FunctionDeclarationArgumentSpacing sniff.
 *
 * @covers \PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\FunctionDeclarationArgumentSpacingSniff
 */
final class FunctionDeclarationArgumentSpacingUnitTest extends AbstractSniffUnitTest
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
        case 'FunctionDeclarationArgumentSpacingUnitTest.1.inc':
            return [
                3   => 1,
                5   => 2,
                7   => 2,
                8   => 2,
                9   => 2,
                11  => 2,
                13  => 7,
                14  => 2,
                15  => 2,
                16  => 4,
                18  => 2,
                35  => 2,
                36  => 2,
                44  => 2,
                45  => 1,
                46  => 1,
                51  => 2,
                53  => 2,
                55  => 1,
                56  => 1,
                58  => 1,
                73  => 7,
                76  => 1,
                77  => 1,
                81  => 1,
                89  => 2,
                92  => 1,
                93  => 1,
                94  => 1,
                95  => 1,
                99  => 11,
                100 => 2,
                101 => 2,
                102 => 2,
                103 => 1,
                106 => 1,
                107 => 2,
                111 => 3,
                113 => 1,
                117 => 1,
                123 => 1,
                129 => 1,
                135 => 1,
                141 => 1,
                149 => 2,
                155 => 2,
                163 => 2,
                174 => 2,
                182 => 1,
                185 => 1,
                191 => 1,
                193 => 1,
                195 => 1,
                196 => 1,
                200 => 2,
                205 => 1,
                206 => 1,
                207 => 2,
                208 => 1,
                209 => 1,
                215 => 2,
                220 => 1,
                221 => 1,
                222 => 2,
                223 => 1,
                224 => 1,
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
