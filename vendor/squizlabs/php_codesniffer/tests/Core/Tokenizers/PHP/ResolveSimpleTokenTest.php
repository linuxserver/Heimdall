<?php
/**
 * Tests that simple tokens are assigned the correct token type and code.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Tests that simple tokens are assigned the correct token type and code.
 *
 * @covers PHP_CodeSniffer\Tokenizers\PHP::resolveSimpleToken
 */
final class ResolveSimpleTokenTest extends AbstractTokenizerTestCase
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
     * Verify tokenization of parentheses, square brackets, curly brackets and a switch colon.
     *
     * @return void
     */
    public function testBracesAndColon()
    {
        $expectedSequence = [
            T_OPEN_PARENTHESIS,
            T_VARIABLE,
            T_OPEN_SQUARE_BRACKET,
            T_LNUMBER,
            T_CLOSE_SQUARE_BRACKET,
            T_CLOSE_PARENTHESIS,
            T_OPEN_CURLY_BRACKET,
            T_CASE,
            T_STRING,
            T_COLON,
            T_BREAK,
            T_SEMICOLON,
            T_CLOSE_CURLY_BRACKET,
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_SWITCH);

        $this->checkTokenSequence(($target + 1), $expectedSequence);

    }//end testBracesAndColon()


    /**
     * Verify tokenization of colon after named parameter.
     *
     * @return void
     */
    public function testNamedParamColon()
    {
        $expectedSequence = [
            T_OPEN_PARENTHESIS,
            T_PARAM_NAME,
            T_COLON,
            T_VARIABLE,
            T_CLOSE_PARENTHESIS,
            T_SEMICOLON,
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_STRING);

        $this->checkTokenSequence(($target + 1), $expectedSequence);

    }//end testNamedParamColon()


    /**
     * Verify tokenization of colon for a return type.
     *
     * @return void
     */
    public function testReturnTypeColon()
    {
        $expectedSequence = [
            T_EQUAL,
            T_CLOSURE,
            T_OPEN_PARENTHESIS,
            T_CLOSE_PARENTHESIS,
            T_COLON,
            T_STRING,
            T_OPEN_CURLY_BRACKET,
            T_CLOSE_CURLY_BRACKET,
            T_SEMICOLON,
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_VARIABLE);

        $this->checkTokenSequence(($target + 1), $expectedSequence);

    }//end testReturnTypeColon()


    /**
     * Verify tokenization of a concatenation operator.
     *
     * @return void
     */
    public function testConcat()
    {
        $expectedSequence = [
            T_STRING_CONCAT,
            T_VARIABLE,
            T_SEMICOLON,
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_CONSTANT_ENCAPSED_STRING);

        $this->checkTokenSequence(($target + 1), $expectedSequence);

    }//end testConcat()


    /**
     * Verify tokenization of simple math operators.
     *
     * @return void
     */
    public function testSimpleMathTokens()
    {
        $expectedSequence = [
            T_EQUAL,
            T_LNUMBER,
            T_MULTIPLY,
            T_LNUMBER,
            T_DIVIDE,
            T_LNUMBER,
            T_PLUS,
            T_LNUMBER,
            T_MINUS,
            T_LNUMBER,
            T_MODULUS,
            T_LNUMBER,
            T_SEMICOLON,
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_VARIABLE);

        $this->checkTokenSequence(($target + 1), $expectedSequence);

    }//end testSimpleMathTokens()


    /**
     * Verify tokenization of unary plus/minus operators.
     *
     * @return void
     */
    public function testUnaryPlusMinus()
    {
        $expectedSequence = [
            T_EQUAL,
            T_PLUS,
            T_LNUMBER,
            T_DIVIDE,
            T_MINUS,
            T_LNUMBER,
            T_SEMICOLON,
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_VARIABLE);

        $this->checkTokenSequence(($target + 1), $expectedSequence);

    }//end testUnaryPlusMinus()


    /**
     * Verify tokenization of bitwise operator tokens.
     *
     * @return void
     */
    public function testBitwiseTokens()
    {
        $expectedSequence = [
            T_EQUAL,
            T_STRING,
            T_BITWISE_XOR,
            T_STRING,
            T_BITWISE_AND,
            T_STRING,
            T_BITWISE_OR,
            T_STRING,
            T_BITWISE_NOT,
            T_STRING,
            T_SEMICOLON,
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_VARIABLE);

        $this->checkTokenSequence(($target + 1), $expectedSequence);

    }//end testBitwiseTokens()


    /**
     * Verify tokenization of bitwise operator tokens.
     *
     * @return void
     */
    public function testBitwiseOrInCatch()
    {
        $expectedSequence = [
            T_OPEN_PARENTHESIS,
            T_STRING,
            T_BITWISE_OR,
            T_STRING,
            T_VARIABLE,
            T_CLOSE_PARENTHESIS,
            T_OPEN_CURLY_BRACKET,
            T_CLOSE_CURLY_BRACKET,
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_CATCH);

        $this->checkTokenSequence(($target + 1), $expectedSequence);

    }//end testBitwiseOrInCatch()


    /**
     * Verify tokenization of a less than operator.
     *
     * @return void
     */
    public function testLessThan()
    {
        $expectedSequence = [
            T_LESS_THAN,
            T_VARIABLE,
            T_SEMICOLON,
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_LNUMBER);

        $this->checkTokenSequence(($target + 1), $expectedSequence);

    }//end testLessThan()


    /**
     * Verify tokenization of a greater than operator.
     *
     * @return void
     */
    public function testGreaterThan()
    {
        $expectedSequence = [
            T_GREATER_THAN,
            T_VARIABLE,
            T_SEMICOLON,
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_LNUMBER);

        $this->checkTokenSequence(($target + 1), $expectedSequence);

    }//end testGreaterThan()


    /**
     * Verify tokenization of a boolean not operator.
     *
     * @return void
     */
    public function testBooleanNot()
    {
        $expectedSequence = [
            T_BOOLEAN_NOT,
            T_VARIABLE,
            T_SEMICOLON,
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_EQUAL);

        $this->checkTokenSequence(($target + 1), $expectedSequence);

    }//end testBooleanNot()


    /**
     * Verify tokenization of commas.
     *
     * @return void
     */
    public function testComma()
    {
        $expectedSequence = [
            T_COMMA,
            T_VARIABLE,
            T_COMMA,
            T_VARIABLE,
            T_SEMICOLON,
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_VARIABLE);

        $this->checkTokenSequence(($target + 1), $expectedSequence);

    }//end testComma()


    /**
     * Verify tokenization of the silence operator.
     *
     * @return void
     */
    public function testAsperand()
    {
        $expectedSequence = [
            T_ASPERAND,
            T_STRING,
            T_OPEN_PARENTHESIS,
            T_CLOSE_PARENTHESIS,
            T_SEMICOLON,
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_EQUAL);

        $this->checkTokenSequence(($target + 1), $expectedSequence);

    }//end testAsperand()


    /**
     * Verify tokenization of the dollar token and curlies for a variable variable.
     *
     * @return void
     */
    public function testDollarAndCurlies()
    {
        $expectedSequence = [
            T_DOLLAR,
            T_OPEN_CURLY_BRACKET,
            T_VARIABLE,
            T_CLOSE_CURLY_BRACKET,
            T_SEMICOLON,
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_ECHO);

        $this->checkTokenSequence(($target + 1), $expectedSequence);

    }//end testDollarAndCurlies()


    /**
     * Verify tokenization of the backtick operator.
     *
     * @return void
     */
    public function testBacktick()
    {
        $expectedSequence = [
            T_BACKTICK,
            T_ENCAPSED_AND_WHITESPACE,
            T_BACKTICK,
            T_SEMICOLON,
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_EQUAL);

        $this->checkTokenSequence(($target + 1), $expectedSequence);

    }//end testBacktick()


    /**
     * Test helper. Check a token sequence complies with an expected token sequence.
     *
     * @param int               $startPtr         The position in the file to start checking from.
     * @param array<int|string> $expectedSequence The consecutive token constants to expect.
     *
     * @return void
     */
    private function checkTokenSequence($startPtr, array $expectedSequence)
    {
        $tokens = $this->phpcsFile->getTokens();

        $sequenceKey   = 0;
        $sequenceCount = count($expectedSequence);

        for ($i = $startPtr; $sequenceKey < $sequenceCount; $i++) {
            if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === true) {
                // Ignore whitespace and comments, not interested in the tokenization of those for these tests.
                continue;
            }

            $expectedTokenName = Tokens::tokenName($expectedSequence[$sequenceKey]);

            $this->assertSame(
                $expectedSequence[$sequenceKey],
                $tokens[$i]['code'],
                'Token tokenized as '.Tokens::tokenName($tokens[$i]['code']).', not '.$expectedTokenName.' (code)'
            );

            $this->assertSame(
                $expectedTokenName,
                $tokens[$i]['type'],
                'Token tokenized as '.$tokens[$i]['type'].', not '.$expectedTokenName.' (type)'
            );

            ++$sequenceKey;
        }//end for

    }//end checkTokenSequence()


}//end class
