<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\FileList::__construct method.
 *
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/HEAD/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Files\FileList;

use PHP_CodeSniffer\Files\FileList;

/**
 * Tests for the \PHP_CodeSniffer\Files\FileList::__construct method.
 *
 * @covers \PHP_CodeSniffer\Files\FileList::__construct
 */
final class ConstructTest extends AbstractFileListTestCase
{


    /**
     * Test the __construct() method.
     *
     * @param array<string> $files         List of file paths in the Config class.
     * @param array<string> $expectedFiles List of expected file paths in the FileList.
     *
     * @dataProvider dataConstruct
     *
     * @return void
     */
    public function testConstruct($files, $expectedFiles)
    {
        self::$config->files = $files;

        $fileList = new FileList(self::$config, self::$ruleset);

        $this->assertSame(self::$config, $fileList->config, 'Config object mismatch');
        $this->assertSame(self::$ruleset, $fileList->ruleset, 'Ruleset object mismatch');

        $this->assertCount(count($expectedFiles), $fileList, 'File count mismatch');

        $i = 0;

        // Sort the values to make the tests stable as different OSes will read directories
        // in a different order and the order is not relevant for these tests. Just the values.
        $fileListArray = iterator_to_array($fileList);
        ksort($fileListArray);

        foreach ($fileListArray as $filePath => $fileObject) {
            $this->assertSame(
                $expectedFiles[$i],
                $filePath,
                sprintf('File path mismatch: expected "%s", got "%s"', $expectedFiles[$i], $filePath)
            );
            $this->assertInstanceOf(
                'PHP_CodeSniffer\Files\File',
                $fileObject,
                sprintf('File object for "%s" is not an instance of PHP_CodeSniffer\Files\File', $filePath)
            );
            $i++;
        }

    }//end testConstruct()


    /**
     * Data provider for testConstruct.
     *
     * @return array<string, array<string, array<string>>>
     */
    public static function dataConstruct()
    {
        $fixturesDir = __DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR;

        return [
            'No files'                                     => [
                'files'         => [],
                'expectedFiles' => [],
            ],
            'Two files'                                    => [
                'files'         => [
                    'file1.php',
                    'file2.php',
                ],
                'expectedFiles' => [
                    'file1.php',
                    'file2.php',
                ],
            ],
            'A directory'                                  => [
                'files'         => [$fixturesDir],
                'expectedFiles' => [
                    $fixturesDir.'file1.php',
                    $fixturesDir.'file2.php',
                ],
            ],
            'Same file twice'                              => [
                'files'         => [
                    'file1.php',
                    'file1.php',
                ],
                'expectedFiles' => [
                    'file1.php',
                ],
            ],
            'File and then directory containing that file' => [
                'files'         => [
                    $fixturesDir.'file1.php',
                    $fixturesDir,
                ],
                'expectedFiles' => [
                    $fixturesDir.'file1.php',
                    $fixturesDir.'file2.php',
                ],
            ],
        ];

    }//end dataConstruct()


}//end class
