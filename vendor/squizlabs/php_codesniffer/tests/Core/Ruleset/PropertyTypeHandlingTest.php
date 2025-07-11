<?php
/**
 * Tests for the handling of properties being set via the ruleset.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Ruleset;

use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHPUnit\Framework\TestCase;

/**
 * Test the handling of property value types for properties set via the ruleset and inline.
 *
 * @covers \PHP_CodeSniffer\Ruleset::processRule
 * @covers \PHP_CodeSniffer\Ruleset::setSniffProperty
 */
final class PropertyTypeHandlingTest extends TestCase
{

    /**
     * Sniff code for the sniff used in these tests.
     *
     * @var string
     */
    const SNIFF_CODE = 'TestStandard.SetProperty.PropertyTypeHandling';

    /**
     * Class name of the sniff used in these tests.
     *
     * @var string
     */
    const SNIFF_CLASS = 'Fixtures\\TestStandard\\Sniffs\\SetProperty\\PropertyTypeHandlingSniff';


    /**
     * Verify a deprecation notice is shown when an array property is set from the ruleset using a comma-separated string.
     *
     * Support for this format was (soft) deprecated in PHPCS 3.3.0.
     *
     * @return void
     */
    public function testUsingOldSchoolArrayFormatShowsDeprecationNotice()
    {
        $regex  = '`^(';
        $regex .= 'DEPRECATED: Passing an array of values to a property using a comma-separated string\R';
        $regex .= 'was deprecated in PHP_CodeSniffer 3\.3\.0\. Support will be removed in PHPCS 4\.0\.0\.\R';
        $regex .= 'The deprecated syntax was used for property "expectsOldSchool(?:EmptyArray|ArrayWith(?:Extended|Only)?(?:KeysAnd)?Values)"\R';
        $regex .= 'for sniff "';
        $regex .= '(?:\./tests/Core/Ruleset/Fixtures/TestStandard/Sniffs/SetProperty/PropertyTypeHandlingSniff\.php|TestStandard\.SetProperty\.PropertyTypeHandling)';
        $regex .= '"\.\R';
        $regex .= 'Pass array values via <element \[key="\.\.\." \]value="\.\.\."> nodes instead\.\R';
        $regex .= '){14}\R$`';

        $this->expectOutputRegex($regex);

        // Set up the ruleset.
        $standard = __DIR__.'/PropertyTypeHandlingTest.xml';
        $config   = new ConfigDouble(["--standard=$standard"]);
        new Ruleset($config);

    }//end testUsingOldSchoolArrayFormatShowsDeprecationNotice()


    /**
     * Test the value type handling for properties set via a ruleset.
     *
     * @param string $propertyName Property name.
     * @param mixed  $expected     Expected property value.
     *
     * @dataProvider dataTypeHandling
     * @dataProvider dataArrayPropertyExtending
     *
     * @return void
     */
    public function testTypeHandlingWhenSetViaRuleset($propertyName, $expected)
    {
        $sniffObject = $this->getSniffObjectForRuleset();

        $this->assertSame($expected, $sniffObject->$propertyName);

    }//end testTypeHandlingWhenSetViaRuleset()


    /**
     * Test the value type handling for properties set inline in a test case file.
     *
     * @param string $propertyName Property name.
     * @param mixed  $expected     Expected property value.
     *
     * @dataProvider dataTypeHandling
     *
     * @return void
     */
    public function testTypeHandlingWhenSetInline($propertyName, $expected)
    {
        $sniffObject = $this->getSniffObjectAfterProcessingFile();

        $this->assertSame($expected, $sniffObject->$propertyName);

    }//end testTypeHandlingWhenSetInline()


    /**
     * Data provider.
     *
     * @see self::testTypeHandlingWhenSetViaRuleset()
     * @see self::testTypeHandlingWhenSetInline()
     *
     * @return array<string, array<string, mixed>>
     */
    public static function dataTypeHandling()
    {
        $expectedArrayOnlyValues    = [
            'string',
            '10',
            '1.5',
            'null',
            'true',
            'false',
        ];
        $expectedArrayKeysAndValues = [
            'string' => 'string',
            10       => '10',
            'float'  => '1.5',
            'null'   => 'null',
            'true'   => 'true',
            'false'  => 'false',
        ];

        return [
            'String value (default)'                         => [
                'propertyName' => 'expectsString',
                'expected'     => 'arbitraryvalue',
            ],
            'String value with whitespace gets trimmed'      => [
                'propertyName' => 'expectsTrimmedString',
                'expected'     => 'some value',
            ],
            'String with whitespace only value becomes null' => [
                'propertyName' => 'emptyStringBecomesNull',
                'expected'     => null,
            ],
            'Integer value gets set as string'               => [
                'propertyName' => 'expectsIntButAcceptsString',
                'expected'     => '12345',
            ],
            'Float value gets set as string'                 => [
                'propertyName' => 'expectsFloatButAcceptsString',
                'expected'     => '12.345',
            ],
            'Null value gets set as string'                  => [
                'propertyName' => 'expectsNull',
                'expected'     => 'null',
            ],
            'Null (uppercase) value gets set as string'      => [
                'propertyName' => 'expectsNullCase',
                'expected'     => 'NULL',
            ],
            'True value gets set as boolean'                 => [
                'propertyName' => 'expectsBooleanTrue',
                'expected'     => true,
            ],
            'True (mixed case) value gets set as string'     => [
                'propertyName' => 'expectsBooleanTrueCase',
                'expected'     => 'True',
            ],
            'True (with spaces) value gets set as boolean'   => [
                'propertyName' => 'expectsBooleanTrueTrimmed',
                'expected'     => true,
            ],
            'False value gets set as boolean'                => [
                'propertyName' => 'expectsBooleanFalse',
                'expected'     => false,
            ],
            'False (mixed case) value gets set as string'    => [
                'propertyName' => 'expectsBooleanFalseCase',
                'expected'     => 'fALSe',
            ],
            'False (with spaces) value gets set as boolean'  => [
                'propertyName' => 'expectsBooleanFalseTrimmed',
                'expected'     => false,
            ],
            'Array with only values (new style)'             => [
                'propertyName' => 'expectsArrayWithOnlyValues',
                'expected'     => $expectedArrayOnlyValues,
            ],
            'Array with keys and values (new style)'         => [
                'propertyName' => 'expectsArrayWithKeysAndValues',
                'expected'     => $expectedArrayKeysAndValues,
            ],
            'Empty array (new style)'                        => [
                'propertyName' => 'expectsEmptyArray',
                'expected'     => [],
            ],
            'Array with only values (old style)'             => [
                'propertyName' => 'expectsOldSchoolArrayWithOnlyValues',
                'expected'     => $expectedArrayOnlyValues,
            ],
            'Array with keys and values (old style)'         => [
                'propertyName' => 'expectsOldSchoolArrayWithKeysAndValues',
                'expected'     => $expectedArrayKeysAndValues,
            ],
            'Empty array (old style)'                        => [
                'propertyName' => 'expectsOldSchoolEmptyArray',
                'expected'     => [],
            ],
        ];

    }//end dataTypeHandling()


