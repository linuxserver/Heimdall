<?php
/**
 * Tests the support of PHP 8 attributes
 *
 * @author    Alessandro Chitolina <alekitto@gmail.com>
 * @copyright 2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

final class AttributesTest extends AbstractTokenizerTestCase
{


    /**
     * Test that attributes are parsed correctly.
     *
     * @param string            $testMarker The comment which prefaces the target token in the test file.
     * @param int               $length     The number of tokens between opener and closer.
     * @param array<int|string> $tokenCodes The codes of tokens inside the attributes.
     *
     * @dataProvider dataAttribute
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::findCloser
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::parsePhpAttribute
     *
     * @return void
     */
    public function testAttribute($testMarker, $length, $tokenCodes)
    {
        $tokens = $this->phpcsFile->getTokens();

        $attribute = $this->getTargetToken($testMarker, T_ATTRIBUTE);
        $this->assertArrayHasKey('attribute_closer', $tokens[$attribute]);

        $closer = $tokens[$attribute]['attribute_closer'];
        $this->assertSame(($attribute + $length), $closer);

        $this->assertSame(T_ATTRIBUTE_END, $tokens[$closer]['code']);

        $this->assertSame($tokens[$attribute]['attribute_opener'], $tokens[$closer]['attribute_opener']);
        $this->assertSame($tokens[$attribute]['attribute_closer'], $tokens[$closer]['attribute_closer']);

        $map = array_map(
            function ($token) use ($attribute, $length) {
                $this->assertArrayHasKey('attribute_closer', $token);
                $this->assertSame(($attribute + $length), $token['attribute_closer']);

                return $token['code'];
            },
            array_slice($tokens, ($attribute + 1), ($length - 1))
        );

        $this->assertSame($tokenCodes, $map);

    }//end testAttribute()


    /**
     * Data provider.
     *
     * @see testAttribute()
     *
     * @return array<string, array<string, string|int|array<int|string>>>
     */
    public static function dataAttribute()
    {
        return [
            'class attribute'                                                                   => [
                'testMarker' => '/* testAttribute */',
                'length'     => 2,
                'tokenCodes' => [
                    T_STRING
                ],
            ],
            'class attribute with param'                                                        => [
                'testMarker' => '/* testAttributeWithParams */',
                'length'     => 7,
                'tokenCodes' => [
                    T_STRING,
                    T_OPEN_PARENTHESIS,
                    T_STRING,
                    T_DOUBLE_COLON,
                    T_STRING,
                    T_CLOSE_PARENTHESIS,
                ],
            ],
            'class attribute with named param'                                                  => [
                'testMarker' => '/* testAttributeWithNamedParam */',
                'length'     => 10,
                'tokenCodes' => [
                    T_STRING,
                    T_OPEN_PARENTHESIS,
                    T_PARAM_NAME,
                    T_COLON,
                    T_WHITESPACE,
                    T_STRING,
                    T_DOUBLE_COLON,
                    T_STRING,
                    T_CLOSE_PARENTHESIS,
                ],
            ],
            'function attribute'                                                                => [
                'testMarker' => '/* testAttributeOnFunction */',
                'length'     => 2,
                'tokenCodes' => [
                    T_STRING
                ],
            ],
            'function attribute with params'                                                    => [
                'testMarker' => '/* testAttributeOnFunctionWithParams */',
                'length'     => 17,
                'tokenCodes' => [
                    T_STRING,
                    T_OPEN_PARENTHESIS,
                    T_CONSTANT_ENCAPSED_STRING,
                    T_COMMA,
                    T_WHITESPACE,
                    T_PARAM_NAME,
                    T_COLON,
                    T_WHITESPACE,
                    T_OPEN_SHORT_ARRAY,
                    T_CONSTANT_ENCAPSED_STRING,
                    T_WHITESPACE,
                    T_DOUBLE_ARROW,
                    T_WHITESPACE,
                    T_CONSTANT_ENCAPSED_STRING,
                    T_CLOSE_SHORT_ARRAY,
                    T_CLOSE_PARENTHESIS,
                ],
            ],
            'function attribute with arrow function as param'                                   => [
                'testMarker' => '/* testAttributeWithShortClosureParameter */',
                'length'     => 17,
                'tokenCodes' => [
                    T_STRING,
                    T_OPEN_PARENTHESIS,
                    T_STATIC,
                    T_WHITESPACE,
                    T_FN,
                    T_WHITESPACE,
                    T_OPEN_PARENTHESIS,
                    T_VARIABLE,
                    T_CLOSE_PARENTHESIS,
                    T_WHITESPACE,
                    T_FN_ARROW,
                    T_WHITESPACE,
                    T_BOOLEAN_NOT,
                    T_WHITESPACE,
                    T_VARIABLE,
                    T_CLOSE_PARENTHESIS,
                ],
            ],
            'function attribute; multiple comma separated classes'                              => [
                'testMarker' => '/* testAttributeGrouping */',
                'length'     => 26,
                'tokenCodes' => [
                    T_STRING,
                    T_COMMA,
                    T_WHITESPACE,
                    T_STRING,
                    T_OPEN_PARENTHESIS,
                    T_CONSTANT_ENCAPSED_STRING,
                    T_CLOSE_PARENTHESIS,
                    T_COMMA,
                    T_WHITESPACE,
                    T_STRING,
                    T_OPEN_PARENTHESIS,
                    T_CONSTANT_ENCAPSED_STRING,
                    T_COMMA,
                    T_WHITESPACE,
                    T_PARAM_NAME,
                    T_COLON,
                    T_WHITESPACE,
                    T_OPEN_SHORT_ARRAY,
                    T_CONSTANT_ENCAPSED_STRING,
                    T_WHITESPACE,
                    T_DOUBLE_ARROW,
                    T_WHITESPACE,
                    T_CONSTANT_ENCAPSED_STRING,
                    T_CLOSE_SHORT_ARRAY,
                    T_CLOSE_PARENTHESIS,
                ],
            ],
            'function attribute; multiple comma separated classes, one per line'                => [
                'testMarker' => '/* testAttributeMultiline */',
                'length'     => 31,
                'tokenCodes' => [
                    T_WHITESPACE,
                    T_WHITESPACE,
                    T_STRING,
                    T_COMMA,
                    T_WHITESPACE,
                    T_WHITESPACE,
                    T_STRING,
                    T_OPEN_PARENTHESIS,
                    T_CONSTANT_ENCAPSED_STRING,
                    T_CLOSE_PARENTHESIS,
                    T_COMMA,
                    T_WHITESPACE,
                    T_WHITESPACE,
                    T_STRING,
                    T_OPEN_PARENTHESIS,
                    T_CONSTANT_ENCAPSED_STRING,
                    T_COMMA,
                    T_WHITESPACE,
                    T_PARAM_NAME,
                    T_COLON,
                    T_WHITESPACE,
                    T_OPEN_SHORT_ARRAY,
                    T_CONSTANT_ENCAPSED_STRING,
                    T_WHITESPACE,
                    T_DOUBLE_ARROW,
                    T_WHITESPACE,
                    T_CONSTANT_ENCAPSED_STRING,
                    T_CLOSE_SHORT_ARRAY,
                    T_CLOSE_PARENTHESIS,
                    T_WHITESPACE,
                ],
            ],
            'function attribute; multiple comma separated classes, one per line, with comments' => [
                'testMarker' => '/* testAttributeMultilineWithComment */',
                'length'     => 34,
                'tokenCodes' => [
                    T_WHITESPACE,
                    T_WHITESPACE,
                    T_STRING,
                    T_COMMA,
                    T_WHITESPACE,
                    T_COMMENT,
                    T_WHITESPACE,
                    T_STRING,
                    T_OPEN_PARENTHESIS,
                    T_COMMENT,
                    T_WHITESPACE,
                    T_CONSTANT_ENCAPSED_STRING,
                    T_CLOSE_PARENTHESIS,
                    T_COMMA,
                    T_WHITESPACE,
                    T_WHITESPACE,
                    T_STRING,
                    T_OPEN_PARENTHESIS,
                    T_CONSTANT_ENCAPSED_STRING,
                    T_COMMA,
                    T_WHITESPACE,
                    T_PARAM_NAME,
                    T_COLON,
                    T_WHITESPACE,
                    T_OPEN_SHORT_ARRAY,
                    T_CONSTANT_ENCAPSED_STRING,
                    T_WHITESPACE,
                    T_DOUBLE_ARROW,
                    T_WHITESPACE,
                    T_CONSTANT_ENCAPSED_STRING,
                    T_CLOSE_SHORT_ARRAY,
                    T_CLOSE_PARENTHESIS,
                    T_WHITESPACE,
                ],
            ],
            'function attribute; using partially qualified and fully qualified class names'     => [
                'testMarker' => '/* testFqcnAttribute */',
                'length'     => 13,
                'tokenCodes' => [
                    T_STRING,
                    T_NS_SEPARATOR,
                    T_STRING,
                    T_COMMA,
                    T_WHITESPACE,
                    T_NS_SEPARATOR,
                    T_STRING,
                    T_NS_SEPARATOR,
                    T_STRING,
                    T_OPEN_PARENTHESIS,
                    T_CONSTANT_ENCAPSED_STRING,
                    T_CLOSE_PARENTHESIS,
                ],
            ],
        ];

    }//end dataAttribute()


    /**
     * Test that multiple attributes on the same line are parsed correctly.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
     * @covers PHP_CodeSniffer\Tokenizers\PHP::findCloser
     * @covers PHP_CodeSniffer\Tokenizers\PHP::parsePhpAttribute
     *
     * @return void
     */
    public function testTwoAttributesOnTheSameLine()
    {
        $tokens = $this->phpcsFile->getTokens();

        $attribute = $this->getTargetToken('/* testTwoAttributeOnTheSameLine */', T_ATTRIBUTE);
        $this->assertArrayHasKey('attribute_closer', $tokens[$attribute]);

        $closer = $tokens[$attribute]['attribute_closer'];
        $this->assertSame(T_WHITESPACE, $tokens[($closer + 1)]['code']);
        $this->assertSame(T_ATTRIBUTE, $tokens[($closer + 2)]['code']);
        $this->assertArrayHasKey('attribute_closer', $tokens[($closer + 2)]);

    }//end testTwoAttributesOnTheSameLine()


    /**
     * Test that attribute followed by a line comment is parsed correctly.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
     * @covers PHP_CodeSniffer\Tokenizers\PHP::findCloser
     * @covers PHP_CodeSniffer\Tokenizers\PHP::parsePhpAttribute
     *
     * @return void
     */
    public function testAttributeAndLineComment()
    {
        $tokens = $this->phpcsFile->getTokens();

        $attribute = $this->getTargetToken('/* testAttributeAndCommentOnTheSameLine */', T_ATTRIBUTE);
        $this->assertArrayHasKey('attribute_closer', $tokens[$attribute]);

        $closer = $tokens[$attribute]['attribute_closer'];
        $this->assertSame(T_WHITESPACE, $tokens[($closer + 1)]['code']);
        $this->assertSame(T_COMMENT, $tokens[($closer + 2)]['code']);

    }//end testAttributeAndLineComment()


    /**
     * Test that attributes on function declaration parameters are parsed correctly.
     *
     * @param string            $testMarker The comment which prefaces the target token in the test file.
     * @param int               $position   The token position (starting from T_FUNCTION) of T_ATTRIBUTE token.
     * @param int               $length     The number of tokens between opener and closer.
     * @param array<int|string> $tokenCodes The codes of tokens inside the attributes.
     *
     * @dataProvider dataAttributeOnParameters
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
     * @covers PHP_CodeSniffer\Tokenizers\PHP::findCloser
     * @covers PHP_CodeSniffer\Tokenizers\PHP::parsePhpAttribute
     *
     * @return void
     */
    public function testAttributeOnParameters($testMarker, $position, $length, array $tokenCodes)
    {
        $tokens = $this->phpcsFile->getTokens();

        $function  = $this->getTargetToken($testMarker, T_FUNCTION);
        $attribute = ($function + $position);

        $this->assertSame(T_ATTRIBUTE, $tokens[$attribute]['code']);
        $this->assertArrayHasKey('attribute_closer', $tokens[$attribute]);

        $this->assertSame(($attribute + $length), $tokens[$attribute]['attribute_closer']);

        $closer = $tokens[$attribute]['attribute_closer'];
        $this->assertSame(T_WHITESPACE, $tokens[($closer + 1)]['code']);
        $this->assertSame(T_STRING, $tokens[($closer + 2)]['code']);
        $this->assertSame('int', $tokens[($closer + 2)]['content']);

        $this->assertSame(T_VARIABLE, $tokens[($closer + 4)]['code']);
        $this->assertSame('$param', $tokens[($closer + 4)]['content']);

        $map = array_map(
            function ($token) use ($attribute, $length) {
                $this->assertArrayHasKey('attribute_closer', $token);
                $this->assertSame(($attribute + $length), $token['attribute_closer']);

                return $token['code'];
            },
            array_slice($tokens, ($attribute + 1), ($length - 1))
        );

        $this->assertSame($tokenCodes, $map);

    }//end testAttributeOnParameters()


    /**
     * Data provider.
     *
     * @see testAttributeOnParameters()
     *
     * @return array<string, array<string, string|int|array<int|string>>>
     */
    public static function dataAttributeOnParameters()
    {
        return [
            'parameter attribute; single, inline'                   => [
                'testMarker' => '/* testSingleAttributeOnParameter */',
                'position'   => 4,
                'length'     => 2,
                'tokenCodes' => [
                    T_STRING
                ],
            ],
            'parameter attribute; multiple comma separated, inline' => [
                'testMarker' => '/* testMultipleAttributesOnParameter */',
                'position'   => 4,
                'length'     => 10,
                'tokenCodes' => [
                    T_STRING,
                    T_COMMA,
                    T_WHITESPACE,
                    T_STRING,
                    T_OPEN_PARENTHESIS,
                    T_COMMENT,
                    T_WHITESPACE,
                    T_CONSTANT_ENCAPSED_STRING,
                    T_CLOSE_PARENTHESIS,
                ],
            ],
            'parameter attribute; single, multiline'                => [
                'testMarker' => '/* testMultilineAttributesOnParameter */',
                'position'   => 4,
                'length'     => 13,
                'tokenCodes' => [
                    T_WHITESPACE,
                    T_WHITESPACE,
                    T_STRING,
                    T_OPEN_PARENTHESIS,
                    T_WHITESPACE,
                    T_WHITESPACE,
                    T_CONSTANT_ENCAPSED_STRING,
                    T_WHITESPACE,
                    T_WHITESPACE,
                    T_CLOSE_PARENTHESIS,
                    T_WHITESPACE,
                    T_WHITESPACE,
                ],
            ],
        ];

    }//end dataAttributeOnParameters()


    /**
     * Test that an attribute containing text which looks like a PHP close tag is tokenized correctly.
     *
     * @param string               $testMarker              The comment which prefaces the target token in the test file.
     * @param int                  $length                  The number of tokens between opener and closer.
     * @param array<array<string>> $expectedTokensAttribute The codes of tokens inside the attributes.
     * @param array<int|string>    $expectedTokensAfter     The codes of tokens after the attributes.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::parsePhpAttribute
     *
     * @dataProvider dataAttributeOnTextLookingLikeCloseTag
     *
     * @return void
     */
    public function testAttributeContainingTextLookingLikeCloseTag($testMarker, $length, array $expectedTokensAttribute, array $expectedTokensAfter)
    {
        $tokens = $this->phpcsFile->getTokens();

        $attribute = $this->getTargetToken($testMarker, T_ATTRIBUTE);

        $this->assertSame('T_ATTRIBUTE', $tokens[$attribute]['type']);
        $this->assertArrayHasKey('attribute_closer', $tokens[$attribute]);

        $closer = $tokens[$attribute]['attribute_closer'];
        $this->assertSame(($attribute + $length), $closer);
        $this->assertSame(T_ATTRIBUTE_END, $tokens[$closer]['code']);
        $this->assertSame('T_ATTRIBUTE_END', $tokens[$closer]['type']);

        $this->assertSame($tokens[$attribute]['attribute_opener'], $tokens[$closer]['attribute_opener']);
        $this->assertSame($tokens[$attribute]['attribute_closer'], $tokens[$closer]['attribute_closer']);

        $i = ($attribute + 1);
        foreach ($expectedTokensAttribute as $item) {
            list($expectedType, $expectedContents) = $item;
            $this->assertSame($expectedType, $tokens[$i]['type']);
            $this->assertSame($expectedContents, $tokens[$i]['content']);
            $this->assertArrayHasKey('attribute_opener', $tokens[$i]);
            $this->assertArrayHasKey('attribute_closer', $tokens[$i]);
            ++$i;
        }

        $i = ($closer + 1);
        foreach ($expectedTokensAfter as $expectedCode) {
            $this->assertSame($expectedCode, $tokens[$i]['code']);
            ++$i;
        }

    }//end testAttributeContainingTextLookingLikeCloseTag()


    /**
     * Data provider.
     *
     * @see dataAttributeOnTextLookingLikeCloseTag()
     *
     * @return array<string, array<string, string|int|array<array<string>>|array<int|string>>>
     */
    public static function dataAttributeOnTextLookingLikeCloseTag()
    {
        return [
            'function attribute; string param with "?>"'            => [
                'testMarker'              => '/* testAttributeContainingTextLookingLikeCloseTag */',
                'length'                  => 5,
                'expectedTokensAttribute' => [
                    [
                        'T_STRING',
                        'DeprecationReason',
                    ],
                    [
                        'T_OPEN_PARENTHESIS',
                        '(',
                    ],
                    [
                        'T_CONSTANT_ENCAPSED_STRING',
                        "'reason: <https://some-website/reason?>'",
                    ],
                    [
                        'T_CLOSE_PARENTHESIS',
                        ')',
                    ],
                    [
                        'T_ATTRIBUTE_END',
                        ']',
                    ],
                ],
                'expectedTokensAfter'     => [
                    T_WHITESPACE,
                    T_FUNCTION,
                    T_WHITESPACE,
                    T_STRING,
                    T_OPEN_PARENTHESIS,
                    T_CLOSE_PARENTHESIS,
                    T_WHITESPACE,
                    T_OPEN_CURLY_BRACKET,
                    T_CLOSE_CURLY_BRACKET,
                ],
            ],
            'function attribute; string param with "?>"; multiline' => [
                'testMarker'              => '/* testAttributeContainingMultilineTextLookingLikeCloseTag */',
                'length'                  => 8,
                'expectedTokensAttribute' => [
                    [
                        'T_STRING',
                        'DeprecationReason',
                    ],
                    [
                        'T_OPEN_PARENTHESIS',
                        '(',
                    ],
                    [
                        'T_WHITESPACE',
                        "\n",
                    ],
                    [
                        'T_WHITESPACE',
                        "    ",
                    ],
                    [
                        'T_CONSTANT_ENCAPSED_STRING',
                        "'reason: <https://some-website/reason?>'",
                    ],
                    [
                        'T_WHITESPACE',
                        "\n",
                    ],
                    [
                        'T_CLOSE_PARENTHESIS',
                        ')',
                    ],
                    [
                        'T_ATTRIBUTE_END',
                        ']',
                    ],
                ],
                'expectedTokensAfter'     => [
                    T_WHITESPACE,
                    T_FUNCTION,
                    T_WHITESPACE,
                    T_STRING,
                    T_OPEN_PARENTHESIS,
                    T_CLOSE_PARENTHESIS,
                    T_WHITESPACE,
                    T_OPEN_CURLY_BRACKET,
                    T_CLOSE_CURLY_BRACKET,
                ],
            ],
        ];

    }//end dataAttributeOnTextLookingLikeCloseTag()


    /**
     * Test that invalid attribute (or comment starting with #[ and without ]) are parsed correctly.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
     * @covers PHP_CodeSniffer\Tokenizers\PHP::findCloser
     * @covers PHP_CodeSniffer\Tokenizers\PHP::parsePhpAttribute
     *
     * @return void
     */
    public function testInvalidAttribute()
    {
        $tokens = $this->phpcsFile->getTokens();

        $attribute = $this->getTargetToken('/* testInvalidAttribute */', T_ATTRIBUTE);

        $this->assertArrayHasKey('attribute_closer', $tokens[$attribute]);
        $this->assertNull($tokens[$attribute]['attribute_closer']);

    }//end testInvalidAttribute()


    /**
     * Test that nested attributes are parsed correctly.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
     * @covers PHP_CodeSniffer\Tokenizers\PHP::findCloser
     * @covers PHP_CodeSniffer\Tokenizers\PHP::parsePhpAttribute
     * @covers PHP_CodeSniffer\Tokenizers\PHP::createAttributesNestingMap
     *
     * @return void
     */
    public function testNestedAttributes()
    {
        $tokens     = $this->phpcsFile->getTokens();
        $tokenCodes = [
            T_STRING,
            T_NS_SEPARATOR,
            T_STRING,
            T_OPEN_PARENTHESIS,
            T_FN,
            T_WHITESPACE,
            T_OPEN_PARENTHESIS,
            T_ATTRIBUTE,
            T_STRING,
            T_OPEN_PARENTHESIS,
            T_CONSTANT_ENCAPSED_STRING,
            T_CLOSE_PARENTHESIS,
            T_ATTRIBUTE_END,
            T_WHITESPACE,
            T_VARIABLE,
            T_CLOSE_PARENTHESIS,
            T_WHITESPACE,
            T_FN_ARROW,
            T_WHITESPACE,
            T_STRING_CAST,
            T_WHITESPACE,
            T_VARIABLE,
            T_CLOSE_PARENTHESIS,
        ];

        $attribute = $this->getTargetToken('/* testNestedAttributes */', T_ATTRIBUTE);
        $this->assertArrayHasKey('attribute_closer', $tokens[$attribute]);

        $closer = $tokens[$attribute]['attribute_closer'];
        $this->assertSame(($attribute + 24), $closer);

        $this->assertSame(T_ATTRIBUTE_END, $tokens[$closer]['code']);

        $this->assertSame($tokens[$attribute]['attribute_opener'], $tokens[$closer]['attribute_opener']);
        $this->assertSame($tokens[$attribute]['attribute_closer'], $tokens[$closer]['attribute_closer']);

        $this->assertArrayNotHasKey('nested_attributes', $tokens[$attribute]);
        $this->assertArrayHasKey('nested_attributes', $tokens[($attribute + 8)]);
        $this->assertSame([$attribute => ($attribute + 24)], $tokens[($attribute + 8)]['nested_attributes']);

        $test = function (array $tokens, $length, $nestedMap) use ($attribute) {
            foreach ($tokens as $token) {
                $this->assertArrayHasKey('attribute_closer', $token);
                $this->assertSame(($attribute + $length), $token['attribute_closer']);
                $this->assertSame($nestedMap, $token['nested_attributes']);
            }
        };

        $test(array_slice($tokens, ($attribute + 1), 7), 24, [$attribute => $attribute + 24]);
        $test(array_slice($tokens, ($attribute + 8), 1), 8 + 5, [$attribute => $attribute + 24]);

        // Length here is 8 (nested attribute offset) + 5 (real length).
        $test(
            array_slice($tokens, ($attribute + 9), 4),
            8 + 5,
            [
                $attribute     => $attribute + 24,
                $attribute + 8 => $attribute + 13,
            ]
        );

        $test(array_slice($tokens, ($attribute + 13), 1), 8 + 5, [$attribute => $attribute + 24]);
        $test(array_slice($tokens, ($attribute + 14), 10), 24, [$attribute => $attribute + 24]);

        $map = array_map(
            static function ($token) {
                return $token['code'];
            },
            array_slice($tokens, ($attribute + 1), 23)
        );

        $this->assertSame($tokenCodes, $map);

    }//end testNestedAttributes()


}//end class
