<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File:getCondition and \PHP_CodeSniffer\Files\File:hasCondition methods.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2022-2024 PHPCSStandards Contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Files\File;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Tests for the \PHP_CodeSniffer\Files\File:getCondition and \PHP_CodeSniffer\Files\File:hasCondition methods.
 *
 * @covers \PHP_CodeSniffer\Files\File::getCondition
 * @covers \PHP_CodeSniffer\Files\File::hasCondition
 */
final class GetConditionTest extends AbstractMethodUnitTest
{

    /**
     * List of all the test markers with their target token in the test case file.
     *
     * - The startPoint token is left out as it is tested separately.
     * - The key is the type of token to look for after the test marker.
     *
     * @var array<int|string, string>
     */
    protected static $testTargets = [
        T_VARIABLE                 => '/* testSeriouslyNestedMethod */',
        T_RETURN                   => '/* testDeepestNested */',
        T_ECHO                     => '/* testInException */',
        T_CONSTANT_ENCAPSED_STRING => '/* testInDefault */',
    ];

    /**
     * List of all the condition markers in the test case file.
     *
     * @var array<string>
     */
    protected $conditionMarkers = [
        '/* condition 0: namespace */',
        '/* condition 1: if */',
        '/* condition 2: function */',
        '/* condition 3-1: if */',
        '/* condition 3-2: else */',
        '/* condition 4: if */',
        '/* condition 5: nested class */',
        '/* condition 6: class method */',
        '/* condition 7: switch */',
        '/* condition 8a: case */',
        '/* condition 9: while */',
        '/* condition 10-1: if */',
        '/* condition 11-1: nested anonymous class */',
        '/* condition 12: nested anonymous class method */',
        '/* condition 13: closure */',
        '/* condition 10-2: elseif */',
        '/* condition 10-3: foreach */',
        '/* condition 11-2: try */',
        '/* condition 11-3: catch */',
        '/* condition 11-4: finally */',
        '/* condition 8b: default */',
    ];

    /**
     * Base array with all the scope opening tokens.
     *
     * This array is merged with expected result arrays for various unit tests
     * to make sure all possible conditions are tested.
     *
     * This array should be kept in sync with the Tokens::$scopeOpeners array.
     * This array isn't auto-generated based on the array in Tokens as for these
     * tests we want to have access to the token constant names, not just their values.
     *
     * @var array<string, bool>
     */
    protected $conditionDefaults = [
        'T_CLASS'      => false,
        'T_ANON_CLASS' => false,
        'T_INTERFACE'  => false,
        'T_TRAIT'      => false,
        'T_NAMESPACE'  => false,
        'T_FUNCTION'   => false,
        'T_CLOSURE'    => false,
        'T_IF'         => false,
        'T_SWITCH'     => false,
        'T_CASE'       => false,
        'T_DECLARE'    => false,
        'T_DEFAULT'    => false,
        'T_WHILE'      => false,
        'T_ELSE'       => false,
        'T_ELSEIF'     => false,
        'T_FOR'        => false,
        'T_FOREACH'    => false,
        'T_DO'         => false,
        'T_TRY'        => false,
        'T_CATCH'      => false,
        'T_FINALLY'    => false,
        'T_PROPERTY'   => false,
        'T_OBJECT'     => false,
        'T_USE'        => false,
    ];

    /**
     * Cache for the test token stack pointers.
     *
     * @var array<string, int>
     */
    protected static $testTokens = [];

    /**
     * Cache for the marker token stack pointers.
     *
     * @var array<string, int>
     */
    protected static $markerTokens = [];


