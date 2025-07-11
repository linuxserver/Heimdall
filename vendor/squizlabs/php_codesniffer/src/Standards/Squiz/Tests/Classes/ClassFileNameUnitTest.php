<?php
/**
 * Unit test class for the ClassFileName sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\Classes;

use DirectoryIterator;
use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the ClassFileName sniff.
 *
 * @covers \PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\ClassFileNameSniff
 */
final class ClassFileNameUnitTest extends AbstractSniffUnitTest
{


    /**
     * Get a list of all test files to check.
     *
     * These will have the same base as the sniff name but different extensions.
     * We ignore the .php file as it is the class.
     *
     * @param string $testFileBase The base path that the unit tests files will have.
     *
     * @return string[]
     */
    protected function getTestFiles($testFileBase)
    {
        $testFiles = [];

        $dir = substr($testFileBase, 0, strrpos($testFileBase, DIRECTORY_SEPARATOR));
        $di  = new DirectoryIterator($dir);

        // Strip off the path and the "UnitTest." suffix from the $testFileBase to allow
        // for some less conventionally named test case files.
        $fileBase = str_replace($dir, '', $testFileBase);
        $fileBase = substr($fileBase, 1, -9);

        foreach ($di as $file) {
            $fileName  = $file->getBasename('UnitTest.inc');
            $extension = $file->getExtension();
            if (substr($fileName, 0, strlen($fileBase)) === $fileBase
                && $extension === 'inc'
            ) {
                $testFiles[] = $file->getPathname();
            }
        }

        // Put them in order.
        sort($testFiles, SORT_NATURAL);

        return $testFiles;

    }//end getTestFiles()


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
        case 'ClassFileNameUnitTest.inc':
            return [
                12 => 1,
                13 => 1,
                14 => 1,
                15 => 1,
                16 => 1,
                17 => 1,
                18 => 1,
                19 => 1,
                20 => 1,
                21 => 1,
                22 => 1,
                23 => 1,
                27 => 1,
                28 => 1,
                29 => 1,
                30 => 1,
                31 => 1,
                32 => 1,
                33 => 1,
                34 => 1,
                35 => 1,
                36 => 1,
                37 => 1,
                38 => 1,
                39 => 1,
                40 => 1,
                41 => 1,
                42 => 1,
            ];

        case 'ClassFileNameLiveCodingFailUnitTest.inc':
            return [
                6 => 1,
            ];

        case 'ClassFileName Spaces In FilenameUnitTest.inc':
            return [
                7 => 1,
            ];

        case 'ClassFileName-Dashes-In-FilenameUnitTest.inc':
            return [
                7 => 1,
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
