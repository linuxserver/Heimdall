<?php
/**
 * Test handling of <autoload> instructions.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Ruleset;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHP_CodeSniffer\Tests\Core\Ruleset\AbstractRulesetTestCase;

/**
 * Test handling of <autoload> instructions.
 *
 * Note: these tests need to run in separate processes as otherwise we cannot
 * reliably determine whether or not the correct files were loaded as the
 * underlying code uses `include_once`.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState         disabled
 *
 * @covers \PHP_CodeSniffer\Ruleset::processRuleset
 */
final class ProcessRulesetAutoloadTest extends AbstractRulesetTestCase
{


    /**
     * Verify that in CS mode, phpcs-only <autoload> directives are respected and phpcbf-only <autoload>
     * directives are ignored.
     *
     * @return void
     */
    public function testShouldProcessAutoloadCsonly()
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $this->markTestSkipped('This test needs CS mode to run');
        }

        $originallyIncludes = get_included_files();

        // Set up the ruleset.
        $standard = __DIR__.'/ProcessRulesetAutoloadTest.xml';
        $config   = new ConfigDouble(["--standard=$standard"]);
        new Ruleset($config);

        $finalIncludes = get_included_files();
        $diff          = array_diff($finalIncludes, $originallyIncludes);

        $this->assertContains(
            __DIR__.str_replace('/', DIRECTORY_SEPARATOR, '/Fixtures/ProcessRulesetAutoloadLoadAlways.1.php'),
            $diff,
            'ProcessRulesetAutoloadLoadAlways.1.php autoload file was not loaded'
        );
        $this->assertContains(
            __DIR__.str_replace('/', DIRECTORY_SEPARATOR, '/Fixtures/ProcessRulesetAutoloadLoadAlways.2.php'),
            $diff,
            'ProcessRulesetAutoloadLoadAlways.2.php autoload file was not loaded'
        );
        $this->assertContains(
            __DIR__.str_replace('/', DIRECTORY_SEPARATOR, '/Fixtures/ProcessRulesetAutoloadLoadAlways.3.php'),
            $diff,
            'ProcessRulesetAutoloadLoadAlways.3.php autoload file was not loaded'
        );
        $this->assertContains(
            __DIR__.str_replace('/', DIRECTORY_SEPARATOR, '/Fixtures/ProcessRulesetAutoloadLoadAlways.4.php'),
            $diff,
            'ProcessRulesetAutoloadLoadAlways.4.php autoload file was not loaded'
        );
        $this->assertContains(
            __DIR__.str_replace('/', DIRECTORY_SEPARATOR, '/Fixtures/ProcessRulesetAutoloadLoadPhpcsOnly.php'),
            $diff,
            'ProcessRulesetAutoloadLoadPhpcsOnly.php autoload file was not loaded'
        );
        $this->assertNotContains(
            __DIR__.str_replace('/', DIRECTORY_SEPARATOR, '/Fixtures/ProcessRulesetAutoloadLoadPhpcbfOnly.php'),
            $diff,
            'ProcessRulesetAutoloadLoadPhpcbfOnly.php autoload file was loaded, while it shouldn\'t have been'
        );

    }//end testShouldProcessAutoloadCsonly()


    /**
     * Verify that in CBF mode, phpcbf-only <autoload> directives are respected and phpcs-only <autoload>
     * directives are ignored.
     *
     * @group CBF
     *
     * @return void
     */
    public function testShouldProcessAutoloadCbfonly()
    {
        if (PHP_CODESNIFFER_CBF === false) {
            $this->markTestSkipped('This test needs CBF mode to run');
        }

        $originallyIncludes = get_included_files();

        // Set up the ruleset.
        $standard = __DIR__.'/ProcessRulesetAutoloadTest.xml';
        $config   = new ConfigDouble(["--standard=$standard"]);
        new Ruleset($config);

        $finalIncludes = get_included_files();
        $diff          = array_diff($finalIncludes, $originallyIncludes);

        $this->assertContains(
            __DIR__.str_replace('/', DIRECTORY_SEPARATOR, '/Fixtures/ProcessRulesetAutoloadLoadAlways.1.php'),
            $diff,
            'ProcessRulesetAutoloadLoadAlways.1.php autoload file was not loaded'
        );
        $this->assertContains(
            __DIR__.str_replace('/', DIRECTORY_SEPARATOR, '/Fixtures/ProcessRulesetAutoloadLoadAlways.2.php'),
            $diff,
            'ProcessRulesetAutoloadLoadAlways.2.php autoload file was not loaded'
        );
        $this->assertContains(
            __DIR__.str_replace('/', DIRECTORY_SEPARATOR, '/Fixtures/ProcessRulesetAutoloadLoadAlways.3.php'),
            $diff,
            'ProcessRulesetAutoloadLoadAlways.3.php autoload file was not loaded'
        );
        $this->assertContains(
            __DIR__.str_replace('/', DIRECTORY_SEPARATOR, '/Fixtures/ProcessRulesetAutoloadLoadAlways.4.php'),
            $diff,
            'ProcessRulesetAutoloadLoadAlways.4.php autoload file was not loaded'
        );
        $this->assertNotContains(
            __DIR__.str_replace('/', DIRECTORY_SEPARATOR, '/Fixtures/ProcessRulesetAutoloadLoadPhpcsOnly.php'),
            $diff,
            'ProcessRulesetAutoloadLoadPhpcsOnly.php autoload file was loaded, while it shouldn\'t have been'
        );
        $this->assertContains(
            __DIR__.str_replace('/', DIRECTORY_SEPARATOR, '/Fixtures/ProcessRulesetAutoloadLoadPhpcbfOnly.php'),
            $diff,
            'ProcessRulesetAutoloadLoadPhpcbfOnly.php autoload file was not loaded'
        );

    }//end testShouldProcessAutoloadCbfonly()


    /**
     * Test an exception is thrown when the <autoload> directive points to a file which doesn't exist.
     *
     * @return void
     */
    public function testFileNotFoundException()
    {
        $exceptionMsg = 'ERROR: The specified autoload file "./tests/Core/Ruleset/Fixtures/ThisFileDoesNotExist.php" does not exist';
        $this->expectRuntimeExceptionMessage($exceptionMsg);

        // Set up the ruleset.
        $standard = __DIR__.'/ProcessRulesetAutoloadFileNotFoundTest.xml';
        $config   = new ConfigDouble(["--standard=$standard"]);
        new Ruleset($config);

    }//end testFileNotFoundException()


}//end class
