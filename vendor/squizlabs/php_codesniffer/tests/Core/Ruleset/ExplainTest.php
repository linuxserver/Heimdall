<?php
/**
 * Tests to verify that the "explain" command functions as expected.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2023 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Ruleset;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHPUnit\Framework\TestCase;

/**
 * Test the Ruleset::explain() function.
 *
 * @covers \PHP_CodeSniffer\Ruleset::explain
 */
final class ExplainTest extends TestCase
{


    /**
     * Test the output of the "explain" command.
     *
     * @return void
     */
    public function testExplain()
    {
        // Set up the ruleset.
        $config  = new ConfigDouble(['--standard=PSR1', '-e']);
        $ruleset = new Ruleset($config);

        $expected  = PHP_EOL;
        $expected .= 'The PSR1 standard contains 8 sniffs'.PHP_EOL.PHP_EOL;
        $expected .= 'Generic (4 sniffs)'.PHP_EOL;
        $expected .= '------------------'.PHP_EOL;
        $expected .= '  Generic.Files.ByteOrderMark'.PHP_EOL;
        $expected .= '  Generic.NamingConventions.UpperCaseConstantName'.PHP_EOL;
        $expected .= '  Generic.PHP.DisallowAlternativePHPTags'.PHP_EOL;
        $expected .= '  Generic.PHP.DisallowShortOpenTag'.PHP_EOL.PHP_EOL;
        $expected .= 'PSR1 (3 sniffs)'.PHP_EOL;
        $expected .= '---------------'.PHP_EOL;
        $expected .= '  PSR1.Classes.ClassDeclaration'.PHP_EOL;
        $expected .= '  PSR1.Files.SideEffects'.PHP_EOL;
        $expected .= '  PSR1.Methods.CamelCapsMethodName'.PHP_EOL.PHP_EOL;
        $expected .= 'Squiz (1 sniff)'.PHP_EOL;
        $expected .= '---------------'.PHP_EOL;
        $expected .= '  Squiz.Classes.ValidClassName'.PHP_EOL;

        $this->expectOutputString($expected);

        $ruleset->explain();

    }//end testExplain()


    /**
     * Test the output of the "explain" command is not influenced by a user set report width.
     *
     * @return void
     */
    public function testExplainAlwaysDisplaysCompleteSniffName()
    {
        // Set up the ruleset.
        $config  = new ConfigDouble(['--standard=PSR1', '-e', '--report-width=30']);
        $ruleset = new Ruleset($config);

        $expected  = PHP_EOL;
        $expected .= 'The PSR1 standard contains 8 sniffs'.PHP_EOL.PHP_EOL;
        $expected .= 'Generic (4 sniffs)'.PHP_EOL;
        $expected .= '------------------'.PHP_EOL;
        $expected .= '  Generic.Files.ByteOrderMark'.PHP_EOL;
        $expected .= '  Generic.NamingConventions.UpperCaseConstantName'.PHP_EOL;
        $expected .= '  Generic.PHP.DisallowAlternativePHPTags'.PHP_EOL;
        $expected .= '  Generic.PHP.DisallowShortOpenTag'.PHP_EOL.PHP_EOL;
        $expected .= 'PSR1 (3 sniffs)'.PHP_EOL;
        $expected .= '---------------'.PHP_EOL;
        $expected .= '  PSR1.Classes.ClassDeclaration'.PHP_EOL;
        $expected .= '  PSR1.Files.SideEffects'.PHP_EOL;
        $expected .= '  PSR1.Methods.CamelCapsMethodName'.PHP_EOL.PHP_EOL;
        $expected .= 'Squiz (1 sniff)'.PHP_EOL;
        $expected .= '---------------'.PHP_EOL;
        $expected .= '  Squiz.Classes.ValidClassName'.PHP_EOL;

        $this->expectOutputString($expected);

        $ruleset->explain();

    }//end testExplainAlwaysDisplaysCompleteSniffName()


    /**
     * Test the output of the "explain" command when a ruleset only contains a single sniff.
     *
     * This is mostly about making sure that the summary line uses the correct grammar.
     *
     * @return void
     */
    public function testExplainSingleSniff()
    {
        // Set up the ruleset.
        $standard = __DIR__.'/ExplainSingleSniffTest.xml';
        $config   = new ConfigDouble(["--standard=$standard", '-e']);
        $ruleset  = new Ruleset($config);

        $expected  = PHP_EOL;
        $expected .= 'The ExplainSingleSniffTest standard contains 1 sniff'.PHP_EOL.PHP_EOL;
        $expected .= 'Squiz (1 sniff)'.PHP_EOL;
        $expected .= '---------------'.PHP_EOL;
        $expected .= '  Squiz.Scope.MethodScope'.PHP_EOL;

        $this->expectOutputString($expected);

        $ruleset->explain();

    }//end testExplainSingleSniff()


