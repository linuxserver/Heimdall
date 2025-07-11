<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File::getMethodProperties method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Files\File;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

/**
 * Tests for the \PHP_CodeSniffer\Files\File::getMethodProperties method.
 *
 * @covers \PHP_CodeSniffer\Files\File::getMethodProperties
 */
final class GetMethodPropertiesTest extends AbstractMethodUnitTest
{


    /**
     * Test receiving an expected exception when a non function token is passed.
     *
     * @param string                       $commentString   The comment which preceeds the test.
     * @param string|int|array<int|string> $targetTokenType The token type to search for after $commentString.
     *
     * @dataProvider dataNotAFunctionException
     *
     * @return void
     */
    public function testNotAFunctionException($commentString, $targetTokenType)
    {
        $this->expectRunTimeException('$stackPtr must be of type T_FUNCTION or T_CLOSURE or T_FN');

        $next = $this->getTargetToken($commentString, $targetTokenType);
        self::$phpcsFile->getMethodProperties($next);

    }//end testNotAFunctionException()


    /**
     * Data Provider.
     *
     * @see testNotAFunctionException() For the array format.
     *
     * @return array<string, array<string, string|int|array<int|string>>>
     */
    public static function dataNotAFunctionException()
    {
        return [
            'return'                             => [
                'commentString'   => '/* testNotAFunction */',
                'targetTokenType' => T_RETURN,
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

    }//end dataNotAFunctionException()


    /**
     * Test a basic function.
     *
     * @return void
     */
    public function testBasicFunction()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '',
            'return_type_token'     => false,
            'return_type_end_token' => false,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testBasicFunction()


    /**
     * Test a function with a return type.
     *
     * @return void
     */
    public function testReturnFunction()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'array',
            'return_type_token'     => 11,
            'return_type_end_token' => 11,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testReturnFunction()


    /**
     * Test a closure used as a function argument.
     *
     * @return void
     */
    public function testNestedClosure()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'int',
            'return_type_token'     => 8,
            'return_type_end_token' => 8,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testNestedClosure()


    /**
     * Test a basic method.
     *
     * @return void
     */
    public function testBasicMethod()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '',
            'return_type_token'     => false,
            'return_type_end_token' => false,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testBasicMethod()


    /**
     * Test a private static method.
     *
     * @return void
     */
    public function testPrivateStaticMethod()
    {
        $expected = [
            'scope'                 => 'private',
            'scope_specified'       => true,
            'return_type'           => '',
            'return_type_token'     => false,
            'return_type_end_token' => false,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => true,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPrivateStaticMethod()


    /**
     * Test a basic final method.
     *
     * @return void
     */
    public function testFinalMethod()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => true,
            'return_type'           => '',
            'return_type_token'     => false,
            'return_type_end_token' => false,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => true,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testFinalMethod()


    /**
     * Test a protected method with a return type.
     *
     * @return void
     */
    public function testProtectedReturnMethod()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'protected',
            'scope_specified'       => true,
            'return_type'           => 'int',
            'return_type_token'     => 8,
            'return_type_end_token' => 8,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testProtectedReturnMethod()


    /**
     * Test a public method with a return type.
     *
     * @return void
     */
    public function testPublicReturnMethod()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => true,
            'return_type'           => 'array',
            'return_type_token'     => 7,
            'return_type_end_token' => 7,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPublicReturnMethod()


    /**
     * Test a public method with a nullable return type.
     *
     * @return void
     */
    public function testNullableReturnMethod()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => true,
            'return_type'           => '?array',
            'return_type_token'     => 8,
            'return_type_end_token' => 8,
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testNullableReturnMethod()


    /**
     * Test a public method with a nullable return type.
     *
     * @return void
     */
    public function testMessyNullableReturnMethod()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => true,
            'return_type'           => '?array',
            'return_type_token'     => 18,
            'return_type_end_token' => 18,
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testMessyNullableReturnMethod()


    /**
     * Test a method with a namespaced return type.
     *
     * @return void
     */
    public function testReturnNamespace()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '\MyNamespace\MyClass',
            'return_type_token'     => 7,
            'return_type_end_token' => 10,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testReturnNamespace()


    /**
     * Test a method with a messy namespaced return type.
     *
     * @return void
     */
    public function testReturnMultilineNamespace()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '\MyNamespace\MyClass\Foo',
            'return_type_token'     => 7,
            'return_type_end_token' => 23,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testReturnMultilineNamespace()


    /**
     * Test a method with an unqualified named return type.
     *
     * @return void
     */
    public function testReturnUnqualifiedName()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'private',
            'scope_specified'       => true,
            'return_type'           => '?MyClass',
            'return_type_token'     => 8,
            'return_type_end_token' => 8,
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testReturnUnqualifiedName()


    /**
     * Test a method with a partially qualified namespaced return type.
     *
     * @return void
     */
    public function testReturnPartiallyQualifiedName()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'Sub\Level\MyClass',
            'return_type_token'     => 7,
            'return_type_end_token' => 11,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testReturnPartiallyQualifiedName()


    /**
     * Test a basic abstract method.
     *
     * @return void
     */
    public function testAbstractMethod()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '',
            'return_type_token'     => false,
            'return_type_end_token' => false,
            'nullable_return_type'  => false,
            'is_abstract'           => true,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => false,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testAbstractMethod()


    /**
     * Test an abstract method with a return type.
     *
     * @return void
     */
    public function testAbstractReturnMethod()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'protected',
            'scope_specified'       => true,
            'return_type'           => 'bool',
            'return_type_token'     => 7,
            'return_type_end_token' => 7,
            'nullable_return_type'  => false,
            'is_abstract'           => true,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => false,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testAbstractReturnMethod()


    /**
     * Test a basic interface method.
     *
     * @return void
     */
    public function testInterfaceMethod()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '',
            'return_type_token'     => false,
            'return_type_end_token' => false,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => false,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testInterfaceMethod()


    /**
     * Test a static arrow function.
     *
     * @return void
     */
    public function testArrowFunction()
    {
        // Offsets are relative to the T_FN token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'int',
            'return_type_token'     => 9,
            'return_type_end_token' => 9,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => true,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testArrowFunction()


    /**
     * Test a function with return type "static".
     *
     * @return void
     */
    public function testReturnTypeStatic()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'private',
            'scope_specified'       => true,
            'return_type'           => 'static',
            'return_type_token'     => 7,
            'return_type_end_token' => 7,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testReturnTypeStatic()


    /**
     * Test a function with return type "?static".
     *
     * @return void
     */
    public function testReturnTypeNullableStatic()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '?static',
            'return_type_token'     => 8,
            'return_type_end_token' => 8,
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testReturnTypeNullableStatic()


    /**
     * Test a function with return type "mixed".
     *
     * @return void
     */
    public function testPHP8MixedTypeHint()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'mixed',
            'return_type_token'     => 7,
            'return_type_end_token' => 7,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8MixedTypeHint()


    /**
     * Test a function with return type "mixed" and nullability.
     *
     * @return void
     */
    public function testPHP8MixedTypeHintNullable()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '?mixed',
            'return_type_token'     => 8,
            'return_type_end_token' => 8,
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8MixedTypeHintNullable()


    /**
     * Test a function with return type using the namespace operator.
     *
     * @return void
     */
    public function testNamespaceOperatorTypeHint()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '?namespace\Name',
            'return_type_token'     => 9,
            'return_type_end_token' => 11,
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testNamespaceOperatorTypeHint()


    /**
     * Verify recognition of PHP8 union type declaration.
     *
     * @return void
     */
    public function testPHP8UnionTypesSimple()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'int|float',
            'return_type_token'     => 9,
            'return_type_end_token' => 11,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8UnionTypesSimple()


    /**
     * Verify recognition of PHP8 union type declaration with two classes.
     *
     * @return void
     */
    public function testPHP8UnionTypesTwoClasses()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'MyClassA|\Package\MyClassB',
            'return_type_token'     => 6,
            'return_type_end_token' => 11,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8UnionTypesTwoClasses()


    /**
     * Verify recognition of PHP8 union type declaration with all base types.
     *
     * @return void
     */
    public function testPHP8UnionTypesAllBaseTypes()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'array|bool|callable|int|float|null|Object|string',
            'return_type_token'     => 8,
            'return_type_end_token' => 22,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

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
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'false|MIXED|self|parent|static|iterable|Resource|void',
            'return_type_token'     => 9,
            'return_type_end_token' => 23,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8UnionTypesAllPseudoTypes()


    /**
     * Verify recognition of PHP8 union type declaration with (illegal) nullability.
     *
     * @return void
     */
    public function testPHP8UnionTypesNullable()
    {
        // Offsets are relative to the T_CLOSURE token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '?int|float',
            'return_type_token'     => 12,
            'return_type_end_token' => 14,
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8UnionTypesNullable()


    /**
     * Verify recognition of PHP8 type declaration with (illegal) single type null.
     *
     * @return void
     */
    public function testPHP8PseudoTypeNull()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'null',
            'return_type_token'     => 7,
            'return_type_end_token' => 7,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8PseudoTypeNull()


    /**
     * Verify recognition of PHP8 type declaration with (illegal) single type false.
     *
     * @return void
     */
    public function testPHP8PseudoTypeFalse()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'false',
            'return_type_token'     => 7,
            'return_type_end_token' => 7,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8PseudoTypeFalse()


    /**
     * Verify recognition of PHP8 type declaration with (illegal) type false combined with type bool.
     *
     * @return void
     */
    public function testPHP8PseudoTypeFalseAndBool()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'bool|false',
            'return_type_token'     => 7,
            'return_type_end_token' => 9,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8PseudoTypeFalseAndBool()


    /**
     * Verify recognition of PHP8 type declaration with (illegal) type object combined with a class name.
     *
     * @return void
     */
    public function testPHP8ObjectAndClass()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'object|ClassName',
            'return_type_token'     => 7,
            'return_type_end_token' => 9,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8ObjectAndClass()


    /**
     * Verify recognition of PHP8 type declaration with (illegal) type iterable combined with array/Traversable.
     *
     * @return void
     */
    public function testPHP8PseudoTypeIterableAndArray()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => true,
            'return_type'           => 'iterable|array|Traversable',
            'return_type_token'     => 7,
            'return_type_end_token' => 11,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => false,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8PseudoTypeIterableAndArray()


    /**
     * Verify recognition of PHP8 type declaration with (illegal) duplicate types.
     *
     * @return void
     */
    public function testPHP8DuplicateTypeInUnionWhitespaceAndComment()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'int|string|INT',
            'return_type_token'     => 7,
            'return_type_end_token' => 17,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8DuplicateTypeInUnionWhitespaceAndComment()


    /**
     * Verify recognition of PHP8.1 type "never".
     *
     * @return void
     */
    public function testPHP81NeverType()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'never',
            'return_type_token'     => 7,
            'return_type_end_token' => 7,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP81NeverType()


    /**
     * Verify recognition of PHP8.1 type "never"  with (illegal) nullability.
     *
     * @return void
     */
    public function testPHP81NullableNeverType()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '?never',
            'return_type_token'     => 8,
            'return_type_end_token' => 8,
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP81NullableNeverType()


    /**
     * Verify recognition of PHP8.1 intersection type declaration.
     *
     * @return void
     */
    public function testPHP8IntersectionTypes()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'Foo&Bar',
            'return_type_token'     => 7,
            'return_type_end_token' => 9,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8IntersectionTypes()


    /**
     * Verify recognition of PHP8.1 intersection type declaration with more types.
     *
     * @return void
     */
    public function testPHP81MoreIntersectionTypes()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'MyClassA&\Package\MyClassB&\Package\MyClassC',
            'return_type_token'     => 7,
            'return_type_end_token' => 17,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP81MoreIntersectionTypes()


    /**
     * Verify recognition of PHP8.1 intersection type declaration in arrow function.
     *
     * @return void
     */
    public function testPHP81IntersectionArrowFunction()
    {
        // Offsets are relative to the T_FN token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'MyClassA&\Package\MyClassB',
            'return_type_token'     => 6,
            'return_type_end_token' => 11,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP81IntersectionArrowFunction()


    /**
     * Verify recognition of PHP8.1 intersection type declaration with illegal simple types.
     *
     * @return void
     */
    public function testPHP81IllegalIntersectionTypes()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'string&int',
            'return_type_token'     => 6,
            'return_type_end_token' => 8,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP81IllegalIntersectionTypes()


    /**
     * Verify recognition of PHP8.1 intersection type declaration with (illegal) nullability.
     *
     * @return void
     */
    public function testPHP81NullableIntersectionTypes()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '?Foo&Bar',
            'return_type_token'     => 7,
            'return_type_end_token' => 9,
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP81NullableIntersectionTypes()


    /**
     * Verify recognition of PHP 8.2 stand-alone `true` type.
     *
     * @return void
     */
    public function testPHP82PseudoTypeTrue()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '?true',
            'return_type_token'     => 8,
            'return_type_end_token' => 8,
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP82PseudoTypeTrue()


    /**
     * Verify recognition of PHP 8.2 type declaration with (illegal) type false combined with type true.
     *
     * @return void
     */
    public function testPHP82PseudoTypeFalseAndTrue()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'true|false',
            'return_type_token'     => 7,
            'return_type_end_token' => 9,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP82PseudoTypeFalseAndTrue()


    /**
     * Verify recognition of PHP 8.2 DNF return type declaration.
     *
     * @return void
     */
    public function testPHP82DNFType()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'bool|(Foo&Bar)|string',
            'return_type_token'     => 8,
            'return_type_end_token' => 16,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP82DNFType()


    /**
     * Verify recognition of PHP 8.2 DNF return type declaration on an abstract method.
     *
     * @return void
     */
    public function testPHP82DNFTypeAbstractMethod()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'protected',
            'scope_specified'       => true,
            'return_type'           => 'float|(Foo&Bar)',
            'return_type_token'     => 8,
            'return_type_end_token' => 14,
            'nullable_return_type'  => false,
            'is_abstract'           => true,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => false,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP82DNFTypeAbstractMethod()


    /**
     * Verify recognition of PHP 8.2 DNF return type declaration with illegal nullability.
     *
     * @return void
     */
    public function testPHP82DNFTypeIllegalNullable()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '?(A&\Pck\B)|bool',
            'return_type_token'     => 8,
            'return_type_end_token' => 17,
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP82DNFTypeIllegalNullable()


    /**
     * Verify recognition of PHP 8.2 DNF return type declaration on a closure.
     *
     * @return void
     */
    public function testPHP82DNFTypeClosure()
    {
        // Offsets are relative to the T_CLOSURE token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'object|(namespace\Foo&Countable)',
            'return_type_token'     => 6,
            'return_type_end_token' => 14,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP82DNFTypeClosure()


    /**
     * Verify recognition of PHP 8.2 DNF return type declaration on an arrow function.
     *
     * @return void
     */
    public function testPHP82DNFTypeFn()
    {
        // Offsets are relative to the T_FN token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'null|(Partially\Qualified&Traversable)|void',
            'return_type_token'     => 6,
            'return_type_end_token' => 16,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP82DNFTypeFn()


    /**
     * Test for incorrect tokenization of array return type declarations in PHPCS < 2.8.0.
     *
     * @link https://github.com/squizlabs/PHP_CodeSniffer/pull/1264
     *
     * @return void
     */
    public function testPhpcsIssue1264()
    {
        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'array',
            'return_type_token'     => 8,
            'return_type_end_token' => 8,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPhpcsIssue1264()


    /**
     * Test handling of incorrect tokenization of array return type declarations for arrow functions
     * in a very specific code sample in PHPCS < 3.5.4.
     *
     * @link https://github.com/squizlabs/PHP_CodeSniffer/issues/2773
     *
     * @return void
     */
    public function testArrowFunctionArrayReturnValue()
    {
        // Offsets are relative to the T_FN token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'array',
            'return_type_token'     => 5,
            'return_type_end_token' => 5,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testArrowFunctionArrayReturnValue()


    /**
     * Test handling of an arrow function returning by reference.
     *
     * @return void
     */
    public function testArrowFunctionReturnByRef()
    {
        // Offsets are relative to the T_FN token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '?string',
            'return_type_token'     => 12,
            'return_type_end_token' => 12,
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testArrowFunctionReturnByRef()


    /**
     * Test handling of function declaration nested in a ternary, where the colon for the
     * return type was incorrectly tokenized as T_INLINE_ELSE prior to PHPCS 3.5.7.
     *
     * @return void
     */
    public function testFunctionDeclarationNestedInTernaryPHPCS2975()
    {
        // Offsets are relative to the T_FN token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => true,
            'return_type'           => 'c',
            'return_type_token'     => 7,
            'return_type_end_token' => 7,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testFunctionDeclarationNestedInTernaryPHPCS2975()


    /**
     * Test handling of closure declarations with a use variable import without a return type declaration.
     *
     * @return void
     */
    public function testClosureWithUseNoReturnType()
    {
        // Offsets are relative to the T_CLOSURE token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '',
            'return_type_token'     => false,
            'return_type_end_token' => false,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testClosureWithUseNoReturnType()


    /**
     * Test handling of closure declarations with an illegal use variable for a property import (not allowed in PHP)
     * without a return type declaration.
     *
     * @return void
     */
    public function testClosureWithUseNoReturnTypeIllegalUseProp()
    {
        // Offsets are relative to the T_CLOSURE token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '',
            'return_type_token'     => false,
            'return_type_end_token' => false,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testClosureWithUseNoReturnTypeIllegalUseProp()


    /**
     * Test handling of closure declarations with a use variable import with a return type declaration.
     *
     * @return void
     */
    public function testClosureWithUseWithReturnType()
    {
        // Offsets are relative to the T_CLOSURE token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'Type',
            'return_type_token'     => 14,
            'return_type_end_token' => 14,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testClosureWithUseWithReturnType()


    /**
     * Test handling of closure declarations with a use variable import with a return type declaration.
     *
     * @return void
     */
    public function testClosureWithUseMultiParamWithReturnType()
    {
        // Offsets are relative to the T_CLOSURE token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '?array',
            'return_type_token'     => 32,
            'return_type_end_token' => 32,
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testClosureWithUseMultiParamWithReturnType()


    /**
     * Test helper.
     *
     * @param string                         $commentString The comment which preceeds the test.
     * @param array<string, string|int|bool> $expected      The expected function output.
     *
     * @return void
     */
    private function getMethodPropertiesTestHelper($commentString, $expected)
    {
        $function = $this->getTargetToken($commentString, [T_FUNCTION, T_CLOSURE, T_FN]);
        $found    = self::$phpcsFile->getMethodProperties($function);

        // Convert offsets to absolute positions in the token stream.
        if (is_int($expected['return_type_token']) === true) {
            $expected['return_type_token'] += $function;
        }

        if (is_int($expected['return_type_end_token']) === true) {
            $expected['return_type_end_token'] += $function;
        }

        $this->assertSame($expected, $found);

    }//end getMethodPropertiesTestHelper()


}//end class
