<?php
/**
 * Test the Ruleset::expandRulesetReference() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Ruleset;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHP_CodeSniffer\Tests\Core\Ruleset\AbstractRulesetTestCase;

/**
 * Test handling of "internal" references in the Ruleset::expandRulesetReference() method.
 *
 * @covers \PHP_CodeSniffer\Ruleset::expandRulesetReference
 */
final class ExpandRulesetReferenceInternalTest extends AbstractRulesetTestCase
{


    /**
     * Verify that a ruleset reference starting with "Internal." (including the dot) doesn't cause any sniffs to be registered.
     *
     * @return void
     */
    public function testInternalRefDoesNotGetExpanded()
    {
        // Set up the ruleset.
        $standard = __DIR__.'/ExpandRulesetReferenceInternalIgnoreTest.xml';
        $config   = new ConfigDouble(["--standard=$standard"]);
        $ruleset  = new Ruleset($config);

        $expected = ['Generic.PHP.BacktickOperator' => 'PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\PHP\\BacktickOperatorSniff'];

        $this->assertSame($expected, $ruleset->sniffCodes);

    }//end testInternalRefDoesNotGetExpanded()


    /**
     * While definitely not recommended, including a standard named "Internal", _does_ allow for sniffs to be registered.
     *
     * Note: customizations (exclusions/property setting etc) for individual sniffs may not always be handled correctly,
     * which is why naming a standard "Internal" is definitely not recommended.
     *
     * @return void
     */
    public function testInternalStandardDoesGetExpanded()
    {
        $message  = 'DEPRECATED: The name "Internal" is reserved for internal use. A PHP_CodeSniffer standard should not be called "Internal".'.PHP_EOL;
        $message .= 'Contact the maintainer of the standard to fix this.'.PHP_EOL.PHP_EOL;

        $this->expectOutputString($message);

        // Set up the ruleset.
        $standard = __DIR__.'/ExpandRulesetReferenceInternalStandardTest.xml';
        $config   = new ConfigDouble(["--standard=$standard"]);
        $ruleset  = new Ruleset($config);

        $expected = ['Internal.Valid.Valid' => 'Fixtures\\Internal\\Sniffs\\Valid\\ValidSniff'];

        $this->assertSame($expected, $ruleset->sniffCodes);

    }//end testInternalStandardDoesGetExpanded()


}//end class
