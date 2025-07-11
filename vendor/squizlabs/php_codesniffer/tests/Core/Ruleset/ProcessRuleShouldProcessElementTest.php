<?php
/**
 * Test handling of `phpc(cs|cbf)-only` instructions at rule level.
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
 * Test handling of `phpc(cs|cbf)-only` instructions at rule level.
 *
 * @covers \PHP_CodeSniffer\Ruleset::processRule
 * @covers \PHP_CodeSniffer\Ruleset::shouldProcessElement
 */
final class ProcessRuleShouldProcessElementTest extends AbstractRulesetTestCase
{

    /**
     * The Ruleset object.
     *
     * @var \PHP_CodeSniffer\Ruleset
     */
    private static $ruleset;


    /**
     * Initialize the config and ruleset objects for this test.
     *
     * @before
     *
     * @return void
     */
    protected function initializeConfigAndRuleset()
    {
        if (isset(self::$ruleset) === false) {
            // Set up the ruleset.
            $standard      = __DIR__.'/ProcessRuleShouldProcessElementTest.xml';
            $config        = new ConfigDouble(["--standard=$standard"]);
            self::$ruleset = new Ruleset($config);
        }

    }//end initializeConfigAndRuleset()


    /**
     * Verify that in CS mode, phpcs-only <severity> directives are set and phpcbf-only <severity>
     * directives are ignored.
     *
     * @return void
     */
    public function testShouldProcessSeverityCsonly()
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $this->markTestSkipped('This test needs CS mode to run');
        }

        $key = 'severity';

        // Verify that the non-selective severity directive IS applied.
        $sniffCode = 'Internal.NoCodeFound';
        $this->assertRulesetPropertySame(0, $sniffCode, $key);

        $sniffCode = 'PSR1.Files.SideEffects';
        $this->assertRulesetPropertySame(3, $sniffCode, $key);

        // Verify that the CS-only severity directive IS applied.
        $sniffCode = 'Generic.Metrics.CyclomaticComplexity';
        $this->assertRulesetPropertySame(2, $sniffCode, $key);

        // Verify that the CBF-only severity directive is NOT applied.
        $sniffCode = 'PSR2.Namespaces.NamespaceDeclaration';
        $this->assertNotHasRulesetDirective($sniffCode, $key);

    }//end testShouldProcessSeverityCsonly()


    /**
     * Verify that in CBF mode, phpcbf-only <severity> directives are set and phpcs-only <severity>
     * directives are ignored.
     *
     * @group CBF
     *
     * @return void
     */
    public function testShouldProcessSeverityCbfonly()
    {
        if (PHP_CODESNIFFER_CBF === false) {
            $this->markTestSkipped('This test needs CBF mode to run');
        }

        $key = 'severity';

        // Verify that the non-selective severity directive IS applied.
        $sniffCode = 'PSR1.Files.SideEffects';
        $this->assertRulesetPropertySame(3, $sniffCode, $key);

        // Verify that the CS-only severity directive is NOT applied.
        $sniffCode = 'Generic.Metrics.CyclomaticComplexity';
        $this->assertNotHasRulesetDirective($sniffCode, $key);

        // Verify that the CBF-only severity directive IS applied.
        $sniffCode = 'PSR2.Namespaces.NamespaceDeclaration';
        $this->assertRulesetPropertySame(4, $sniffCode, $key);

    }//end testShouldProcessSeverityCbfonly()


    /**
     * Verify that in CS mode, phpcs-only <type> directives are set and phpcbf-only <type>
     * directives are ignored.
     *
     * @return void
     */
    public function testShouldProcessTypeCsonly()
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $this->markTestSkipped('This test needs CS mode to run');
        }

        $key = 'type';

        // Verify that the non-selective type directive IS applied.
        $sniffCode = 'PSR1.Files.SideEffects';
        $this->assertRulesetPropertySame('warning', $sniffCode, $key);

        // Verify that the CS-only type directive IS applied.
        $sniffCode = 'Generic.Metrics.CyclomaticComplexity';
        $this->assertRulesetPropertySame('warning', $sniffCode, $key);

        // Verify that the CBF-only type directive is NOT applied.
        $sniffCode = 'PSR2.Namespaces.NamespaceDeclaration';
        $this->assertNotHasRulesetDirective($sniffCode, $key);

    }//end testShouldProcessTypeCsonly()


    /**
     * Verify that in CBF mode, phpcbf-only <type> directives are set and phpcs-only <type>
     * directives are ignored.
     *
     * @group CBF
     *
     * @return void
     */
    public function testShouldProcessTypeCbfonly()
    {
        if (PHP_CODESNIFFER_CBF === false) {
            $this->markTestSkipped('This test needs CBF mode to run');
        }

        $key = 'type';

        // Verify that the non-selective type directive IS applied.
        $sniffCode = 'PSR1.Files.SideEffects';
        $this->assertRulesetPropertySame('warning', $sniffCode, $key);

        // Verify that the CS-only type directive is NOT applied.
        $sniffCode = 'Generic.Metrics.CyclomaticComplexity';
        $this->assertNotHasRulesetDirective($sniffCode, $key);

        // Verify that the CBF-only type directive IS applied.
        $sniffCode = 'PSR2.Namespaces.NamespaceDeclaration';
        $this->assertRulesetPropertySame('error', $sniffCode, $key);

    }//end testShouldProcessTypeCbfonly()


    /**
     * Verify that in CS mode, phpcs-only <message> directives are set and phpcbf-only <message>
     * directives are ignored.
     *
     * @return void
     */
    public function testShouldProcessMessageCsonly()
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $this->markTestSkipped('This test needs CS mode to run');
        }

        $key = 'message';

        // Verify that the non-selective message directive IS applied.
        $sniffCode = 'PSR1.Files.SideEffects';
        $this->assertRulesetPropertySame('A different warning message', $sniffCode, $key);

        // Verify that the CS-only message directive IS applied.
        $sniffCode = 'Generic.Metrics.CyclomaticComplexity';
        $this->assertRulesetPropertySame('A different warning but only for phpcs', $sniffCode, $key);

        // Verify that the CBF-only message directive is NOT applied.
        $sniffCode = 'PSR2.Namespaces.NamespaceDeclaration';
        $this->assertNotHasRulesetDirective($sniffCode, $key);

    }//end testShouldProcessMessageCsonly()


    /**
     * Verify that in CBF mode, phpcbf-only <message> directives are set and phpcs-only <message>
     * directives are ignored.
     *
     * @group CBF
     *
     * @return void
     */
    public function testShouldProcessMessageCbfonly()
    {
        if (PHP_CODESNIFFER_CBF === false) {
            $this->markTestSkipped('This test needs CBF mode to run');
        }

        $key = 'message';

        // Verify that the non-selective message directive IS applied.
        $sniffCode = 'Internal.NoCodeFound';
        $this->assertRulesetPropertySame("We don't to be notified if files don't contain code", $sniffCode, $key);

        $sniffCode = 'PSR1.Files.SideEffects';
        $this->assertRulesetPropertySame('A different warning message', $sniffCode, $key);

        // Verify that the CS-only message directive is NOT applied.
        $sniffCode = 'Generic.Metrics.CyclomaticComplexity';
        $this->assertNotHasRulesetDirective($sniffCode, $key);

        // Verify that the CBF-only message directive IS applied.
        $sniffCode = 'PSR2.Namespaces.NamespaceDeclaration';
        $this->assertRulesetPropertySame('A different warning but only for phpcbf', $sniffCode, $key);

    }//end testShouldProcessMessageCbfonly()


    /**
     * Verify that in CS mode, phpcs-only <include-pattern> directives are set and phpcbf-only <include-pattern>
     * directives are ignored.
     *
     * @return void
     */
    public function testShouldProcessIncludePatternCsonly()
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $this->markTestSkipped('This test needs CS mode to run');
        }

        $includedKey = './vendor/';

        // Verify that the non-selective include-pattern directive IS applied.
        $sniffCode = 'PSR1.Methods.CamelCapsMethodName';
        $this->assertArrayHasKey($sniffCode, self::$ruleset->includePatterns, "Sniff $sniffCode not registered");
        $this->assertArrayHasKey($includedKey, self::$ruleset->includePatterns[$sniffCode], "Include pattern for sniff $sniffCode not registered");

        // Verify that the CS-only include-pattern directive IS applied.
        $sniffCode = 'Generic.Files.LineLength';
        $this->assertArrayHasKey($sniffCode, self::$ruleset->includePatterns, "Sniff $sniffCode not registered");
        $this->assertArrayHasKey($includedKey, self::$ruleset->includePatterns[$sniffCode], "Include pattern for sniff $sniffCode not registered");

        // Verify that the CBF-only include-pattern directive is NOT applied.
        $sniffCode = 'PSR2.Files.ClosingTag';
        $this->assertArrayNotHasKey($sniffCode, self::$ruleset->includePatterns, "Sniff $sniffCode was registered");

    }//end testShouldProcessIncludePatternCsonly()


    /**
     * Verify that in CS mode, phpcbf-only <include-pattern> directives are set and phpcs-only <include-pattern>
     * directives are ignored.
     *
     * @group CBF
     *
     * @return void
     */
    public function testShouldProcessIncludePatternCbfonly()
    {
        if (PHP_CODESNIFFER_CBF === false) {
            $this->markTestSkipped('This test needs CBF mode to run');
        }

        $includedKey = './vendor/';

        // Verify that the non-selective include-pattern directive IS applied.
        $sniffCode = 'PSR1.Methods.CamelCapsMethodName';
        $this->assertArrayHasKey($sniffCode, self::$ruleset->includePatterns, "Sniff $sniffCode not registered");
        $this->assertArrayHasKey($includedKey, self::$ruleset->includePatterns[$sniffCode], "Include pattern for sniff $sniffCode not registered");

        // Verify that the CS-only include-pattern directive is NOT applied.
        $sniffCode = 'Generic.Files.LineLength';
        $this->assertArrayNotHasKey($sniffCode, self::$ruleset->includePatterns, "Sniff $sniffCode was registered");

        // Verify that the CBF-only include-pattern directive is IS applied.
        $sniffCode = 'PSR2.Files.ClosingTag';
        $this->assertArrayHasKey($sniffCode, self::$ruleset->includePatterns, "Sniff $sniffCode not registered");
        $this->assertArrayHasKey($includedKey, self::$ruleset->includePatterns[$sniffCode], "Include pattern for sniff $sniffCode not registered");

    }//end testShouldProcessIncludePatternCbfonly()


    /**
     * Verify that in CS mode, phpcs-only <exclude-pattern> directives are set and phpcbf-only <exclude-pattern>
     * directives are ignored.
     *
     * @return void
     */
    public function testShouldProcessExcludePatternCsonly()
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $this->markTestSkipped('This test needs CS mode to run');
        }

        $excludedKey = './tests/';

        // Verify that the non-selective exclude-pattern directive IS applied.
        $sniffCode = 'PSR1.Classes.ClassDeclaration';
        $this->assertArrayHasKey($sniffCode, self::$ruleset->ignorePatterns, "Sniff $sniffCode not registered");
        $this->assertArrayHasKey($excludedKey, self::$ruleset->ignorePatterns[$sniffCode], "Ignore pattern for sniff $sniffCode not registered");

        // Verify that the CS-only exclude-pattern directive IS applied.
        $sniffCode = 'Generic.Formatting.SpaceAfterCast';
        $this->assertArrayHasKey($sniffCode, self::$ruleset->ignorePatterns, "Sniff $sniffCode not registered");
        $this->assertArrayHasKey($excludedKey, self::$ruleset->ignorePatterns[$sniffCode], "Ignore pattern for sniff $sniffCode not registered");

        // Verify that the CBF-only exclude-pattern directive is NOT applied.
        $sniffCode = 'PSR2.Methods.FunctionClosingBrace';
        $this->assertArrayNotHasKey($sniffCode, self::$ruleset->ignorePatterns, "Sniff $sniffCode was registered");

    }//end testShouldProcessExcludePatternCsonly()


    /**
     * Verify that in CS mode, phpcbf-only <exclude-pattern> directives are set and phpcs-only <exclude-pattern>
     * directives are ignored.
     *
     * @group CBF
     *
     * @return void
     */
    public function testShouldProcessExcludePatternCbfonly()
    {
        if (PHP_CODESNIFFER_CBF === false) {
            $this->markTestSkipped('This test needs CBF mode to run');
        }

        $excludedKey = './tests/';

        // Verify that the non-selective exclude-pattern directive IS applied.
        $sniffCode = 'PSR1.Classes.ClassDeclaration';
        $this->assertArrayHasKey($sniffCode, self::$ruleset->ignorePatterns, "Sniff $sniffCode not registered");
        $this->assertArrayHasKey($excludedKey, self::$ruleset->ignorePatterns[$sniffCode], "Ignore pattern for sniff $sniffCode not registered");

        // Verify that the CS-only exclude-pattern directive is NOT applied.
        $sniffCode = 'Generic.Formatting.SpaceAfterCast';
        $this->assertArrayNotHasKey($sniffCode, self::$ruleset->ignorePatterns, "Sniff $sniffCode was registered");

        // Verify that the CBF-only exclude-pattern directive is IS applied.
        $sniffCode = 'PSR2.Methods.FunctionClosingBrace';
        $this->assertArrayHasKey($sniffCode, self::$ruleset->ignorePatterns, "Sniff $sniffCode not registered");
        $this->assertArrayHasKey($excludedKey, self::$ruleset->ignorePatterns[$sniffCode], "Ignore pattern for sniff $sniffCode not registered");

    }//end testShouldProcessExcludePatternCbfonly()


    /**
     * Verify that in CS mode, phpcs-only <properties> directives are set and phpcbf-only <properties>
     * directives are ignored.
     *
     * @return void
     */
    public function testShouldProcessPropertiesCsonly()
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $this->markTestSkipped('This test needs CS mode to run');
        }

        $csSniffClass  = 'PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\ArrayIndentSniff';
        $cbfSniffClass = 'PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff';

        $propertyName    = 'indent';
        $propertyDefault = 4;
        $propertyChanged = '2';

        // Verify that the CS-only property directive IS applied.
        $this->assertArrayHasKey($csSniffClass, self::$ruleset->sniffs, "Sniff $csSniffClass not registered");
        $this->assertXObjectHasProperty($propertyName, self::$ruleset->sniffs[$csSniffClass]);

        $actualValue = self::$ruleset->sniffs[$csSniffClass]->$propertyName;
        $this->assertSame($propertyChanged, $actualValue, 'cs-only property change directive not applied');

        // Verify that the CBF-only property directive is NOT applied.
        $this->assertArrayHasKey($cbfSniffClass, self::$ruleset->sniffs, "Sniff $cbfSniffClass not registered");
        $this->assertXObjectHasProperty($propertyName, self::$ruleset->sniffs[$cbfSniffClass]);

        $actualValue = self::$ruleset->sniffs[$cbfSniffClass]->$propertyName;
        $this->assertSame($propertyDefault, $actualValue, 'cbf-only property change directive was applied');

    }//end testShouldProcessPropertiesCsonly()


    /**
     * Verify that in CBF mode, phpcbf-only <properties> directives are set and phpcs-only <properties>
     * directives are ignored.
     *
     * @group CBF
     *
     * @return void
     */
    public function testShouldProcessPropertiesCbfonly()
    {
        if (PHP_CODESNIFFER_CBF === false) {
            $this->markTestSkipped('This test needs CBF mode to run');
        }

        $csSniffClass  = 'PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\ArrayIndentSniff';
        $cbfSniffClass = 'PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff';

        $propertyName    = 'indent';
        $propertyDefault = 4;
        $propertyChanged = '2';

        // Verify that the CS-only property directive is NOT applied.
        $this->assertArrayHasKey($csSniffClass, self::$ruleset->sniffs, "Sniff $csSniffClass not registered");
        $this->assertXObjectHasProperty($propertyName, self::$ruleset->sniffs[$csSniffClass]);

        $actualValue = self::$ruleset->sniffs[$csSniffClass]->$propertyName;
        $this->assertSame($propertyDefault, $actualValue, 'cs-only property change directive was applied');

        // Verify that the CBF-only property directive IS applied.
        $this->assertArrayHasKey($cbfSniffClass, self::$ruleset->sniffs, "Sniff $cbfSniffClass not registered");
        $this->assertXObjectHasProperty($propertyName, self::$ruleset->sniffs[$cbfSniffClass]);

        $actualValue = self::$ruleset->sniffs[$cbfSniffClass]->$propertyName;
        $this->assertSame($propertyChanged, $actualValue, 'cbf-only property change directive not applied');

    }//end testShouldProcessPropertiesCbfonly()


    /**
     * Verify that in CS mode, phpcs-only <property> directives are set and phpcbf-only <property>
     * directives are ignored.
     *
     * @return void
     */
    public function testShouldProcessPropertyCsonly()
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $this->markTestSkipped('This test needs CS mode to run');
        }

        // Verify the sniff is registed.
        $sniffClass = 'PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\ScopeIndentSniff';
        $this->assertArrayHasKey($sniffClass, self::$ruleset->sniffs, "Sniff $sniffClass not registered");

        $sniffObject = self::$ruleset->sniffs[$sniffClass];

        // Verify that the non-selective property directive IS applied.
        $propertyName = 'exact';
        $expected     = true;

        $this->assertXObjectHasProperty($propertyName, $sniffObject);
        $this->assertSame($expected, $sniffObject->$propertyName, 'Non-selective property change directive not applied');

        // Verify that the CS-only property directive IS applied.
        $propertyName = 'indent';
        $expected     = '2';

        $this->assertXObjectHasProperty($propertyName, $sniffObject);
        $this->assertSame($expected, $sniffObject->$propertyName, 'cs-only property change directive not applied');

        // Verify that the CBF-only property directive is NOT applied.
        $propertyName    = 'tabIndent';
        $expectedDefault = false;

        $this->assertXObjectHasProperty($propertyName, $sniffObject);
        $this->assertSame($expectedDefault, $sniffObject->$propertyName, 'cbf-only property change directive was applied');

    }//end testShouldProcessPropertyCsonly()


    /**
     * Verify that in CBF mode, phpcbf-only <property> directives are set and phpcs-only <property>
     * directives are ignored.
     *
     * @group CBF
     *
     * @return void
     */
    public function testShouldProcessPropertyCbfonly()
    {
        if (PHP_CODESNIFFER_CBF === false) {
            $this->markTestSkipped('This test needs CBF mode to run');
        }

        // Verify the sniff is registed.
        $sniffClass = 'PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\ScopeIndentSniff';
        $this->assertArrayHasKey($sniffClass, self::$ruleset->sniffs, "Sniff $sniffClass not registered");

        $sniffObject = self::$ruleset->sniffs[$sniffClass];

        // Verify that the non-selective property directive IS applied.
        $propertyName = 'exact';
        $expected     = true;

        $this->assertXObjectHasProperty($propertyName, $sniffObject);
        $this->assertSame($expected, $sniffObject->$propertyName, 'Non-selective property change directive not applied');

        // Verify that the CS-only property directive is NOT applied.
        $propertyName    = 'indent';
        $expectedDefault = 4;

        $this->assertXObjectHasProperty($propertyName, $sniffObject);
        $this->assertSame($expectedDefault, $sniffObject->$propertyName, 'cs-only property change directive was applied');

        // Verify that the CBF-only property directive IS applied.
        $propertyName = 'tabIndent';
        $expected     = true;

        $this->assertXObjectHasProperty($propertyName, $sniffObject);
        $this->assertSame($expected, $sniffObject->$propertyName, 'cbf-only property change directive not applied');

    }//end testShouldProcessPropertyCbfonly()


    /**
     * Verify that in CS mode, phpcs-only <element> directives are set and phpcbf-only <element>
     * directives are ignored.
     *
     * @return void
     */
    public function testShouldProcessElementCsonly()
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $this->markTestSkipped('This test needs CS mode to run');
        }

        $expected = [
            // Non-selective element directive.
            'T_COMMENT',
            // Phpcs-only element directive.
            'T_CLASS',
            // Non-selective element directive via `extend`.
            'T_BACKTICK',
            // Phpcs-only element directive via `extend`.
            'T_INTERFACE',
        ];

        $this->verifyShouldProcessElement($expected);

    }//end testShouldProcessElementCsonly()


    /**
     * Verify that in CBF mode, phpcbf-only <element> directives are set and phpcs-only <element>
     * directives are ignored.
     *
     * @group CBF
     *
     * @return void
     */
    public function testShouldProcessElementCbfonly()
    {
        if (PHP_CODESNIFFER_CBF === false) {
            $this->markTestSkipped('This test needs CBF mode to run');
        }

        $expected = [
            // Non-selective element directive.
            'T_COMMENT',
            // Phpcbf-only element directive.
            'T_ENUM',
            // Non-selective element directive via `extend`.
            'T_BACKTICK',
            // Phpcbf-only element directive via `extend`.
            'T_TRAIT',
        ];

        $this->verifyShouldProcessElement($expected);

    }//end testShouldProcessElementCbfonly()


    /**
     * Verify that <element> directives are set correctly.
     *
     * @param array<string> $expected Expected sniff property value.
     *
     * @return void
     */
    private function verifyShouldProcessElement($expected)
    {
        // Verify the sniff is registed.
        $sniffClass = 'PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\ScopeIndentSniff';
        $this->assertArrayHasKey($sniffClass, self::$ruleset->sniffs, "Sniff $sniffClass not registered");

        // Verify the target property exists.
        $sniffObject  = self::$ruleset->sniffs[$sniffClass];
        $propertyName = 'ignoreIndentationTokens';

        $this->assertXObjectHasProperty($propertyName, $sniffObject);

        // Verify the value.
        $actualValue = $sniffObject->$propertyName;
        $this->assertSame($expected, $actualValue, 'Selective element directives not applied correctly');

    }//end verifyShouldProcessElement()


    /**
     * Custom assertion to verify that a Ruleset `$ruleset` property has a certain directive set for a certain sniff code.
     *
     * @param string $sniffCode Sniff code.
     * @param string $key       Array key.
     *
     * @return void
     */
    private function assertHasRulesetDirective($sniffCode, $key)
    {
        $this->assertArrayHasKey($sniffCode, self::$ruleset->ruleset, "Sniff $sniffCode not registered");
        $this->assertTrue(is_array(self::$ruleset->ruleset[$sniffCode]), "Sniff $sniffCode is not an array");
        $this->assertArrayHasKey($key, self::$ruleset->ruleset[$sniffCode], "Directive $key not registered for sniff $sniffCode");

    }//end assertHasRulesetDirective()


    /**
     * Custom assertion to verify that a Ruleset `$ruleset` property does NOT have a certain directive set for a certain sniff code.
     *
     * @param string $sniffCode Sniff code.
     * @param string $key       Array key.
     *
     * @return void
     */
    private function assertNotHasRulesetDirective($sniffCode, $key)
    {
        if (isset(self::$ruleset->ruleset[$sniffCode]) === true
            && is_array(self::$ruleset->ruleset[$sniffCode]) === true
            && isset(self::$ruleset->ruleset[$sniffCode][$key]) === true
        ) {
            $this->fail("Directive $key is registered for sniff $sniffCode");
        }

    }//end assertNotHasRulesetDirective()


    /**
     * Custom assertion to verify that the value of a certain directive for a certain sniff code on the ruleset is correct.
     *
     * @param mixed  $expected  Expected value.
     * @param string $sniffCode Sniff code.
     * @param string $key       Array key.
     *
     * @return void
     */
    private function assertRulesetPropertySame($expected, $sniffCode, $key)
    {
        $this->assertHasRulesetDirective($sniffCode, $key);

        $actual = self::$ruleset->ruleset[$sniffCode][$key];
        $this->assertSame($expected, $actual, "Value for $key on sniff $sniffCode does not meet expectations");

    }//end assertRulesetPropertySame()


}//end class
