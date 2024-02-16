<?php
/**
 * Tests the tokenization of the finally keyword.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2021 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

final class FinallyTest extends AbstractTokenizerTestCase
{


    /**
     * Test that the finally keyword is tokenized as such.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataFinallyKeyword
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testFinallyKeyword($testMarker)
    {
        $tokens     = $this->phpcsFile->getTokens();
        $target     = $this->getTargetToken($testMarker, [T_FINALLY, T_STRING]);
        $tokenArray = $tokens[$target];

        $this->assertSame(T_FINALLY, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_FINALLY (code)');
        $this->assertSame('T_FINALLY', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_FINALLY (type)');

    }//end testFinallyKeyword()


    /**
     * Data provider.
     *
     * @see testFinallyKeyword()
     *
     * @return array<string, array<string>>
     */
    public static function dataFinallyKeyword()
    {
        return [
            'finally after try and catch'   => ['/* testTryCatchFinally */'],
            'finally between try and catch' => ['/* testTryFinallyCatch */'],
            'finally after try, no catch'   => ['/* testTryFinally */'],
        ];

    }//end dataFinallyKeyword()


    /**
     * Test that 'finally' when not used as the reserved keyword is tokenized as `T_STRING`.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataFinallyNonKeyword
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testFinallyNonKeyword($testMarker)
    {
        $tokens     = $this->phpcsFile->getTokens();
        $target     = $this->getTargetToken($testMarker, [T_FINALLY, T_STRING]);
        $tokenArray = $tokens[$target];

        $this->assertSame(T_STRING, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (code)');
        $this->assertSame('T_STRING', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (type)');

    }//end testFinallyNonKeyword()


    /**
     * Data provider.
     *
     * @see testFinallyNonKeyword()
     *
     * @return array<string, array<string>>
     */
    public static function dataFinallyNonKeyword()
    {
        return [
            'finally used as class constant name' => ['/* testFinallyUsedAsClassConstantName */'],
            'finally used as method name'         => ['/* testFinallyUsedAsMethodName */'],
            'finally used as property name'       => ['/* testFinallyUsedAsPropertyName */'],
        ];

    }//end dataFinallyNonKeyword()


}//end class
