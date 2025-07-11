<?php
/**
 * Unit test class for the InterfaceNameSuffix sniff.
 *
 * @author  Anna Borzenko <annnechko@gmail.com>
 * @license https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\NamingConventions;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the InterfaceNameSuffix sniff.
 *
 * @covers \PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\InterfaceNameSuffixSniff
 */
final class InterfaceNameSuffixUnitTest extends AbstractSniffUnitTest
{


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @param string $testFile The name of the test file.
     *
     * @return array<int, int>
     */
    public function getErrorList($testFile='')
    {
        switch ($testFile) {
        case 'InterfaceNameSuffixUnitTest.1.inc':
            return [
                5 => 1,
                9 => 1,
            ];
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
     * @return array<int, int>
     */
    public function getWarningList()
    {
        return [];

    }//end getWarningList()


}//end class
