<?php
/**
 * Test the Ruleset::populateTokenListeners() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Ruleset;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHP_CodeSniffer\Tests\Core\Ruleset\AbstractRulesetTestCase;
use PHP_CodeSniffer\Util\Tokens;
use ReflectionObject;
use ReflectionProperty;

/**
 * Test the Ruleset::populateTokenListeners() method.
 *
 * @covers \PHP_CodeSniffer\Ruleset::populateTokenListeners
 */
final class PopulateTokenListenersTest extends AbstractRulesetTestCase
{

    /**
     * The Ruleset object.
     *
     * @var \PHP_CodeSniffer\Ruleset
     */
    private static $ruleset;


    /**
     * Initialize the config and ruleset objects for this test only once (but do allow recording code coverage).
     *
     * @before
     *
     * @return void
     */
    protected function initializeConfigAndRuleset()
    {
        if (isset(self::$ruleset) === false) {
            // Set up the ruleset.
            $standard      = __DIR__.'/PopulateTokenListenersTest.xml';
            $config        = new ConfigDouble(["--standard=$standard"]);
            self::$ruleset = new Ruleset($config);
        }

    }//end initializeConfigAndRuleset()


    /**
     * Test an exception is thrown when the register() method of a sniff doesn't return an array.
     *
     * @return void
     */
    public function testSniffWhereRegisterDoesNotReturnAnArrayThrowsException()
    {
        $standard = __DIR__.'/PopulateTokenListenersRegisterNoArrayTest.xml';
        $config   = new ConfigDouble(["--standard=$standard"]);

        $sniffClass = 'Fixtures\\TestStandard\\Sniffs\\InvalidSniffs\\RegisterNoArraySniff';
        $message    = "ERROR: The sniff {$sniffClass}::register() method must return an array.".PHP_EOL.PHP_EOL;
        $this->expectRuntimeExceptionMessage($message);

        new Ruleset($config);

        // Verify that the sniff has not been registered/has been unregistered.
        // These assertions will only take effect for PHPUnit 10+.
        $this->assertArrayNotHasKey($sniffClass, self::$ruleset->sniffs, "Sniff class $sniffClass is listed in registered sniffs");
        $this->assertArrayNotHasKey('TestStandard.InvalidSniffs.RegisterNoArray', self::$ruleset->sniffCodes, 'Sniff code is registered');

    }//end testSniffWhereRegisterDoesNotReturnAnArrayThrowsException()


    /**
     * Test that a sniff not registering any tokens is not listed as a listener.
     *
     * @return void
     */
    public function testSniffWithRegisterMethodReturningEmptyArrayIsSilentlyIgnored()
    {
        $target = 'Fixtures\\TestStandard\\Sniffs\\ValidSniffs\\RegisterEmptyArraySniff';

        foreach (self::$ruleset->tokenListeners as $token => $listeners) {
            $this->assertTrue(is_array($listeners), 'No listeners registered for token'.Tokens::tokenName($token));
            $this->assertArrayNotHasKey(
                $target,
                $listeners,
                sprintf('Found the %s sniff registered for token %s', $target, Tokens::tokenName($token))
            );
        }

    }//end testSniffWithRegisterMethodReturningEmptyArrayIsSilentlyIgnored()


    /**
     * Tests that sniffs registering tokens, will end up listening to these tokens.
     *
     * @param string $sniffClass    The FQN for the sniff class to check.
     * @param int    $expectedCount Expected number of tokens to which the sniff should be listening.
     *
     * @dataProvider dataSniffListensToTokenss
     *
     * @return void
     */
    public function testRegistersSniffsToListenToTokens($sniffClass, $expectedCount)
    {
        $counter = 0;

        foreach (self::$ruleset->tokenListeners as $listeners) {
            if (isset($listeners[$sniffClass]) === true) {
                ++$counter;
            }
        }

        $this->assertSame($expectedCount, $counter);

    }//end testRegistersSniffsToListenToTokens()


