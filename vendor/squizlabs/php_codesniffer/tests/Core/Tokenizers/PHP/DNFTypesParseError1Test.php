<?php
/**
 * Tests that parentheses tokens are not converted to type parentheses tokens in broken DNF types.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

final class DNFTypesParseError1Test extends AbstractTokenizerTestCase
{


    /**
     * Document handling for a DNF type / parse error where the last significant type specific token is an open parenthesis.
     *
     * @param string $testMarker The comment prefacing the target token.
     *
     * @dataProvider dataBrokenDNFTypeCantEndOnOpenParenthesis
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testBrokenDNFTypeCantEndOnOpenParenthesis($testMarker)
    {
        $tokens = $this->phpcsFile->getTokens();

        $openPtr = $this->getTargetToken($testMarker, [T_OPEN_PARENTHESIS, T_TYPE_OPEN_PARENTHESIS], '(');
        $token   = $tokens[$openPtr];

        // Verify that the open parenthesis is tokenized as a normal parenthesis.
        $this->assertSame(T_OPEN_PARENTHESIS, $token['code'], 'Token tokenized as '.$token['type'].', not T_OPEN_PARENTHESIS (code)');
        $this->assertSame('T_OPEN_PARENTHESIS', $token['type'], 'Token tokenized as '.$token['type'].', not T_OPEN_PARENTHESIS (type)');

        // Verify that the type union is still tokenized as T_BITWISE_OR as the type declaration
        // is not recognized as a valid type declaration.
        $unionPtr = $this->getTargetToken($testMarker, [T_BITWISE_OR, T_TYPE_UNION], '|');
        $token    = $tokens[$unionPtr];

        $this->assertSame(T_BITWISE_OR, $token['code'], 'Token tokenized as '.$token['type'].', not T_BITWISE_OR (code)');
        $this->assertSame('T_BITWISE_OR', $token['type'], 'Token tokenized as '.$token['type'].', not T_BITWISE_OR (type)');

    }//end testBrokenDNFTypeCantEndOnOpenParenthesis()


    /**
     * Data provider.
     *
     * @see testBrokenDNFTypeCantEndOnOpenParenthesis()
     *
     * @return array<string, array<string>>
     */
    public static function dataBrokenDNFTypeCantEndOnOpenParenthesis()
    {
        return [
            'OO const type'    => ['/* testBrokenConstDNFTypeEndOnOpenParenthesis */'],
            'OO property type' => ['/* testBrokenPropertyDNFTypeEndOnOpenParenthesis */'],
            'Parameter type'   => ['/* testBrokenParamDNFTypeEndOnOpenParenthesis */'],
            'Return type'      => ['/* testBrokenReturnDNFTypeEndOnOpenParenthesis */'],
        ];

    }//end dataBrokenDNFTypeCantEndOnOpenParenthesis()


}//end class