    /**
     * Data provider.
     *
     * Array property extending is a feature which is only supported from a ruleset, not for inline property setting.
     *
     * @see self::testTypeHandlingWhenSetViaRuleset()
     *
     * @return array<string, array<string, mixed>>
     */
    public static function dataArrayPropertyExtending()
    {
        $expectedArrayOnlyValuesExtended    = [
            'string',
            '15',
            'another string',
        ];
        $expectedArrayKeysAndValuesExtended = [
            10               => '10',
            'string'         => 'string',
            15               => '15',
            'another string' => 'another string',
        ];

        return [
            'Array with only values extended (new style)'     => [
                'propertyName' => 'expectsArrayWithExtendedValues',
                'expected'     => $expectedArrayOnlyValuesExtended,
            ],
            'Array with keys and values extended (new style)' => [
                'propertyName' => 'expectsArrayWithExtendedKeysAndValues',
                'expected'     => $expectedArrayKeysAndValuesExtended,
            ],
            'Array with only values extended (old style)'     => [
                'propertyName' => 'expectsOldSchoolArrayWithExtendedValues',
                'expected'     => $expectedArrayOnlyValuesExtended,
            ],
            'Array with keys and values extended (old style)' => [
                'propertyName' => 'expectsOldSchoolArrayWithExtendedKeysAndValues',
                'expected'     => $expectedArrayKeysAndValuesExtended,
            ],
        ];

    }//end dataArrayPropertyExtending()


    /**
     * Test Helper.
     *
     * Note: the deprecations for using comma-separated string to pass an array, are silenced in this helper
     * as that's not what's being tested here.
     *
     * @see self::testTypeHandlingWhenSetViaRuleset()
     *
     * @return \PHP_CodeSniffer\Sniffs\Sniff
     */
    private function getSniffObjectForRuleset()
    {
        static $sniffObject;

        if (isset($sniffObject) === false) {
            // Set up the ruleset.
            $standard = __DIR__.'/PropertyTypeHandlingTest.xml';
            $config   = new ConfigDouble(["--standard=$standard", '-q']);
            $ruleset  = new Ruleset($config);

            // Verify that our target sniff has been registered.
            $this->assertArrayHasKey(self::SNIFF_CODE, $ruleset->sniffCodes, 'Target sniff not registered');
            $this->assertSame(self::SNIFF_CLASS, $ruleset->sniffCodes[self::SNIFF_CODE], 'Target sniff not registered with the correct class');
            $this->assertArrayHasKey(self::SNIFF_CLASS, $ruleset->sniffs, 'Sniff class not listed in registered sniffs');

            $sniffObject = $ruleset->sniffs[self::SNIFF_CLASS];
        }

        return $sniffObject;

    }//end getSniffObjectForRuleset()


    /**
     * Test Helper
     *
     * @see self::testTypeHandlingWhenSetInline()
     *
     * @return \PHP_CodeSniffer\Sniffs\Sniff
     */
    private function getSniffObjectAfterProcessingFile()
    {
        static $sniffObject;

        if (isset($sniffObject) === false) {
            // Set up the ruleset.
            $standard = __DIR__.'/PropertyTypeHandlingInlineTest.xml';
            $config   = new ConfigDouble(["--standard=$standard"]);
            $ruleset  = new Ruleset($config);

            // Verify that our target sniff has been registered.
            $this->assertArrayHasKey(self::SNIFF_CODE, $ruleset->sniffCodes, 'Target sniff not registered');
            $this->assertSame(self::SNIFF_CLASS, $ruleset->sniffCodes[self::SNIFF_CODE], 'Target sniff not registered with the correct class');
            $this->assertArrayHasKey(self::SNIFF_CLASS, $ruleset->sniffs, 'Sniff class not listed in registered sniffs');

            $sniffObject = $ruleset->sniffs[self::SNIFF_CLASS];

            // Process the file with inline phpcs:set annotations.
            $testFile = realpath(__DIR__.'/Fixtures/PropertyTypeHandlingInline.inc');
            $this->assertNotFalse($testFile);

            $phpcsFile = new LocalFile($testFile, $ruleset, $config);
            $phpcsFile->process();
        }

        return $sniffObject;

    }//end getSniffObjectAfterProcessingFile()


}//end class
