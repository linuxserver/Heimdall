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
use PHP_CodeSniffer\Util\Tokens;

final class DNFTypesParseError2Test extends AbstractTokenizerTestCase
{


    /**
     * Document handling for a DNF type / parse error where the type declaration contains an unmatched parenthesis.
     *
     * @param string $testMarker The comment prefacing the target token.
     *
     * @dataProvider dataBrokenDNFTypeParensShouldAlwaysBeAPairMissingCloseParens
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testBrokenDNFTypeParensShouldAlwaysBeAPairMissingCloseParens($testMarker)
    {
        $tokens = $this->phpcsFile->getTokens();

        // Verify that the type union is still tokenized as T_BITWISE_OR as the type declaration
        // is not recognized as a valid type declaration.
        $unionPtr = $this->getTargetToken($testMarker, [T_BITWISE_OR, T_TYPE_UNION], '|');
        $token    = $tokens[$unionPtr];

        $this->assertSame(T_BITWISE_OR, $token['code'], 'Token tokenized as '.$token['type'].', not T_BITWISE_OR (code)');
        $this->assertSame('T_BITWISE_OR', $token['type'], 'Token tokenized as '.$token['type'].', not T_BITWISE_OR (type)');

        // Verify that the unmatched open parenthesis is tokenized as a normal parenthesis.
        $openPtr = $this->getTargetToken($testMarker, [T_OPEN_PARENTHESIS, T_TYPE_OPEN_PARENTHESIS], '(');
        $token   = $tokens[$openPtr];

        $this->assertSame(T_OPEN_PARENTHESIS, $token['code'], 'Token tokenized as '.$token['type'].', not T_OPEN_PARENTHESIS (code)');
        $this->assertSame('T_OPEN_PARENTHESIS', $token['type'], 'Token tokenized as '.$token['type'].', not T_OPEN_PARENTHESIS (type)');

        // Verify that the type intersection is still tokenized as T_BITWISE_AND as the type declaration
        // is not recognized as a valid type declaration.
        $intersectPtr = $this->getTargetToken($testMarker, [T_BITWISE_AND, T_TYPE_INTERSECTION], '&');
        $token        = $tokens[$intersectPtr];

        $this->assertSame(T_BITWISE_AND, $token['code'], 'Token tokenized as '.$token['type'].', not T_BITWISE_AND (code)');
        $this->assertSame('T_BITWISE_AND', $token['type'], 'Token tokenized as '.$token['type'].', not T_BITWISE_AND (type)');

    }//end testBrokenDNFTypeParensShouldAlwaysBeAPairMissingCloseParens()


    /**
     * Data provider.
     *
     * @see testBrokenDNFTypeParensShouldAlwaysBeAPairMissingCloseParens()
     *
     * @return array<string, array<string>>
     */
    public static function dataBrokenDNFTypeParensShouldAlwaysBeAPairMissingCloseParens()
    {
        return [
            'OO const type'    => ['/* testBrokenConstDNFTypeParensMissingClose */'],
            'OO property type' => ['/* testBrokenPropertyDNFTypeParensMissingClose */'],
            'Parameter type'   => ['/* testBrokenParamDNFTypeParensMissingClose */'],
            'Return type'      => ['/* testBrokenReturnDNFTypeParensMissingClose */'],
        ];

    }//end dataBrokenDNFTypeParensShouldAlwaysBeAPairMissingCloseParens()


    /**
     * Document handling for a DNF type / parse error where the type declaration contains an unmatched parenthesis.
     *
     * @param string $testMarker The comment prefacing the target token.
     *
     * @dataProvider dataBrokenDNFTypeParensShouldAlwaysBeAPairMissingOpenParens
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testBrokenDNFTypeParensShouldAlwaysBeAPairMissingOpenParens($testMarker)
    {
        $tokens = $this->phpcsFile->getTokens();

        // Verify that the type union is still tokenized as T_BITWISE_OR as the type declaration
        // is not recognized as a valid type declaration.
        $unionPtr = $this->getTargetToken($testMarker, [T_BITWISE_OR, T_TYPE_UNION], '|');
        $token    = $tokens[$unionPtr];

        $this->assertSame(T_BITWISE_OR, $token['code'], 'Token tokenized as '.$token['type'].', not T_BITWISE_OR (code)');
        $this->assertSame('T_BITWISE_OR', $token['type'], 'Token tokenized as '.$token['type'].', not T_BITWISE_OR (type)');

        // Verify that the unmatched open parenthesis is tokenized as a normal parenthesis.
        $closePtr = $this->getTargetToken($testMarker, [T_CLOSE_PARENTHESIS, T_TYPE_CLOSE_PARENTHESIS], ')');
        $token    = $tokens[$closePtr];

        $this->assertSame(T_CLOSE_PARENTHESIS, $token['code'], 'Token tokenized as '.$token['type'].', not T_CLOSE_PARENTHESIS (code)');
        $this->assertSame('T_CLOSE_PARENTHESIS', $token['type'], 'Token tokenized as '.$token['type'].', not T_CLOSE_PARENTHESIS (type)');

        // Verify that the type intersection is still tokenized as T_BITWISE_AND as the type declaration
        // is not recognized as a valid type declaration.
        $intersectPtr = $this->getTargetToken($testMarker, [T_BITWISE_AND, T_TYPE_INTERSECTION], '&');
        $token        = $tokens[$intersectPtr];

        $this->assertSame(T_BITWISE_AND, $token['code'], 'Token tokenized as '.$token['type'].', not T_BITWISE_AND (code)');
        $this->assertSame('T_BITWISE_AND', $token['type'], 'Token tokenized as '.$token['type'].', not T_BITWISE_AND (type)');

    }//end testBrokenDNFTypeParensShouldAlwaysBeAPairMissingOpenParens()


    /**
     * Data provider.
     *
     * @see testBrokenDNFTypeParensShouldAlwaysBeAPairMissingOpenParens()
     *
     * @return array<string, array<string>>
     */
    public static function dataBrokenDNFTypeParensShouldAlwaysBeAPairMissingOpenParens()
    {
        return [
            'OO const type'    => ['/* testBrokenConstDNFTypeParensMissingOpen */'],
            'OO property type' => ['/* testBrokenPropertyDNFTypeParensMissingOpen */'],
            'Parameter type'   => ['/* testBrokenParamDNFTypeParensMissingOpen */'],
            'Return type'      => ['/* testBrokenReturnDNFTypeParensMissingOpen */'],
        ];

    }//end dataBrokenDNFTypeParensShouldAlwaysBeAPairMissingOpenParens()