    /**
     * Set up the token position caches for the tests.
     *
     * Retrieves the test tokens and marker token stack pointer positions
     * only once and caches them as they won't change between the tests anyway.
     *
     * @before
     *
     * @return void
     */
    protected function setUpCaches()
    {
        if (empty(self::$testTokens) === true) {
            foreach (self::$testTargets as $targetToken => $marker) {
                self::$testTokens[$marker] = $this->getTargetToken($marker, $targetToken);
            }
        }

        if (empty(self::$markerTokens) === true) {
            foreach ($this->conditionMarkers as $marker) {
                self::$markerTokens[$marker] = $this->getTargetToken($marker, Tokens::$scopeOpeners);
            }
        }

    }//end setUpCaches()


    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $result = self::$phpcsFile->getCondition(100000, T_CLASS);
        $this->assertFalse($result);

        $result = self::$phpcsFile->hasCondition(100000, T_IF);
        $this->assertFalse($result);

    }//end testNonExistentToken()


    /**
     * Test passing a non conditional token.
     *
     * @return void
     */
    public function testNonConditionalToken()
    {
        $targetType = T_STRING;
        $stackPtr   = $this->getTargetToken('/* testStartPoint */', $targetType);

        $result = self::$phpcsFile->getCondition($stackPtr, T_IF);
        $this->assertFalse($result);

        $result = self::$phpcsFile->hasCondition($stackPtr, Tokens::$ooScopeTokens);
        $this->assertFalse($result);

    }//end testNonConditionalToken()


    /**
     * Test retrieving a specific condition from a tokens "conditions" array.
     *
     * @param string                $testMarker      The comment which prefaces the target token in the test file.
     * @param array<string, string> $expectedResults Array with the condition token type to search for as key
     *                                               and the marker for the expected stack pointer result as a value.
     *
     * @dataProvider dataGetCondition
     *
     * @return void
     */
    public function testGetCondition($testMarker, $expectedResults)
    {
        $stackPtr = self::$testTokens[$testMarker];

        // Add expected results for all test markers not listed in the data provider.
        $expectedResults += $this->conditionDefaults;

        foreach ($expectedResults as $conditionType => $expected) {
            if (is_string($expected) === true) {
                $expected = self::$markerTokens[$expected];
            }

            $result = self::$phpcsFile->getCondition($stackPtr, constant($conditionType));
            $this->assertSame(
                $expected,
                $result,
                "Assertion failed for test marker '{$testMarker}' with condition {$conditionType}"
            );
        }

    }//end testGetCondition()


    /**
     * Data provider.
     *
     * Only the conditions which are expected to be *found* need to be listed here.
     * All other potential conditions will automatically also be tested and will expect
     * `false` as a result.
     *
     * @see testGetCondition() For the array format.
     *
     * @return array<string, array<string, string|array<string, string>>>
     */
    public static function dataGetCondition()
    {
        return [
            'testSeriouslyNestedMethod' => [
                'testMarker'      => '/* testSeriouslyNestedMethod */',
                'expectedResults' => [
                    'T_CLASS'     => '/* condition 5: nested class */',
                    'T_NAMESPACE' => '/* condition 0: namespace */',
                    'T_FUNCTION'  => '/* condition 2: function */',
                    'T_IF'        => '/* condition 1: if */',
                    'T_ELSE'      => '/* condition 3-2: else */',
                ],
            ],
            'testDeepestNested'         => [
                'testMarker'      => '/* testDeepestNested */',
                'expectedResults' => [
                    'T_CLASS'      => '/* condition 5: nested class */',
                    'T_ANON_CLASS' => '/* condition 11-1: nested anonymous class */',
                    'T_NAMESPACE'  => '/* condition 0: namespace */',
                    'T_FUNCTION'   => '/* condition 2: function */',
                    'T_CLOSURE'    => '/* condition 13: closure */',
                    'T_IF'         => '/* condition 1: if */',
                    'T_SWITCH'     => '/* condition 7: switch */',
                    'T_CASE'       => '/* condition 8a: case */',
                    'T_WHILE'      => '/* condition 9: while */',
                    'T_ELSE'       => '/* condition 3-2: else */',
                ],
            ],
            'testInException'           => [
                'testMarker'      => '/* testInException */',
                'expectedResults' => [
                    'T_CLASS'     => '/* condition 5: nested class */',
                    'T_NAMESPACE' => '/* condition 0: namespace */',
                    'T_FUNCTION'  => '/* condition 2: function */',
                    'T_IF'        => '/* condition 1: if */',
                    'T_SWITCH'    => '/* condition 7: switch */',
                    'T_CASE'      => '/* condition 8a: case */',
                    'T_WHILE'     => '/* condition 9: while */',
                    'T_ELSE'      => '/* condition 3-2: else */',
                    'T_FOREACH'   => '/* condition 10-3: foreach */',
                    'T_CATCH'     => '/* condition 11-3: catch */',
                ],
            ],
            'testInDefault'             => [
                'testMarker'      => '/* testInDefault */',
                'expectedResults' => [
                    'T_CLASS'     => '/* condition 5: nested class */',
                    'T_NAMESPACE' => '/* condition 0: namespace */',
                    'T_FUNCTION'  => '/* condition 2: function */',
                    'T_IF'        => '/* condition 1: if */',
                    'T_SWITCH'    => '/* condition 7: switch */',
                    'T_DEFAULT'   => '/* condition 8b: default */',
                    'T_ELSE'      => '/* condition 3-2: else */',
                ],
            ],
        ];

    }//end dataGetCondition()


    /**
     * Test retrieving a specific condition from a tokens "conditions" array.
     *
     * @param string                $testMarker      The comment which prefaces the target token in the test file.
     * @param array<string, string> $expectedResults Array with the condition token type to search for as key
     *                                               and the marker for the expected stack pointer result as a value.
     *
     * @dataProvider dataGetConditionReversed
     *
     * @return void
     */
    public function testGetConditionReversed($testMarker, $expectedResults)
    {
        $stackPtr = self::$testTokens[$testMarker];

        // Add expected results for all test markers not listed in the data provider.
        $expectedResults += $this->conditionDefaults;

        foreach ($expectedResults as $conditionType => $expected) {
            if (is_string($expected) === true) {
                $expected = self::$markerTokens[$expected];
            }

            $result = self::$phpcsFile->getCondition($stackPtr, constant($conditionType), false);
            $this->assertSame(
                $expected,
                $result,
                "Assertion failed for test marker '{$testMarker}' with condition {$conditionType} (reversed)"
            );
        }

    }//end testGetConditionReversed()


    /**
     * Data provider.
     *
     * Only the conditions which are expected to be *found* need to be listed here.
     * All other potential conditions will automatically also be tested and will expect
     * `false` as a result.
     *
     * @see testGetConditionReversed() For the array format.
     *
     * @return array<string, array<string, string|array<string, string>>>
     */
    public static function dataGetConditionReversed()
    {
        $data = self::dataGetCondition();

        // Set up the data for the reversed results.
        $data['testSeriouslyNestedMethod']['expectedResults']['T_IF'] = '/* condition 4: if */';

        $data['testDeepestNested']['expectedResults']['T_FUNCTION'] = '/* condition 12: nested anonymous class method */';
        $data['testDeepestNested']['expectedResults']['T_IF']       = '/* condition 10-1: if */';

        $data['testInException']['expectedResults']['T_FUNCTION'] = '/* condition 6: class method */';
        $data['testInException']['expectedResults']['T_IF']       = '/* condition 4: if */';

        $data['testInDefault']['expectedResults']['T_FUNCTION'] = '/* condition 6: class method */';
        $data['testInDefault']['expectedResults']['T_IF']       = '/* condition 4: if */';

        return $data;

    }//end dataGetConditionReversed()


    /**
     * Test whether a token has a condition of a certain type.
     *
     * @param string              $testMarker      The comment which prefaces the target token in the test file.
     * @param array<string, bool> $expectedResults Array with the condition token type to search for as key
     *                                             and the expected result as a value.
     *
     * @dataProvider dataHasCondition
     *
     * @return void
     */
    public function testHasCondition($testMarker, $expectedResults)
    {
        $stackPtr = self::$testTokens[$testMarker];

        // Add expected results for all test markers not listed in the data provider.
        $expectedResults += $this->conditionDefaults;

        foreach ($expectedResults as $conditionType => $expected) {
            $result = self::$phpcsFile->hasCondition($stackPtr, constant($conditionType));
            $this->assertSame(
                $expected,
                $result,
                "Assertion failed for test marker '{$testMarker}' with condition {$conditionType}"
            );
        }

    }//end testHasCondition()


    /**
     * Data Provider.
     *
     * Only list the "true" conditions in the $results array.
     * All other potential conditions will automatically also be tested
     * and will expect "false" as a result.
     *
     * @see testHasCondition() For the array format.
     *
     * @return array<string, array<string, string|array<string, bool>>>
     */
    public static function dataHasCondition()
    {
        return [
            'testSeriouslyNestedMethod' => [
                'testMarker'      => '/* testSeriouslyNestedMethod */',
                'expectedResults' => [
                    'T_CLASS'     => true,
                    'T_NAMESPACE' => true,
                    'T_FUNCTION'  => true,
                    'T_IF'        => true,
                    'T_ELSE'      => true,
                ],
            ],
            'testDeepestNested'         => [
                'testMarker'      => '/* testDeepestNested */',
                'expectedResults' => [
                    'T_CLASS'      => true,
                    'T_ANON_CLASS' => true,
                    'T_NAMESPACE'  => true,
                    'T_FUNCTION'   => true,
                    'T_CLOSURE'    => true,
                    'T_IF'         => true,
                    'T_SWITCH'     => true,
                    'T_CASE'       => true,
                    'T_WHILE'      => true,
                    'T_ELSE'       => true,
                ],
            ],
            'testInException'           => [
                'testMarker'      => '/* testInException */',
                'expectedResults' => [
                    'T_CLASS'     => true,
                    'T_NAMESPACE' => true,
                    'T_FUNCTION'  => true,
                    'T_IF'        => true,
                    'T_SWITCH'    => true,
                    'T_CASE'      => true,
                    'T_WHILE'     => true,
                    'T_ELSE'      => true,
                    'T_FOREACH'   => true,
                    'T_CATCH'     => true,
                ],
            ],
            'testInDefault'             => [
                'testMarker'      => '/* testInDefault */',
                'expectedResults' => [
                    'T_CLASS'     => true,
                    'T_NAMESPACE' => true,
                    'T_FUNCTION'  => true,
                    'T_IF'        => true,
                    'T_SWITCH'    => true,
                    'T_DEFAULT'   => true,
                    'T_ELSE'      => true,
                ],
            ],
        ];

    }//end dataHasCondition()


    /**
     * Test whether a token has a condition of a certain type, with multiple allowed possibilities.
     *
     * @return void
     */
    public function testHasConditionMultipleTypes()
    {
        $stackPtr = self::$testTokens['/* testInException */'];

        $result = self::$phpcsFile->hasCondition($stackPtr, [T_TRY, T_FINALLY]);
        $this->assertFalse(
            $result,
            'Failed asserting that "testInException" does not have a "try" nor a "finally" condition'
        );

        $result = self::$phpcsFile->hasCondition($stackPtr, [T_TRY, T_CATCH, T_FINALLY]);
        $this->assertTrue(
            $result,
            'Failed asserting that "testInException" has a "try", "catch" or "finally" condition'
        );

        $stackPtr = self::$testTokens['/* testSeriouslyNestedMethod */'];

        $result = self::$phpcsFile->hasCondition($stackPtr, [T_ANON_CLASS, T_CLOSURE]);
        $this->assertFalse(
            $result,
            'Failed asserting that "testSeriouslyNestedMethod" does not have an anonymous class nor a closure condition'
        );

        $result = self::$phpcsFile->hasCondition($stackPtr, Tokens::$ooScopeTokens);
        $this->assertTrue(
            $result,
            'Failed asserting that "testSeriouslyNestedMethod" has an OO Scope token condition'
        );

    }//end testHasConditionMultipleTypes()


}//end class
