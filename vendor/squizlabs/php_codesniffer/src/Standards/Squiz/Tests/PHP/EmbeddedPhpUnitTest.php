<?php
/**
 * Unit test class for the EmbeddedPhp sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\PHP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the EmbeddedPhp sniff.
 *
 * @covers \PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\EmbeddedPhpSniff
 */
final class EmbeddedPhpUnitTest extends AbstractSniffUnitTest
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
        case 'EmbeddedPhpUnitTest.1.inc':
            return [
                7   => 1,
                12  => 1,
                18  => 1,
                19  => 2,
                20  => 1,
                21  => 1,
                22  => 3,
                24  => 1,
                26  => 1,
                29  => 1,
                30  => 1,
                31  => 1,
                34  => 1,
                36  => 1,
                40  => 1,
                41  => 1,
                44  => 1,
                45  => 1,
                49  => 1,
                59  => 1,
                63  => 1,
                93  => 1,
                94  => 2,
                100 => 1,
                102 => 1,
                112 => 1,
                113 => 1,
                116 => 1,
                117 => 1,
                120 => 1,
                121 => 1,
                128 => 1,
                129 => 1,
                132 => 1,
                134 => 1,
                136 => 1,
                138 => 1,
                142 => 1,
                145 => 1,
                151 => 1,
                158 => 1,
                165 => 1,
                169 => 1,
                175 => 1,
                176 => 2,
                178 => 1,
                179 => 1,
                180 => 2,
                181 => 1,
                189 => 1,
                212 => 1,
                214 => 2,
                219 => 1,
                223 => 1,
                225 => 1,
                226 => 1,
                227 => 2,
                228 => 1,
                235 => 1,
                241 => 1,
                248 => 1,
                253 => 1,
                258 => 1,
                263 => 1,
                264 => 1,
            ];

        case 'EmbeddedPhpUnitTest.2.inc':
        case 'EmbeddedPhpUnitTest.4.inc':
            return [
                5 => 2,
                6 => 2,
                7 => 2,
            ];

        case 'EmbeddedPhpUnitTest.3.inc':
            return [
                10  => 1,
                15  => 1,
                21  => 1,
                22  => 2,
                23  => 1,
                24  => 1,
                25  => 3,
                28  => 1,
                29  => 1,
                30  => 1,
                33  => 1,
                35  => 1,
                39  => 1,
                40  => 1,
                43  => 1,
                44  => 1,
                48  => 1,
                53  => 1,
                55  => 1,
                61  => 1,
                62  => 1,
                65  => 2,
                66  => 2,
                69  => 1,
                70  => 1,
                75  => 1,
                82  => 1,
                89  => 1,
                93  => 1,
                98  => 2,
                99  => 1,
                103 => 2,
                105 => 1,
                111 => 1,
                112 => 2,
                114 => 1,
                115 => 1,
                116 => 2,
                117 => 1,
            ];

        case 'EmbeddedPhpUnitTest.5.inc':
            return [
                16 => 1,
                18 => 1,
                25 => 1,
                26 => 1,
                29 => 1,
                31 => 1,
                33 => 1,
                35 => 1,
                39 => 1,
                42 => 1,
            ];

        case 'EmbeddedPhpUnitTest.12.inc':
        case 'EmbeddedPhpUnitTest.13.inc':
            return [
                10 => 1,
                12 => 1,
            ];

        case 'EmbeddedPhpUnitTest.18.inc':
            return [11 => 1];

        case 'EmbeddedPhpUnitTest.19.inc':
            return [13 => 1];

        case 'EmbeddedPhpUnitTest.20.inc':
        case 'EmbeddedPhpUnitTest.21.inc':
            return [12 => 2];

        case 'EmbeddedPhpUnitTest.22.inc':
            return [
                14 => 1,
                22 => 2,
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
