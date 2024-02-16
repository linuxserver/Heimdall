<?php
/**
 * Tests the conversion of PHPCS native context sensitive keyword tokens to T_STRING.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

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
     * @return array
     */
    public static function dataKeywords()
    {
        return [
            'self: param type declaration'            => [
                'testMarker'        => '/* testSelfIsKeyword */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: param type declaration'          => [
                'testMarker'        => '/* testParentIsKeyword */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'parent: class instantiation'             => [
                'testMarker'        => '/* testClassInstantiationParentIsKeyword */',
                'expectedTokenType' => 'T_PARENT',
            ],
            'self: class instantiation'               => [
                'testMarker'        => '/* testClassInstantiationSelfIsKeyword */',
                'expectedTokenType' => 'T_SELF',
            ],

            'false: param type declaration'           => [
                'testMarker'        => '/* testFalseIsKeywordAsParamType */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: param type declaration'            => [
                'testMarker'        => '/* testTrueIsKeywordAsParamType */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: param type declaration'            => [
                'testMarker'        => '/* testNullIsKeywordAsParamType */',
                'expectedTokenType' => 'T_NULL',
            ],
            'false: return type declaration in union' => [
                'testMarker'        => '/* testFalseIsKeywordAsReturnType */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: return type declaration in union'  => [
                'testMarker'        => '/* testTrueIsKeywordAsReturnType */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: return type declaration in union'  => [
                'testMarker'        => '/* testNullIsKeywordAsReturnType */',
                'expectedTokenType' => 'T_NULL',
            ],
            'false: in comparison'                    => [
                'testMarker'        => '/* testFalseIsKeywordInComparison */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: in comparison'                     => [
                'testMarker'        => '/* testTrueIsKeywordInComparison */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: in comparison'                     => [
                'testMarker'        => '/* testNullIsKeywordInComparison */',
                'expectedTokenType' => 'T_NULL',
            ],

            'false: type in OO constant declaration'  => [
                'testMarker'        => '/* testFalseIsKeywordAsConstType */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: type in OO constant declaration'   => [
                'testMarker'        => '/* testTrueIsKeywordAsConstType */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: type in OO constant declaration'   => [
                'testMarker'        => '/* testNullIsKeywordAsConstType */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: type in OO constant declaration'   => [
                'testMarker'        => '/* testSelfIsKeywordAsConstType */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: type in OO constant declaration' => [
                'testMarker'        => '/* testParentIsKeywordAsConstType */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'false: value in constant declaration'    => [
                'testMarker'        => '/* testFalseIsKeywordAsConstDefault */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: value in constant declaration'     => [
                'testMarker'        => '/* testTrueIsKeywordAsConstDefault */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: value in constant declaration'     => [
                'testMarker'        => '/* testNullIsKeywordAsConstDefault */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: value in constant declaration'     => [
                'testMarker'        => '/* testSelfIsKeywordAsConstDefault */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: value in constant declaration'   => [
                'testMarker'        => '/* testParentIsKeywordAsConstDefault */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'false: type in property declaration'     => [
                'testMarker'        => '/* testFalseIsKeywordAsPropertyType */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: type in property declaration'      => [
                'testMarker'        => '/* testTrueIsKeywordAsPropertyType */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: type in property declaration'      => [
                'testMarker'        => '/* testNullIsKeywordAsPropertyType */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: type in property declaration'      => [
                'testMarker'        => '/* testSelfIsKeywordAsPropertyType */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: type in property declaration'    => [
                'testMarker'        => '/* testParentIsKeywordAsPropertyType */',
                'expectedTokenType' => 'T_PARENT',
            ],

            'false: value in property declaration'    => [
                'testMarker'        => '/* testFalseIsKeywordAsPropertyDefault */',
                'expectedTokenType' => 'T_FALSE',
            ],
            'true: value in property declaration'     => [
                'testMarker'        => '/* testTrueIsKeywordAsPropertyDefault */',
                'expectedTokenType' => 'T_TRUE',
            ],
            'null: value in property declaration'     => [
                'testMarker'        => '/* testNullIsKeywordAsPropertyDefault */',
                'expectedTokenType' => 'T_NULL',
            ],
            'self: value in property declaration'     => [
                'testMarker'        => '/* testSelfIsKeywordAsPropertyDefault */',
                'expectedTokenType' => 'T_SELF',
            ],
            'parent: value in property declaration'   => [
                'testMarker'        => '/* testParentIsKeywordAsPropertyDefault */',
                'expectedTokenType' => 'T_PARENT',
            ],
        ];

    }//end dataKeywords()


}//end class
