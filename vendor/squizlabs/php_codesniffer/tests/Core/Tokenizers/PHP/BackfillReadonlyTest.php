<?php
/**
 * Tests the support of PHP 8.1 "readonly" keyword.
 *
 * @author    Jaroslav Hanslík <kukulich@kukulich.cz>
 * @copyright 2021 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

final class BackfillReadonlyTest extends AbstractTokenizerTestCase
{


    /**
     * Test that the "readonly" keyword is tokenized as such.
     *
     * @param string $testMarker  The comment which prefaces the target token in the test file.
     * @param string $testContent Optional. The token content to look for.
     *                            Defaults to lowercase "readonly".
     *
     * @dataProvider dataReadonly
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testReadonly($testMarker, $testContent='readonly')
    {
        $tokens     = $this->phpcsFile->getTokens();
        $target     = $this->getTargetToken($testMarker, [T_READONLY, T_STRING], $testContent);
        $tokenArray = $tokens[$target];

        $this->assertSame(T_READONLY, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_READONLY (code)');
        $this->assertSame('T_READONLY', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_READONLY (type)');

    }//end testReadonly()


    /**
     * Data provider.
     *
     * @see testReadonly()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataReadonly()
    {
        return [
            'property declaration, no visibility'                                             => [
                'testMarker' => '/* testReadonlyProperty */',
            ],
            'property declaration, var keyword before'                                        => [
                'testMarker' => '/* testVarReadonlyProperty */',
            ],
            'property declaration, var keyword after'                                         => [
                'testMarker' => '/* testReadonlyVarProperty */',
            ],
            'property declaration, static before'                                             => [
                'testMarker' => '/* testStaticReadonlyProperty */',
            ],
            'property declaration, static after'                                              => [
                'testMarker' => '/* testReadonlyStaticProperty */',
            ],
            'constant declaration, with visibility'                                           => [
                'testMarker' => '/* testConstReadonlyProperty */',
            ],
            'property declaration, missing type'                                              => [
                'testMarker' => '/* testReadonlyPropertyWithoutType */',
            ],
            'property declaration, public before'                                             => [
                'testMarker' => '/* testPublicReadonlyProperty */',
            ],
            'property declaration, protected before'                                          => [
                'testMarker' => '/* testProtectedReadonlyProperty */',
            ],
            'property declaration, private before'                                            => [
                'testMarker' => '/* testPrivateReadonlyProperty */',
            ],
            'property declaration, public after'                                              => [
                'testMarker' => '/* testPublicReadonlyPropertyWithReadonlyFirst */',
            ],
            'property declaration, protected after'                                           => [
                'testMarker' => '/* testProtectedReadonlyPropertyWithReadonlyFirst */',
            ],
            'property declaration, private after'                                             => [
                'testMarker' => '/* testPrivateReadonlyPropertyWithReadonlyFirst */',
            ],
            'property declaration, private before, comments in declaration'                   => [
                'testMarker' => '/* testReadonlyWithCommentsInDeclaration */',
            ],
            'property declaration, private before, nullable type'                             => [
                'testMarker' => '/* testReadonlyWithNullableProperty */',
            ],
            'property declaration, private before, union type, null first'                    => [
                'testMarker' => '/* testReadonlyNullablePropertyWithUnionTypeHintAndNullFirst */',
            ],
            'property declaration, private before, union type, null last'                     => [
                'testMarker' => '/* testReadonlyNullablePropertyWithUnionTypeHintAndNullLast */',
            ],
            'property declaration, private before, array type'                                => [
                'testMarker' => '/* testReadonlyPropertyWithArrayTypeHint */',
            ],
            'property declaration, private before, self type'                                 => [
                'testMarker' => '/* testReadonlyPropertyWithSelfTypeHint */',
            ],
            'property declaration, private before, parent type'                               => [
                'testMarker' => '/* testReadonlyPropertyWithParentTypeHint */',
            ],
            'property declaration, private before, FQN type'                                  => [
                'testMarker' => '/* testReadonlyPropertyWithFullyQualifiedTypeHint */',
            ],
            'property declaration, public before, mixed case'                                 => [
                'testMarker'  => '/* testReadonlyIsCaseInsensitive */',
                'testContent' => 'ReAdOnLy',
            ],
            'property declaration, constructor property promotion'                            => [
                'testMarker' => '/* testReadonlyConstructorPropertyPromotion */',
            ],
            'property declaration, constructor property promotion with reference, mixed case' => [
                'testMarker'  => '/* testReadonlyConstructorPropertyPromotionWithReference */',
                'testContent' => 'ReadOnly',
            ],
            'property declaration, in anonymous class'                                        => [
                'testMarker' => '/* testReadonlyPropertyInAnonymousClass */',
            ],
            'property declaration, no visibility, DNF type, unqualified'                      => [
                'testMarker' => '/* testReadonlyPropertyDNFTypeUnqualified */',
            ],
            'property declaration, public before, DNF type, fully qualified'                  => [
                'testMarker' => '/* testReadonlyPropertyDNFTypeFullyQualified */',
            ],
            'property declaration, protected before, DNF type, partially qualified'           => [
                'testMarker' => '/* testReadonlyPropertyDNFTypePartiallyQualified */',
            ],
            'property declaration, private before, DNF type, namespace relative name'         => [
                'testMarker' => '/* testReadonlyPropertyDNFTypeRelativeName */',
            ],
            'property declaration, private before, DNF type, multiple sets'                   => [
                'testMarker' => '/* testReadonlyPropertyDNFTypeMultipleSets */',
            ],
            'property declaration, private before, DNF type, union with array'                => [
                'testMarker' => '/* testReadonlyPropertyDNFTypeWithArray */',
            ],
            'property declaration, private before, DNF type, with spaces and comment'         => [
                'testMarker' => '/* testReadonlyPropertyDNFTypeWithSpacesAndComments */',
            ],
            'property declaration, constructor property promotion, DNF type'                  => [
                'testMarker' => '/* testReadonlyConstructorPropertyPromotionWithDNF */',
            ],
            'property declaration, constructor property promotion, DNF type and reference'    => [
                'testMarker' => '/* testReadonlyConstructorPropertyPromotionWithDNFAndReference */',
            ],
            'anon class declaration, with parentheses'                                        => [
                'testMarker' => '/* testReadonlyAnonClassWithParens */',
            ],
            'anon class declaration, without parentheses'                                     => [
                'testMarker'  => '/* testReadonlyAnonClassWithoutParens */',
                'testContent' => 'Readonly',
            ],
            'anon class declaration, with comments and whitespace'                            => [
                'testMarker'  => '/* testReadonlyAnonClassWithCommentsAndWhitespace */',
                'testContent' => 'READONLY',
            ],
            'live coding / parse error'                                                       => [
                'testMarker' => '/* testParseErrorLiveCoding */',
            ],
        ];

    }//end dataReadonly()


    /**
     * Test that "readonly" when not used as the keyword is still tokenized as `T_STRING`.
     *
     * @param string $testMarker  The comment which prefaces the target token in the test file.
     * @param string $testContent Optional. The token content to look for.
     *                            Defaults to lowercase "readonly".
     *
     * @dataProvider dataNotReadonly
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testNotReadonly($testMarker, $testContent='readonly')
    {
        $tokens     = $this->phpcsFile->getTokens();
        $target     = $this->getTargetToken($testMarker, [T_READONLY, T_STRING], $testContent);
        $tokenArray = $tokens[$target];

        $this->assertSame(T_STRING, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (code)');
        $this->assertSame('T_STRING', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (type)');

    }//end testNotReadonly()


    /**
     * Data provider.
     *
     * @see testNotReadonly()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataNotReadonly()
    {
        return [
            'name of a constant, context: declaration using "const" keyword, uppercase'           => [
                'testMarker'  => '/* testReadonlyUsedAsClassConstantName */',
                'testContent' => 'READONLY',
            ],
            'name of a method, context: declaration'                                              => [
                'testMarker' => '/* testReadonlyUsedAsMethodName */',
            ],
            'name of a property, context: property access'                                        => [
                'testMarker' => '/* testReadonlyUsedAsPropertyName */',
            ],
            'name of a property, context: property access in ternary'                             => [
                'testMarker' => '/* testReadonlyPropertyInTernaryOperator */',
            ],
            'name of a function, context: declaration'                                            => [
                'testMarker' => '/* testReadonlyUsedAsFunctionName */',
            ],
            'name of a function, context: declaration with return by ref'                         => [
                'testMarker' => '/* testReadonlyUsedAsFunctionNameWithReturnByRef */',
            ],
            'name of namespace, context: declaration, mixed case'                                 => [
                'testMarker'  => '/* testReadonlyUsedAsNamespaceName */',
                'testContent' => 'Readonly',
            ],
            'partial name of namespace, context: declaration, mixed case'                         => [
                'testMarker'  => '/* testReadonlyUsedAsPartOfNamespaceName */',
                'testContent' => 'Readonly',
            ],
            'name of a function, context: call'                                                   => [
                'testMarker' => '/* testReadonlyAsFunctionCall */',
            ],
            'name of a namespaced function, context: partially qualified call'                    => [
                'testMarker' => '/* testReadonlyAsNamespacedFunctionCall */',
            ],
            'name of a function, context: namespace relative call, mixed case'                    => [
                'testMarker'  => '/* testReadonlyAsNamespaceRelativeFunctionCall */',
                'testContent' => 'ReadOnly',
            ],
            'name of a method, context: method call on object'                                    => [
                'testMarker' => '/* testReadonlyAsMethodCall */',
            ],
            'name of a method, context: nullsafe method call on object'                           => [
                'testMarker'  => '/* testReadonlyAsNullsafeMethodCall */',
                'testContent' => 'readOnly',
            ],
            'name of a method, context: static method call with space after'                      => [
                'testMarker' => '/* testReadonlyAsStaticMethodCallWithSpace */',
            ],
            'name of a constant, context: constant access - uppercase'                            => [
                'testMarker'  => '/* testClassConstantFetchWithReadonlyAsConstantName */',
                'testContent' => 'READONLY',
            ],
            'name of a function, context: call with space and comment between keyword and parens' => [
                'testMarker' => '/* testReadonlyUsedAsFunctionCallWithSpaceBetweenKeywordAndParens */',
            ],
            'name of a method, context: declaration with DNF parameter'                           => [
                'testMarker' => '/* testReadonlyUsedAsMethodNameWithDNFParam */',
            ],
        ];

    }//end dataNotReadonly()


}//end class
