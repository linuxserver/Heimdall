<?php
/**
 * Unit test class for the UnnecessaryHeredoc sniff.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\Strings;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the UnnecessaryHeredoc sniff.
 *
 * @covers \PHP_CodeSniffer\Standards\Generic\Sniffs\Strings\UnnecessaryHeredocSniff
 */
final class UnnecessaryHeredocUnitTest extends AbstractSniffUnitTest
{


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
        $warnings = [
            100 => 1,
            104 => 1,
        ];

        switch ($testFile) {
        case 'UnnecessaryHeredocUnitTest.1.inc':
            return $warnings;

        case 'UnnecessaryHeredocUnitTest.2.inc':
            if (PHP_VERSION_ID >= 70300) {
                return $warnings;
            }

            // PHP 7.2 or lower: PHP version which doesn't support flexible heredocs/nowdocs yet.
            return [];

        default:
            return [];
        }

    }//end getWarningList()


}//end class