    /**
     * Data provider.
     *
     * @see testSniffListensToTokens()
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataSniffListensToTokenss()
    {
        return [
            'TestStandard.SupportedTokenizers.ListensForPHPAndCSSAndJS' => [
                'sniffClass'    => 'Fixtures\\TestStandard\\Sniffs\\SupportedTokenizers\\ListensForPHPAndCSSAndJSSniff',
                'expectedCount' => 2,
            ],
            'Generic.NamingConventions.UpperCaseConstantName'           => [
                'sniffClass'    => 'PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\NamingConventions\\UpperCaseConstantNameSniff',
                'expectedCount' => 2,
            ],
            'PSR1.Files.SideEffects'                                    => [
                'sniffClass'    => 'PHP_CodeSniffer\\Standards\\PSR1\\Sniffs\\Files\\SideEffectsSniff',
                'expectedCount' => 1,
            ],
            'PSR12.ControlStructures.BooleanOperatorPlacement'          => [
                'sniffClass'    => 'PHP_CodeSniffer\\Standards\\PSR12\\Sniffs\\ControlStructures\\BooleanOperatorPlacementSniff',
                'expectedCount' => 5,
            ],
            'Squiz.ControlStructures.ForEachLoopDeclaration'            => [
                'sniffClass'    => 'PHP_CodeSniffer\\Standards\\Squiz\\Sniffs\\ControlStructures\\ForEachLoopDeclarationSniff',
                'expectedCount' => 1,
            ],
            'TestStandard.Deprecated.WithReplacement'                   => [
                'sniffClass'    => 'Fixtures\\TestStandard\\Sniffs\\Deprecated\\WithReplacementSniff',
                'expectedCount' => 1,
            ],
            'TestStandard.ValidSniffs.RegisterEmptyArray'               => [
                'sniffClass'    => 'Fixtures\\TestStandard\\Sniffs\\ValidSniffs\\RegisterEmptyArraySniff',
                'expectedCount' => 0,
            ],
        ];

    }//end dataSniffListensToTokenss()


    /**
     * Test that deprecated sniffs get recognized and added to the $deprecatedSniffs list.
     *
     * @return void
     */
    public function testRegistersWhenADeprecatedSniffIsLoaded()
    {
        $property = new ReflectionProperty(self::$ruleset, 'deprecatedSniffs');
        $property->setAccessible(true);
        $actualValue = $property->getValue(self::$ruleset);
        $property->setAccessible(false);

        // Only verify there is one deprecated sniff registered.
        // There are other tests which test the deprecated sniff handling in more detail.
        $this->assertTrue(is_array($actualValue));
        $this->assertCount(1, $actualValue);

    }//end testRegistersWhenADeprecatedSniffIsLoaded()


    /**
     * Verify that the setting of properties on a sniff was not triggered when there are no properties being set.
     *
     * @return void
     */
    public function testDoesntTriggerPropertySettingForNoProperties()
    {
        $sniffClass = 'PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\NamingConventions\\UpperCaseConstantNameSniff';

        // Verify that our target sniff has been registered.
        $this->assertArrayHasKey($sniffClass, self::$ruleset->sniffs, "Sniff class $sniffClass not listed in registered sniffs");

        $sniffObject = self::$ruleset->sniffs[$sniffClass];
        $reflection  = new ReflectionObject($sniffObject);

        // Just making sure there are no properties on the sniff object (which doesn't have declared properties).
        $this->assertSame([], $reflection->getProperties(), "Unexpected properties found on sniff class $sniffClass");

    }//end testDoesntTriggerPropertySettingForNoProperties()


