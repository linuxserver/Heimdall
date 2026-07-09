<?php
/**
 * Tests the retokenization of the `exit`/`die` keywords to T_EXIT on PHP 8.4 and higher.
 *
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/HEAD/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

final class ExitKeywordTest extends AbstractTokenizerTestCase
{


    /**
     * Test the retokenization of the `exit`/`die` keywords to T_EXIT.
     *
     * @param string $testMarker  The comment prefacing the target token.
     * @param string $testContent The token content to look for.
     *
     * @dataProvider dataExitIsKeyword
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testExitIsKeyword($testMarker, $testContent)
    {
        $tokens = $this->phpcsFile->getTokens();

        $token      = $this->getTargetToken($testMarker, [T_EXIT, T_STRING], $testContent);
        $tokenArray = $tokens[$token];

        $this->assertSame(T_EXIT, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_EXIT (code)');
        $this->assertSame('T_EXIT', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_EXIT (type)');

    }//end testExitIsKeyword()


    /**
     * Data provider.
     *
     * @see testExitIsKeyword()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataExitIsKeyword()
    {
        return [
            'exit as constant'                                       => [
                'testMarker'  => '/* testExitAsConstant */',
                'testContent' => 'exit',
            ],
            'die as constant'                                        => [
                'testMarker'  => '/* testDieAsConstant */',
                'testContent' => 'die',
            ],
            'exit as constant; mixed case'                           => [
                'testMarker'  => '/* testExitAsConstantMixedCase */',
                'testContent' => 'Exit',
            ],
            'die as constant; uppercase'                             => [
                'testMarker'  => '/* testDieAsConstantUppercase */',
                'testContent' => 'DIE',
            ],
            'exit as function call; no parameters'                   => [
                'testMarker'  => '/* testExitAsFunctionCallNoParam */',
                'testContent' => 'exit',
            ],
            'die as function call; no parameters'                    => [
                'testMarker'  => '/* testDieAsFunctionCallNoParam */',
                'testContent' => 'die',
            ],
            'exit as function call; with parameters'                 => [
                'testMarker'  => '/* testExitAsFunctionCallWithParam */',
                'testContent' => 'exit',
            ],
            'die as function call; with parameters'                  => [
                'testMarker'  => '/* testDieAsFunctionCallWithParam */',
                'testContent' => 'die',
            ],
            'exit as function call; uppercase'                       => [
                'testMarker'  => '/* testExitAsFunctionCallUppercase */',
                'testContent' => 'EXIT',
            ],
            'die as function call; mixed case'                       => [
                'testMarker'  => '/* testDieAsFunctionCallMixedCase */',
                'testContent' => 'dIE',
            ],
            'exit as fully qualified function call; with parameters' => [
                'testMarker'  => '/* testExitAsFQFunctionCallWithParam */',
                'testContent' => 'exit',
            ],
            'die as fully qualified function call; no parameters'    => [
                'testMarker'  => '/* testDieAsFQFunctionCallNoParam */',
                'testContent' => 'die',
            ],
            'exit as fully qualified constant (illegal)'             => [
                'testMarker'  => '/* testExitAsFQConstant */',
                'testContent' => 'exit',
            ],
            'die as fully qualified constant (illegal)'              => [
                'testMarker'  => '/* testDieAsFQConstant */',
                'testContent' => 'die',
            ],
        ];

    }//end dataExitIsKeyword()


    /**
     * Verify that the retokenization of `T_EXIT` tokens doesn't negatively impact the tokenization
     * of `T_STRING` tokens with the contents "exit" or "die" which aren't in actual fact the keyword.
     *
     * @param string $testMarker  The comment prefacing the target token.
     * @param string $testContent The token content to look for.
     * @param string $expected    The expected token type. Defaults to `T_STRING`.
     *
     * @dataProvider dataNotExitKeyword
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testNotExitKeyword($testMarker, $testContent, $expected='T_STRING')
    {
        $tokens    = $this->phpcsFile->getTokens();
        $tokenCode = constant($expected);

        $token      = $this->getTargetToken($testMarker, [T_EXIT, $tokenCode], $testContent);
        $tokenArray = $tokens[$token];

        $this->assertSame($tokenCode, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not '.$expected.' (code)');
        $this->assertSame($expected, $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not '.$expected.' (type)');

    }//end testNotExitKeyword()


    /**
     * Data provider.
     *
     * @see testNotExitKeyword()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataNotExitKeyword()
    {
        return [
            'exit not keyword: OO constant use'                       => [
                'testMarker'  => '/* testNotExitOOConstantAccess */',
                'testContent' => 'exit',
            ],
            'die not keyword: OO constant use'                        => [
                'testMarker'  => '/* testNotDieOOConstantAccess */',
                'testContent' => 'die',
            ],
            'exit not keyword: OO property access'                    => [
                'testMarker'  => '/* testNotExitOOPropertyAccess */',
                'testContent' => 'exit',
            ],
            'die not keyword: OO property access'                     => [
                'testMarker'  => '/* testNotDieOOPropertyAccess */',
                'testContent' => 'DIE',
            ],
            'exit not keyword: OO method call'                        => [
                'testMarker'  => '/* testNotExitOOMethodCall */',
                'testContent' => 'exit',
            ],
            'die not keyword: OO method call'                         => [
                'testMarker'  => '/* testNotDieOOMethodCall */',
                'testContent' => 'die',
            ],
            'exit not keyword: OO constant declaration'               => [
                'testMarker'  => '/* testNotExitOOConstDeclaration */',
                'testContent' => 'exit',
            ],
            'die not keyword: OO constant declaration'                => [
                'testMarker'  => '/* testNotDieOOConstDeclaration */',
                'testContent' => 'die',
            ],
            'exit not keyword: OO method declaration'                 => [
                'testMarker'  => '/* testNotExitOOMethodDeclaration */',
                'testContent' => 'Exit',
            ],
            'die not keyword: OO method declaration'                  => [
                'testMarker'  => '/* testNotDieOOMethodDeclaration */',
                'testContent' => 'die',
            ],
            'exit not keyword: parameter label for named param'       => [
                'testMarker'  => '/* testNotExitParamName */',
                'testContent' => 'exit',
                'expected'    => 'T_PARAM_NAME',
            ],
            'die not keyword: parameter label for named param'        => [
                'testMarker'  => '/* testNotDieParamName */',
                'testContent' => 'die',
                'expected'    => 'T_PARAM_NAME',
            ],
            'exit not keyword: part of a namespaced name'             => [
                'testMarker'  => '/* testNotExitNamespacedName */',
                'testContent' => 'exit',
            ],
            'die not keyword: part of a namespaced name'              => [
                'testMarker'  => '/* testNotDieNamespacedName */',
                'testContent' => 'die',
            ],
            'exit not keyword: global constant declaration (illegal)' => [
                'testMarker'  => '/* testNotExitConstantDeclaration */',
                'testContent' => 'exit',
            ],
            'die not keyword: global constant declaration (illegal)'  => [
                'testMarker'  => '/* testNotDieConstantDeclaration */',
                'testContent' => 'die',
            ],
            'exit not keyword: global function declaration (illegal)' => [
                'testMarker'  => '/* testNotExitFunctionDeclaration */',
                'testContent' => 'exit',
            ],
            'die not keyword: global function declaration (illegal)'  => [
                'testMarker'  => '/* testNotDieFunctionDeclaration */',
                'testContent' => 'die',
            ],
        ];

    }//end dataNotExitKeyword()


}//end class
