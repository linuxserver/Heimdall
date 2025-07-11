<?php
/**
 * Test the Ruleset::processRuleset() method.
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
 * Test various aspects of the Ruleset::processRuleset() method not covered via other tests.
 *
 * @covers \PHP_CodeSniffer\Ruleset::processRuleset
 */
final class ProcessRulesetTest extends TestCase
{


    /**
     * Verify that a registered standard which doesn't have a "Sniffs" directory, but does have a file
     * called "Sniffs" doesn't result in any errors being thrown.
     *
     * @return void
     */
    public function testSniffsFileNotDirectory()
    {
        // Set up the ruleset.
        $standard = __DIR__.'/ProcessRulesetInvalidNoSniffsDirTest.xml';
        $config   = new ConfigDouble(["--standard=$standard"]);
        $ruleset  = new Ruleset($config);

        $expected = ['Generic.PHP.BacktickOperator' => 'PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\BacktickOperatorSniff'];

        $this->assertSame($expected, $ruleset->sniffCodes);

    }//end testSniffsFileNotDirectory()


    /**
     * Verify that all sniffs in a registered standard included in a ruleset automatically get added.
     *
     * @return void
     */
    public function testAutoExpandSniffsDirectory()
    {
        // Set up the ruleset.
        $standard = __DIR__.'/ProcessRulesetAutoExpandSniffsDirectoryTest.xml';
        $config   = new ConfigDouble(["--standard=$standard"]);
        $ruleset  = new Ruleset($config);

        $std      = 'TestStandard';
        $sniffDir = 'Fixtures\TestStandard\Sniffs';
        $expected = [
            "$std.Deprecated.WithLongReplacement"                    => "$sniffDir\Deprecated\WithLongReplacementSniff",
            "$std.Deprecated.WithReplacement"                        => "$sniffDir\Deprecated\WithReplacementSniff",
            "$std.Deprecated.WithReplacementContainingLinuxNewlines" => "$sniffDir\Deprecated\WithReplacementContainingLinuxNewlinesSniff",
            "$std.Deprecated.WithReplacementContainingNewlines"      => "$sniffDir\Deprecated\WithReplacementContainingNewlinesSniff",
            "$std.Deprecated.WithoutReplacement"                     => "$sniffDir\Deprecated\WithoutReplacementSniff",
            "$std.DeprecatedInvalid.EmptyDeprecationVersion"         => "$sniffDir\DeprecatedInvalid\EmptyDeprecationVersionSniff",
            "$std.DeprecatedInvalid.EmptyRemovalVersion"             => "$sniffDir\DeprecatedInvalid\EmptyRemovalVersionSniff",
            "$std.DeprecatedInvalid.InvalidDeprecationMessage"       => "$sniffDir\DeprecatedInvalid\InvalidDeprecationMessageSniff",
            "$std.DeprecatedInvalid.InvalidDeprecationVersion"       => "$sniffDir\DeprecatedInvalid\InvalidDeprecationVersionSniff",
            "$std.DeprecatedInvalid.InvalidRemovalVersion"           => "$sniffDir\DeprecatedInvalid\InvalidRemovalVersionSniff",
            "$std.MissingInterface.ValidImplements"                  => "$sniffDir\MissingInterface\ValidImplementsSniff",
            "$std.MissingInterface.ValidImplementsViaAbstract"       => "$sniffDir\MissingInterface\ValidImplementsViaAbstractSniff",
            "$std.SetProperty.AllowedAsDeclared"                     => "$sniffDir\SetProperty\AllowedAsDeclaredSniff",
            "$std.SetProperty.AllowedViaMagicMethod"                 => "$sniffDir\SetProperty\AllowedViaMagicMethodSniff",
            "$std.SetProperty.AllowedViaStdClass"                    => "$sniffDir\SetProperty\AllowedViaStdClassSniff",
            "$std.SetProperty.NotAllowedViaAttribute"                => "$sniffDir\SetProperty\NotAllowedViaAttributeSniff",
            "$std.SetProperty.PropertyTypeHandling"                  => "$sniffDir\SetProperty\PropertyTypeHandlingSniff",
            "$std.ValidSniffs.RegisterEmptyArray"                    => "$sniffDir\ValidSniffs\RegisterEmptyArraySniff",
        ];

        // Sort the value to make the tests stable as different OSes will read directories
        // in a different order and the order is not relevant for these tests. Just the values.
        $actual = $ruleset->sniffCodes;
        ksort($actual);

        $this->assertSame($expected, $actual);

    }//end testAutoExpandSniffsDirectory()


