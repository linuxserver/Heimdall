<?php
/**
 * Test the Ruleset::expandSniffDirectory() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Ruleset;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHPUnit\Framework\TestCase;

/**
 * Test the Ruleset::expandSniffDirectory() method.
 *
 * @covers \PHP_CodeSniffer\Ruleset::expandSniffDirectory
 */
final class ExpandSniffDirectoryTest extends TestCase
{


    /**
     * Test finding sniff files based on a given directory.
     *
     * This test verifies that:
     * - Hidden (sub)directories are ignored, but the given directory is allowed to be within a hidden directory.
     * - Hidden files are ignored.
     * - Files without a "php" extension are ignored.
     * - Files without a "Sniff" suffix in the file name are ignored.
     *
     * Note: the "[Another]AbstractSniff" files will be found and included in the return value
     * from `Ruleset::expandSniffDirectory()`.
     * Those are filtered out later in the `Ruleset::registerSniffs()` method.
     *
     * @return void
     */
    public function testExpandSniffDirectory()
    {
        // Set up the ruleset.
        $standard = __DIR__.'/ExpandSniffDirectoryTest.xml';
        $config   = new ConfigDouble(["--standard=$standard"]);
        $ruleset  = new Ruleset($config);

        $expectedPathToRuleset = __DIR__.'/Fixtures/DirectoryExpansion/.hiddenAbove/src/MyStandard/ruleset.xml';
        $expectedPathToRuleset = realpath($expectedPathToRuleset);
        $this->assertNotFalse($expectedPathToRuleset, 'Ruleset file could not be found');
        $this->assertContains($expectedPathToRuleset, $ruleset->paths, 'Ruleset file not included in the "seen ruleset paths"');

        $expectedSniffCodes = [
            'MyStandard.CategoryA.FindMe' => 'MyStandard\\Sniffs\\CategoryA\\FindMeSniff',
            'MyStandard.CategoryB.FindMe' => 'MyStandard\\Sniffs\\CategoryB\\FindMeSniff',
        ];

        // Sort the value to make the tests stable as different OSes will read directories
        // in a different order and the order is not relevant for these tests. Just the values.
        $actual = $ruleset->sniffCodes;
        ksort($actual);

        $this->assertSame($expectedSniffCodes, $actual, 'Registered sniffs do not match expectation');

    }//end testExpandSniffDirectory()


}//end class
