<?php
/**
 * Tests a security fix for the Gitblame report generation.
 *
 * @copyright 2026 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/HEAD/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Reports\Gitblame;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Runner;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Tests a security fix for the Gitblame report generation.
 *
 * @coversNothing
 *
 * @group Windows
 */
final class GitblameEscapesFilenameTest extends TestCase
{

    /**
     * Name of the file which, if the filename of the temporary file is escaped correctly, should *not* be created.
     *
     * @var string
     */
    const DO_NOT_CREATE_FILE = 'newfile.txt';

    /**
     * List of files to clean up after running the tests in this file.
     *
     * @var string[]
     */
    private static $temporaryFiles = [];


    /**
     * Skip these tests when in CBF mode.
     *
     * @before
     *
     * @return void
     */
    protected function maybeSkipTests()
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $this->markTestSkipped('This test needs CS mode to run');
        }

    }//end maybeSkipTests()


    /**
     * Clean up temporary file(s).
     *
     * @afterClass
     *
     * @return void
     */
    public static function deleteTemporaryFiles()
    {
        foreach (self::$temporaryFiles as $file) {
            @unlink($file);
        }

        @unlink(__DIR__.'/'.self::DO_NOT_CREATE_FILE);

    }//end deleteTemporaryFiles()


    /**
     * Test that arbitrary shell commands injected in a filename do not get executed.
     *
     * @return void
     */
    public function testFilenameEscaping()
    {
        $untrustedFileName = __DIR__.'/$(touch '.self::DO_NOT_CREATE_FILE.').php';
        $this->createTemporaryFile($untrustedFileName);

        $errorMessage = sprintf('File %s should not exist at the start of the test', self::DO_NOT_CREATE_FILE);
        $this->verifyFileDoesNotExist(__DIR__.'/'.self::DO_NOT_CREATE_FILE, $errorMessage);

        // While we do not care about the report output for this particular test,
        // we do need to be sure that the correct report ran.
        // This regex checks that the output complies with the expected format for a 'Gitblame' reports.
        $this->expectOutputRegex('`\bUnknown\s+\([0-9\.]+\)\s+\(100\)\s+3\b`');

        $args = [
            '--standard=PSR12',
            '--basepath='.__DIR__,
            '--report=Gitblame',
            '--report-width=80',
            $untrustedFileName,
        ];
        $this->generateReport($args);

        $errorMessage = sprintf('File %s should not exist at the end of the test', self::DO_NOT_CREATE_FILE);
        $this->verifyFileDoesNotExist(__DIR__.'/'.self::DO_NOT_CREATE_FILE, $errorMessage);

    }//end testFilenameEscaping()


    /**
     * Test that file names are not interpreted as parameters/CLI flags to the external command 'git'.
     *
     * @param string $fileName Name for a temporary file to create to run the test with.
     *
     * @dataProvider dataFilenameParameterInjection
     *
     * @return void
     */
    public function testFilenameParameterInjection($fileName)
    {
        $this->createTemporaryFile($fileName);

        // This line of the report is different when 'git' interprets the
        // filename as a parameter.
        $this->expectOutputRegex('`\bUnknown\s+\(100\)\s+\(100\)\s+3\b`');

        $args = [
            '--standard=PSR12',
            '--basepath='.__DIR__,
            '--report=Gitblame',
            '--report-width=80',
            $fileName,
        ];
        $this->generateReport($args);

    }//end testFilenameParameterInjection()


    /**
     * Data provider.
     *
     * @return array<string, array<string>>
     */
    public static function dataFilenameParameterInjection()
    {
        return [
            'long-option'  => [__DIR__.'/--invalid-parameter-injection.php'],

            // At time of writing, there is no valid '-V' option to git(1).
            'short-option' => [__DIR__.'/-V.php'],
        ];

    }//end dataFilenameParameterInjection()


    /**
     * Create a temporary file with the supplied name.
     *
     * The created file MUST contain at least one error when run against the PSR12 ruleset,
     * as otherwise the report code will not be reached.
     *
     * @param string $fileName Name of the file to create.
     *
     * @return void
     */
    private function createTemporaryFile($fileName)
    {
        file_put_contents($fileName, "<?php\n\$x=1 ;\n");
        $this->assertFileExists($fileName, 'Failed to write temporary test file');

        // Remember that the file was created for clean up later.
        self::$temporaryFiles[] = $fileName;

    }//end createTemporaryFile()


    /**
     * Helper function to run PHPCS and create the report with the provided CLI arguments.
     *
     * @param array<string> $args CLI arguments to pass to the PHPCS run.
     *
     * @return void
     */
    private function generateReport($args)
    {
        $runner          = new Runner();
        $runner->config  = new ConfigDouble($args);
        $runner->ruleset = new Ruleset($runner->config);

        $reflMethod = new ReflectionMethod($runner, 'run');
        (PHP_VERSION_ID < 80100) && $reflMethod->setAccessible(true);
        $result = $reflMethod->invoke($runner);

        $this->assertGreaterThan(0, $result, 'File scanned did not contain any errors. Report code would not be triggered');

        $runner->reporter->printReports();

    }//end generateReport()


    /**
     * Helper function for PHPUnit cross-version compatible checking whether a file does *not* exist.
     *
     * @param string $fileName     Name of the file to check.
     * @param string $errorMessage Message to display if the file unexpectedly would be found.
     *
     * @return void
     */
    public function verifyFileDoesNotExist($fileName, $errorMessage)
    {
        if (method_exists($this, 'assertFileDoesNotExist') === true) {
            // PHPUnit 9.1.0+.
            $this->assertFileDoesNotExist($fileName, $errorMessage);
        } else {
            // PHPUnit < 9.1.0.
            $this->assertFileNotExists($fileName, $errorMessage);
        }

    }//end verifyFileDoesNotExist()


}//end class