    /**
     * Verify handling of exclusions of groups of sniffs after inclusion via an even larger "group".
     *
     * @return void
     */
    public function testExcludeSniffGroup()
    {
        // Set up the ruleset.
        $standard = __DIR__.'/ProcessRulesetExcludeSniffGroupTest.xml';
        $config   = new ConfigDouble(["--standard=$standard"]);
        $ruleset  = new Ruleset($config);

        $expected = [
            'PSR1.Classes.ClassDeclaration'    => 'PHP_CodeSniffer\Standards\PSR1\Sniffs\Classes\ClassDeclarationSniff',
            'PSR1.Methods.CamelCapsMethodName' => 'PHP_CodeSniffer\Standards\PSR1\Sniffs\Methods\CamelCapsMethodNameSniff',
        ];

        // Sort the value to make the tests stable as different OSes will read directories
        // in a different order and the order is not relevant for these tests. Just the values.
        $actual = $ruleset->sniffCodes;
        ksort($actual);

        $this->assertSame($expected, $actual);

    }//end testExcludeSniffGroup()


    /*
     * No test for <ini> without "name" as there is nothing we can assert to verify it's being ignored.
     */


    /**
     * Test that an `<ini>` directive without a "value" attribute will be set to the ini equivalent of `true`.
     *
     * @return void
     */
    public function testIniWithoutValue()
    {
        $originalValue = ini_get('user_agent');

        // Set up the ruleset.
        $this->getMiscRuleset();

        $actualValue = ini_get('user_agent');
        // Reset the ini to its original value before the assertion to ensure it's never left in an incorrect state.
        if ($originalValue !== false) {
            ini_set('user_agent', $originalValue);
        }

        $this->assertSame('1', $actualValue);

    }//end testIniWithoutValue()


    /**
     * Verify that inclusion of a single error code:
     * - Includes the sniff, but sets "severity" for the sniff to 0;
     * - Sets "severity" for the specific error code included to 5.;
     *
     * @return void
     */
    public function testIncludeSingleErrorCode()
    {
        // Set up the ruleset.
        $ruleset = $this->getMiscRuleset();

        $key = 'severity';

        $sniffCode = 'Generic.PHP.RequireStrictTypes';
        $this->assertArrayHasKey($sniffCode, $ruleset->ruleset, "Sniff $sniffCode not registered");
        $this->assertTrue(is_array($ruleset->ruleset[$sniffCode]), "Sniff $sniffCode is not an array");
        $this->assertArrayHasKey($key, $ruleset->ruleset[$sniffCode], "Directive $key not registered for sniff $sniffCode");
        $this->assertSame(0, $ruleset->ruleset[$sniffCode][$key], "$key has unexpected value for sniff $sniffCode");

        $sniffCode = 'Generic.PHP.RequireStrictTypes.MissingDeclaration';
        $this->assertArrayHasKey($sniffCode, $ruleset->ruleset, "Sniff $sniffCode not registered");
        $this->assertTrue(is_array($ruleset->ruleset[$sniffCode]), "Sniff $sniffCode is not an array");
        $this->assertArrayHasKey($key, $ruleset->ruleset[$sniffCode], "Directive $key not registered for sniff $sniffCode");
        $this->assertSame(5, $ruleset->ruleset[$sniffCode][$key], "$key has unexpected value for sniff $sniffCode");

    }//end testIncludeSingleErrorCode()


