<?php
/**
 * Tests that typed OO constants will be tokenized correctly for:
 * - the type keywords, including keywords like array (T_STRING).
 * - the ? in nullable types
 * - namespaced name types (PHPCS 3.x vs 4.x).
 * - the | in union types
 * - the & in intersection types
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Util\Tokens;

final class TypedConstantsTest extends AbstractTokenizerTestCase
{


    /**
     * Test that a ? after a "const" which is not the constant keyword is tokenized as ternary then, not as the nullable operator.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testTernaryIsInlineThen()
    {
        $tokens = $this->phpcsFile->getTokens();
        $target = $this->getTargetToken('/* testTernaryIsTernaryAfterConst */', [T_NULLABLE, T_INLINE_THEN]);

        $this->assertSame(
            T_INLINE_THEN,
            $tokens[$target]['code'],
            'Token tokenized as '.Tokens::tokenName($tokens[$target]['code']).', not T_INLINE_THEN (code)'
        );
        $this->assertSame(
            'T_INLINE_THEN',
            $tokens[$target]['type'],
            'Token tokenized as '.$tokens[$target]['type'].', not T_INLINE_THEN (type)'
        );

    }//end testTernaryIsInlineThen()


    /**
     * Test the token name for an untyped constant is tokenized as T_STRING.
     *
     * @param string $testMarker The comment prefacing the target token.
     *
     * @dataProvider dataUntypedConstant
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testUntypedConstant($testMarker)
    {
        $tokens = $this->phpcsFile->getTokens();
        $target = $this->getTargetToken($testMarker, T_CONST);

        for ($i = ($target + 1); $tokens[$i]['code'] !== T_EQUAL; $i++) {
            if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === true) {
                // Ignore whitespace and comments, not interested in the tokenization of those.
                continue;
            }

            $this->assertSame(
                T_STRING,
                $tokens[$i]['code'],
                'Token tokenized as '.Tokens::tokenName($tokens[$i]['code']).', not T_STRING (code)'
            );
            $this->assertSame(
                'T_STRING',
                $tokens[$i]['type'],
                'Token tokenized as '.$tokens[$i]['type'].', not T_STRING (type)'
            );
        }

    }//end testUntypedConstant()


    /**
     * Data provider.
     *
     * @see testUntypedConstant()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataUntypedConstant()
    {
        return [
            'non OO constant (untyped)'                  => [
                'testMarker' => '/* testGlobalConstantCannotBeTyped */',
            ],
            'OO constant, final, untyped'                => [
                'testMarker' => '/* testClassConstFinalUntyped */',
            ],
            'OO constant, public, untyped, with comment' => [
                'testMarker' => '/* testClassConstVisibilityUntyped */',
            ],
        ];

    }//end dataUntypedConstant()


    /**
     * Test the tokens in the type of a typed constant as well as the constant name are tokenized correctly.
     *
     * @param string $testMarker The comment prefacing the target token.
     * @param string $sequence   The expected token sequence.
     *
     * @dataProvider dataTypedConstant
     * @dataProvider dataNullableTypedConstant
     * @dataProvider dataUnionTypedConstant
     * @dataProvider dataIntersectionTypedConstant
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testTypedConstant($testMarker, array $sequence)
    {
        $tokens = $this->phpcsFile->getTokens();
        $target = $this->getTargetToken($testMarker, T_CONST);

        $current = 0;
        for ($i = ($target + 1); $tokens[$i]['code'] !== T_EQUAL; $i++) {
            if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === true) {
                // Ignore whitespace and comments, not interested in the tokenization of those.
                continue;
            }

            $this->assertSame(
                $sequence[$current],
                $tokens[$i]['code'],
                'Token tokenized as '.Tokens::tokenName($tokens[$i]['code']).', not '.Tokens::tokenName($sequence[$current]).' (code)'
            );

            ++$current;
        }

    }//end testTypedConstant()


    /**
     * Data provider.
     *
     * @see testTypedConstant()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataTypedConstant()
    {
        $data = [
            'simple type: true'                        => [
                'testMarker' => '/* testClassConstTypedTrue */',
                'sequence'   => [T_TRUE],
            ],
            'simple type: false'                       => [
                'testMarker' => '/* testClassConstTypedFalse */',
                'sequence'   => [T_FALSE],
            ],
            'simple type: null'                        => [
                'testMarker' => '/* testClassConstTypedNull */',
                'sequence'   => [T_NULL],
            ],
            'simple type: bool'                        => [
                'testMarker' => '/* testClassConstTypedBool */',
                'sequence'   => [T_STRING],
            ],
            'simple type: int'                         => [
                'testMarker' => '/* testClassConstTypedInt */',
                'sequence'   => [T_STRING],
            ],
            'simple type: float'                       => [
                'testMarker' => '/* testClassConstTypedFloat */',
                'sequence'   => [T_STRING],
            ],
            'simple type: string'                      => [
                'testMarker' => '/* testClassConstTypedString */',
                'sequence'   => [T_STRING],
            ],
            'simple type: array'                       => [
                'testMarker' => '/* testClassConstTypedArray */',
                'sequence'   => [T_STRING],
            ],
            'simple type: object'                      => [
                'testMarker' => '/* testClassConstTypedObject */',
                'sequence'   => [T_STRING],
            ],
            'simple type: iterable'                    => [
                'testMarker' => '/* testClassConstTypedIterable */',
                'sequence'   => [T_STRING],
            ],
            'simple type: mixed'                       => [
                'testMarker' => '/* testClassConstTypedMixed */',
                'sequence'   => [T_STRING],
            ],
            'simple type: unqualified name'            => [
                'testMarker' => '/* testClassConstTypedClassUnqualified */',
                'sequence'   => [T_STRING],
            ],
            'simple type: fully qualified name'        => [
                'testMarker' => '/* testClassConstTypedClassFullyQualified */',
                'sequence'   => [
                    T_NS_SEPARATOR,
                    T_STRING,
                ],
            ],
            'simple type: namespace relative name'     => [
                'testMarker' => '/* testClassConstTypedClassNamespaceRelative */',
                'sequence'   => [
                    T_NAMESPACE,
                    T_NS_SEPARATOR,
                    T_STRING,
                ],
            ],
            'simple type: partially qualified name'    => [
                'testMarker' => '/* testClassConstTypedClassPartiallyQualified */',
                'sequence'   => [
                    T_STRING,
                    T_NS_SEPARATOR,
                    T_STRING,
                ],
            ],
            'simple type: parent'                      => [
                'testMarker' => '/* testClassConstTypedParent */',
                'sequence'   => [T_PARENT],
            ],

            'simple type: callable (invalid)'          => [
                'testMarker' => '/* testClassConstTypedCallable */',
                'sequence'   => [T_CALLABLE],
            ],
            'simple type: void (invalid)'              => [
                'testMarker' => '/* testClassConstTypedVoid */',
                'sequence'   => [T_STRING],
            ],
            'simple type: NEVER (invalid)'             => [
                'testMarker' => '/* testClassConstTypedNever */',
                'sequence'   => [T_STRING],
            ],

            'simple type: self (only valid in enum)'   => [
                'testMarker' => '/* testEnumConstTypedSelf */',
                'sequence'   => [T_SELF],
            ],
            'simple type: static (only valid in enum)' => [
                'testMarker' => '/* testEnumConstTypedStatic */',
                'sequence'   => [T_STATIC],
            ],
        ];

        // The constant name, as the last token in the sequence, is always T_STRING.
        foreach ($data as $key => $value) {
            $data[$key]['sequence'][] = T_STRING;
        }

        return $data;

    }//end dataTypedConstant()


    /**
     * Data provider.
     *
     * @see testTypedConstant()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataNullableTypedConstant()
    {
        $data = [
            // Global constants cannot be typed in PHP, but that's not our concern.
            'global typed constant, invalid, ?int'       => [
                'testMarker' => '/* testGlobalConstantTypedShouldStillBeHandled */',
                'sequence'   => [T_STRING],
            ],

            // OO constants.
            'nullable type: true'                        => [
                'testMarker' => '/* testTraitConstTypedNullableTrue */',
                'sequence'   => [T_TRUE],
            ],
            'nullable type: false'                       => [
                'testMarker' => '/* testTraitConstTypedNullableFalse */',
                'sequence'   => [T_FALSE],
            ],
            'nullable type: null'                        => [
                'testMarker' => '/* testTraitConstTypedNullableNull */',
                'sequence'   => [T_NULL],
            ],
            'nullable type: bool'                        => [
                'testMarker' => '/* testTraitConstTypedNullableBool */',
                'sequence'   => [T_STRING],
            ],
            'nullable type: int'                         => [
                'testMarker' => '/* testTraitConstTypedNullableInt */',
                'sequence'   => [T_STRING],
            ],
            'nullable type: float'                       => [
                'testMarker' => '/* testTraitConstTypedNullableFloat */',
                'sequence'   => [T_STRING],
            ],
            'nullable type: string'                      => [
                'testMarker' => '/* testTraitConstTypedNullableString */',
                'sequence'   => [T_STRING],
            ],
            'nullable type: array'                       => [
                'testMarker' => '/* testTraitConstTypedNullableArray */',
                'sequence'   => [T_STRING],
            ],
            'nullable type: object'                      => [
                'testMarker' => '/* testTraitConstTypedNullableObject */',
                'sequence'   => [T_STRING],
            ],
            'nullable type: iterable'                    => [
                'testMarker' => '/* testTraitConstTypedNullableIterable */',
                'sequence'   => [T_STRING],
            ],
            'nullable type: mixed'                       => [
                'testMarker' => '/* testTraitConstTypedNullableMixed */',
                'sequence'   => [T_STRING],
            ],
            'nullable type: unqualified name'            => [
                'testMarker' => '/* testTraitConstTypedNullableClassUnqualified */',
                'sequence'   => [T_STRING],
            ],
            'nullable type: fully qualified name'        => [
                'testMarker' => '/* testTraitConstTypedNullableClassFullyQualified */',
                'sequence'   => [
                    T_NS_SEPARATOR,
                    T_STRING,
                ],
            ],
            'nullable type: namespace relative name'     => [
                'testMarker' => '/* testTraitConstTypedNullableClassNamespaceRelative */',
                'sequence'   => [
                    T_NAMESPACE,
                    T_NS_SEPARATOR,
                    T_STRING,
                ],
            ],
            'nullable type: partially qualified name'    => [
                'testMarker' => '/* testTraitConstTypedNullableClassPartiallyQualified */',
                'sequence'   => [
                    T_STRING,
                    T_NS_SEPARATOR,
                    T_STRING,
                ],
            ],
            'nullable type: parent'                      => [
                'testMarker' => '/* testTraitConstTypedNullableParent */',
                'sequence'   => [T_PARENT],
            ],

            'nullable type: self (only valid in enum)'   => [
                'testMarker' => '/* testEnumConstTypedNullableSelf */',
                'sequence'   => [T_SELF],
            ],
            'nullable type: static (only valid in enum)' => [
                'testMarker' => '/* testEnumConstTypedNullableStatic */',
                'sequence'   => [T_STATIC],
            ],
        ];

        // The nullable operator, as the first token in the sequence, is always T_NULLABLE.
        // The constant name, as the last token in the sequence, is always T_STRING.
        foreach ($data as $key => $value) {
            array_unshift($data[$key]['sequence'], T_NULLABLE);
            $data[$key]['sequence'][] = T_STRING;
        }

        return $data;

    }//end dataNullableTypedConstant()


    /**
     * Data provider.
     *
     * @see testTypedConstant()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataUnionTypedConstant()
    {
        $data = [
            'union type: true|null'                      => [
                'testMarker' => '/* testInterfaceConstTypedUnionTrueNull */',
                'sequence'   => [
                    T_TRUE,
                    T_TYPE_UNION,
                    T_NULL,
                ],
            ],
            'union type: array|object'                   => [
                'testMarker' => '/* testInterfaceConstTypedUnionArrayObject */',
                'sequence'   => [
                    T_STRING,
                    T_TYPE_UNION,
                    T_STRING,
                ],
            ],
            'union type: string|array|int'               => [
                'testMarker' => '/* testInterfaceConstTypedUnionStringArrayInt */',
                'sequence'   => [
                    T_STRING,
                    T_TYPE_UNION,
                    T_STRING,
                    T_TYPE_UNION,
                    T_STRING,
                ],
            ],
            'union type: float|bool|array'               => [
                'testMarker' => '/* testInterfaceConstTypedUnionFloatBoolArray */',
                'sequence'   => [
                    T_STRING,
                    T_TYPE_UNION,
                    T_STRING,
                    T_TYPE_UNION,
                    T_STRING,
                ],
            ],
            'union type: iterable|false'                 => [
                'testMarker' => '/* testInterfaceConstTypedUnionIterableFalse */',
                'sequence'   => [
                    T_STRING,
                    T_TYPE_UNION,
                    T_FALSE,
                ],
            ],
            'union type: Unqualified|Namespace\Relative' => [
                'testMarker' => '/* testInterfaceConstTypedUnionUnqualifiedNamespaceRelative */',
                'sequence'   => [
                    T_STRING,
                    T_TYPE_UNION,
                    T_NAMESPACE,
                    T_NS_SEPARATOR,
                    T_STRING,
                ],
            ],
            'union type: FQN|Partial'                    => [
                'testMarker' => '/* testInterfaceConstTypedUnionFullyQualifiedPartiallyQualified */',
                'sequence'   => [
                    T_NS_SEPARATOR,
                    T_STRING,
                    T_NS_SEPARATOR,
                    T_STRING,
                    T_TYPE_UNION,
                    T_STRING,
                    T_NS_SEPARATOR,
                    T_STRING,
                ],
            ],
        ];

        // The constant name, as the last token in the sequence, is always T_STRING.
        foreach ($data as $key => $value) {
            $data[$key]['sequence'][] = T_STRING;
        }

        return $data;

    }//end dataUnionTypedConstant()


    /**
     * Data provider.
     *
     * @see testTypedConstant()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataIntersectionTypedConstant()
    {
        $data = [
            'intersection type: Unqualified&Namespace\Relative' => [
                'testMarker' => '/* testEnumConstTypedIntersectUnqualifiedNamespaceRelative */',
                'sequence'   => [
                    T_STRING,
                    T_TYPE_INTERSECTION,
                    T_NAMESPACE,
                    T_NS_SEPARATOR,
                    T_STRING,
                ],
            ],
            'intersection type: FQN&Partial'                    => [
                'testMarker' => '/* testEnumConstTypedIntersectFullyQualifiedPartiallyQualified */',
                'sequence'   => [
                    T_NS_SEPARATOR,
                    T_STRING,
                    T_NS_SEPARATOR,
                    T_STRING,
                    T_TYPE_INTERSECTION,
                    T_STRING,
                    T_NS_SEPARATOR,
                    T_STRING,
                ],
            ],
        ];

        // The constant name, as the last token in the sequence, is always T_STRING.
        foreach ($data as $key => $value) {
            $data[$key]['sequence'][] = T_STRING;
        }

        return $data;

    }//end dataIntersectionTypedConstant()


}//end class
