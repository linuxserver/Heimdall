<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\FileList::addFile method.
 *
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/HEAD/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Files\FileList;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Files\FileList;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;

/**
 * Tests for the \PHP_CodeSniffer\Files\FileList::addFile method.
 *
 * @covers \PHP_CodeSniffer\Files\FileList::addFile
 */
final class AddFileTest extends AbstractFileListTestCase
{

    /**
     * The FileList object.
     *
     * @var \PHP_CodeSniffer\Files\FileList
     */
    private $fileList;


    /**
     * Initialize the FileList object.
     *
     * @before
     *
     * @return void
     */
    protected function initializeFileList()
    {
        self::$config->files = [];
        $this->fileList      = new FileList(self::$config, self::$ruleset);

    }//end initializeFileList()


    /**
     * Test adding a file to the list.
     *
     * @param string $fileName       The name of the file to add.
     * @param bool   $passFileObject Whether to pass a File object to addFile() or not.
     *
     * @dataProvider dataAddFile
     *
     * @return void
     */
    public function testAddFile($fileName, $passFileObject=false)
    {
        $fileObject = null;

        if ($passFileObject === true) {
            $fileObject = new File($fileName, self::$ruleset, self::$config);
        }

        $this->assertCount(0, $this->fileList);

        $this->fileList->addFile($fileName, $fileObject);

        $fileListArray = iterator_to_array($this->fileList);

        $this->assertCount(1, $this->fileList, 'File count mismatch');
        $this->assertArrayHasKey($fileName, $fileListArray, 'File not found in list');

        if ($fileObject instanceof File) {
            $this->assertSame($fileObject, $fileListArray[$fileName], 'File object mismatch');
        } else {
            $this->assertInstanceOf(
                'PHP_CodeSniffer\Files\File',
                $fileListArray[$fileName],
                'File object not found in list'
            );
        }

    }//end testAddFile()


    /**
     * Data provider for testAddFile.
     *
     * @return array<string, array<string, string|bool>>
     */
    public static function dataAddFile()
    {
        return [
            'Regular file'                  => [
                'fileName' => 'test1.php',
            ],
            'STDIN'                         => [
                'fileName' => 'STDIN',
            ],
            'Regular file with file object' => [
                'fileName'       => 'test1.php',
                'passFileObject' => true,
            ],
        ];

    }//end dataAddFile()


    /**
     * Test that it is not possible to add the same file twice.
     *
     * @return void
     */
    public function testAddFileShouldNotAddTheSameFileTwice()
    {
        $file1         = 'test1.php';
        $file2         = 'test2.php';
        $expectedFiles = [
            $file1,
            $file2,
        ];

        // Add $file1 once.
        $this->fileList->addFile($file1);
        $this->assertCount(1, $this->fileList);
        $this->assertSame([$file1], array_keys(iterator_to_array($this->fileList)));

        // Try to add $file1 again. Should be ignored.
        $this->fileList->addFile($file1);
        $this->assertCount(1, $this->fileList);
        $this->assertSame([$file1], array_keys(iterator_to_array($this->fileList)));

        // Add $file2. Should be added.
        $this->fileList->addFile($file2);
        $this->assertCount(2, $this->fileList);
        $this->assertSame($expectedFiles, array_keys(iterator_to_array($this->fileList)));

    }//end testAddFileShouldNotAddTheSameFileTwice()


}//end class
