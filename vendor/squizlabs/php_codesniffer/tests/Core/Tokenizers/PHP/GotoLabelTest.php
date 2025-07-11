<?php
/**
 * Tests the tokenization of goto declarations and statements.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

/**
 * Tests the tokenization of goto declarations and statements.
 *
 * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
 */
final class GotoLabelTest extends AbstractTokenizerTestCase
{


    /**
     * Verify that the label in a goto statement is tokenized as T_STRING.
     *
     * @param string $testMarker  The comment prefacing the target token.
     * @param string $testContent The token content to expect.
     *
     * @dataProvider dataGotoStatement
     *
     * @return void
     */
    public function testGotoStatement($testMarker, $testContent)
    {
        $tokens = $this->phpcsFile->getTokens();

        $label = $this->getTargetToken($testMarker, T_STRING);

        $this->assertTrue(is_int($label));
        $this->assertSame($testContent, $tokens[$label]['content']);

    }//end testGotoStatement()


    /**
     * Data provider.
     *
     * @see testGotoStatement()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataGotoStatement()
    {
        return [
            'label for goto statement'                              => [
                'testMarker'  => '/* testGotoStatement */',
                'testContent' => 'marker',
            ],
            'label for goto statement in loop, keyword capitalized' => [
                'testMarker'  => '/* testGotoStatementInLoop */',
                'testContent' => 'end',
            ],
            'label for goto statement in switch'                    => [
                'testMarker'  => '/* testGotoStatementInSwitch */',
                'testContent' => 'def',
            ],
            'label for goto statement within function'              => [
                'testMarker'  => '/* testGotoStatementInFunction */',
                'testContent' => 'label',
            ],
        ];

    }//end dataGotoStatement()


    /**
     * Verify that the label in a goto declaration is tokenized as T_GOTO_LABEL.
     *
     * @param string $testMarker  The comment prefacing the target token.
     * @param string $testContent The token content to expect.
     *
     * @dataProvider dataGotoDeclaration
     *
     * @return void
     */
    public function testGotoDeclaration($testMarker, $testContent)
    {
        $tokens = $this->phpcsFile->getTokens();

        $label = $this->getTargetToken($testMarker, T_GOTO_LABEL);

        $this->assertTrue(is_int($label));
        $this->assertSame($testContent, $tokens[$label]['content']);

    }//end testGotoDeclaration()


    /**
     * Data provider.
     *
     * @see testGotoDeclaration()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataGotoDeclaration()
    {
        return [
            'label in goto declaration - marker' => [
                'testMarker'  => '/* testGotoDeclaration */',
                'testContent' => 'marker:',
            ],
            'label in goto declaration - end'    => [
                'testMarker'  => '/* testGotoDeclarationOutsideLoop */',
                'testContent' => 'end:',
            ],
            'label in goto declaration - def'    => [
                'testMarker'  => '/* testGotoDeclarationInSwitch */',
                'testContent' => 'def:',
            ],
            'label in goto declaration - label'  => [
                'testMarker'  => '/* testGotoDeclarationInFunction */',
                'testContent' => 'label:',
            ],
        ];

    }//end dataGotoDeclaration()


    /**
     * Verify that the constant used in a switch - case statement is not confused with a goto label.
     *
     * @param string $testMarker  The comment prefacing the target token.
     * @param string $testContent The token content to expect.
     *
     * @dataProvider dataNotAGotoDeclaration
     *
     * @return void
     */
    public function testNotAGotoDeclaration($testMarker, $testContent)
    {
        $tokens     = $this->phpcsFile->getTokens();
        $target     = $this->getTargetToken($testMarker, [T_GOTO_LABEL, T_STRING], $testContent);
        $tokenArray = $tokens[$target];

        $this->assertSame(T_STRING, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (code)');
        $this->assertSame('T_STRING', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (type)');

    }//end testNotAGotoDeclaration()


    /**
     * Data provider.
     *
     * @see testNotAGotoDeclaration()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataNotAGotoDeclaration()
    {
        return [
            'not goto label - global constant followed by switch-case colon'     => [
                'testMarker'  => '/* testNotGotoDeclarationGlobalConstant */',
                'testContent' => 'CONSTANT',
            ],
            'not goto label - namespaced constant followed by switch-case colon' => [
                'testMarker'  => '/* testNotGotoDeclarationNamespacedConstant */',
                'testContent' => 'CONSTANT',
            ],
            'not goto label - class constant followed by switch-case colon'      => [
                'testMarker'  => '/* testNotGotoDeclarationClassConstant */',
                'testContent' => 'CONSTANT',
            ],
            'not goto label - class property use followed by switch-case colon'  => [
                'testMarker'  => '/* testNotGotoDeclarationClassProperty */',
                'testContent' => 'property',
            ],
            'not goto label - global constant followed by ternary else'          => [
                'testMarker'  => '/* testNotGotoDeclarationGlobalConstantInTernary */',
                'testContent' => 'CONST_A',
            ],
            'not goto label - global constant after ternary else'                => [
                'testMarker'  => '/* testNotGotoDeclarationGlobalConstantInTernary */',
                'testContent' => 'CONST_B',
            ],
            'not goto label - name of backed enum'                               => [
                'testMarker'  => '/* testNotGotoDeclarationEnumWithType */',
                'testContent' => 'Suit',
            ],
        ];

    }//end dataNotAGotoDeclaration()


}//end class
