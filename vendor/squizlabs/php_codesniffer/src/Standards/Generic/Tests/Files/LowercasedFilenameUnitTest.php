<?php
/**
 * Unit test class for the LowercasedFilename sniff.
 *
 * @author    Andy Grunwald <andygrunwald@gmail.com>
 * @copyright 2010-2014 Andy Grunwald
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\Files;

use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the LowercasedFilename sniff.
 *
 * @covers \PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LowercasedFilenameSniff
 */
final class LowercasedFilenameUnitTest extends AbstractSniffUnitTest
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
        $testFileDir = dirname($testFileBase);
        $testFiles   = parent::getTestFiles($testFileBase);
        $testFiles[] = $testFileDir.DIRECTORY_SEPARATOR.'lowercased_filename_unit_test.inc';

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
        case 'LowercasedFilenameUnitTest.1.inc':
        case 'LowercasedFilenameUnitTest.2.inc':
            return [1 => 1];
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


    /**
     * Test the sniff bails early when handling STDIN.
     *
     * @return void
     */
    public function testStdIn()
    {
        $config            = new ConfigDouble();
        $config->standards = ['Generic'];
        $config->sniffs    = ['Generic.Files.LowercasedFilename'];

        $ruleset = new Ruleset($config);

        $content = '<?php ';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $this->assertSame(0, $file->getErrorCount());
        $this->assertSame(0, $file->getWarningCount());
        $this->assertCount(0, $file->getErrors());

    }//end testStdIn()


}//end class
