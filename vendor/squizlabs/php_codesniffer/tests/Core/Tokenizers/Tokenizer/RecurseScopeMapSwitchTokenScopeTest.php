<?php
/**
 * Tests setting the scope for T_SWITCH token (normal and alternative syntax).
 *
 * @author    Rodrigo Primo <rodrigosprimo@gmail.com>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\Tokenizer;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

final class RecurseScopeMapSwitchTokenScopeTest extends AbstractTokenizerTestCase
{


    /**
     * Tests setting the scope for T_SWITCH token (normal and alternative syntax).
     *
     * @param string                    $testMarker       The comment which prefaces the target token in the test file.
     * @param array<string, int|string> $expectedTokens   The expected token codes for the scope opener/closer.
     * @param string|null               $testOpenerMarker Optional. The comment which prefaces the scope opener if different
     *                                                    from the test marker.
     * @param string|null               $testCloserMarker Optional. The comment which prefaces the scope closer if different
     *                                                    from the test marker.
     *
     * @link https://github.com/squizlabs/PHP_CodeSniffer/issues/497#ref-commit-b24b96b
     *
     * @dataProvider dataSwitchScope
     * @covers       \PHP_CodeSniffer\Tokenizers\Tokenizer::recurseScopeMap
     *
     * @return void
     */
    public function testSwitchScope($testMarker, $expectedTokens, $testOpenerMarker=null, $testCloserMarker=null)
    {
        $tokens      = $this->phpcsFile->getTokens();
        $switchIndex = $this->getTargetToken($testMarker, [T_SWITCH]);
        $tokenArray  = $tokens[$switchIndex];

        $scopeOpenerMarker = $testMarker;
        if (isset($testOpenerMarker) === true) {
            $scopeOpenerMarker = $testOpenerMarker;
        }

        $scopeCloserMarker = $testMarker;
        if (isset($testCloserMarker) === true) {
            $scopeCloserMarker = $testCloserMarker;
        }

        $expectedScopeCondition = $switchIndex;
        $expectedScopeOpener    = $this->getTargetToken($scopeOpenerMarker, $expectedTokens['scope_opener']);
        $expectedScopeCloser    = $this->getTargetToken($scopeCloserMarker, $expectedTokens['scope_closer']);

        $this->assertArrayHasKey('scope_condition', $tokenArray, 'Scope condition not set');
        $this->assertSame(
            $expectedScopeCondition,
            $tokenArray['scope_condition'],
            sprintf(
                'Scope condition not set correctly; expected T_SWITCH, found %s',
                $tokens[$tokenArray['scope_condition']]['type']
            )
        );

        $this->assertArrayHasKey('scope_opener', $tokenArray, 'Scope opener not set');
        $this->assertSame(
            $expectedScopeOpener,
            $tokenArray['scope_opener'],
            sprintf(
                'Scope opener not set correctly; expected %s, found %s',
                $tokens[$expectedScopeOpener]['type'],
                $tokens[$tokenArray['scope_opener']]['type']
            )
        );

        $this->assertArrayHasKey('scope_closer', $tokenArray, 'Scope closer not set');
        $this->assertSame(
            $expectedScopeCloser,
            $tokenArray['scope_closer'],
            sprintf(
                'Scope closer not set correctly; expected %s, found %s',
                $tokens[$expectedScopeCloser]['type'],
                $tokens[$tokenArray['scope_closer']]['type']
            )
        );

    }//end testSwitchScope()


    /**
     * Data provider.
     *
     * @see testSwitchScope()
     *
     * @return array<string, array<string, string|array<string, int|string>|null>>
     */
    public static function dataSwitchScope()
    {
        return [
            'switch normal syntax'                                                                  => [
                'testMarker'       => '/* testSwitchNormalSyntax */',
                'expectedTokens'   => [
                    'scope_opener' => T_OPEN_CURLY_BRACKET,
                    'scope_closer' => T_CLOSE_CURLY_BRACKET,
                ],
                'testOpenerMarker' => null,
                'testCloserMarker' => '/* testSwitchNormalSyntaxScopeCloser */',
            ],
            'switch alternative syntax'                                                             => [
                'testMarker'       => '/* testSwitchAlternativeSyntax */',
                'expectedTokens'   => [
                    'scope_opener' => T_COLON,
                    'scope_closer' => T_ENDSWITCH,
                ],
                'testOpenerMarker' => null,
                'testCloserMarker' => '/* testSwitchAlternativeSyntaxScopeCloser */',
            ],
            'switch with closure in the condition'                                                  => [
                'testMarker'       => '/* testSwitchClosureWithinCondition */',
                'expectedTokens'   => [
                    'scope_opener' => T_OPEN_CURLY_BRACKET,
                    'scope_closer' => T_CLOSE_CURLY_BRACKET,
                ],
                'testOpenerMarker' => '/* testSwitchClosureWithinConditionScopeOpener */',
                'testCloserMarker' => '/* testSwitchClosureWithinConditionScopeCloser */',
            ],
            'switch alternative syntax with closure containing return type in the condition'        => [
                'testMarker'       => '/* testSwitchClosureWithReturnTypeWithinCondition */',
                'expectedTokens'   => [
                    'scope_opener' => T_COLON,
                    'scope_closer' => T_ENDSWITCH,
                ],
                'testOpenerMarker' => '/* testSwitchClosureWithReturnTypeWithinConditionScopeOpener */',
                'testCloserMarker' => '/* testSwitchClosureWithReturnTypeWithinConditionScopeCloser */',
            ],
            'switch with arrow function in the condition'                                           => [
                'testMarker'       => '/* testSwitchArrowFunctionWithinCondition */',
                'expectedTokens'   => [
                    'scope_opener' => T_OPEN_CURLY_BRACKET,
                    'scope_closer' => T_CLOSE_CURLY_BRACKET,
                ],
                'testOpenerMarker' => '/* testSwitchArrowFunctionWithinConditionScopeOpener */',
                'testCloserMarker' => '/* testSwitchArrowFunctionWithinConditionScopeCloser */',
            ],
            'switch alternative syntax with arrow function containing return type in the condition' => [
                'testMarker'       => '/* testSwitchArrowFunctionWithReturnTypeWithinCondition */',
                'expectedTokens'   => [
                    'scope_opener' => T_COLON,
                    'scope_closer' => T_ENDSWITCH,
                ],
                'testOpenerMarker' => '/* testSwitchArrowFunctionWithReturnTypeWithinConditionScopeOpener */',
                'testCloserMarker' => '/* testSwitchArrowFunctionWithReturnTypeWithinConditionScopeCloser */',
            ],

        ];

    }//end dataSwitchScope()


}//end class
