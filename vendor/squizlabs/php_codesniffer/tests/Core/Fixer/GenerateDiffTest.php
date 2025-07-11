<?php
/**
 * Tests for diff generation.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Fixer;

use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHPUnit\Framework\TestCase;

/**
 * Tests for diff generation.
 *
 * Note: these tests are specifically about the Fixer::generateDiff() method and do not
 * test running the fixer itself, nor generating a diff based on a fixer run.
 *
 * @covers PHP_CodeSniffer\Fixer::generateDiff
 * @group  Windows
 */
final class GenerateDiffTest extends TestCase
{

    /**
     * A \PHP_CodeSniffer\Files\File object to compare the files against.
     *
     * @var \PHP_CodeSniffer\Files\LocalFile
     */
    private static $phpcsFile;


    /**
     * Initialize an \PHP_CodeSniffer\Files\File object with code.
     *
     * Things to take note of in the code snippet used for these tests:
     * - Line endings are \n.
     * - Tab indent.
     * - Trailing whitespace.
     *
     * Also note that the Config object is deliberately created without a `tabWidth` setting to
     * prevent doing tab replacement when parsing the file. This is to allow for testing a
     * diff with tabs vs spaces (which wouldn't yield a diff if tabs had already been replaced).
     *
     * @beforeClass
     *
     * @return void
     */
    public static function initializeFile()
    {
        $config  = new ConfigDouble();
        $ruleset = new Ruleset($config);

        self::$phpcsFile = new LocalFile(__DIR__.'/Fixtures/GenerateDiffTest.inc', $ruleset, $config);
        self::$phpcsFile->parse();
        self::$phpcsFile->fixer->startFile(self::$phpcsFile);

    }//end initializeFile()


    /**
     * Test generating a diff on the file object itself.
     *
     * @return void
     */
    public function testGenerateDiffNoFile()
    {
        $diff = self::$phpcsFile->fixer->generateDiff(null, false);

        $this->assertSame('', $diff);

    }//end testGenerateDiffNoFile()


    /**
     * Test generating a diff between a PHPCS File object and a file on disk.
     *
     * @param string $filePath The path to the file to compare the File object against.
     *
     * @dataProvider dataGenerateDiff
     *
     * @return void
     */
    public function testGenerateDiff($filePath)
    {
        $diff = self::$phpcsFile->fixer->generateDiff($filePath, false);

        // Allow for the tests to pass on Windows too.
        $diff = str_replace('--- tests\Core\Fixer/', '--- tests/Core/Fixer/', $diff);

        $expectedDiffFile = str_replace('.inc', '.diff', $filePath);

        $this->assertStringEqualsFile($expectedDiffFile, $diff);

    }//end testGenerateDiff()


