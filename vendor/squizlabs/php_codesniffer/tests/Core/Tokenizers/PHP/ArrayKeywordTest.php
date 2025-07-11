<?php
/**
 * Tests that the array keyword is tokenized correctly.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2021 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

final class ArrayKeywordTest extends AbstractTokenizerTestCase
{


    /**
     * Test that the array keyword is correctly tokenized as `T_ARRAY`.
     *
     * @param string $testMarker  The comment prefacing the target token.
     * @param string $testContent Optional. The token content to look for.
     *
     * @dataProvider dataArrayKeyword
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testArrayKeyword($testMarker, $testContent='array')
    {
        $tokens = $this->phpcsFile->getTokens();

        $token      = $this->getTargetToken($testMarker, [T_ARRAY, T_STRING], $testContent);
        $tokenArray = $tokens[$token];

        $this->assertSame(T_ARRAY, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_ARRAY (code)');
        $this->assertSame('T_ARRAY', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_ARRAY (type)');

    }//end testArrayKeyword()


    /**
     * Data provider.
     *
     * @see testArrayKeyword()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataArrayKeyword()
    {
        return [
            'empty array'                           => [
                'testMarker' => '/* testEmptyArray */',
            ],
            'array with space before parenthesis'   => [
                'testMarker' => '/* testArrayWithSpace */',
            ],
            'array with comment before parenthesis' => [
                'testMarker'  => '/* testArrayWithComment */',
                'testContent' => 'Array',
            ],
            'nested: outer array'                   => [
                'testMarker' => '/* testNestingArray */',
            ],
            'nested: inner array'                   => [
                'testMarker' => '/* testNestedArray */',
            ],
            'OO constant default value'             => [
                'testMarker' => '/* testOOConstDefault */',
            ],
        ];

    }//end dataArrayKeyword()


    /**
     * Test that the array keyword when used in a type declaration is correctly tokenized as `T_STRING`.
     *
     * @param string $testMarker  The comment prefacing the target token.
     * @param string $testContent Optional. The token content to look for.
     *
     * @dataProvider dataArrayType
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testArrayType($testMarker, $testContent='array')
    {
        $tokens = $this->phpcsFile->getTokens();

        $token      = $this->getTargetToken($testMarker, [T_ARRAY, T_STRING], $testContent);
        $tokenArray = $tokens[$token];

        $this->assertSame(T_STRING, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (code)');
        $this->assertSame('T_STRING', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (type)');

    }//end testArrayType()


    /**
     * Data provider.
     *
     * @see testArrayType()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataArrayType()
    {
        return [
            'closure return type'        => [
                'testMarker'  => '/* testClosureReturnType */',
                'testContent' => 'Array',
            ],
            'function param type'        => [
                'testMarker' => '/* testFunctionDeclarationParamType */',
            ],
            'function union return type' => [
                'testMarker' => '/* testFunctionDeclarationReturnType */',
            ],
            'OO constant type'           => [
                'testMarker' => '/* testOOConstType */',
            ],
            'OO property type'           => [
                'testMarker' => '/* testOOPropertyType */',
            ],

            'OO constant DNF type'       => [
                'testMarker' => '/* testOOConstDNFType */',
            ],
            'OO property DNF type'       => [
                'testMarker'  => '/* testOOPropertyDNFType */',
                'testContent' => 'ARRAY',
            ],
            'function param DNF type'    => [
                'testMarker' => '/* testFunctionDeclarationParamDNFType */',
            ],
            'closure param DNF type'     => [
                'testMarker' => '/* testClosureDeclarationParamDNFType */',
            ],
            'arrow return DNF type'      => [
                'testMarker'  => '/* testArrowDeclarationReturnDNFType */',
                'testContent' => 'Array',
            ],
        ];

    }//end dataArrayType()


    /**
     * Verify that the retokenization of `T_ARRAY` tokens to `T_STRING` is handled correctly
     * for tokens with the contents 'array' which aren't in actual fact the array keyword.
     *
     * @param string $testMarker  The comment prefacing the target token.
     * @param string $testContent The token content to look for.
     *
     * @dataProvider dataNotArrayKeyword
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testNotArrayKeyword($testMarker, $testContent='array')
    {
        $tokens = $this->phpcsFile->getTokens();

        $token      = $this->getTargetToken($testMarker, [T_ARRAY, T_STRING], $testContent);
        $tokenArray = $tokens[$token];

        $this->assertSame(T_STRING, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (code)');
        $this->assertSame('T_STRING', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (type)');

    }//end testNotArrayKeyword()


    /**
     * Data provider.
     *
     * @see testNotArrayKeyword()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataNotArrayKeyword()
    {
        return [
            'class-constant-name'            => [
                'testMarker'  => '/* testClassConst */',
                'testContent' => 'ARRAY',
            ],
            'class-method-name'              => [
                'testMarker' => '/* testClassMethod */',
            ],
            'class-constant-name-after-type' => [
                'testMarker'  => '/* testTypedOOConstName */',
                'testContent' => 'ARRAY',
            ],
        ];

    }//end dataNotArrayKeyword()


}//end class
