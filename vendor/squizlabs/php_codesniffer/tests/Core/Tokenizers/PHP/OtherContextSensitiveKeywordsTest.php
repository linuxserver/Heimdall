<?php
/**
 * Tests the conversion of PHPCS native context sensitive keyword tokens to T_STRING.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

/**
 * Tests the conversion of PHPCS native context sensitive keyword tokens to T_STRING.
 *
 * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
 * @covers PHP_CodeSniffer\Tokenizers\PHP::standardiseToken
 */
final class OtherContextSensitiveKeywordsTest extends AbstractTokenizerTestCase
{


    /**
     * Clear the "resolved tokens" cache before running this test as otherwise the code
     * under test may not be run during the test.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function clearTokenCache()
    {
        parent::clearResolvedTokensCache();

    }//end clearTokenCache()


    /**
     * Test that context sensitive keyword is tokenized as string when it should be string.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataStrings
     *
     * @return void
     */
    public function testStrings($testMarker)
    {
        $tokens     = $this->phpcsFile->getTokens();
        $target     = $this->getTargetToken($testMarker, [T_STRING, T_NULL, T_FALSE, T_TRUE, T_PARENT, T_SELF]);
        $tokenArray = $tokens[$target];

        $this->assertSame(T_STRING, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (code)');
        $this->assertSame('T_STRING', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (type)');

    }//end testStrings()


    /**
     * Data provider.
     *
     * @see testStrings()
     *
     * @return array<string, array<string>>
     */
    public static function dataStrings()
    {
        return [
            'constant declaration: parent'                                      => ['/* testParent */'],
            'constant declaration: self'                                        => ['/* testSelf */'],
            'constant declaration: false'                                       => ['/* testFalse */'],
            'constant declaration: true'                                        => ['/* testTrue */'],
            'constant declaration: null'                                        => ['/* testNull */'],

            'function declaration with return by ref: self'                     => ['/* testKeywordSelfAfterFunctionByRefShouldBeString */'],
            'function declaration with return by ref: parent'                   => ['/* testKeywordParentAfterFunctionByRefShouldBeString */'],
            'function declaration with return by ref: false'                    => ['/* testKeywordFalseAfterFunctionByRefShouldBeString */'],
            'function declaration with return by ref: true'                     => ['/* testKeywordTrueAfterFunctionByRefShouldBeString */'],
            'function declaration with return by ref: null'                     => ['/* testKeywordNullAfterFunctionByRefShouldBeString */'],

            'function call: self'                                               => ['/* testKeywordAsFunctionCallNameShouldBeStringSelf */'],
            'function call: parent'                                             => ['/* testKeywordAsFunctionCallNameShouldBeStringParent */'],
            'function call: false'                                              => ['/* testKeywordAsFunctionCallNameShouldBeStringFalse */'],
            'function call: true'                                               => ['/* testKeywordAsFunctionCallNameShouldBeStringTrue */'],
            'function call: null; with comment between keyword and parentheses' => ['/* testKeywordAsFunctionCallNameShouldBeStringNull */'],

            'class instantiation: false'                                        => ['/* testClassInstantiationFalseIsString */'],
            'class instantiation: true'                                         => ['/* testClassInstantiationTrueIsString */'],
            'class instantiation: null'                                         => ['/* testClassInstantiationNullIsString */'],

            'constant declaration: false as name after type'                    => ['/* testFalseIsNameForTypedConstant */'],
            'constant declaration: true as name after type'                     => ['/* testTrueIsNameForTypedConstant */'],
            'constant declaration: null as name after type'                     => ['/* testNullIsNameForTypedConstant */'],
            'constant declaration: self as name after type'                     => ['/* testSelfIsNameForTypedConstant */'],
            'constant declaration: parent as name after type'                   => ['/* testParentIsNameForTypedConstant */'],
        ];

    }//end dataStrings()


    /**
     * Test that context sensitive keyword is tokenized as keyword when it should be keyword.
     *
     * @param string $testMarker        The comment which prefaces the target token in the test file.
     * @param string $expectedTokenType The expected token type.
     *
     * @dataProvider dataKeywords
     *
     * @return void
     */
    public function testKeywords($testMarker, $expectedTokenType)
    {
        $tokens     = $this->phpcsFile->getTokens();
        $target     = $this->getTargetToken($testMarker, [T_STRING, T_NULL, T_FALSE, T_TRUE, T_PARENT, T_SELF]);
        $tokenArray = $tokens[$target];

        $this->assertSame(
            constant($expectedTokenType),
            $tokenArray['code'],
            'Token tokenized as '.$tokenArray['type'].', not '.$expectedTokenType.' (code)'
        );
        $this->assertSame(
            $expectedTokenType,
            $tokenArray['type'],
            'Token tokenized as '.$tokenArray['type'].', not '.$expectedTokenType.' (type)'
        );

    }//end testKeywords()


    /**
     * Data provider.
     *
     * @see testKeywords()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataKeywords()
    {
        return [
            'self: param type declaration'                                    => [
                'testMarker'        => '/* testSelfIsKeyword */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: param type declaration'                                  => [
                'testMarker'        => '/* testParentIsKeyword */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'parent: class instantiation'                                     => [
                'testMarker'        => '/* testClassInstantiationParentIsKeyword */',
                'expectedTokenType' => 'T_PARENT',
            ],
            'self: class instantiation'                                       => [
                'testMarker'        => '/* testClassInstantiationSelfIsKeyword */',
                'expectedTokenType' => 'T_SELF',
            ],

            'false: param type declaration'                                   => [
                'testMarker'        => '/* testFalseIsKeywordAsParamType */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: param type declaration'                                    => [
                'testMarker'        => '/* testTrueIsKeywordAsParamType */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: param type declaration'                                    => [
                'testMarker'        => '/* testNullIsKeywordAsParamType */',
                'expectedTokenType' => 'T_NULL',
            ],
            'false: return type declaration in union'                         => [
                'testMarker'        => '/* testFalseIsKeywordAsReturnType */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: return type declaration in union'                          => [
                'testMarker'        => '/* testTrueIsKeywordAsReturnType */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: return type declaration in union'                          => [
                'testMarker'        => '/* testNullIsKeywordAsReturnType */',
                'expectedTokenType' => 'T_NULL',
            ],
            'false: in comparison'                                            => [
                'testMarker'        => '/* testFalseIsKeywordInComparison */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: in comparison'                                             => [
                'testMarker'        => '/* testTrueIsKeywordInComparison */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: in comparison'                                             => [
                'testMarker'        => '/* testNullIsKeywordInComparison */',
                'expectedTokenType' => 'T_NULL',
            ],

            'false: type in OO constant declaration'                          => [
                'testMarker'        => '/* testFalseIsKeywordAsConstType */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: type in OO constant declaration'                           => [
                'testMarker'        => '/* testTrueIsKeywordAsConstType */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: type in OO constant declaration'                           => [
                'testMarker'        => '/* testNullIsKeywordAsConstType */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: type in OO constant declaration'                           => [
                'testMarker'        => '/* testSelfIsKeywordAsConstType */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: type in OO constant declaration'                         => [
                'testMarker'        => '/* testParentIsKeywordAsConstType */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'false: value in constant declaration'                            => [
                'testMarker'        => '/* testFalseIsKeywordAsConstDefault */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: value in constant declaration'                             => [
                'testMarker'        => '/* testTrueIsKeywordAsConstDefault */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: value in constant declaration'                             => [
                'testMarker'        => '/* testNullIsKeywordAsConstDefault */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: value in constant declaration'                             => [
                'testMarker'        => '/* testSelfIsKeywordAsConstDefault */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: value in constant declaration'                           => [
                'testMarker'        => '/* testParentIsKeywordAsConstDefault */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'false: type in property declaration'                             => [
                'testMarker'        => '/* testFalseIsKeywordAsPropertyType */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: type in property declaration'                              => [
                'testMarker'        => '/* testTrueIsKeywordAsPropertyType */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: type in property declaration'                              => [
                'testMarker'        => '/* testNullIsKeywordAsPropertyType */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: type in property declaration'                              => [
                'testMarker'        => '/* testSelfIsKeywordAsPropertyType */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: type in property declaration'                            => [
                'testMarker'        => '/* testParentIsKeywordAsPropertyType */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'false: value in property declaration'                            => [
                'testMarker'        => '/* testFalseIsKeywordAsPropertyDefault */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: value in property declaration'                             => [
                'testMarker'        => '/* testTrueIsKeywordAsPropertyDefault */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: value in property declaration'                             => [
                'testMarker'        => '/* testNullIsKeywordAsPropertyDefault */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: value in property declaration'                             => [
                'testMarker'        => '/* testSelfIsKeywordAsPropertyDefault */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: value in property declaration'                           => [
                'testMarker'        => '/* testParentIsKeywordAsPropertyDefault */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'false: first in union type for OO constant declaration'          => [
                'testMarker'        => '/* testFalseIsKeywordAsConstUnionTypeFirst */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: first in union type for OO constant declaration'           => [
                'testMarker'        => '/* testTrueIsKeywordAsConstUnionTypeFirst */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: first in union type for OO constant declaration'           => [
                'testMarker'        => '/* testNullIsKeywordAsConstUnionTypeFirst */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: first in union type for OO constant declaration'           => [
                'testMarker'        => '/* testSelfIsKeywordAsConstUnionTypeFirst */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: first in union type for OO constant declaration'         => [
                'testMarker'        => '/* testParentIsKeywordAsConstUnionTypeFirst */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'false: middle in union type for OO constant declaration'         => [
                'testMarker'        => '/* testFalseIsKeywordAsConstUnionTypeMiddle */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: middle in union type for OO constant declaration'          => [
                'testMarker'        => '/* testTrueIsKeywordAsConstUnionTypeMiddle */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: middle in union type for OO constant declaration'          => [
                'testMarker'        => '/* testNullIsKeywordAsConstUnionTypeMiddle */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: middle in union type for OO constant declaration'          => [
                'testMarker'        => '/* testSelfIsKeywordAsConstUnionTypeMiddle */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: middle in union type for OO constant declaration'        => [
                'testMarker'        => '/* testParentIsKeywordAsConstUnionTypeMiddle */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'false: last in union type for OO constant declaration'           => [
                'testMarker'        => '/* testFalseIsKeywordAsConstUnionTypeLast */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: last in union type for OO constant declaration'            => [
                'testMarker'        => '/* testTrueIsKeywordAsConstUnionTypeLast */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: last in union type for OO constant declaration'            => [
                'testMarker'        => '/* testNullIsKeywordAsConstUnionTypeLast */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: last in union type for OO constant declaration'            => [
                'testMarker'        => '/* testSelfIsKeywordAsConstUnionTypeLast */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: last in union type for OO constant declaration'          => [
                'testMarker'        => '/* testParentIsKeywordAsConstUnionTypeLast */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'false: first in union type for property declaration'             => [
                'testMarker'        => '/* testFalseIsKeywordAsPropertyUnionTypeFirst */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: first in union type for property declaration'              => [
                'testMarker'        => '/* testTrueIsKeywordAsPropertyUnionTypeFirst */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: first in union type for property declaration'              => [
                'testMarker'        => '/* testNullIsKeywordAsPropertyUnionTypeFirst */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: first in union type for property declaration'              => [
                'testMarker'        => '/* testSelfIsKeywordAsPropertyUnionTypeFirst */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: first in union type for property declaration'            => [
                'testMarker'        => '/* testParentIsKeywordAsPropertyUnionTypeFirst */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'false: middle in union type for property declaration'            => [
                'testMarker'        => '/* testFalseIsKeywordAsPropertyUnionTypeMiddle */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: middle in union type for property declaration'             => [
                'testMarker'        => '/* testTrueIsKeywordAsPropertyUnionTypeMiddle */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: middle in union type for property declaration'             => [
                'testMarker'        => '/* testNullIsKeywordAsPropertyUnionTypeMiddle */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: middle in union type for property declaration'             => [
                'testMarker'        => '/* testSelfIsKeywordAsPropertyUnionTypeMiddle */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: middle in union type for property declaration'           => [
                'testMarker'        => '/* testParentIsKeywordAsPropertyUnionTypeMiddle */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'false: last in union type for property declaration'              => [
                'testMarker'        => '/* testFalseIsKeywordAsPropertyUnionTypeLast */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: last in union type for property declaration'               => [
                'testMarker'        => '/* testTrueIsKeywordAsPropertyUnionTypeLast */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: last in union type for property declaration'               => [
                'testMarker'        => '/* testNullIsKeywordAsPropertyUnionTypeLast */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: last in union type for property declaration'               => [
                'testMarker'        => '/* testSelfIsKeywordAsPropertyUnionTypeLast */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: last in union type for property declaration'             => [
                'testMarker'        => '/* testParentIsKeywordAsPropertyUnionTypeLast */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'false: first in union type for param declaration'                => [
                'testMarker'        => '/* testFalseIsKeywordAsParamUnionTypeFirst */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: first in union type for param declaration'                 => [
                'testMarker'        => '/* testTrueIsKeywordAsParamUnionTypeFirst */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: first in union type for param declaration'                 => [
                'testMarker'        => '/* testNullIsKeywordAsParamUnionTypeFirst */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: first in union type for param declaration'                 => [
                'testMarker'        => '/* testSelfIsKeywordAsParamUnionTypeFirst */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: first in union type for param declaration'               => [
                'testMarker'        => '/* testParentIsKeywordAsParamUnionTypeFirst */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'false: middle in union type for param declaration'               => [
                'testMarker'        => '/* testFalseIsKeywordAsParamUnionTypeMiddle */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: middle in union type for param declaration'                => [
                'testMarker'        => '/* testTrueIsKeywordAsParamUnionTypeMiddle */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: middle in union type for param declaration'                => [
                'testMarker'        => '/* testNullIsKeywordAsParamUnionTypeMiddle */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: middle in union type for param declaration'                => [
                'testMarker'        => '/* testSelfIsKeywordAsParamUnionTypeMiddle */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: middle in union type for param declaration'              => [
                'testMarker'        => '/* testParentIsKeywordAsParamUnionTypeMiddle */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'false: last in union type for param declaration'                 => [
                'testMarker'        => '/* testFalseIsKeywordAsParamUnionTypeLast */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: last in union type for param declaration'                  => [
                'testMarker'        => '/* testTrueIsKeywordAsParamUnionTypeLast */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: last in union type for param declaration'                  => [
                'testMarker'        => '/* testNullIsKeywordAsParamUnionTypeLast */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: last in union type for param declaration'                  => [
                'testMarker'        => '/* testSelfIsKeywordAsParamUnionTypeLast */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: last in union type for param declaration'                => [
                'testMarker'        => '/* testParentIsKeywordAsParamUnionTypeLast */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'false: first in union type for return declaration'               => [
                'testMarker'        => '/* testFalseIsKeywordAsReturnUnionTypeFirst */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: first in union type for return declaration'                => [
                'testMarker'        => '/* testTrueIsKeywordAsReturnUnionTypeFirst */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: first in union type for return declaration'                => [
                'testMarker'        => '/* testNullIsKeywordAsReturnUnionTypeFirst */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: first in union type for return declaration'                => [
                'testMarker'        => '/* testSelfIsKeywordAsReturnUnionTypeFirst */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: first in union type for return declaration'              => [
                'testMarker'        => '/* testParentIsKeywordAsReturnUnionTypeFirst */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'false: middle in union type for return declaration'              => [
                'testMarker'        => '/* testFalseIsKeywordAsReturnUnionTypeMiddle */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: middle in union type for return declaration'               => [
                'testMarker'        => '/* testTrueIsKeywordAsReturnUnionTypeMiddle */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: middle in union type for return declaration'               => [
                'testMarker'        => '/* testNullIsKeywordAsReturnUnionTypeMiddle */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: middle in union type for return declaration'               => [
                'testMarker'        => '/* testSelfIsKeywordAsReturnUnionTypeMiddle */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: middle in union type for return declaration'             => [
                'testMarker'        => '/* testParentIsKeywordAsReturnUnionTypeMiddle */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'false: last in union type for return declaration'                => [
                'testMarker'        => '/* testFalseIsKeywordAsReturnUnionTypeLast */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: last in union type for return declaration'                 => [
                'testMarker'        => '/* testTrueIsKeywordAsReturnUnionTypeLast */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: last in union type for return declaration'                 => [
                'testMarker'        => '/* testNullIsKeywordAsReturnUnionTypeLast */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: last in union type for return declaration'                 => [
                'testMarker'        => '/* testSelfIsKeywordAsReturnUnionTypeLast */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: last in union type for return declaration'               => [
                'testMarker'        => '/* testParentIsKeywordAsReturnUnionTypeLast */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'self: first in intersection type for OO constant declaration'    => [
                'testMarker'        => '/* testSelfIsKeywordAsConstIntersectionTypeFirst */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: first in intersection type for OO constant declaration'  => [
                'testMarker'        => '/* testParentIsKeywordAsConstIntersectionTypeFirst */',
                'expectedTokenType' => 'T_PARENT',
            ],
            'self: middle in intersection type for OO constant declaration'   => [
                'testMarker'        => '/* testSelfIsKeywordAsConstIntersectionTypeMiddle */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: middle in intersection type for OO constant declaration' => [
                'testMarker'        => '/* testParentIsKeywordAsConstIntersectionTypeMiddle */',
                'expectedTokenType' => 'T_PARENT',
            ],
            'self: last in intersection type for OO constant declaration'     => [
                'testMarker'        => '/* testSelfIsKeywordAsConstIntersectionTypeLast */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: last in intersection type for OO constant declaration'   => [
                'testMarker'        => '/* testParentIsKeywordAsConstIntersectionTypeLast */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'self: first in intersection type for property declaration'       => [
                'testMarker'        => '/* testSelfIsKeywordAsPropertyIntersectionTypeFirst */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: first in intersection type for property declaration'     => [
                'testMarker'        => '/* testParentIsKeywordAsPropertyIntersectionTypeFirst */',
                'expectedTokenType' => 'T_PARENT',
            ],
            'self: middle in intersection type for property declaration'      => [
                'testMarker'        => '/* testSelfIsKeywordAsPropertyIntersectionTypeMiddle */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: middle in intersection type for property declaration'    => [
                'testMarker'        => '/* testParentIsKeywordAsPropertyIntersectionTypeMiddle */',
                'expectedTokenType' => 'T_PARENT',
            ],
            'self: last in intersection type for property declaration'        => [
                'testMarker'        => '/* testSelfIsKeywordAsPropertyIntersectionTypeLast */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: last in intersection type for property declaration'      => [
                'testMarker'        => '/* testParentIsKeywordAsPropertyIntersectionTypeLast */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'self: first in intersection type for param declaration'          => [
                'testMarker'        => '/* testSelfIsKeywordAsParamIntersectionTypeFirst */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: first in intersection type for param declaration'        => [
                'testMarker'        => '/* testParentIsKeywordAsParamIntersectionTypeFirst */',
                'expectedTokenType' => 'T_PARENT',
            ],
            'self: middle in intersection type for param declaration'         => [
                'testMarker'        => '/* testSelfIsKeywordAsParamIntersectionTypeMiddle */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: middle in intersection type for param declaration'       => [
                'testMarker'        => '/* testParentIsKeywordAsParamIntersectionTypeMiddle */',
                'expectedTokenType' => 'T_PARENT',
            ],
            'self: last in intersection type for param declaration'           => [
                'testMarker'        => '/* testSelfIsKeywordAsParamIntersectionTypeLast */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: last in intersection type for param declaration'         => [
                'testMarker'        => '/* testParentIsKeywordAsParamIntersectionTypeLast */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'self: first in intersection type for return declaration'         => [
                'testMarker'        => '/* testSelfIsKeywordAsReturnIntersectionTypeFirst */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: first in intersection type for return declaration'       => [
                'testMarker'        => '/* testParentIsKeywordAsReturnIntersectionTypeFirst */',
                'expectedTokenType' => 'T_PARENT',
            ],
            'self: middle in intersection type for return declaration'        => [
                'testMarker'        => '/* testSelfIsKeywordAsReturnIntersectionTypeMiddle */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: middle in intersection type for return declaration'      => [
                'testMarker'        => '/* testParentIsKeywordAsReturnIntersectionTypeMiddle */',
                'expectedTokenType' => 'T_PARENT',
            ],
            'self: last in intersection type for return declaration'          => [
                'testMarker'        => '/* testSelfIsKeywordAsReturnIntersectionTypeLast */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: last in intersection type for return declaration'        => [
                'testMarker'        => '/* testParentIsKeywordAsReturnIntersectionTypeLast */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'false: DNF type in OO constant declaration'                      => [
                'testMarker'        => '/* testFalseIsKeywordAsConstDNFType */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: DNF type in OO constant declaration'                       => [
                'testMarker'        => '/* testTrueIsKeywordAsConstDNFType */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: DNF type in OO constant declaration'                       => [
                'testMarker'        => '/* testNullIsKeywordAsConstDNFType */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: DNF type in OO constant declaration'                       => [
                'testMarker'        => '/* testSelfIsKeywordAsConstDNFType */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: DNF type in OO constant declaration'                     => [
                'testMarker'        => '/* testParentIsKeywordAsConstDNFType */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'false: DNF type in property declaration'                         => [
                'testMarker'        => '/* testFalseIsKeywordAsPropertyDNFType */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: DNF type in property declaration'                          => [
                'testMarker'        => '/* testTrueIsKeywordAsPropertyDNFType */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: DNF type in property declaration'                          => [
                'testMarker'        => '/* testNullIsKeywordAsPropertyDNFType */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: DNF type in property declaration'                          => [
                'testMarker'        => '/* testSelfIsKeywordAsPropertyDNFType */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: DNF type in property declaration'                        => [
                'testMarker'        => '/* testParentIsKeywordAsPropertyDNFType */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'false: DNF type in function param declaration'                   => [
                'testMarker'        => '/* testFalseIsKeywordAsParamDNFType */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'false: DNF type in function return declaration'                  => [
                'testMarker'        => '/* testFalseIsKeywordAsReturnDNFType */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: DNF type in function param declaration'                    => [
                'testMarker'        => '/* testTrueIsKeywordAsParamDNFType */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'true: DNF type in function return declaration'                   => [
                'testMarker'        => '/* testTrueIsKeywordAsReturnDNFType */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: DNF type in function param declaration'                    => [
                'testMarker'        => '/* testNullIsKeywordAsParamDNFType */',
                'expectedTokenType' => 'T_NULL',
            ],
            'null: DNF type in function return declaration'                   => [
                'testMarker'        => '/* testNullIsKeywordAsReturnDNFType */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: DNF type in function param declaration'                    => [
                'testMarker'        => '/* testSelfIsKeywordAsParamDNFType */',
                'expectedTokenType' => 'T_SELF',
            ],
            'self: DNF type in function return declaration'                   => [
                'testMarker'        => '/* testSelfIsKeywordAsReturnDNFType */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: DNF type in function param declaration'                  => [
                'testMarker'        => '/* testParentIsKeywordAsParamDNFType */',
                'expectedTokenType' => 'T_PARENT',
            ],
            'parent: DNF type in function return declaration'                 => [
                'testMarker'        => '/* testParentIsKeywordAsReturnDNFType */',
                'expectedTokenType' => 'T_PARENT',
            ],

        ];

    }//end dataKeywords()


}//end class