    /**
     * Verify that the setting of properties on a sniff was triggered.
     *
     * @param string $sniffClass   The FQN for the sniff class on which the property should be set.
     * @param string $propertyName The property name.
     * @param string $expected     The expected property value.
     *
     * @dataProvider dataTriggersPropertySettingWhenPropertiesProvided
     *
     * @return void
     */
    public function testTriggersPropertySettingWhenPropertiesProvided($sniffClass, $propertyName, $expected)
    {
        // Verify that our target sniff has been registered.
        $this->assertArrayHasKey($sniffClass, self::$ruleset->sniffs, "Sniff class $sniffClass not listed in registered sniffs");

        $sniffObject = self::$ruleset->sniffs[$sniffClass];

        // Verify the property has been set.
        $this->assertSame($expected, $sniffObject->$propertyName, "Property on sniff class $sniffClass set to unexpected value");

    }//end testTriggersPropertySettingWhenPropertiesProvided()


    /**
     * Data provider.
     *
     * @see testTriggersPropertySettingWhenPropertiesProvided()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataTriggersPropertySettingWhenPropertiesProvided()
    {
        return [
            'Sniff with single property being set'                       => [
                'sniffClass'   => 'PHP_CodeSniffer\\Standards\\PSR12\\Sniffs\\ControlStructures\\BooleanOperatorPlacementSniff',
                'propertyName' => 'allowOnly',
                'expected'     => 'first',
            ],
            'Sniff with multiple properties being set - first property'  => [
                'sniffClass'   => 'PHP_CodeSniffer\\Standards\\Squiz\\Sniffs\\ControlStructures\\ForEachLoopDeclarationSniff',
                'propertyName' => 'requiredSpacesAfterOpen',
                'expected'     => '3',
            ],
            'Sniff with multiple properties being set - second property' => [
                'sniffClass'   => 'PHP_CodeSniffer\\Standards\\Squiz\\Sniffs\\ControlStructures\\ForEachLoopDeclarationSniff',
                'propertyName' => 'requiredSpacesBeforeClose',
                'expected'     => '8',
            ],
        ];

    }//end dataTriggersPropertySettingWhenPropertiesProvided()


    /**
     * Verifies that the "class" and "source" indexes get set.
     *
     * @return void
     */
    public function testSetsClassAndSourceIndexes()
    {
        foreach (self::$ruleset->tokenListeners as $token => $listeners) {
            $this->assertTrue(is_array($listeners), 'No listeners registered for token'.Tokens::tokenName($token));

            foreach ($listeners as $className => $details) {
                $this->assertArrayHasKey(
                    'class',
                    $details,
                    sprintf('"tokenizers" key missing for sniff class %s for token %s', $className, Tokens::tokenName($token))
                );

                $this->assertSame(
                    $className,
                    $details['class'],
                    sprintf('Unexpected value for "class" key for sniff class %s for token %s', $className, Tokens::tokenName($token))
                );

                $this->assertArrayHasKey(
                    'source',
                    $details,
                    sprintf('"source" key missing for sniff class %s for token %s', $className, Tokens::tokenName($token))
                );

                $this->assertTrue(
                    is_string($details['source']),
                    sprintf('Value for "source" key is not a string for token %s', Tokens::tokenName($token))
                );

                $expected = '.'.substr($className, (strrpos($className, '\\') + 1), -5);

                $this->assertStringEndsWith(
                    $expected,
                    $details['source'],
                    sprintf('Unexpected value for "source" key for sniff class %s for token %s', $className, Tokens::tokenName($token))
                );
            }//end foreach
        }//end foreach

    }//end testSetsClassAndSourceIndexes()


    /**
     * Verifies that sniffs by default are listening for PHP files only.
     *
     * @return void
     */
    public function testSetsSupportedTokenizersToPHPByDefault()
    {
        $exclude  = 'Fixtures\\TestStandard\\Sniffs\\SupportedTokenizers\\ListensForPHPAndCSSAndJSSniff';
        $expected = ['PHP' => 'PHP'];

        foreach (self::$ruleset->tokenListeners as $token => $listeners) {
            $this->assertTrue(is_array($listeners), 'No listeners registered for token'.Tokens::tokenName($token));

            foreach ($listeners as $className => $details) {
                if ($className === $exclude) {
                    // Skip this one as it is the one sniff for which things will be different.
                    continue;
                }

                $this->assertArrayHasKey(
                    'tokenizers',
                    $details,
                    sprintf('"tokenizers" key missing for sniff class %s for token %s', $className, Tokens::tokenName($token))
                );

                $this->assertSame(
                    $expected,
                    $details['tokenizers'],
                    sprintf('Unexpected value for "tokenizers" key for sniff class %s for token %s', $className, Tokens::tokenName($token))
                );
            }
        }//end foreach

    }//end testSetsSupportedTokenizersToPHPByDefault()