    /**
     * Verify that if all error codes, save one, from a sniff were previously excluded, an include for an additional
     * error code from that same sniff will be respected.
     *
     * @return void
     */
    public function testErrorCodeIncludeAfterExclude()
    {
        // Set up the ruleset.
        $ruleset = $this->getMiscRuleset();

        $key = 'severity';

        $sniffCode = 'PEAR.Files.IncludingFile';
        $this->assertArrayHasKey($sniffCode, $ruleset->ruleset, "Sniff $sniffCode not registered");
        $this->assertTrue(is_array($ruleset->ruleset[$sniffCode]), "Sniff $sniffCode is not an array");
        $this->assertArrayHasKey($key, $ruleset->ruleset[$sniffCode], "Directive $key not registered for sniff $sniffCode");
        $this->assertSame(0, $ruleset->ruleset[$sniffCode][$key], "$key has unexpected value for sniff $sniffCode");

        $sniffCode = 'PEAR.Files.IncludingFile.BracketsNotRequired';
        $this->assertArrayHasKey($sniffCode, $ruleset->ruleset, "Sniff $sniffCode not registered");
        $this->assertTrue(is_array($ruleset->ruleset[$sniffCode]), "Sniff $sniffCode is not an array");
        $this->assertArrayHasKey($key, $ruleset->ruleset[$sniffCode], "Directive $key not registered for sniff $sniffCode");
        $this->assertSame(5, $ruleset->ruleset[$sniffCode][$key], "$key has unexpected value for sniff $sniffCode");

        $sniffCode = 'PEAR.Files.IncludingFile.UseRequire';
        $this->assertArrayHasKey($sniffCode, $ruleset->ruleset, "Sniff $sniffCode not registered");
        $this->assertTrue(is_array($ruleset->ruleset[$sniffCode]), "Sniff $sniffCode is not an array");
        $this->assertArrayHasKey($key, $ruleset->ruleset[$sniffCode], "Directive $key not registered for sniff $sniffCode");
        $this->assertSame(5, $ruleset->ruleset[$sniffCode][$key], "$key has unexpected value for sniff $sniffCode");

    }//end testErrorCodeIncludeAfterExclude()


    /**
     * Verify that a <rule> element without a "ref" is completely ignored.
     *
     * @return void
     */
    public function testRuleWithoutRefIsIgnored()
    {
        // Set up the ruleset.
        $ruleset = $this->getMiscRuleset();

        $sniffCode = 'Generic.Metrics.CyclomaticComplexity';
        $this->assertArrayNotHasKey($sniffCode, $ruleset->sniffCodes, "Sniff $sniffCode registered");
        $this->assertArrayNotHasKey($sniffCode, $ruleset->ruleset, "Sniff $sniffCode adjusted");

    }//end testRuleWithoutRefIsIgnored()


    /**
     * Verify that no "ruleset adjustments" are registered via an `<exclude>` without a "name".
     *
     * @return void
     */
    public function testRuleExcludeWithoutNameIsIgnored()
    {
        // Set up the ruleset.
        $ruleset = $this->getMiscRuleset();

        $sniffCode = 'Generic.PHP.BacktickOperator';
        $this->assertArrayHasKey($sniffCode, $ruleset->sniffCodes, "Sniff $sniffCode not registered");
        $this->assertArrayNotHasKey($sniffCode, $ruleset->ruleset, "Sniff $sniffCode adjusted");

        $sniffCode = 'Generic.PHP.BacktickOperator.Found';
        $this->assertArrayNotHasKey($sniffCode, $ruleset->ruleset, "Sniff $sniffCode adjusted");

    }//end testRuleExcludeWithoutNameIsIgnored()


    /**
     * Test Helper.
     *
     * @return \PHP_CodeSniffer\Ruleset
     */
    private function getMiscRuleset()
    {
        static $ruleset;

        if (isset($ruleset) === false) {
            // Set up the ruleset.
            $standard = __DIR__.'/ProcessRulesetMiscTest.xml';
            $config   = new ConfigDouble(["--standard=$standard"]);
            $ruleset  = new Ruleset($config);
        }

        return $ruleset;

    }//end getMiscRuleset()


}//end class
