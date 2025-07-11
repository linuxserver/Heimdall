<?php
/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Ruleset\PropertyTypeHandlingTest
 */

namespace Fixtures\TestStandard\Sniffs\SetProperty;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class PropertyTypeHandlingSniff implements Sniff
{

    /**
     * Used to verify that string properties are set as string.
     *
     * This is the default behaviour.
     *
     * @var string
     */
    public $expectsString;

    /**
     * Used to verify that string properties are set as string, with surrounding whitespace trimmed.
     *
     * This is the default behaviour.
     *
     * @var string
     */
    public $expectsTrimmedString;

    /**
     * Used to verify that a string value with only whitespace will end up being set as null.
     *
     * @var string|null
     */
    public $emptyStringBecomesNull;

    /**
     * Used to verify that integer properties do not have special handling and will be set as a string.
     *
     * @var int
     */
    public $expectsIntButAcceptsString;

    /**
     * Used to verify that floating point properties do not have special handling and will be set as a string.
     *
     * @var float
     */
    public $expectsFloatButAcceptsString;

    /**
     * Used to verify that null gets set as a string.
     *
     * @var null
     */
    public $expectsNull;

    /**
     * Used to verify that null gets set as a string.
     *
     * @var null
     */
    public $expectsNullCase;

    /**
     * Used to verify that booleans get set as proper boolean values.
     *
     * @var bool
     */
    public $expectsBooleanTrue;

    /**
     * Used to verify that booleans get set as proper boolean values.
     *
     * @var bool
     */
    public $expectsBooleanTrueCase;

    /**
     * Used to verify that booleans get set as proper boolean values.
     *
     * @var bool
     */
    public $expectsBooleanTrueTrimmed;

    /**
     * Used to verify that booleans get set as proper boolean values.
     *
     * @var bool
     */
    public $expectsBooleanFalse;

    /**
     * Used to verify that booleans get set as proper boolean values.
     *
     * @var bool
     */
    public $expectsBooleanFalseCase;

    /**
     * Used to verify that booleans get set as proper boolean values.
     *
     * @var bool
     */
    public $expectsBooleanFalseTrimmed;

    /**
     * Used to verify that array properties get parsed to a proper array.
     *
     * @var array<mixed>
     */
    public $expectsArrayWithOnlyValues;

    /**
     * Used to verify that array properties with keys get parsed to a proper array.
     *
     * @var array<string, mixed>
     */
    public $expectsArrayWithKeysAndValues;

    /**
     * Used to verify that array properties can get extended.
     *
     * @var array<mixed>
     */
    public $expectsArrayWithExtendedValues;

    /**
     * Used to verify that array properties can get extended.
     *
     * @var array<mixed>
     */
    public $expectsArrayWithExtendedKeysAndValues;

    /**
     * Used to verify that array properties allow for setting a property to an empty array.
     *
     * @var array<mixed>
     */
    public $expectsEmptyArray;

    /**
     * Used to verify that array properties passed as a string get parsed to a proper array.
     *
     * @var array<mixed>
     */
    public $expectsOldSchoolArrayWithOnlyValues;

    /**
     * Used to verify that array properties passed as a string with keys get parsed to a proper array.
     *
     * @var array<string, mixed>
     */
    public $expectsOldSchoolArrayWithKeysAndValues;

    /**
     * Used to verify that array properties passed as a string can get extended.
     *
     * @var array<string, mixed>
     */
    public $expectsOldSchoolArrayWithExtendedValues;

    /**
     * Used to verify that array properties passed as a string can get extended.
     *
     * @var array<string, mixed>
     */
    public $expectsOldSchoolArrayWithExtendedKeysAndValues;

    /**
     * Used to verify that array properties passed as a string allow for setting a property to an empty array.
     *
     * @var array<mixed>
     */
    public $expectsOldSchoolEmptyArray;

    public function register()
    {
        return [T_ECHO];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        // Do something.
    }
}