    /**
     * Test that if a sniff has the $supportedTokenizers property set, the tokenizers listed there
     * will be registered in the listeners array.
     *
     * @param int $token The token constant for which the sniff should be registered.
     *
     * @dataProvider dataSetsSupportedTokenizersWhenProvidedBySniff
     *
     * @return void
     */
    public function testSetsSupportedTokenizersWhenProvidedBySniff($token)
    {
        $sniffClass = 'Fixtures\\TestStandard\\Sniffs\\SupportedTokenizers\\ListensForPHPAndCSSAndJSSniff';
        $expected   = [
            'PHP' => 'PHP',
            'JS'  => 'JS',
            'CSS' => 'CSS',
        ];

        $this->assertArrayHasKey(
            $token,
            self::$ruleset->tokenListeners,
            sprintf('The token constant %s is not registered to the listeners array', Tokens::tokenName($token))
        );
        $this->assertArrayHasKey(
            $sniffClass,
            self::$ruleset->tokenListeners[$token],
            sprintf('The sniff class %s is not registered for token %s', $sniffClass, Tokens::tokenName($token))
        );
        $this->assertArrayHasKey(
            'tokenizers',
            self::$ruleset->tokenListeners[$token][$sniffClass],
            sprintf('"tokenizers" key missing for sniff class %s for token %s', $sniffClass, Tokens::tokenName($token))
        );

        $this->assertSame(
            $expected,
            self::$ruleset->tokenListeners[$token][$sniffClass]['tokenizers'],
            sprintf('Unexpected value for "tokenizers" key for sniff class %s for token %s', $sniffClass, Tokens::tokenName($token))
        );

    }//end testSetsSupportedTokenizersWhenProvidedBySniff()


    /**
     * Data provider.
     *
     * @see testSetsSupportedTokenizersWhenProvidedBySniff()
     *
     * @return array<string, array<int>>
     */
    public static function dataSetsSupportedTokenizersWhenProvidedBySniff()
    {
        return [
            'T_OPEN_TAG'           => [T_OPEN_TAG],
            'T_OPEN_TAG_WITH_ECHO' => [T_OPEN_TAG_WITH_ECHO],
        ];

    }//end dataSetsSupportedTokenizersWhenProvidedBySniff()


    /**
     * Verifies that by default no explicit include patterns are registered for sniffs.
     *
     * @return void
     */
    public function testSetsIncludePatternsToEmptyArrayByDefault()
    {
        $exclude = 'PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\NamingConventions\\UpperCaseConstantNameSniff';

        foreach (self::$ruleset->tokenListeners as $token => $listeners) {
            $this->assertTrue(is_array($listeners), 'No listeners registered for token'.Tokens::tokenName($token));

            foreach ($listeners as $className => $details) {
                if ($className === $exclude) {
                    // Skip this one as it is the one sniff for which things will be different.
                    continue;
                }

                $this->assertArrayHasKey(
                    'include',
                    $details,
                    sprintf('"include" key missing for sniff class %s for token %s', $className, Tokens::tokenName($token))
                );

                $this->assertSame(
                    [],
                    $details['include'],
                    sprintf('Unexpected value for "include" key for sniff class %s for token %s', $className, Tokens::tokenName($token))
                );
            }
        }//end foreach

    }//end testSetsIncludePatternsToEmptyArrayByDefault()


