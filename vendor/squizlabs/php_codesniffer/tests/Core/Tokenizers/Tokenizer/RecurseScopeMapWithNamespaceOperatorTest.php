<?php
/**
 * Tests the scope opener/closers are set correctly when the namespace keyword is used as an operator.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\Tokenizer;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

final class RecurseScopeMapWithNamespaceOperatorTest extends AbstractTokenizerTestCase
{


    /**
     * Test that the scope opener/closers are set correctly when the namespace keyword is encountered as an operator.
     *
     * @param string            $testMarker The comment which prefaces the target tokens in the test file.
     * @param array<int|string> $tokenTypes The token type to search for.
     * @param array<int|string> $open       Optional. The token type for the scope opener.
     * @param array<int|string> $close      Optional. The token type for the scope closer.
     *
     * @dataProvider dataScopeSetting
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::recurseScopeMap
     *
     * @return void
     */
    public function testScopeSetting($testMarker, $tokenTypes, $open=[T_OPEN_CURLY_BRACKET], $close=[T_CLOSE_CURLY_BRACKET])
    {
        $tokens = $this->phpcsFile->getTokens();

        $target = $this->getTargetToken($testMarker, $tokenTypes);
        $opener = $this->getTargetToken($testMarker, $open);
        $closer = $this->getTargetToken($testMarker, $close);

        $this->assertArrayHasKey('scope_opener', $tokens[$target], 'Scope opener missing');
        $this->assertArrayHasKey('scope_closer', $tokens[$target], 'Scope closer missing');
        $this->assertSame($opener, $tokens[$target]['scope_opener'], 'Scope opener not same');
        $this->assertSame($closer, $tokens[$target]['scope_closer'], 'Scope closer not same');

        $this->assertArrayHasKey('scope_opener', $tokens[$opener], 'Scope opener missing for open curly');
        $this->assertArrayHasKey('scope_closer', $tokens[$opener], 'Scope closer missing for open curly');
        $this->assertSame($opener, $tokens[$opener]['scope_opener'], 'Scope opener not same for open curly');
        $this->assertSame($closer, $tokens[$opener]['scope_closer'], 'Scope closer not same for open curly');

        $this->assertArrayHasKey('scope_opener', $tokens[$closer], 'Scope opener missing for close curly');
        $this->assertArrayHasKey('scope_closer', $tokens[$closer], 'Scope closer missing for close curly');
        $this->assertSame($opener, $tokens[$closer]['scope_opener'], 'Scope opener not same for close curly');
        $this->assertSame($closer, $tokens[$closer]['scope_closer'], 'Scope closer not same for close curly');

    }//end testScopeSetting()


    /**
     * Data provider.
     *
     * @see testScopeSetting()
     *
     * @return array<string, array<string, string|array<int|string>>>
     */
    public static function dataScopeSetting()
    {
        return [
            'class which extends namespace relative name'           => [
                'testMarker' => '/* testClassExtends */',
                'tokenTypes' => [T_CLASS],
            ],
            'class which implements namespace relative name'        => [
                'testMarker' => '/* testClassImplements */',
                'tokenTypes' => [T_ANON_CLASS],
            ],
            'interface which extend namespace relative name'        => [
                'testMarker' => '/* testInterfaceExtends */',
                'tokenTypes' => [T_INTERFACE],
            ],
            'namespace relative name in function return type'       => [
                'testMarker' => '/* testFunctionReturnType */',
                'tokenTypes' => [T_FUNCTION],
            ],
            'namespace relative name in closure return type'        => [
                'testMarker' => '/* testClosureReturnType */',
                'tokenTypes' => [T_CLOSURE],
            ],
            'namespace relative name in arrow function return type' => [
                'testMarker' => '/* testArrowFunctionReturnType */',
                'tokenTypes' => [T_FN],
                'open'       => [T_FN_ARROW],
                'close'      => [T_SEMICOLON],
            ],
        ];

    }//end dataScopeSetting()


}//end class