    /**
     * Data provider.
     *
     * @see testGenerateDiff()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataGenerateDiff()
    {
        return [
            'no difference'                    => [
                'filePath' => __DIR__.'/Fixtures/GenerateDiffTest-NoDiff.inc',
            ],
            'line removed'                     => [
                'filePath' => __DIR__.'/Fixtures/GenerateDiffTest-LineRemoved.inc',
            ],
            'line added'                       => [
                'filePath' => __DIR__.'/Fixtures/GenerateDiffTest-LineAdded.inc',
            ],
            'var name changed'                 => [
                'filePath' => __DIR__.'/Fixtures/GenerateDiffTest-VarNameChanged.inc',
            ],
            'trailing whitespace removed'      => [
                'filePath' => __DIR__.'/Fixtures/GenerateDiffTest-NoTrailingWhitespace.inc',
            ],
            'tab replaced with spaces'         => [
                'filePath' => __DIR__.'/Fixtures/GenerateDiffTest-TabsToSpaces.inc',
            ],
            'blank lines at start of file'     => [
                'filePath' => __DIR__.'/Fixtures/GenerateDiffTest-BlankLinesAtStart.inc',
            ],
            'whitespace diff at start of file' => [
                'filePath' => __DIR__.'/Fixtures/GenerateDiffTest-WhiteSpaceAtStart.inc',
            ],
            'blank lines at end of file'       => [
                'filePath' => __DIR__.'/Fixtures/GenerateDiffTest-BlankLinesAtEnd.inc',
            ],
            'whitespace diff at end of file'   => [
                'filePath' => __DIR__.'/Fixtures/GenerateDiffTest-WhiteSpaceAtEnd.inc',
            ],
        ];

    }//end dataGenerateDiff()


    /**
     * Test generating a diff between a PHPCS File object and a file on disk and colourizing the output.
     *
     * @return void
     */
    public function testGenerateDiffColoured()
    {
        $expected  = "\033[31m--- tests/Core/Fixer/Fixtures/GenerateDiffTest-VarNameChanged.inc\033[0m".PHP_EOL;
        $expected .= "\033[32m+++ PHP_CodeSniffer\033[0m".PHP_EOL;
        $expected .= '@@ -1,7 +1,7 @@'.PHP_EOL;
        $expected .= ' <?php'.PHP_EOL;
        $expected .= ' // Comment with 2 spaces trailing whitespace.  '.PHP_EOL;
        $expected .= "\033[31m".'-$rav = FALSE;'."\033[0m".PHP_EOL;
        $expected .= "\033[32m".'+$var = FALSE;'."\033[0m".PHP_EOL;
        $expected .= ' '.PHP_EOL;
        $expected .= "\033[31m".'-if ($rav) {'."\033[0m".PHP_EOL;
        $expected .= "\033[32m".'+if ($var) {'."\033[0m".PHP_EOL;
        $expected .= ' 	echo \'This line is tab indented\';'.PHP_EOL;
        $expected .= ' }';

        $filePath = __DIR__.'/Fixtures/GenerateDiffTest-VarNameChanged.inc';
        $diff     = self::$phpcsFile->fixer->generateDiff($filePath);

        // Allow for the tests to pass on Windows too.
        $diff = str_replace('--- tests\Core\Fixer/', '--- tests/Core/Fixer/', $diff);

        $this->assertSame($expected, $diff);

    }//end testGenerateDiffColoured()


    /**
     * Test generating a diff between a PHPCS File object using *nix line endings and a file on disk
     * using Windows line endings.
     *
     * The point of this test is to verify that all lines are marked as having a difference.
     * The actual lines endings used in the diff shown to the end-user are not relevant for this
     * test.
     * As the "diff" command is finicky with what type of line endings are used when the only
     * difference on a line is the line ending, the test normalizes the line endings of the
     * received diff before testing it.
     *
     * @return void
     */
    public function testGenerateDiffDifferentLineEndings()
    {
        // By the looks of it, if the only diff between two files is line endings, the
        // diff generated by the *nix "diff" command will always contain *nix line endings.
        $expected  = '--- tests/Core/Fixer/Fixtures/GenerateDiffTest-WindowsLineEndings.inc'."\n";
        $expected .= '+++ PHP_CodeSniffer'."\n";
        $expected .= '@@ -1,7 +1,7 @@'."\n";
        $expected .= '-<?php'."\n";
        $expected .= '-// Comment with 2 spaces trailing whitespace.  '."\n";
        $expected .= '-$var = FALSE;'."\n";
        $expected .= '-'."\n";
        $expected .= '-if ($var) {'."\n";
        $expected .= '-	echo \'This line is tab indented\';'."\n";
        $expected .= '-}'."\n";
        $expected .= '+<?php'."\n";
        $expected .= '+// Comment with 2 spaces trailing whitespace.  '."\n";
        $expected .= '+$var = FALSE;'."\n";
        $expected .= '+'."\n";
        $expected .= '+if ($var) {'."\n";
        $expected .= '+	echo \'This line is tab indented\';'."\n";
        $expected .= '+}'."\n";

        $filePath = __DIR__.'/Fixtures/GenerateDiffTest-WindowsLineEndings.inc';
        $diff     = self::$phpcsFile->fixer->generateDiff($filePath, false);

        // Allow for the tests to pass on Windows too.
        $diff = str_replace('--- tests\Core\Fixer/', '--- tests/Core/Fixer/', $diff);

        // Normalize line endings of the diff.
        $diff = preg_replace('`\R`', "\n", $diff);

        $this->assertSame($expected, $diff);

    }//end testGenerateDiffDifferentLineEndings()


}//end class
