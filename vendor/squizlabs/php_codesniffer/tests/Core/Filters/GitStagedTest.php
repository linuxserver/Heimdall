<?php
/**
 * Tests for the \PHP_CodeSniffer\Filters\GitStaged class.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2023 PHPCSStandards Contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Filters;

use PHP_CodeSniffer\Filters\GitStaged;
use PHP_CodeSniffer\Tests\Core\Filters\AbstractFilterTestCase;
use RecursiveArrayIterator;
use ReflectionMethod;

/**
 * Tests for the \PHP_CodeSniffer\Filters\GitStaged class.
 *
 * @covers \PHP_CodeSniffer\Filters\GitStaged
 */
final class GitStagedTest extends AbstractFilterTestCase
{


    /**
     * Test filtering a file list for excluded paths.
     *
     * @return void
     */
    public function testFileNamePassesAsBasePathWillTranslateToDirname()
    {
        $rootFile = self::getBaseDir().'/autoload.php';

        $fakeDI          = new RecursiveArrayIterator(self::getFakeFileList());
        $constructorArgs = [
            $fakeDI,
            $rootFile,
            self::$config,
            self::$ruleset,
        ];
        $mockObj         = $this->getMockedClass('PHP_CodeSniffer\Filters\GitStaged', $constructorArgs, ['exec']);

        $mockObj->expects($this->once())
            ->method('exec')
            ->willReturn(['autoload.php']);

        $this->assertEquals([$rootFile], $this->getFilteredResultsAsArray($mockObj));

    }//end testFileNamePassesAsBasePathWillTranslateToDirname()


    /**
     * Test filtering a file list for excluded paths.
     *
     * @param array<string> $inputPaths      List of file paths to be filtered.
     * @param array<string> $outputGitStaged Simulated "git staged" output.
     * @param array<string> $expectedOutput  Expected filtering result.
     *
     * @dataProvider dataAcceptOnlyGitStaged
     *
     * @return void
     */
    public function testAcceptOnlyGitStaged($inputPaths, $outputGitStaged, $expectedOutput)
    {
        $fakeDI          = new RecursiveArrayIterator($inputPaths);
        $constructorArgs = [
            $fakeDI,
            self::getBaseDir(),
            self::$config,
            self::$ruleset,
        ];
        $mockObj         = $this->getMockedClass('PHP_CodeSniffer\Filters\GitStaged', $constructorArgs, ['exec']);

        $mockObj->expects($this->once())
            ->method('exec')
            ->willReturn($outputGitStaged);

        $this->assertEquals($expectedOutput, $this->getFilteredResultsAsArray($mockObj));

    }//end testAcceptOnlyGitStaged()


