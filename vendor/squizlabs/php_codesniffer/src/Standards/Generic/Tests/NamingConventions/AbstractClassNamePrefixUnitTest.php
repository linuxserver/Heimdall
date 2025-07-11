<?php
/**
 * Unit test class for the AbstractClassNamePrefix sniff.
 *
 * @author  Anna Borzenko <annnechko@gmail.com>
 * @license https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\NamingConventions;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the AbstractClassNamePrefix sniff.
 *
 * @covers \PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\AbstractClassNamePrefixSniff
 */
final class AbstractClassNamePrefixUnitTest extends AbstractSniffUnitTest
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
        case 'AbstractClassNamePrefixUnitTest.1.inc':
            return [
                3  => 1,
                7  => 1,
                11 => 1,
                16 => 1,
                29 => 1,
                44 => 1,
                45 => 1,
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
