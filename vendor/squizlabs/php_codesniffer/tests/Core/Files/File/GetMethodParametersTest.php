<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File::getMethodParameters method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @copyright 2019-2024 PHPCSStandards Contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Files\File;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

/**
 * Tests for the \PHP_CodeSniffer\Files\File::getMethodParameters method.
 *
 * @covers \PHP_CodeSniffer\Files\File::getMethodParameters
 */
final class GetMethodParametersTest extends AbstractMethodUnitTest
{


    /**
     * Test receiving an expected exception when a non function/use token is passed.
     *
     * @param string                       $commentString   The comment which preceeds the test.
     * @param int|string|array<int|string> $targetTokenType The token type to search for after $commentString.
     *
     * @dataProvider dataUnexpectedTokenException
     *
     * @return void
     */
    public function testUnexpectedTokenException($commentString, $targetTokenType)
    {
        $this->expectRunTimeException('$stackPtr must be of type T_FUNCTION or T_CLOSURE or T_USE or T_FN');

        $target = $this->getTargetToken($commentString, $targetTokenType);
        self::$phpcsFile->getMethodParameters($target);

    }//end testUnexpectedTokenException()


    /**
     * Data Provider.
     *
     * @see testUnexpectedTokenException() For the array format.
     *
     * @return array<string, array<string, int|string|array<int|string>>>
     */
    public static function dataUnexpectedTokenException()
    {
        return [
            'interface'                          => [
                'commentString'   => '/* testNotAFunction */',
                'targetTokenType' => T_INTERFACE,
            ],
            'function-call-fn-phpcs-3.5.3-3.5.4' => [
                'commentString'   => '/* testFunctionCallFnPHPCS353-354 */',
                'targetTokenType' => [
                    T_FN,
                    T_STRING,
                ],
            ],
            'fn-live-coding'                     => [
                'commentString'   => '/* testArrowFunctionLiveCoding */',
                'targetTokenType' => [
                    T_FN,
                    T_STRING,
                ],
            ],
        ];

    }//end dataUnexpectedTokenException()


    /**
     * Test receiving an expected exception when a non-closure use token is passed.
     *
     * @param string $identifier The comment which preceeds the test.
     *
     * @dataProvider dataInvalidUse
     *
     * @return void
     */
    public function testInvalidUse($identifier)
    {
        $this->expectRunTimeException('$stackPtr was not a valid T_USE');

        $use = $this->getTargetToken($identifier, [T_USE]);
        self::$phpcsFile->getMethodParameters($use);

    }//end testInvalidUse()


    /**
     * Data Provider.
     *
     * @see testInvalidUse() For the array format.
     *
     * @return array<string, array<string>>
     */
    public static function dataInvalidUse()
    {
        return [
            'ImportUse'      => ['/* testImportUse */'],
            'ImportGroupUse' => ['/* testImportGroupUse */'],
            'TraitUse'       => ['/* testTraitUse */'],
        ];

    }//end dataInvalidUse()


    /**
     * Test receiving an empty array when there are no parameters.
     *
     * @param string                       $commentString   The comment which preceeds the test.
     * @param int|string|array<int|string> $targetTokenType Optional. The token type to search for after $commentString.
     *                                                      Defaults to the function/closure/arrow tokens.
     *
     * @dataProvider dataNoParams
     *
     * @return void
     */
    public function testNoParams($commentString, $targetTokenType=[T_FUNCTION, T_CLOSURE, T_FN])
    {
        $target = $this->getTargetToken($commentString, $targetTokenType);
        $result = self::$phpcsFile->getMethodParameters($target);

        $this->assertSame([], $result);

    }//end testNoParams()


    /**
     * Data Provider.
     *
     * @see testNoParams() For the array format.
     *
     * @return array<string, array<int|string|array<int|string>>>
     */
    public static function dataNoParams()
    {
        return [
            'FunctionNoParams'   => [
                'commentString' => '/* testFunctionNoParams */',
            ],
            'ClosureNoParams'    => [
                'commentString' => '/* testClosureNoParams */',
            ],
            'ClosureUseNoParams' => [
                'commentString'   => '/* testClosureUseNoParams */',
                'targetTokenType' => T_USE,
            ],
        ];

    }//end dataNoParams()


    /**
     * Verify pass-by-reference parsing.
     *
     * @return void
     */
    public function testPassByReference()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 5,
            'name'                => '$var',
            'content'             => '&$var',
            'has_attributes'      => false,
            'pass_by_reference'   => true,
            'reference_token'     => 4,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPassByReference()


    /**
     * Verify array hint parsing.
     *
     * @return void
     */
    public function testArrayHint()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 6,
            'name'                => '$var',
            'content'             => 'array $var',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'array',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 4,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testArrayHint()


    /**
     * Verify variable.
     *
     * @return void
     */
    public function testVariable()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 4,
            'name'                => '$var',
            'content'             => '$var',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testVariable()


    /**
     * Verify default value parsing with a single function param.
     *
     * @return void
     */
    public function testSingleDefaultValue()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 4,
            'name'                => '$var1',
            'content'             => '$var1=self::CONSTANT',
            'default'             => 'self::CONSTANT',
            'default_token'       => 6,
            'default_equal_token' => 5,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testSingleDefaultValue()