    /**
     * Data provider.
     *
     * @see testAcceptOnlyGitStaged
     *
     * @return array<string, array<string, array<string>>>
     */
    public static function dataAcceptOnlyGitStaged()
    {
        $basedir      = self::getBaseDir();
        $fakeFileList = self::getFakeFileList();

        $testCases = [
            'no files marked as git modified'                                      => [
                'inputPaths'      => $fakeFileList,
                'outputGitStaged' => [],
                'expectedOutput'  => [],
            ],

            'files marked as git modified which don\'t actually exist'             => [
                'inputPaths'      => $fakeFileList,
                'outputGitStaged' => [
                    'src/WillNotExist.php',
                    'src/WillNotExist.bak',
                    'src/WillNotExist.orig',
                ],
                'expectedOutput'  => [],
            ],

            'single file marked as git modified - file in root dir'                => [
                'inputPaths'      => $fakeFileList,
                'outputGitStaged' => [
                    'autoload.php',
                ],
                'expectedOutput'  => [
                    $basedir.'/autoload.php',
                ],
            ],
            'single file marked as git modified - file in sub dir'                 => [
                'inputPaths'      => $fakeFileList,
                'outputGitStaged' => [
                    'src/Standards/Generic/Sniffs/Classes/DuplicateClassNameSniff.php',
                ],
                'expectedOutput'  => [
                    $basedir.'/src',
                    $basedir.'/src/Standards',
                    $basedir.'/src/Standards/Generic',
                    $basedir.'/src/Standards/Generic/Sniffs',
                    $basedir.'/src/Standards/Generic/Sniffs/Classes',
                    $basedir.'/src/Standards/Generic/Sniffs/Classes/DuplicateClassNameSniff.php',
                ],
            ],

            'multiple files marked as git modified, none valid for scan'           => [
                'inputPaths'      => $fakeFileList,
                'outputGitStaged' => [
                    '.gitignore',
                    'phpcs.xml.dist',
                    'src/Standards/Squiz/Tests/WhiteSpace/OperatorSpacingUnitTest.js.fixed',
                ],
                'expectedOutput'  => [
                    $basedir.'/src',
                    $basedir.'/src/Standards',
                    $basedir.'/src/Standards/Squiz',
                    $basedir.'/src/Standards/Squiz/Tests',
                    $basedir.'/src/Standards/Squiz/Tests/WhiteSpace',
                ],
            ],

            'multiple files marked as git modified, only one file valid for scan'  => [
                'inputPaths'      => $fakeFileList,
                'outputGitStaged' => [
                    '.gitignore',
                    'src/Standards/Generic/Docs/Classes/DuplicateClassNameStandard.xml',
                    'src/Standards/Generic/Sniffs/Classes/DuplicateClassNameSniff.php',
                ],
                'expectedOutput'  => [
                    $basedir.'/src',
                    $basedir.'/src/Standards',
                    $basedir.'/src/Standards/Generic',
                    $basedir.'/src/Standards/Generic/Docs',
                    $basedir.'/src/Standards/Generic/Docs/Classes',
                    $basedir.'/src/Standards/Generic/Sniffs',
                    $basedir.'/src/Standards/Generic/Sniffs/Classes',
                    $basedir.'/src/Standards/Generic/Sniffs/Classes/DuplicateClassNameSniff.php',
                ],
            ],

            'multiple files marked as git modified, multiple files valid for scan' => [
                'inputPaths'      => $fakeFileList,
                'outputGitStaged' => [
                    '.yamllint.yml',
                    'autoload.php',
                    'src/Standards/Squiz/Sniffs/WhiteSpace/OperatorSpacingSniff.php',
                    'src/Standards/Squiz/Tests/WhiteSpace/OperatorSpacingUnitTest.inc',
                    'src/Standards/Squiz/Tests/WhiteSpace/OperatorSpacingUnitTest.inc.fixed',
                    'src/Standards/Squiz/Tests/WhiteSpace/OperatorSpacingUnitTest.js',
                    'src/Standards/Squiz/Tests/WhiteSpace/OperatorSpacingUnitTest.js.fixed',
                    'src/Standards/Squiz/Tests/WhiteSpace/OperatorSpacingUnitTest.php',
                ],
                'expectedOutput'  => [
                    $basedir.'/autoload.php',
                    $basedir.'/src',
                    $basedir.'/src/Standards',
                    $basedir.'/src/Standards/Squiz',
                    $basedir.'/src/Standards/Squiz/Sniffs',
                    $basedir.'/src/Standards/Squiz/Sniffs/WhiteSpace',
                    $basedir.'/src/Standards/Squiz/Sniffs/WhiteSpace/OperatorSpacingSniff.php',
                    $basedir.'/src/Standards/Squiz/Tests',
                    $basedir.'/src/Standards/Squiz/Tests/WhiteSpace',
                    $basedir.'/src/Standards/Squiz/Tests/WhiteSpace/OperatorSpacingUnitTest.inc',
                    $basedir.'/src/Standards/Squiz/Tests/WhiteSpace/OperatorSpacingUnitTest.js',
                    $basedir.'/src/Standards/Squiz/Tests/WhiteSpace/OperatorSpacingUnitTest.php',
                ],
            ],
        ];

        return $testCases;

    }//end dataAcceptOnlyGitStaged()


    /**
     * Test filtering a file list for excluded paths.
     *
     * @param string        $cmd      Command to run.
     * @param array<string> $expected Expected return value.
     *
     * @dataProvider dataExecAlwaysReturnsArray
     *
     * @return void
     */
    public function testExecAlwaysReturnsArray($cmd, $expected)
    {
        if (is_dir(__DIR__.'/../../../.git') === false) {
            $this->markTestSkipped('Not a git repository');
        }

        $fakeDI = new RecursiveArrayIterator(self::getFakeFileList());
        $filter = new GitStaged($fakeDI, '/', self::$config, self::$ruleset);

        $reflMethod = new ReflectionMethod($filter, 'exec');
        $reflMethod->setAccessible(true);
        $result = $reflMethod->invoke($filter, $cmd);

        $this->assertSame($expected, $result);

    }//end testExecAlwaysReturnsArray()


    /**
     * Data provider.
     *
     * @see testExecAlwaysReturnsArray
     *
     * {@internal Missing: test with a command which yields a `false` return value.
     *            JRF: I've not managed to find a command which does so, let alone one, which then
     *            doesn't have side-effects of uncatchable output while running the tests.}
     *
     * @return array<string, array<string, array<string>>>
     */
    public static function dataExecAlwaysReturnsArray()
    {
        return [
            'valid command which won\'t have any output unless files in the bin dir have been modified & staged' => [
                // Largely using the command used in the filter, but only checking the bin dir.
                // This should prevent the test unexpectedly failing during local development (in most cases).
                'cmd'      => 'git diff --cached --name-only -- '.escapeshellarg(self::getBaseDir().'/bin'),
                'expected' => [],
            ],
            'valid command which will have output'                                                               => [
                'cmd'      => 'git ls-files --exclude-standard -- '.escapeshellarg(self::getBaseDir().'/bin'),
                'expected' => [
                    'bin/phpcbf',
                    'bin/phpcbf.bat',
                    'bin/phpcs',
                    'bin/phpcs.bat',
                ],
            ],
        ];

    }//end dataExecAlwaysReturnsArray()


}//end class