    /**
     * Document handling for a DNF type / parse error where the type declaration contains an unmatched parenthesis,
     * but also contains a set of matched parentheses.
     *
     * @param string $testMarker The comment prefacing the target token.
     *
     * @dataProvider dataBrokenDNFTypeParensShouldAlwaysBeAPairMatchedAndUnmatched
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testBrokenDNFTypeParensShouldAlwaysBeAPairMatchedAndUnmatched($testMarker)
    {
        $tokens   = $this->phpcsFile->getTokens();
        $startPtr = $this->getTargetToken($testMarker, [T_OPEN_PARENTHESIS, T_TYPE_OPEN_PARENTHESIS], '(');

        for ($i = $startPtr; $i < $this->phpcsFile->numTokens; $i++) {
            if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === true) {
                continue;
            }

            if ($tokens[$i]['code'] === T_EQUAL
                || $tokens[$i]['code'] === T_VARIABLE
                || $tokens[$i]['code'] === T_OPEN_CURLY_BRACKET
            ) {
                // Reached the end of the type.
                break;
            }

            $errorPrefix = 'Token tokenized as '.$tokens[$i]['type'];

            // Verify that type tokens have not been retokenized to `T_TYPE_*` tokens for broken type declarations.
            switch ($tokens[$i]['content']) {
            case '|':
                $this->assertSame(T_BITWISE_OR, $tokens[$i]['code'], $errorPrefix.', not T_BITWISE_OR (code)');
                $this->assertSame('T_BITWISE_OR', $tokens[$i]['type'], $errorPrefix.', not T_BITWISE_OR (type)');
                break;

            case '&':
                $this->assertSame(T_BITWISE_AND, $tokens[$i]['code'], $errorPrefix.', not T_BITWISE_AND (code)');
                $this->assertSame('T_BITWISE_AND', $tokens[$i]['type'], $errorPrefix.', not T_BITWISE_AND (type)');
                break;

            case '(':
                // Verify that the open parenthesis is tokenized as a normal parenthesis.
                $this->assertSame(T_OPEN_PARENTHESIS, $tokens[$i]['code'], $errorPrefix.', not T_OPEN_PARENTHESIS (code)');
                $this->assertSame('T_OPEN_PARENTHESIS', $tokens[$i]['type'], $errorPrefix.', not T_OPEN_PARENTHESIS (type)');
                break;

            case ')':
                $this->assertSame(T_CLOSE_PARENTHESIS, $tokens[$i]['code'], $errorPrefix.', not T_CLOSE_PARENTHESIS (code)');
                $this->assertSame('T_CLOSE_PARENTHESIS', $tokens[$i]['type'], $errorPrefix.', not T_CLOSE_PARENTHESIS (type)');
                break;

            default:
                break;
            }//end switch
        }//end for

    }//end testBrokenDNFTypeParensShouldAlwaysBeAPairMatchedAndUnmatched()


    /**
     * Data provider.
     *
     * @see testBrokenDNFTypeParensShouldAlwaysBeAPairMatchedAndUnmatched()
     *
     * @return array<string, array<string>>
     */
    public static function dataBrokenDNFTypeParensShouldAlwaysBeAPairMatchedAndUnmatched()
    {
        return [
            'OO const type - missing one close parenthesis'   => ['/* testBrokenConstDNFTypeParensMissingOneClose */'],
            'OO property type - missing one open parenthesis' => ['/* testBrokenPropertyDNFTypeParensMissingOneOpen */'],
            'Parameter type - missing one close parenthesis'  => ['/* testBrokenParamDNFTypeParensMissingOneClose */'],
            'Return type - missing one open parenthesis'      => ['/* testBrokenReturnDNFTypeParensMissingOneOpen */'],
        ];

    }//end dataBrokenDNFTypeParensShouldAlwaysBeAPairMatchedAndUnmatched()


}//end class