    /**
     * Verifies that by default no explicit ignore patterns are registered for sniffs.
     *
     * @return void
     */
    public function testSetsIgnorePatternsToEmptyArrayByDefault()
    {
        $exclude = 'PHP_CodeSniffer\\Standards\\PSR1\\Sniffs\\Files\\SideEffectsSniff';

        foreach (self::$ruleset->tokenListeners as $token => $listeners) {
            $this->assertTrue(is_array($listeners), 'No listeners registered for token'.Tokens::tokenName($token));

            foreach ($listeners as $className => $details) {
                if ($className === $exclude) {
                    // Skip this one as it is the one sniff for which things will be different.
                    continue;
                }

                $this->assertArrayHasKey(
                    'ignore',
                    $details,
                    sprintf('"ignore" key missing for sniff class %s for token %s', $className, Tokens::tokenName($token))
                );

                $this->assertSame(
                    [],
                    $details['ignore'],
                    sprintf('Unexpected value for "ignore" key for sniff class %s for token %s', $className, Tokens::tokenName($token))
                );
            }
        }//end foreach

    }//end testSetsIgnorePatternsToEmptyArrayByDefault()


    /**
     * Tests that if there are <[include|exclude]-pattern> directives set on a sniff, these are set for the relevant listeners.
     *
     * Includes verification that the transformation of "regex"-like patterns is handled correctly.
     *
     * @param int|string $token       A token constant on which the sniff should be registered.
     * @param string     $sniffClass  The FQN for the sniff class on which the patterns should be registered.
     * @param string     $patternType The type of patterns expected to be registered for the sniff.
     *
     * @dataProvider dataSetsIncludeAndIgnorePatterns
     *
     * @return void
     */
    public function testSetsIncludeAndIgnorePatterns($token, $sniffClass, $patternType)
    {
        $expected = [
            '/no-transformation/',
            '/simple.*transformation/.*',
            '/escaped\\,comma/becomes/comma/to/allow/commas/in/filenames.css',
            '/pat?tern(is|regex)\\.php$',
        ];

        $this->assertArrayHasKey(
            $token,
            self::$ruleset->tokenListeners,
            sprintf('The token constant %s is not registered to the listeners array', Tokens::tokenName($token))
        );
        $this->assertArrayHasKey(
            $sniffClass,
            self::$ruleset->tokenListeners[$token],
            sprintf('The sniff class %s is not registered for token %s', $sniffClass, Tokens::tokenName($token))
        );
        $this->assertArrayHasKey(
            $patternType,
            self::$ruleset->tokenListeners[$token][$sniffClass],
            sprintf('"%s" key missing for sniff class %s for token %s', $patternType, $sniffClass, Tokens::tokenName($token))
        );

        $this->assertSame(
            $expected,
            self::$ruleset->tokenListeners[$token][$sniffClass][$patternType],
            sprintf('Unexpected value for "%s" key for sniff class %s for token %s', $patternType, $sniffClass, Tokens::tokenName($token))
        );

    }//end testSetsIncludeAndIgnorePatterns()


    /**
     * Data provider.
     *
     * @see testSetsIncludeAndIgnorePatterns()
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataSetsIncludeAndIgnorePatterns()
    {
        return [
            'Sniff with <include-pattern>s in the ruleset - first token'  => [
                'token'       => T_STRING,
                'sniffClass'  => 'PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\NamingConventions\\UpperCaseConstantNameSniff',
                'patternType' => 'include',
            ],
            'Sniff with <include-pattern>s in the ruleset - second token' => [
                'token'       => T_CONST,
                'sniffClass'  => 'PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\NamingConventions\\UpperCaseConstantNameSniff',
                'patternType' => 'include',
            ],
            'Sniff with <exclude-pattern>s in the ruleset'                => [
                'token'       => T_OPEN_TAG,
                'sniffClass'  => 'PHP_CodeSniffer\\Standards\\PSR1\\Sniffs\\Files\\SideEffectsSniff',
                'patternType' => 'ignore',
            ],
        ];

    }//end dataSetsIncludeAndIgnorePatterns()


}//end class