    /**
     * Verify default value parsing.
     *
     * @return void
     */
    public function testDefaultValues()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 4,
            'name'                => '$var1',
            'content'             => '$var1=1',
            'default'             => '1',
            'default_token'       => 6,
            'default_equal_token' => 5,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 7,
        ];
        $expected[1] = [
            'token'               => 9,
            'name'                => '$var2',
            'content'             => "\$var2='value'",
            'default'             => "'value'",
            'default_token'       => 11,
            'default_equal_token' => 10,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testDefaultValues()


    /**
     * Verify type hint parsing.
     *
     * @return void
     */
    public function testTypeHint()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 6,
            'name'                => '$var1',
            'content'             => 'foo $var1',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'foo',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 4,
            'nullable_type'       => false,
            'comma_token'         => 7,
        ];

        $expected[1] = [
            'token'               => 11,
            'name'                => '$var2',
            'content'             => 'bar $var2',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'bar',
            'type_hint_token'     => 9,
            'type_hint_end_token' => 9,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testTypeHint()


    /**
     * Verify self type hint parsing.
     *
     * @return void
     */
    public function testSelfTypeHint()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 6,
            'name'                => '$var',
            'content'             => 'self $var',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'self',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 4,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testSelfTypeHint()


    /**
     * Verify nullable type hint parsing.
     *
     * @return void
     */
    public function testNullableTypeHint()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 7,
            'name'                => '$var1',
            'content'             => '?int $var1',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?int',
            'type_hint_token'     => 5,
            'type_hint_end_token' => 5,
            'nullable_type'       => true,
            'comma_token'         => 8,
        ];

        $expected[1] = [
            'token'               => 14,
            'name'                => '$var2',
            'content'             => '?\bar $var2',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?\bar',
            'type_hint_token'     => 11,
            'type_hint_end_token' => 12,
            'nullable_type'       => true,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testNullableTypeHint()


    /**
     * Verify "bitwise and" in default value !== pass-by-reference.
     *
     * @return void
     */
    public function testBitwiseAndConstantExpressionDefaultValue()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 4,
            'name'                => '$a',
            'content'             => '$a = 10 & 20',
            'default'             => '10 & 20',
            'default_token'       => 8,
            'default_equal_token' => 6,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testBitwiseAndConstantExpressionDefaultValue()


    /**
     * Verify that arrow functions are supported.
     *
     * @return void
     */
    public function testArrowFunction()
    {
        // Offsets are relative to the T_FN token.
        $expected    = [];
        $expected[0] = [
            'token'               => 4,
            'name'                => '$a',
            'content'             => 'int $a',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'int',
            'type_hint_token'     => 2,
            'type_hint_end_token' => 2,
            'nullable_type'       => false,
            'comma_token'         => 5,
        ];

        $expected[1] = [
            'token'               => 8,
            'name'                => '$b',
            'content'             => '...$b',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => true,
            'variadic_token'      => 7,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testArrowFunction()


    /**
     * Verify that arrow functions are supported.
     *
     * @return void
     */
    public function testArrowFunctionReturnByRef()
    {
        // Offsets are relative to the T_FN token.
        $expected    = [];
        $expected[0] = [
            'token'               => 6,
            'name'                => '$a',
            'content'             => '?string $a',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?string',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 4,
            'nullable_type'       => true,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testArrowFunctionReturnByRef()


    /**
     * Verify default value parsing with array values.
     *
     * @return void
     */
    public function testArrayDefaultValues()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 4,
            'name'                => '$var1',
            'content'             => '$var1 = []',
            'default'             => '[]',
            'default_token'       => 8,
            'default_equal_token' => 6,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 10,
        ];
        $expected[1] = [
            'token'               => 12,
            'name'                => '$var2',
            'content'             => '$var2 = array(1, 2, 3)',
            'default'             => 'array(1, 2, 3)',
            'default_token'       => 16,
            'default_equal_token' => 14,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testArrayDefaultValues()


    /**
     * Verify having a T_STRING constant as a default value for the second parameter.
     *
     * @return void
     */
    public function testConstantDefaultValueSecondParam()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 4,
            'name'                => '$var1',
            'content'             => '$var1',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 5,
        ];
        $expected[1] = [
            'token'               => 7,
            'name'                => '$var2',
            'content'             => '$var2 = M_PI',
            'default'             => 'M_PI',
            'default_token'       => 11,
            'default_equal_token' => 9,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testConstantDefaultValueSecondParam()


    /**
     * Verify distinquishing between a nullable type and a ternary within a default expression.
     *
     * @return void
     */
    public function testScalarTernaryExpressionInDefault()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 5,
            'name'                => '$a',
            'content'             => '$a = FOO ? \'bar\' : 10',
            'default'             => 'FOO ? \'bar\' : 10',
            'default_token'       => 9,
            'default_equal_token' => 7,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 18,
        ];
        $expected[1] = [
            'token'               => 24,
            'name'                => '$b',
            'content'             => '? bool $b',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?bool',
            'type_hint_token'     => 22,
            'type_hint_end_token' => 22,
            'nullable_type'       => true,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testScalarTernaryExpressionInDefault()


    /**
     * Verify a variadic parameter being recognized correctly.
     *
     * @return void
     */
    public function testVariadicFunction()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 9,
            'name'                => '$a',
            'content'             => 'int ... $a',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => true,
            'variadic_token'      => 7,
            'type_hint'           => 'int',
            'type_hint_token'     => 5,
            'type_hint_end_token' => 5,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testVariadicFunction()


    /**
     * Verify a variadic parameter passed by reference being recognized correctly.
     *
     * @return void
     */
    public function testVariadicByRefFunction()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 7,
            'name'                => '$a',
            'content'             => '&...$a',
            'has_attributes'      => false,
            'pass_by_reference'   => true,
            'reference_token'     => 5,
            'variable_length'     => true,
            'variadic_token'      => 6,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testVariadicByRefFunction()


    /**
     * Verify handling of a variadic parameter with a class based type declaration.
     *
     * @return void
     */
    public function testVariadicFunctionClassType()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 4,
            'name'                => '$unit',
            'content'             => '$unit',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 5,
        ];
        $expected[1] = [
            'token'               => 10,
            'name'                => '$intervals',
            'content'             => 'DateInterval ...$intervals',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => true,
            'variadic_token'      => 9,
            'type_hint'           => 'DateInterval',
            'type_hint_token'     => 7,
            'type_hint_end_token' => 7,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testVariadicFunctionClassType()


    /**
     * Verify distinquishing between a nullable type and a ternary within a default expression.
     *
     * @return void
     */
    public function testNameSpacedTypeDeclaration()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 12,
            'name'                => '$a',
            'content'             => '\Package\Sub\ClassName $a',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '\Package\Sub\ClassName',
            'type_hint_token'     => 5,
            'type_hint_end_token' => 10,
            'nullable_type'       => false,
            'comma_token'         => 13,
        ];
        $expected[1] = [
            'token'               => 20,
            'name'                => '$b',
            'content'             => '?Sub\AnotherClass $b',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?Sub\AnotherClass',
            'type_hint_token'     => 16,
            'type_hint_end_token' => 18,
            'nullable_type'       => true,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testNameSpacedTypeDeclaration()


    /**
     * Verify correctly recognizing all type declarations supported by PHP.
     *
     * @return void
     */
    public function testWithAllTypes()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected     = [];
        $expected[0]  = [
            'token'               => 9,
            'name'                => '$a',
            'content'             => '?ClassName $a',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?ClassName',
            'type_hint_token'     => 7,
            'type_hint_end_token' => 7,
            'nullable_type'       => true,
            'comma_token'         => 10,
        ];
        $expected[1]  = [
            'token'               => 15,
            'name'                => '$b',
            'content'             => 'self $b',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'self',
            'type_hint_token'     => 13,
            'type_hint_end_token' => 13,
            'nullable_type'       => false,
            'comma_token'         => 16,
        ];
        $expected[2]  = [
            'token'               => 21,
            'name'                => '$c',
            'content'             => 'parent $c',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'parent',
            'type_hint_token'     => 19,
            'type_hint_end_token' => 19,
            'nullable_type'       => false,
            'comma_token'         => 22,
        ];
        $expected[3]  = [
            'token'               => 27,
            'name'                => '$d',
            'content'             => 'object $d',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'object',
            'type_hint_token'     => 25,
            'type_hint_end_token' => 25,
            'nullable_type'       => false,
            'comma_token'         => 28,
        ];
        $expected[4]  = [
            'token'               => 34,
            'name'                => '$e',
            'content'             => '?int $e',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?int',
            'type_hint_token'     => 32,
            'type_hint_end_token' => 32,
            'nullable_type'       => true,
            'comma_token'         => 35,
        ];
        $expected[5]  = [
            'token'               => 41,
            'name'                => '$f',
            'content'             => 'string &$f',
            'has_attributes'      => false,
            'pass_by_reference'   => true,
            'reference_token'     => 40,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'string',
            'type_hint_token'     => 38,
            'type_hint_end_token' => 38,
            'nullable_type'       => false,
            'comma_token'         => 42,
        ];
        $expected[6]  = [
            'token'               => 47,
            'name'                => '$g',
            'content'             => 'iterable $g',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'iterable',
            'type_hint_token'     => 45,
            'type_hint_end_token' => 45,
            'nullable_type'       => false,
            'comma_token'         => 48,
        ];
        $expected[7]  = [
            'token'               => 53,
            'name'                => '$h',
            'content'             => 'bool $h = true',
            'default'             => 'true',
            'default_token'       => 57,
            'default_equal_token' => 55,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'bool',
            'type_hint_token'     => 51,
            'type_hint_end_token' => 51,
            'nullable_type'       => false,
            'comma_token'         => 58,
        ];
        $expected[8]  = [
            'token'               => 63,
            'name'                => '$i',
            'content'             => 'callable $i = \'is_null\'',
            'default'             => "'is_null'",
            'default_token'       => 67,
            'default_equal_token' => 65,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'callable',
            'type_hint_token'     => 61,
            'type_hint_end_token' => 61,
            'nullable_type'       => false,
            'comma_token'         => 68,
        ];
        $expected[9]  = [
            'token'               => 73,
            'name'                => '$j',
            'content'             => 'float $j = 1.1',
            'default'             => '1.1',
            'default_token'       => 77,
            'default_equal_token' => 75,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'float',
            'type_hint_token'     => 71,
            'type_hint_end_token' => 71,
            'nullable_type'       => false,
            'comma_token'         => 78,
        ];
        $expected[10] = [
            'token'               => 84,
            'name'                => '$k',
            'content'             => 'array ...$k',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => true,
            'variadic_token'      => 83,
            'type_hint'           => 'array',
            'type_hint_token'     => 81,
            'type_hint_end_token' => 81,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testWithAllTypes()


    /**
     * Verify correctly recognizing all type declarations supported by PHP when used with an arrow function.
     *
     * @return void
     */
    public function testArrowFunctionWithAllTypes()
    {
        // Offsets are relative to the T_FN token.
        $expected     = [];
        $expected[0]  = [
            'token'               => 7,
            'name'                => '$a',
            'content'             => '?ClassName $a',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?ClassName',
            'type_hint_token'     => 5,
            'type_hint_end_token' => 5,
            'nullable_type'       => true,
            'comma_token'         => 8,
        ];
        $expected[1]  = [
            'token'               => 13,
            'name'                => '$b',
            'content'             => 'self $b',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'self',
            'type_hint_token'     => 11,
            'type_hint_end_token' => 11,
            'nullable_type'       => false,
            'comma_token'         => 14,
        ];
        $expected[2]  = [
            'token'               => 19,
            'name'                => '$c',
            'content'             => 'parent $c',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'parent',
            'type_hint_token'     => 17,
            'type_hint_end_token' => 17,
            'nullable_type'       => false,
            'comma_token'         => 20,
        ];
        $expected[3]  = [
            'token'               => 25,
            'name'                => '$d',
            'content'             => 'object $d',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'object',
            'type_hint_token'     => 23,
            'type_hint_end_token' => 23,
            'nullable_type'       => false,
            'comma_token'         => 26,
        ];
        $expected[4]  = [
            'token'               => 32,
            'name'                => '$e',
            'content'             => '?int $e',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?int',
            'type_hint_token'     => 30,
            'type_hint_end_token' => 30,
            'nullable_type'       => true,
            'comma_token'         => 33,
        ];
        $expected[5]  = [
            'token'               => 39,
            'name'                => '$f',
            'content'             => 'string &$f',
            'has_attributes'      => false,
            'pass_by_reference'   => true,
            'reference_token'     => 38,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'string',
            'type_hint_token'     => 36,
            'type_hint_end_token' => 36,
            'nullable_type'       => false,
            'comma_token'         => 40,
        ];
        $expected[6]  = [
            'token'               => 45,
            'name'                => '$g',
            'content'             => 'iterable $g',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'iterable',
            'type_hint_token'     => 43,
            'type_hint_end_token' => 43,
            'nullable_type'       => false,
            'comma_token'         => 46,
        ];
        $expected[7]  = [
            'token'               => 51,
            'name'                => '$h',
            'content'             => 'bool $h = true',
            'default'             => 'true',
            'default_token'       => 55,
            'default_equal_token' => 53,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'bool',
            'type_hint_token'     => 49,
            'type_hint_end_token' => 49,
            'nullable_type'       => false,
            'comma_token'         => 56,
        ];
        $expected[8]  = [
            'token'               => 61,
            'name'                => '$i',
            'content'             => 'callable $i = \'is_null\'',
            'default'             => "'is_null'",
            'default_token'       => 65,
            'default_equal_token' => 63,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'callable',
            'type_hint_token'     => 59,
            'type_hint_end_token' => 59,
            'nullable_type'       => false,
            'comma_token'         => 66,
        ];
        $expected[9]  = [
            'token'               => 71,
            'name'                => '$j',
            'content'             => 'float $j = 1.1',
            'default'             => '1.1',
            'default_token'       => 75,
            'default_equal_token' => 73,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'float',
            'type_hint_token'     => 69,
            'type_hint_end_token' => 69,
            'nullable_type'       => false,
            'comma_token'         => 76,
        ];
        $expected[10] = [
            'token'               => 82,
            'name'                => '$k',
            'content'             => 'array ...$k',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => true,
            'variadic_token'      => 81,
            'type_hint'           => 'array',
            'type_hint_token'     => 79,
            'type_hint_end_token' => 79,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testArrowFunctionWithAllTypes()


    /**
     * Verify handling of a declaration interlaced with whitespace and comments.
     *
     * @return void
     */
    public function testMessyDeclaration()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 25,
            'name'                => '$a',
            'content'             => '// comment
    ?\MyNS /* comment */
        \ SubCat // phpcs:ignore Standard.Cat.Sniff -- for reasons.
            \  MyClass $a',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?\MyNS\SubCat\MyClass',
            'type_hint_token'     => 9,
            'type_hint_end_token' => 23,
            'nullable_type'       => true,
            'comma_token'         => 26,
        ];
        $expected[1] = [
            'token'               => 29,
            'name'                => '$b',
            'content'             => "\$b /* comment */ = /* comment */ 'default' /* comment*/",
            'default'             => "'default' /* comment*/",
            'default_token'       => 37,
            'default_equal_token' => 33,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 40,
        ];
        $expected[2] = [
            'token'               => 62,
            'name'                => '$c',
            'content'             => '// phpcs:ignore Stnd.Cat.Sniff -- For reasons.
    ? /*comment*/
        bool // phpcs:disable Stnd.Cat.Sniff -- For reasons.
        & /*test*/ ... /* phpcs:ignore */ $c',
            'has_attributes'      => false,
            'pass_by_reference'   => true,
            'reference_token'     => 54,
            'variable_length'     => true,
            'variadic_token'      => 58,
            'type_hint'           => '?bool',
            'type_hint_token'     => 50,
            'type_hint_end_token' => 50,
            'nullable_type'       => true,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testMessyDeclaration()


    /**
     * Verify recognition of PHP8 mixed type declaration.
     *
     * @return void
     */
    public function testPHP8MixedTypeHint()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 8,
            'name'                => '$var1',
            'content'             => 'mixed &...$var1',
            'has_attributes'      => false,
            'pass_by_reference'   => true,
            'reference_token'     => 6,
            'variable_length'     => true,
            'variadic_token'      => 7,
            'type_hint'           => 'mixed',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 4,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8MixedTypeHint()


    /**
     * Verify recognition of PHP8 mixed type declaration with nullability.
     *
     * @return void
     */
    public function testPHP8MixedTypeHintNullable()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 7,
            'name'                => '$var1',
            'content'             => '?Mixed $var1',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?Mixed',
            'type_hint_token'     => 5,
            'type_hint_end_token' => 5,
            'nullable_type'       => true,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8MixedTypeHintNullable()


    /**
     * Verify recognition of type declarations using the namespace operator.
     *
     * @return void
     */
    public function testNamespaceOperatorTypeHint()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 9,
            'name'                => '$var1',
            'content'             => '?namespace\Name $var1',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?namespace\Name',
            'type_hint_token'     => 5,
            'type_hint_end_token' => 7,
            'nullable_type'       => true,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testNamespaceOperatorTypeHint()


    /**
     * Verify recognition of PHP8 union type declaration.
     *
     * @return void
     */
    public function testPHP8UnionTypesSimple()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 8,
            'name'                => '$number',
            'content'             => 'int|float $number',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'int|float',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 6,
            'nullable_type'       => false,
            'comma_token'         => 9,
        ];
        $expected[1] = [
            'token'               => 17,
            'name'                => '$obj',
            'content'             => 'self|parent &...$obj',
            'has_attributes'      => false,
            'pass_by_reference'   => true,
            'reference_token'     => 15,
            'variable_length'     => true,
            'variadic_token'      => 16,
            'type_hint'           => 'self|parent',
            'type_hint_token'     => 11,
            'type_hint_end_token' => 13,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8UnionTypesSimple()


    /**
     * Verify recognition of PHP8 union type declaration when the variable has either a spread operator or a reference.
     *
     * @return void
     */
    public function testPHP8UnionTypesWithSpreadOperatorAndReference()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 9,
            'name'                => '$paramA',
            'content'             => 'float|null &$paramA',
            'has_attributes'      => false,
            'pass_by_reference'   => true,
            'reference_token'     => 8,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'float|null',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 6,
            'nullable_type'       => false,
            'comma_token'         => 10,
        ];
        $expected[1] = [
            'token'               => 17,
            'name'                => '$paramB',
            'content'             => 'string|int ...$paramB',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => true,
            'variadic_token'      => 16,
            'type_hint'           => 'string|int',
            'type_hint_token'     => 12,
            'type_hint_end_token' => 14,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8UnionTypesWithSpreadOperatorAndReference()


    /**
     * Verify recognition of PHP8 union type declaration with a bitwise or in the default value.
     *
     * @return void
     */
    public function testPHP8UnionTypesSimpleWithBitwiseOrInDefault()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 6,
            'name'                => '$var',
            'content'             => 'int|float $var = CONSTANT_A | CONSTANT_B',
            'default'             => 'CONSTANT_A | CONSTANT_B',
            'default_token'       => 10,
            'default_equal_token' => 8,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'int|float',
            'type_hint_token'     => 2,
            'type_hint_end_token' => 4,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8UnionTypesSimpleWithBitwiseOrInDefault()


    /**
     * Verify recognition of PHP8 union type declaration with two classes.
     *
     * @return void
     */
    public function testPHP8UnionTypesTwoClasses()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 11,
            'name'                => '$var',
            'content'             => 'MyClassA|\Package\MyClassB $var',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'MyClassA|\Package\MyClassB',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 9,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8UnionTypesTwoClasses()


    /**
     * Verify recognition of PHP8 union type declaration with all base types.
     *
     * @return void
     */
    public function testPHP8UnionTypesAllBaseTypes()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 20,
            'name'                => '$var',
            'content'             => 'array|bool|callable|int|float|null|object|string $var',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'array|bool|callable|int|float|null|object|string',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 18,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8UnionTypesAllBaseTypes()


    /**
     * Verify recognition of PHP8 union type declaration with all pseudo types.
     *
     * Note: "Resource" is not a type, but seen as a class name.
     *
     * @return void
     */
    public function testPHP8UnionTypesAllPseudoTypes()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 16,
            'name'                => '$var',
            'content'             => 'false|mixed|self|parent|iterable|Resource $var',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'false|mixed|self|parent|iterable|Resource',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 14,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8UnionTypesAllPseudoTypes()


    /**
     * Verify recognition of PHP8 union type declaration with (illegal) nullability.
     *
     * @return void
     */
    public function testPHP8UnionTypesNullable()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 8,
            'name'                => '$number',
            'content'             => '?int|float $number',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?int|float',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 6,
            'nullable_type'       => true,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8UnionTypesNullable()


    /**
     * Verify recognition of PHP8 type declaration with (illegal) single type null.
     *
     * @return void
     */
    public function testPHP8PseudoTypeNull()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 6,
            'name'                => '$var',
            'content'             => 'null $var = null',
            'default'             => 'null',
            'default_token'       => 10,
            'default_equal_token' => 8,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'null',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 4,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8PseudoTypeNull()


    /**
     * Verify recognition of PHP8 type declaration with (illegal) single type false.
     *
     * @return void
     */
    public function testPHP8PseudoTypeFalse()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 6,
            'name'                => '$var',
            'content'             => 'false $var = false',
            'default'             => 'false',
            'default_token'       => 10,
            'default_equal_token' => 8,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'false',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 4,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8PseudoTypeFalse()


    /**
     * Verify recognition of PHP8 type declaration with (illegal) type false combined with type bool.
     *
     * @return void
     */
    public function testPHP8PseudoTypeFalseAndBool()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 8,
            'name'                => '$var',
            'content'             => 'bool|false $var = false',
            'default'             => 'false',
            'default_token'       => 12,
            'default_equal_token' => 10,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'bool|false',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 6,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8PseudoTypeFalseAndBool()


    /**
     * Verify recognition of PHP8 type declaration with (illegal) type object combined with a class name.
     *
     * @return void
     */
    public function testPHP8ObjectAndClass()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 8,
            'name'                => '$var',
            'content'             => 'object|ClassName $var',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'object|ClassName',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 6,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8ObjectAndClass()


    /**
     * Verify recognition of PHP8 type declaration with (illegal) type iterable combined with array/Traversable.
     *
     * @return void
     */
    public function testPHP8PseudoTypeIterableAndArray()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 10,
            'name'                => '$var',
            'content'             => 'iterable|array|Traversable $var',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'iterable|array|Traversable',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 8,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8PseudoTypeIterableAndArray()


    /**
     * Verify recognition of PHP8 type declaration with (illegal) duplicate types.
     *
     * @return void
     */
    public function testPHP8DuplicateTypeInUnionWhitespaceAndComment()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 17,
            'name'                => '$var',
            'content'             => 'int | string /*comment*/ | INT $var',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'int|string|INT',
            'type_hint_token'     => 5,
            'type_hint_end_token' => 15,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8DuplicateTypeInUnionWhitespaceAndComment()


    /**
     * Verify recognition of PHP8 constructor property promotion without type declaration, with defaults.
     *
     * @return void
     */
    public function testPHP8ConstructorPropertyPromotionNoTypes()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 8,
            'name'                => '$x',
            'content'             => 'public $x = 0.0',
            'default'             => '0.0',
            'default_token'       => 12,
            'default_equal_token' => 10,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'property_visibility' => 'public',
            'visibility_token'    => 6,
            'property_readonly'   => false,
            'comma_token'         => 13,
        ];
        $expected[1] = [
            'token'               => 18,
            'name'                => '$y',
            'content'             => 'protected $y = \'\'',
            'default'             => "''",
            'default_token'       => 22,
            'default_equal_token' => 20,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'property_visibility' => 'protected',
            'visibility_token'    => 16,
            'property_readonly'   => false,
            'comma_token'         => 23,
        ];
        $expected[2] = [
            'token'               => 28,
            'name'                => '$z',
            'content'             => 'private $z = null',
            'default'             => 'null',
            'default_token'       => 32,
            'default_equal_token' => 30,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'property_visibility' => 'private',
            'visibility_token'    => 26,
            'property_readonly'   => false,
            'comma_token'         => 33,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8ConstructorPropertyPromotionNoTypes()


    /**
     * Verify recognition of PHP8 constructor property promotion with type declarations.
     *
     * @return void
     */
    public function testPHP8ConstructorPropertyPromotionWithTypes()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 10,
            'name'                => '$x',
            'content'             => 'protected float|int $x',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'float|int',
            'type_hint_token'     => 6,
            'type_hint_end_token' => 8,
            'nullable_type'       => false,
            'property_visibility' => 'protected',
            'visibility_token'    => 4,
            'property_readonly'   => false,
            'comma_token'         => 11,
        ];
        $expected[1] = [
            'token'               => 19,
            'name'                => '$y',
            'content'             => 'public ?string &$y = \'test\'',
            'default'             => "'test'",
            'default_token'       => 23,
            'default_equal_token' => 21,
            'has_attributes'      => false,
            'pass_by_reference'   => true,
            'reference_token'     => 18,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?string',
            'type_hint_token'     => 16,
            'type_hint_end_token' => 16,
            'nullable_type'       => true,
            'property_visibility' => 'public',
            'visibility_token'    => 13,
            'property_readonly'   => false,
            'comma_token'         => 24,
        ];
        $expected[2] = [
            'token'               => 30,
            'name'                => '$z',
            'content'             => 'private mixed $z',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'mixed',
            'type_hint_token'     => 28,
            'type_hint_end_token' => 28,
            'nullable_type'       => false,
            'property_visibility' => 'private',
            'visibility_token'    => 26,
            'property_readonly'   => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8ConstructorPropertyPromotionWithTypes()


    /**
     * Verify recognition of PHP8 constructor with both property promotion as well as normal parameters.
     *
     * @return void
     */
    public function testPHP8ConstructorPropertyPromotionAndNormalParam()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 8,
            'name'                => '$promotedProp',
            'content'             => 'public int $promotedProp',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'int',
            'type_hint_token'     => 6,
            'type_hint_end_token' => 6,
            'nullable_type'       => false,
            'property_visibility' => 'public',
            'visibility_token'    => 4,
            'property_readonly'   => false,
            'comma_token'         => 9,
        ];
        $expected[1] = [
            'token'               => 14,
            'name'                => '$normalArg',
            'content'             => '?int $normalArg',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?int',
            'type_hint_token'     => 12,
            'type_hint_end_token' => 12,
            'nullable_type'       => true,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8ConstructorPropertyPromotionAndNormalParam()


    /**
     * Verify recognition of PHP8 constructor with property promotion using PHP 8.1 readonly keyword.
     *
     * @return void
     */
    public function testPHP81ConstructorPropertyPromotionWithReadOnly()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 11,
            'name'                => '$promotedProp',
            'content'             => 'public readonly ?int $promotedProp',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?int',
            'type_hint_token'     => 9,
            'type_hint_end_token' => 9,
            'nullable_type'       => true,
            'property_visibility' => 'public',
            'visibility_token'    => 4,
            'property_readonly'   => true,
            'readonly_token'      => 6,
            'comma_token'         => 12,
        ];
        $expected[1] = [
            'token'               => 23,
            'name'                => '$promotedToo',
            'content'             => 'ReadOnly private string|bool &$promotedToo',
            'has_attributes'      => false,
            'pass_by_reference'   => true,
            'reference_token'     => 22,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'string|bool',
            'type_hint_token'     => 18,
            'type_hint_end_token' => 20,
            'nullable_type'       => false,
            'property_visibility' => 'private',
            'visibility_token'    => 16,
            'property_readonly'   => true,
            'readonly_token'      => 14,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP81ConstructorPropertyPromotionWithReadOnly()


    /**
     * Verify recognition of PHP8 constructor with property promotion using PHP 8.1 readonly keyword
     * without a property type.
     *
     * @return void
     */
    public function testPHP81ConstructorPropertyPromotionWithReadOnlyNoTypeDeclaration()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 8,
            'name'                => '$promotedProp',
            'content'             => 'public readonly $promotedProp',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'property_visibility' => 'public',
            'visibility_token'    => 4,
            'property_readonly'   => true,
            'readonly_token'      => 6,
            'comma_token'         => 9,
        ];
        $expected[1] = [
            'token'               => 16,
            'name'                => '$promotedToo',
            'content'             => 'ReadOnly private &$promotedToo',
            'has_attributes'      => false,
            'pass_by_reference'   => true,
            'reference_token'     => 15,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'property_visibility' => 'private',
            'visibility_token'    => 13,
            'property_readonly'   => true,
            'readonly_token'      => 11,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP81ConstructorPropertyPromotionWithReadOnlyNoTypeDeclaration()


    /**
     * Verify recognition of PHP8 constructor with property promotion using PHP 8.1 readonly
     * keyword without explicit visibility.
     *
     * @return void
     */
    public function testPHP81ConstructorPropertyPromotionWithOnlyReadOnly()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 10,
            'name'                => '$promotedProp',
            'content'             => 'readonly Foo&Bar $promotedProp',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'Foo&Bar',
            'type_hint_token'     => 6,
            'type_hint_end_token' => 8,
            'nullable_type'       => false,
            'property_visibility' => 'public',
            'visibility_token'    => false,
            'property_readonly'   => true,
            'readonly_token'      => 4,
            'comma_token'         => 11,
        ];
        $expected[1] = [
            'token'               => 18,
            'name'                => '$promotedToo',
            'content'             => 'readonly ?bool $promotedToo',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?bool',
            'type_hint_token'     => 16,
            'type_hint_end_token' => 16,
            'nullable_type'       => true,
            'property_visibility' => 'public',
            'visibility_token'    => false,
            'property_readonly'   => true,
            'readonly_token'      => 13,
            'comma_token'         => 19,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP81ConstructorPropertyPromotionWithOnlyReadOnly()


    /**
     * Verify recognition of PHP8 constructor with property promotion using PHP 8.4 asymmetric visibility.
     *
     * @return void
     */
    public function testPHP84ConstructorPropertyPromotionWithAsymVisibility()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'                => 12,
            'name'                 => '$book',
            'content'              => 'protected(set) string|Book $book',
            'has_attributes'       => false,
            'pass_by_reference'    => false,
            'reference_token'      => false,
            'variable_length'      => false,
            'variadic_token'       => false,
            'type_hint'            => 'string|Book',
            'type_hint_token'      => 8,
            'type_hint_end_token'  => 10,
            'nullable_type'        => false,
            'property_visibility'  => 'public',
            'visibility_token'     => false,
            'set_visibility'       => 'protected(set)',
            'set_visibility_token' => 6,
            'property_readonly'    => false,
            'comma_token'          => 13,
        ];
        $expected[1] = [
            'token'                => 23,
            'name'                 => '$publisher',
            'content'              => 'public private(set) ?Publisher $publisher',
            'has_attributes'       => false,
            'pass_by_reference'    => false,
            'reference_token'      => false,
            'variable_length'      => false,
            'variadic_token'       => false,
            'type_hint'            => '?Publisher',
            'type_hint_token'      => 21,
            'type_hint_end_token'  => 21,
            'nullable_type'        => true,
            'property_visibility'  => 'public',
            'visibility_token'     => 16,
            'set_visibility'       => 'private(set)',
            'set_visibility_token' => 18,
            'property_readonly'    => false,
            'comma_token'          => 24,
        ];
        $expected[2] = [
            'token'                => 33,
            'name'                 => '$author',
            'content'              => 'Private(set) PROTECTED Author $author',
            'has_attributes'       => false,
            'pass_by_reference'    => false,
            'reference_token'      => false,
            'variable_length'      => false,
            'variadic_token'       => false,
            'type_hint'            => 'Author',
            'type_hint_token'      => 31,
            'type_hint_end_token'  => 31,
            'nullable_type'        => false,
            'property_visibility'  => 'PROTECTED',
            'visibility_token'     => 29,
            'set_visibility'       => 'Private(set)',
            'set_visibility_token' => 27,
            'property_readonly'    => false,
            'comma_token'          => 34,
        ];
        $expected[3] = [
            'token'                => 43,
            'name'                 => '$pubYear',
            'content'              => 'readonly public(set) int $pubYear',
            'has_attributes'       => false,
            'pass_by_reference'    => false,
            'reference_token'      => false,
            'variable_length'      => false,
            'variadic_token'       => false,
            'type_hint'            => 'int',
            'type_hint_token'      => 41,
            'type_hint_end_token'  => 41,
            'nullable_type'        => false,
            'property_visibility'  => 'public',
            'visibility_token'     => false,
            'set_visibility'       => 'public(set)',
            'set_visibility_token' => 39,
            'property_readonly'    => true,
            'readonly_token'       => 37,
            'comma_token'          => 44,
        ];
        $expected[4] = [
            'token'                => 49,
            'name'                 => '$illegalMissingType',
            'content'              => 'protected(set) $illegalMissingType',
            'has_attributes'       => false,
            'pass_by_reference'    => false,
            'reference_token'      => false,
            'variable_length'      => false,
            'variadic_token'       => false,
            'type_hint'            => '',
            'type_hint_token'      => false,
            'type_hint_end_token'  => false,
            'nullable_type'        => false,
            'property_visibility'  => 'public',
            'visibility_token'     => false,
            'set_visibility'       => 'protected(set)',
            'set_visibility_token' => 47,
            'property_readonly'    => false,
            'comma_token'          => 50,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP84ConstructorPropertyPromotionWithAsymVisibility()


    /**
     * Verify behaviour when a non-constructor function uses PHP 8 property promotion syntax.
     *
     * @return void
     */
    public function testPHP8ConstructorPropertyPromotionGlobalFunction()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 6,
            'name'                => '$x',
            'content'             => 'private $x',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'property_visibility' => 'private',
            'visibility_token'    => 4,
            'property_readonly'   => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8ConstructorPropertyPromotionGlobalFunction()


    /**
     * Verify behaviour when an abstract constructor uses PHP 8 property promotion syntax.
     *
     * @return void
     */
    public function testPHP8ConstructorPropertyPromotionAbstractMethod()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 8,
            'name'                => '$y',
            'content'             => 'public callable $y',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'callable',
            'type_hint_token'     => 6,
            'type_hint_end_token' => 6,
            'nullable_type'       => false,
            'property_visibility' => 'public',
            'visibility_token'    => 4,
            'property_readonly'   => false,
            'comma_token'         => 9,
        ];
        $expected[1] = [
            'token'               => 14,
            'name'                => '$x',
            'content'             => 'private ...$x',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => true,
            'variadic_token'      => 13,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'property_visibility' => 'private',
            'visibility_token'    => 11,
            'property_readonly'   => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8ConstructorPropertyPromotionAbstractMethod()


    /**
     * Verify and document behaviour when there are comments within a parameter declaration.
     *
     * @return void
     */
    public function testCommentsInParameter()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 19,
            'name'                => '$param',
            'content'             => '// Leading comment.
    ?MyClass /*-*/ & /*-*/.../*-*/ $param /*-*/ = /*-*/ \'default value\' . /*-*/ \'second part\' // Trailing comment.',
            'default'             => '\'default value\' . /*-*/ \'second part\' // Trailing comment.',
            'default_token'       => 27,
            'default_equal_token' => 23,
            'has_attributes'      => false,
            'pass_by_reference'   => true,
            'reference_token'     => 13,
            'variable_length'     => true,
            'variadic_token'      => 16,
            'type_hint'           => '?MyClass',
            'type_hint_token'     => 9,
            'type_hint_end_token' => 9,
            'nullable_type'       => true,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testCommentsInParameter()


    /**
     * Verify behaviour when parameters have attributes attached.
     *
     * @return void
     */
    public function testParameterAttributesInFunctionDeclaration()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 17,
            'name'                => '$constructorPropPromTypedParamSingleAttribute',
            'content'             => '#[\MyExample\MyAttribute] private string $constructorPropPromTypedParamSingleAttribute',
            'has_attributes'      => true,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'string',
            'type_hint_token'     => 15,
            'type_hint_end_token' => 15,
            'nullable_type'       => false,
            'property_visibility' => 'private',
            'visibility_token'    => 13,
            'property_readonly'   => false,
            'comma_token'         => 18,
        ];
        $expected[1] = [
            'token'               => 39,
            'name'                => '$typedParamSingleAttribute',
            'content'             => '#[MyAttr([1, 2])]
        Type|false
        $typedParamSingleAttribute',
            'has_attributes'      => true,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'Type|false',
            'type_hint_token'     => 34,
            'type_hint_end_token' => 36,
            'nullable_type'       => false,
            'comma_token'         => 40,
        ];
        $expected[2] = [
            'token'               => 59,
            'name'                => '$nullableTypedParamMultiAttribute',
            'content'             => '#[MyAttribute(1234), MyAttribute(5678)] ?int $nullableTypedParamMultiAttribute',
            'has_attributes'      => true,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?int',
            'type_hint_token'     => 57,
            'type_hint_end_token' => 57,
            'nullable_type'       => true,
            'comma_token'         => 60,
        ];
        $expected[3] = [
            'token'               => 74,
            'name'                => '$nonTypedParamTwoAttributes',
            'content'             => '#[WithoutArgument] #[SingleArgument(0)] $nonTypedParamTwoAttributes',
            'has_attributes'      => true,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 75,
        ];
        $expected[4] = [
            'token'               => 95,
            'name'                => '$otherParam',
            'content'             => '#[MyAttribute(array("key" => "value"))]
        &...$otherParam',
            'has_attributes'      => true,
            'pass_by_reference'   => true,
            'reference_token'     => 93,
            'variable_length'     => true,
            'variadic_token'      => 94,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 96,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testParameterAttributesInFunctionDeclaration()


    /**
     * Verify recognition of PHP8.1 intersection type declaration.
     *
     * @return void
     */
    public function testPHP8IntersectionTypes()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 8,
            'name'                => '$obj1',
            'content'             => 'Foo&Bar $obj1',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'Foo&Bar',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 6,
            'nullable_type'       => false,
            'comma_token'         => 9,
        ];
        $expected[1] = [
            'token'               => 15,
            'name'                => '$obj2',
            'content'             => 'Boo&Bar $obj2',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'Boo&Bar',
            'type_hint_token'     => 11,
            'type_hint_end_token' => 13,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8IntersectionTypes()


    /**
     * Verify recognition of PHP8.1 intersection type declaration when the variable
     * has either a spread operator or a reference.
     *
     * @return void
     */
    public function testPHP81IntersectionTypesWithSpreadOperatorAndReference()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 9,
            'name'                => '$paramA',
            'content'             => 'Boo&Bar &$paramA',
            'has_attributes'      => false,
            'pass_by_reference'   => true,
            'reference_token'     => 8,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'Boo&Bar',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 6,
            'nullable_type'       => false,
            'comma_token'         => 10,
        ];
        $expected[1] = [
            'token'               => 17,
            'name'                => '$paramB',
            'content'             => 'Foo&Bar ...$paramB',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => true,
            'variadic_token'      => 16,
            'type_hint'           => 'Foo&Bar',
            'type_hint_token'     => 12,
            'type_hint_end_token' => 14,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP81IntersectionTypesWithSpreadOperatorAndReference()


    /**
     * Verify recognition of PHP8.1 intersection type declaration with more types.
     *
     * @return void
     */
    public function testPHP81MoreIntersectionTypes()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 16,
            'name'                => '$var',
            'content'             => 'MyClassA&\Package\MyClassB&\Package\MyClassC $var',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'MyClassA&\Package\MyClassB&\Package\MyClassC',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 14,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP81MoreIntersectionTypes()


    /**
     * Verify recognition of PHP8.1 intersection type declaration with illegal simple types.
     *
     * @return void
     */
    public function testPHP81IllegalIntersectionTypes()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 7,
            'name'                => '$numeric_string',
            'content'             => 'string&int $numeric_string',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'string&int',
            'type_hint_token'     => 3,
            'type_hint_end_token' => 5,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP81IllegalIntersectionTypes()


    /**
     * Verify recognition of PHP8.1 intersection type declaration with (illegal) nullability.
     *
     * @return void
     */
    public function testPHP81NullableIntersectionTypes()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 8,
            'name'                => '$object',
            'content'             => '?Foo&Bar $object',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?Foo&Bar',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 6,
            'nullable_type'       => true,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP81NullableIntersectionTypes()


    /**
     * Verify recognition of PHP 8.2 stand-alone `true` type.
     *
     * @return void
     */
    public function testPHP82PseudoTypeTrue()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 7,
            'name'                => '$var',
            'content'             => '?true $var = true',
            'default'             => 'true',
            'default_token'       => 11,
            'default_equal_token' => 9,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?true',
            'type_hint_token'     => 5,
            'type_hint_end_token' => 5,
            'nullable_type'       => true,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP82PseudoTypeTrue()


    /**
     * Verify recognition of PHP 8.2 type declaration with (illegal) type false combined with type true.
     *
     * @return void
     */
    public function testPHP82PseudoTypeFalseAndTrue()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 8,
            'name'                => '$var',
            'content'             => 'true|false $var = true',
            'default'             => 'true',
            'default_token'       => 12,
            'default_equal_token' => 10,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'true|false',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 6,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP82PseudoTypeFalseAndTrue()


    /**
     * Verify behaviour when the default value uses the "new" keyword, as is allowed per PHP 8.1.
     *
     * @return void
     */
    public function testPHP81NewInInitializers()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 8,
            'name'                => '$new',
            'content'             => 'TypeA $new = new TypeA(self::CONST_VALUE)',
            'default'             => 'new TypeA(self::CONST_VALUE)',
            'default_token'       => 12,
            'default_equal_token' => 10,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'TypeA',
            'type_hint_token'     => 6,
            'type_hint_end_token' => 6,
            'nullable_type'       => false,
            'comma_token'         => 20,
        ];
        $expected[1] = [
            'token'               => 28,
            'name'                => '$newToo',
            'content'             => '\Package\TypeB $newToo = new \Package\TypeB(10, \'string\')',
            'default'             => "new \Package\TypeB(10, 'string')",
            'default_token'       => 32,
            'default_equal_token' => 30,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '\Package\TypeB',
            'type_hint_token'     => 23,
            'type_hint_end_token' => 26,
            'nullable_type'       => false,
            'comma_token'         => 44,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP81NewInInitializers()


    /**
     * Verify recognition of 8.2 DNF parameter type declarations.
     *
     * @return void
     */
    public function testPHP82DNFTypes()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 21,
            'name'                => '$obj1',
            'content'             => '#[MyAttribute]
    false|(Foo&Bar)|true $obj1',
            'has_attributes'      => true,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'false|(Foo&Bar)|true',
            'type_hint_token'     => 11,
            'type_hint_end_token' => 19,
            'nullable_type'       => false,
            'comma_token'         => 22,
        ];
        $expected[1] = [
            'token'               => 41,
            'name'                => '$obj2',
            'content'             => '(\Boo&\Pck\Bar)|(Boo&Baz) $obj2 = new Boo()',
            'default'             => 'new Boo()',
            'default_token'       => 45,
            'default_equal_token' => 43,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '(\Boo&\Pck\Bar)|(Boo&Baz)',
            'type_hint_token'     => 25,
            'type_hint_end_token' => 39,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP82DNFTypes()


    /**
     * Verify recognition of PHP 8.2 DNF parameter type declarations when the variable
     * has either a spread operator or a reference.
     *
     * @return void
     */
    public function testPHP82DNFTypesWithSpreadOperatorAndReference()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 13,
            'name'                => '$paramA',
            'content'             => '(Countable&MeMe)|iterable &$paramA',
            'has_attributes'      => false,
            'pass_by_reference'   => true,
            'reference_token'     => 12,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '(Countable&MeMe)|iterable',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 10,
            'nullable_type'       => false,
            'comma_token'         => 14,
        ];
        $expected[1] = [
            'token'               => 25,
            'name'                => '$paramB',
            'content'             => 'true|(Foo&Bar) ...$paramB',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => true,
            'variadic_token'      => 24,
            'type_hint'           => 'true|(Foo&Bar)',
            'type_hint_token'     => 16,
            'type_hint_end_token' => 22,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP82DNFTypesWithSpreadOperatorAndReference()


    /**
     * Verify recognition of PHP 8.2 DNF parameter type declarations using the nullability operator (not allowed).
     *
     * @return void
     */
    public function testPHP82DNFTypesIllegalNullable()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 27,
            'name'                => '$var',
            'content'             => '? ( MyClassA & /*comment*/ \Package\MyClassB & \Package\MyClassC ) $var',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?(MyClassA&\Package\MyClassB&\Package\MyClassC)',
            'type_hint_token'     => 5,
            'type_hint_end_token' => 25,
            'nullable_type'       => true,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP82DNFTypesIllegalNullable()


    /**
     * Verify recognition of PHP 8.2 DNF parameter type declarations in an arrow function.
     *
     * @return void
     */
    public function testPHP82DNFTypesInArrow()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 12,
            'name'                => '$range',
            'content'             => '(Hi&Ho)|FALSE &...$range',
            'has_attributes'      => false,
            'pass_by_reference'   => true,
            'reference_token'     => 10,
            'variable_length'     => true,
            'variadic_token'      => 11,
            'type_hint'           => '(Hi&Ho)|FALSE',
            'type_hint_token'     => 2,
            'type_hint_end_token' => 8,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP82DNFTypesInArrow()


    /**
     * Verify handling of a closure.
     *
     * @return void
     */
    public function testClosure()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 3,
            'name'                => '$a',
            'content'             => '$a = \'test\'',
            'default'             => "'test'",
            'default_token'       => 7,
            'default_equal_token' => 5,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testClosure()


    /**
     * Verify handling of a closure T_USE token correctly.
     *
     * @return void
     */
    public function testClosureUse()
    {
        // Offsets are relative to the T_USE token.
        $expected    = [];
        $expected[0] = [
            'token'               => 3,
            'name'                => '$foo',
            'content'             => '$foo',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 4,
        ];
        $expected[1] = [
            'token'               => 6,
            'name'                => '$bar',
            'content'             => '$bar',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected, [T_USE]);

    }//end testClosureUse()


    /**
     * Verify handling of a closure T_USE token with variables imported by reference.
     *
     * @return void
     */
    public function testClosureUseWithReference()
    {
        // Offsets are relative to the T_USE token.
        $expected    = [];
        $expected[0] = [
            'token'               => 4,
            'name'                => '$foo',
            'content'             => '&$foo',
            'has_attributes'      => false,
            'pass_by_reference'   => true,
            'reference_token'     => 3,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 5,
        ];
        $expected[1] = [
            'token'               => 8,
            'name'                => '$bar',
            'content'             => '&$bar',
            'has_attributes'      => false,
            'pass_by_reference'   => true,
            'reference_token'     => 7,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected, [T_USE]);

    }//end testClosureUseWithReference()


    /**
     * Verify function declarations with trailing commas are handled correctly.
     *
     * @return void
     */
    public function testFunctionParamListWithTrailingComma()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 9,
            'name'                => '$foo',
            'content'             => '?string $foo  /*comment*/',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?string',
            'type_hint_token'     => 7,
            'type_hint_end_token' => 7,
            'nullable_type'       => true,
            'comma_token'         => 13,
        ];
        $expected[1] = [
            'token'               => 16,
            'name'                => '$bar',
            'content'             => '$bar = 0',
            'default'             => '0',
            'default_token'       => 20,
            'default_equal_token' => 18,
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 21,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testFunctionParamListWithTrailingComma()


    /**
     * Verify closure declarations with trailing commas are handled correctly.
     *
     * @return void
     */
    public function testClosureParamListWithTrailingComma()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected    = [];
        $expected[0] = [
            'token'               => 4,
            'name'                => '$foo',
            'content'             => '$foo',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 5,
        ];
        $expected[1] = [
            'token'               => 8,
            'name'                => '$bar',
            'content'             => '$bar',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 9,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testClosureParamListWithTrailingComma()


    /**
     * Verify arrow function declarations with trailing commas are handled correctly.
     *
     * @return void
     */
    public function testArrowFunctionParamListWithTrailingComma()
    {
        // Offsets are relative to the T_FN token.
        $expected    = [];
        $expected[0] = [
            'token'               => 6,
            'name'                => '$a',
            'content'             => '?int $a',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?int',
            'type_hint_token'     => 4,
            'type_hint_end_token' => 4,
            'nullable_type'       => true,
            'comma_token'         => 8,
        ];
        $expected[1] = [
            'token'               => 11,
            'name'                => '$b',
            'content'             => '...$b',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => true,
            'variadic_token'      => 10,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 12,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testArrowFunctionParamListWithTrailingComma()


    /**
     * Verify closure T_USE statements with trailing commas are handled correctly.
     *
     * @return void
     */
    public function testClosureUseWithTrailingComma()
    {
        // Offsets are relative to the T_USE token.
        $expected    = [];
        $expected[0] = [
            'token'               => 4,
            'name'                => '$foo',
            'content'             => '$foo  /*comment*/',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 8,
        ];
        $expected[1] = [
            'token'               => 11,
            'name'                => '$bar',
            'content'             => '$bar',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 12,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected, [T_USE]);

    }//end testClosureUseWithTrailingComma()


    /**
     * Test helper.
     *
     * @param string                                     $commentString The comment which preceeds the test.
     * @param array<int, array<string, int|string|bool>> $expected      The expected function output.
     * @param int|string|array<int|string>               $targetType    Optional. The token type to search for after $marker.
     *                                                                  Defaults to the function/closure/arrow tokens.
     *
     * @return void
     */
    private function getMethodParametersTestHelper($commentString, $expected, $targetType=[T_FUNCTION, T_CLOSURE, T_FN])
    {
        $target = $this->getTargetToken($commentString, $targetType);
        $found  = self::$phpcsFile->getMethodParameters($target);

        // Convert offsets to absolute positions in the token stream.
        foreach ($expected as $key => $param) {
            $expected[$key]['token'] += $target;

            if (is_int($param['reference_token']) === true) {
                $expected[$key]['reference_token'] += $target;
            }

            if (is_int($param['variadic_token']) === true) {
                $expected[$key]['variadic_token'] += $target;
            }

            if (is_int($param['type_hint_token']) === true) {
                $expected[$key]['type_hint_token'] += $target;
            }

            if (is_int($param['type_hint_end_token']) === true) {
                $expected[$key]['type_hint_end_token'] += $target;
            }

            if (is_int($param['comma_token']) === true) {
                $expected[$key]['comma_token'] += $target;
            }

            if (isset($param['default_token']) === true) {
                $expected[$key]['default_token'] += $target;
            }

            if (isset($param['default_equal_token']) === true) {
                $expected[$key]['default_equal_token'] += $target;
            }

            if (isset($param['visibility_token']) === true && is_int($param['visibility_token']) === true) {
                $expected[$key]['visibility_token'] += $target;
            }

            if (isset($param['set_visibility_token']) === true && is_int($param['set_visibility_token']) === true) {
                $expected[$key]['set_visibility_token'] += $target;
            }

            if (isset($param['readonly_token']) === true) {
                $expected[$key]['readonly_token'] += $target;
            }
        }//end foreach

        $this->assertSame($expected, $found);

    }//end getMethodParametersTestHelper()


}//end class