    /**
     * Test that "explain" works correctly with custom rulesets.
     *
     * Verifies that:
     * - The "standard" name is taken from the custom ruleset.
     * - Any and all sniff additions and exclusions in the ruleset are taken into account correctly.
     * - That the displayed list will have both the standards as well as the sniff names
     *   ordered alphabetically.
     *
     * @return void
     */
    public function testExplainCustomRuleset()
    {
        // Set up the ruleset.
        $standard = __DIR__.'/ExplainCustomRulesetTest.xml';
        $config   = new ConfigDouble(["--standard=$standard", '-e']);
        $ruleset  = new Ruleset($config);

        $expected  = PHP_EOL;
        $expected .= 'The ExplainCustomRulesetTest standard contains 10 sniffs'.PHP_EOL.PHP_EOL;
        $expected .= 'Generic (4 sniffs)'.PHP_EOL;
        $expected .= '------------------'.PHP_EOL;
        $expected .= '  Generic.Files.ByteOrderMark'.PHP_EOL;
        $expected .= '  Generic.NamingConventions.UpperCaseConstantName'.PHP_EOL;
        $expected .= '  Generic.PHP.DisallowAlternativePHPTags'.PHP_EOL;
        $expected .= '  Generic.PHP.DisallowShortOpenTag'.PHP_EOL.PHP_EOL;
        $expected .= 'PSR1 (2 sniffs)'.PHP_EOL;
        $expected .= '---------------'.PHP_EOL;
        $expected .= '  PSR1.Classes.ClassDeclaration'.PHP_EOL;
        $expected .= '  PSR1.Methods.CamelCapsMethodName'.PHP_EOL.PHP_EOL;
        $expected .= 'PSR12 (2 sniffs)'.PHP_EOL;
        $expected .= '----------------'.PHP_EOL;
        $expected .= '  PSR12.ControlStructures.BooleanOperatorPlacement'.PHP_EOL;
        $expected .= '  PSR12.ControlStructures.ControlStructureSpacing'.PHP_EOL.PHP_EOL;
        $expected .= 'Squiz (2 sniffs)'.PHP_EOL;
        $expected .= '----------------'.PHP_EOL;
        $expected .= '  Squiz.Classes.ValidClassName'.PHP_EOL;
        $expected .= '  Squiz.Scope.MethodScope'.PHP_EOL;

        $this->expectOutputString($expected);

        $ruleset->explain();

    }//end testExplainCustomRuleset()


    /**
     * Test the output of the "explain" command for a standard containing both deprecated
     * and non-deprecated sniffs.
     *
     * Tests that:
     * - Deprecated sniffs are marked with an asterix in the list.
     * - A footnote is displayed explaining the asterix.
     * - And that the "standard uses # deprecated sniffs" listing is **not** displayed.
     *
     * @return void
     */
    public function testExplainWithDeprecatedSniffs()
    {
        // Set up the ruleset.
        $standard = __DIR__."/ShowSniffDeprecationsTest.xml";
        $config   = new ConfigDouble(["--standard=$standard", '-e']);
        $ruleset  = new Ruleset($config);

        $expected  = PHP_EOL;
        $expected .= 'The ShowSniffDeprecationsTest standard contains 11 sniffs'.PHP_EOL.PHP_EOL;

        $expected .= 'TestStandard (11 sniffs)'.PHP_EOL;
        $expected .= '------------------------'.PHP_EOL;
        $expected .= '  TestStandard.Deprecated.WithLongReplacement *'.PHP_EOL;
        $expected .= '  TestStandard.Deprecated.WithoutReplacement *'.PHP_EOL;
        $expected .= '  TestStandard.Deprecated.WithReplacement *'.PHP_EOL;
        $expected .= '  TestStandard.Deprecated.WithReplacementContainingLinuxNewlines *'.PHP_EOL;
        $expected .= '  TestStandard.Deprecated.WithReplacementContainingNewlines *'.PHP_EOL;
        $expected .= '  TestStandard.SetProperty.AllowedAsDeclared'.PHP_EOL;
        $expected .= '  TestStandard.SetProperty.AllowedViaMagicMethod'.PHP_EOL;
        $expected .= '  TestStandard.SetProperty.AllowedViaStdClass'.PHP_EOL;
        $expected .= '  TestStandard.SetProperty.NotAllowedViaAttribute'.PHP_EOL;
        $expected .= '  TestStandard.SetProperty.PropertyTypeHandling'.PHP_EOL;
        $expected .= '  TestStandard.ValidSniffs.RegisterEmptyArray'.PHP_EOL.PHP_EOL;

        $expected .= '* Sniffs marked with an asterisk are deprecated.'.PHP_EOL;

        $this->expectOutputString($expected);

        $ruleset->explain();

    }//end testExplainWithDeprecatedSniffs()


}//end class
