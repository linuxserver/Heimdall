<?php
/**
 * Unit test class for the EmptyPHPStatement sniff.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2017 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\CodeAnalysis;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the EmptyPHPStatement sniff.
 *
 * @covers \PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\EmptyPHPStatementSniff
 */
final class EmptyPHPStatementUnitTest extends AbstractSniffUnitTest
{


    /**
     * Get a list of all test files to check.
     *
     * @param string $testFileBase The base path that the unit tests files will have.
     *
     * @return string[]
     */
    protected function getTestFiles($testFileBase)
    {
        $testFiles = [$testFileBase.'1.inc'];

        $option = (bool) ini_get('short_open_tag');
        if ($option === true) {
            $testFiles[] = $testFileBase.'2.inc';
        }

        return $testFiles;

    }//end getTestFiles()


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
     * @param string $testFile The name of the file being tested.
     *
     * @return array<int, int>
     */
    public function getWarningList($testFile='')
    {
        switch ($testFile) {
        case 'EmptyPHPStatementUnitTest.1.inc':
            return [
                9  => 1,
                12 => 1,
                15 => 1,
                18 => 1,
                21 => 1,
                22 => 2,
                31 => 1,
                33 => 1,
                43 => 1,
                45 => 2,
                49 => 1,
                50 => 1,
                57 => 1,
                59 => 1,
                61 => 1,
                63 => 2,
                71 => 1,
                72 => 1,
                80 => 1,
            ];
        case 'EmptyPHPStatementUnitTest.2.inc':
            return [
                3  => 1,
                4  => 1,
                13 => 1,
                15 => 1,
                25 => 1,
                27 => 1,
            ];
        default:
            return [];
        }//end switch

    }//end getWarningList()


}//end class
