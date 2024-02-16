<?php
/**
 * Tests the backfilling of the parameter labels for PHP 8.0 named parameters in function calls.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Util\Tokens;

final class NamedFunctionCallArgumentsTest extends AbstractTokenizerTestCase
{


    /**
     * Verify that parameter labels are tokenized as T_PARAM_NAME and that
     * the colon after it is tokenized as a T_COLON.
     *
     * @param string        $testMarker The comment prefacing the target token.
     * @param array<string> $parameters The token content for each parameter label to look for.
     *
     * @dataProvider dataNamedFunctionCallArguments
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testNamedFunctionCallArguments($testMarker, $parameters)
    {
        $tokens = $this->phpcsFile->getTokens();

        foreach ($parameters as $content) {
            $label = $this->getTargetToken($testMarker, [T_STRING, T_PARAM_NAME], $content);

            $this->assertSame(
                T_PARAM_NAME,
                $tokens[$label]['code'],
                'Token tokenized as '.$tokens[$label]['type'].', not T_PARAM_NAME (code)'
            );
            $this->assertSame(
                'T_PARAM_NAME',
                $tokens[$label]['type'],
                'Token tokenized as '.$tokens[$label]['type'].', not T_PARAM_NAME (type)'
            );

            // Get the next non-empty token.
            $colon = $this->phpcsFile->findNext(Tokens::$emptyTokens, ($label + 1), null, true);

            $this->assertSame(
                ':',
                $tokens[$colon]['content'],
                'Next token after parameter name is not a colon. Found: '.$tokens[$colon]['content']
            );
            $this->assertSame(
                T_COLON,
                $tokens[$colon]['code'],
                'Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (code)'
            );
            $this->assertSame(
                'T_COLON',
                $tokens[$colon]['type'],
                'Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (type)'
            );
        }//end foreach

    }//end testNamedFunctionCallArguments()


    /**
     * Data provider.
     *
     * @see testNamedFunctionCallArguments()
     *
     * @return array<string, array<string, string|array<string>>>
     */
    public static function dataNamedFunctionCallArguments()
    {
        return [
            'function call, single line, all named args'                          => [
                'testMarker' => '/* testNamedArgs */',
                'parameters' => [
                    'start_index',
                    'count',
                    'value',
                ],
            ],
            'function call, multi-line, all named args'                           => [
                'testMarker' => '/* testNamedArgsMultiline */',
                'parameters' => [
                    'start_index',
                    'count',
                    'value',
                ],
            ],
            'function call, single line, all named args; comments and whitespace' => [
                'testMarker' => '/* testNamedArgsWithWhitespaceAndComments */',
                'parameters' => [
                    'start_index',
                    'count',
                    'value',
                ],
            ],
            'function call, single line, mixed positional and named args'         => [
                'testMarker' => '/* testMixedPositionalAndNamedArgs */',
                'parameters' => [
                    'double_encode',
                ],
            ],
            'function call containing nested function call values'                => [
                'testMarker' => '/* testNestedFunctionCallOuter */',
                'parameters' => [
                    'start_index',
                    'count',
                    'value',
                ],
            ],
            'function call nested in named arg [1]'                               => [
                'testMarker' => '/* testNestedFunctionCallInner1 */',
                'parameters' => [
                    'skip',
                ],
            ],
            'function call nested in named arg [2]'                               => [
                'testMarker' => '/* testNestedFunctionCallInner2 */',
                'parameters' => [
                    'array_or_countable',
                ],
            ],
            'namespace relative function call'                                    => [
                'testMarker' => '/* testNamespaceRelativeFunction */',
                'parameters' => [
                    'label',
                    'more',
                ],
            ],
            'partially qualified function call'                                   => [
                'testMarker' => '/* testPartiallyQualifiedFunction */',
                'parameters' => [
                    'label',
                    'more',
                ],
            ],
            'fully qualified function call'                                       => [
                'testMarker' => '/* testFullyQualifiedFunction */',
                'parameters' => [
                    'label',
                    'more',
                ],
            ],
            'variable function call'                                              => [
                'testMarker' => '/* testVariableFunction */',
                'parameters' => [
                    'label',
                    'more',
                ],
            ],
            'variable variable function call'                                     => [
                'testMarker' => '/* testVariableVariableFunction */',
                'parameters' => [
                    'label',
                    'more',
                ],
            ],
            'method call'                                                         => [
                'testMarker' => '/* testMethodCall */',
                'parameters' => [
                    'label',
                    'more',
                ],
            ],
            'variable method call'                                                => [
                'testMarker' => '/* testVariableMethodCall */',
                'parameters' => [
                    'label',
                    'more',
                ],
            ],
            'class instantiation'                                                 => [
                'testMarker' => '/* testClassInstantiation */',
                'parameters' => [
                    'label',
                    'more',
                ],
            ],
            'class instantiation with "self"'                                     => [
                'testMarker' => '/* testClassInstantiationSelf */',
                'parameters' => [
                    'label',
                    'more',
                ],
            ],
            'class instantiation with "static"'                                   => [
                'testMarker' => '/* testClassInstantiationStatic */',
                'parameters' => [
                    'label',
                    'more',
                ],
            ],
            'anonymous class instantiation'                                       => [
                'testMarker' => '/* testAnonClass */',
                'parameters' => [
                    'label',
                    'more',
                ],
            ],
            'function call with non-ascii characters in the variable name labels' => [
                'testMarker' => '/* testNonAsciiNames */',
                'parameters' => [
                    '💩💩💩',
                    'Пасха',
                    '_valid',
                ],
            ],

            // Coding errors which should still be handled.
            'invalid: named arg before positional (compile error)'                => [
                'testMarker' => '/* testCompileErrorNamedBeforePositional */',
                'parameters' => [
                    'param',
                ],
            ],
            'invalid: duplicate parameter name [1]'                               => [
                'testMarker' => '/* testDuplicateName1 */',
                'parameters' => [
                    'param',
                ],
            ],
            'invalid: duplicate parameter name [2]'                               => [
                'testMarker' => '/* testDuplicateName2 */',
                'parameters' => [
                    'param',
                ],
            ],
            'invalid: named arg before variadic (error exception)'                => [
                'testMarker' => '/* testIncorrectOrderWithVariadic */',
                'parameters' => [
                    'start_index',
                ],
            ],
            'invalid: named arg after variadic (compile error)'                   => [
                'testMarker' => '/* testCompileErrorIncorrectOrderWithVariadic */',
                'parameters' => [
                    'param',
                ],
            ],
            'invalid: named arg without value (parse error)'                      => [
                'testMarker' => '/* testParseErrorNoValue */',
                'parameters' => [
                    'param1',
                    'param2',
                ],
            ],
            'invalid: named arg in exit() (parse error)'                          => [
                'testMarker' => '/* testParseErrorExit */',
                'parameters' => [
                    'status',
                ],
            ],
            'invalid: named arg in empty() (parse error)'                         => [
                'testMarker' => '/* testParseErrorEmpty */',
                'parameters' => [
                    'variable',
                ],
            ],
            'invalid: named arg in eval() (parse error)'                          => [
                'testMarker' => '/* testParseErrorEval */',
                'parameters' => [
                    'code',
                ],
            ],
            'invalid: named arg in arbitrary parentheses (parse error)'           => [
                'testMarker' => '/* testParseErrorArbitraryParentheses */',
                'parameters' => [
                    'something',
                ],
            ],
        ];

    }//end dataNamedFunctionCallArguments()


    /**
     * Verify that other T_STRING tokens within a function call are still tokenized as T_STRING.
     *
     * @param string $testMarker The comment prefacing the target token.
     * @param string $content    The token content to look for.
     *
     * @dataProvider dataOtherTstringInFunctionCall
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testOtherTstringInFunctionCall($testMarker, $content)
    {
        $tokens = $this->phpcsFile->getTokens();

        $label = $this->getTargetToken($testMarker, [T_STRING, T_PARAM_NAME], $content);

        $this->assertSame(
            T_STRING,
            $tokens[$label]['code'],
            'Token tokenized as '.$tokens[$label]['type'].', not T_STRING (code)'
        );
        $this->assertSame(
            'T_STRING',
            $tokens[$label]['type'],
            'Token tokenized as '.$tokens[$label]['type'].', not T_STRING (type)'
        );

    }//end testOtherTstringInFunctionCall()


    /**
     * Data provider.
     *
     * @see testOtherTstringInFunctionCall()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataOtherTstringInFunctionCall()
    {
        return [
            'not arg name - global constant'             => [
                'testMarker' => '/* testPositionalArgs */',
                'content'    => 'START_INDEX',
            ],
            'not arg name - fully qualified constant'    => [
                'testMarker' => '/* testPositionalArgs */',
                'content'    => 'COUNT',
            ],
            'not arg name - namespace relative constant' => [
                'testMarker' => '/* testPositionalArgs */',
                'content'    => 'VALUE',
            ],
            'not arg name - unqualified function call'   => [
                'testMarker' => '/* testNestedFunctionCallInner2 */',
                'content'    => 'count',
            ],
        ];

    }//end dataOtherTstringInFunctionCall()


    /**
     * Verify whether the colons are tokenized correctly when a ternary is used in a mixed
     * positional and named arguments function call.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testMixedPositionalAndNamedArgsWithTernary()
    {
        $tokens = $this->phpcsFile->getTokens();

        $true = $this->getTargetToken('/* testMixedPositionalAndNamedArgsWithTernary */', T_TRUE);

        // Get the next non-empty token.
        $colon = $this->phpcsFile->findNext(Tokens::$emptyTokens, ($true + 1), null, true);

        $this->assertSame(
            T_INLINE_ELSE,
            $tokens[$colon]['code'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_INLINE_ELSE (code)'
        );
        $this->assertSame(
            'T_INLINE_ELSE',
            $tokens[$colon]['type'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_INLINE_ELSE (type)'
        );

        $label = $this->getTargetToken('/* testMixedPositionalAndNamedArgsWithTernary */', T_PARAM_NAME, 'name');

        // Get the next non-empty token.
        $colon = $this->phpcsFile->findNext(Tokens::$emptyTokens, ($label + 1), null, true);

        $this->assertSame(
            ':',
            $tokens[$colon]['content'],
            'Next token after parameter name is not a colon. Found: '.$tokens[$colon]['content']
        );
        $this->assertSame(
            T_COLON,
            $tokens[$colon]['code'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (code)'
        );
        $this->assertSame(
            'T_COLON',
            $tokens[$colon]['type'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (type)'
        );

    }//end testMixedPositionalAndNamedArgsWithTernary()


    /**
     * Verify whether the colons are tokenized correctly when a ternary is used
     * in a named arguments function call.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testNamedArgWithTernary()
    {
        $tokens = $this->phpcsFile->getTokens();

        /*
         * First argument.
         */

        $label = $this->getTargetToken('/* testNamedArgWithTernary */', T_PARAM_NAME, 'label');

        // Get the next non-empty token.
        $colon = $this->phpcsFile->findNext(Tokens::$emptyTokens, ($label + 1), null, true);

        $this->assertSame(
            ':',
            $tokens[$colon]['content'],
            'First arg: Next token after parameter name is not a colon. Found: '.$tokens[$colon]['content']
        );
        $this->assertSame(
            T_COLON,
            $tokens[$colon]['code'],
            'First arg: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (code)'
        );
        $this->assertSame(
            'T_COLON',
            $tokens[$colon]['type'],
            'First arg: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (type)'
        );

        $true = $this->getTargetToken('/* testNamedArgWithTernary */', T_TRUE);

        // Get the next non-empty token.
        $colon = $this->phpcsFile->findNext(Tokens::$emptyTokens, ($true + 1), null, true);

        $this->assertSame(
            T_INLINE_ELSE,
            $tokens[$colon]['code'],
            'First arg ternary: Token tokenized as '.$tokens[$colon]['type'].', not T_INLINE_ELSE (code)'
        );
        $this->assertSame(
            'T_INLINE_ELSE',
            $tokens[$colon]['type'],
            'First arg ternary: Token tokenized as '.$tokens[$colon]['type'].', not T_INLINE_ELSE (type)'
        );

        /*
         * Second argument.
         */

        $label = $this->getTargetToken('/* testNamedArgWithTernary */', T_PARAM_NAME, 'more');

        // Get the next non-empty token.
        $colon = $this->phpcsFile->findNext(Tokens::$emptyTokens, ($label + 1), null, true);

        $this->assertSame(
            ':',
            $tokens[$colon]['content'],
            'Second arg: Next token after parameter name is not a colon. Found: '.$tokens[$colon]['content']
        );
        $this->assertSame(
            T_COLON,
            $tokens[$colon]['code'],
            'Second arg: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (code)'
        );
        $this->assertSame(
            'T_COLON',
            $tokens[$colon]['type'],
            'Second arg: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (type)'
        );

        $true = $this->getTargetToken('/* testNamedArgWithTernary */', T_STRING, 'CONSTANT_A');

        // Get the next non-empty token.
        $colon = $this->phpcsFile->findNext(Tokens::$emptyTokens, ($true + 1), null, true);

        $this->assertSame(
            T_INLINE_ELSE,
            $tokens[$colon]['code'],
            'Second arg ternary: Token tokenized as '.$tokens[$colon]['type'].', not T_INLINE_ELSE (code)'
        );
        $this->assertSame(
            'T_INLINE_ELSE',
            $tokens[$colon]['type'],
            'Second arg ternary: Token tokenized as '.$tokens[$colon]['type'].', not T_INLINE_ELSE (type)'
        );

    }//end testNamedArgWithTernary()


    /**
     * Verify whether the colons are tokenized correctly when named arguments
     * function calls are used in a ternary.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testTernaryWithFunctionCallsInThenElse()
    {
        $tokens = $this->phpcsFile->getTokens();

        /*
         * Then.
         */

        $label = $this->getTargetToken('/* testTernaryWithFunctionCallsInThenElse */', T_PARAM_NAME, 'label');

        // Get the next non-empty token.
        $colon = $this->phpcsFile->findNext(Tokens::$emptyTokens, ($label + 1), null, true);

        $this->assertSame(
            ':',
            $tokens[$colon]['content'],
            'Function in then: Next token after parameter name is not a colon. Found: '.$tokens[$colon]['content']
        );
        $this->assertSame(
            T_COLON,
            $tokens[$colon]['code'],
            'Function in then: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (code)'
        );
        $this->assertSame(
            'T_COLON',
            $tokens[$colon]['type'],
            'Function in then: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (type)'
        );

        $closeParens = $this->getTargetToken('/* testTernaryWithFunctionCallsInThenElse */', T_CLOSE_PARENTHESIS);

        // Get the next non-empty token.
        $colon = $this->phpcsFile->findNext(Tokens::$emptyTokens, ($closeParens + 1), null, true);

        $this->assertSame(
            T_INLINE_ELSE,
            $tokens[$colon]['code'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_INLINE_ELSE (code)'
        );
        $this->assertSame(
            'T_INLINE_ELSE',
            $tokens[$colon]['type'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_INLINE_ELSE (type)'
        );

        /*
         * Else.
         */

        $label = $this->getTargetToken('/* testTernaryWithFunctionCallsInThenElse */', T_PARAM_NAME, 'more');

        // Get the next non-empty token.
        $colon = $this->phpcsFile->findNext(Tokens::$emptyTokens, ($label + 1), null, true);

        $this->assertSame(
            ':',
            $tokens[$colon]['content'],
            'Function in else: Next token after parameter name is not a colon. Found: '.$tokens[$colon]['content']
        );
        $this->assertSame(
            T_COLON,
            $tokens[$colon]['code'],
            'Function in else: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (code)'
        );
        $this->assertSame(
            'T_COLON',
            $tokens[$colon]['type'],
            'Function in else: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (type)'
        );

    }//end testTernaryWithFunctionCallsInThenElse()


    /**
     * Verify whether the colons are tokenized correctly when constants are used in a ternary.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testTernaryWithConstantsInThenElse()
    {
        $tokens = $this->phpcsFile->getTokens();

        $constant = $this->getTargetToken('/* testTernaryWithConstantsInThenElse */', T_STRING, 'CONSTANT_NAME');

        // Get the next non-empty token.
        $colon = $this->phpcsFile->findNext(Tokens::$emptyTokens, ($constant + 1), null, true);

        $this->assertSame(
            T_INLINE_ELSE,
            $tokens[$colon]['code'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_INLINE_ELSE (code)'
        );
        $this->assertSame(
            'T_INLINE_ELSE',
            $tokens[$colon]['type'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_INLINE_ELSE (type)'
        );

    }//end testTernaryWithConstantsInThenElse()


    /**
     * Verify whether the colons are tokenized correctly in a switch statement.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testSwitchStatement()
    {
        $tokens = $this->phpcsFile->getTokens();

        $label = $this->getTargetToken('/* testSwitchCaseWithConstant */', T_STRING, 'MY_CONSTANT');

        // Get the next non-empty token.
        $colon = $this->phpcsFile->findNext(Tokens::$emptyTokens, ($label + 1), null, true);

        $this->assertSame(
            T_COLON,
            $tokens[$colon]['code'],
            'First case: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (code)'
        );
        $this->assertSame(
            'T_COLON',
            $tokens[$colon]['type'],
            'First case: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (type)'
        );

        $label = $this->getTargetToken('/* testSwitchCaseWithClassProperty */', T_STRING, 'property');

        // Get the next non-empty token.
        $colon = $this->phpcsFile->findNext(Tokens::$emptyTokens, ($label + 1), null, true);

        $this->assertSame(
            T_COLON,
            $tokens[$colon]['code'],
            'Second case: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (code)'
        );
        $this->assertSame(
            'T_COLON',
            $tokens[$colon]['type'],
            'Second case: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (type)'
        );

        $default = $this->getTargetToken('/* testSwitchDefault */', T_DEFAULT);

        // Get the next non-empty token.
        $colon = $this->phpcsFile->findNext(Tokens::$emptyTokens, ($default + 1), null, true);

        $this->assertSame(
            T_COLON,
            $tokens[$colon]['code'],
            'Default case: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (code)'
        );
        $this->assertSame(
            'T_COLON',
            $tokens[$colon]['type'],
            'Default case: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (type)'
        );

    }//end testSwitchStatement()


    /**
     * Verify that a variable parameter label (parse error) is still tokenized as T_VARIABLE.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testParseErrorVariableLabel()
    {
        $tokens = $this->phpcsFile->getTokens();

        $label = $this->getTargetToken('/* testParseErrorDynamicName */', [T_VARIABLE, T_PARAM_NAME], '$variableStoringParamName');

        $this->assertSame(
            T_VARIABLE,
            $tokens[$label]['code'],
            'Token tokenized as '.$tokens[$label]['type'].', not T_VARIABLE (code)'
        );
        $this->assertSame(
            'T_VARIABLE',
            $tokens[$label]['type'],
            'Token tokenized as '.$tokens[$label]['type'].', not T_VARIABLE (type)'
        );

        // Get the next non-empty token.
        $colon = $this->phpcsFile->findNext(Tokens::$emptyTokens, ($label + 1), null, true);

        $this->assertSame(
            ':',
            $tokens[$colon]['content'],
            'Next token after parameter name is not a colon. Found: '.$tokens[$colon]['content']
        );
        $this->assertSame(
            T_COLON,
            $tokens[$colon]['code'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (code)'
        );
        $this->assertSame(
            'T_COLON',
            $tokens[$colon]['type'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (type)'
        );

    }//end testParseErrorVariableLabel()


    /**
     * Verify whether the colons are tokenized correctly when a return type is used for an inline
     * closure/arrow function declaration in a ternary.
     *
     * @param string $testMarker The comment prefacing the target token.
     *
     * @dataProvider dataOtherColonsInTernary
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testOtherColonsInTernary($testMarker)
    {
        $tokens = $this->phpcsFile->getTokens();

        $startOfStatement = $this->getTargetToken($testMarker, T_VARIABLE);

        // Walk the statement and check the tokenization.
        // There should be no T_PARAM_NAME tokens.
        // First colon should be T_COLON for the return type.
        // Second colon should be T_INLINE_ELSE for the ternary.
        // Third colon should be T_COLON for the return type.
        $colonCount = 0;
        for ($i = ($startOfStatement + 1); $tokens[$i]['line'] === $tokens[$startOfStatement]['line']; $i++) {
            $this->assertNotEquals(T_PARAM_NAME, $tokens[$i]['code'], "Token $i is tokenized as parameter label");

            if ($tokens[$i]['content'] === ':') {
                ++$colonCount;

                if ($colonCount === 1) {
                    $this->assertSame(T_COLON, $tokens[$i]['code'], 'First colon is not tokenized as T_COLON');
                } else if ($colonCount === 2) {
                    $this->assertSame(T_INLINE_ELSE, $tokens[$i]['code'], 'Second colon is not tokenized as T_INLINE_ELSE');
                } else if ($colonCount === 3) {
                    $this->assertSame(T_COLON, $tokens[$i]['code'], 'Third colon is not tokenized as T_COLON');
                } else {
                    $this->fail('Unexpected colon encountered in statement');
                }
            }
        }

    }//end testOtherColonsInTernary()


    /**
     * Data provider.
     *
     * @see testOtherColonsInTernary()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataOtherColonsInTernary()
    {
        return [
            'closures with return types in ternary'        => [
                'testMarker' => '/* testTernaryWithClosuresAndReturnTypes */',
            ],
            'arrow functions with return types in ternary' => [
                'testMarker' => '/* testTernaryWithArrowFunctionsAndReturnTypes */',
            ],
        ];

    }//end dataOtherColonsInTernary()


    /**
     * Verify that reserved keywords used as a parameter label are tokenized as T_PARAM_NAME
     * and that the colon after it is tokenized as a T_COLON.
     *
     * @param string            $testMarker   The comment prefacing the target token.
     * @param array<string|int> $tokenTypes   The token codes to look for.
     * @param string            $tokenContent The token content to look for.
     *
     * @dataProvider dataReservedKeywordsAsName
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testReservedKeywordsAsName($testMarker, $tokenTypes, $tokenContent)
    {
        $tokens = $this->phpcsFile->getTokens();
        $label  = $this->getTargetToken($testMarker, $tokenTypes, $tokenContent);

        $this->assertSame(
            T_PARAM_NAME,
            $tokens[$label]['code'],
            'Token tokenized as '.$tokens[$label]['type'].', not T_PARAM_NAME (code)'
        );
        $this->assertSame(
            'T_PARAM_NAME',
            $tokens[$label]['type'],
            'Token tokenized as '.$tokens[$label]['type'].', not T_PARAM_NAME (type)'
        );

        // Get the next non-empty token.
        $colon = $this->phpcsFile->findNext(Tokens::$emptyTokens, ($label + 1), null, true);

        $this->assertSame(
            ':',
            $tokens[$colon]['content'],
            'Next token after parameter name is not a colon. Found: '.$tokens[$colon]['content']
        );
        $this->assertSame(
            T_COLON,
            $tokens[$colon]['code'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (code)'
        );
        $this->assertSame(
            'T_COLON',
            $tokens[$colon]['type'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (type)'
        );

    }//end testReservedKeywordsAsName()


    /**
     * Data provider.
     *
     * @see testReservedKeywordsAsName()
     *
     * @return array<string, array<string|array<string|int>>>
     */
    public static function dataReservedKeywordsAsName()
    {
        $reservedKeywords = [
            // '__halt_compiler', NOT TESTABLE
            'abstract',
            'and',
            'array',
            'as',
            'break',
            'callable',
            'case',
            'catch',
            'class',
            'clone',
            'const',
            'continue',
            'declare',
            'default',
            'die',
            'do',
            'echo',
            'else',
            'elseif',
            'empty',
            'enddeclare',
            'endfor',
            'endforeach',
            'endif',
            'endswitch',
            'endwhile',
            'enum',
            'eval',
            'exit',
            'extends',
            'final',
            'finally',
            'fn',
            'for',
            'foreach',
            'function',
            'global',
            'goto',
            'if',
            'implements',
            'include',
            'include_once',
            'instanceof',
            'insteadof',
            'interface',
            'isset',
            'list',
            'match',
            'namespace',
            'new',
            'or',
            'print',
            'private',
            'protected',
            'public',
            'readonly',
            'require',
            'require_once',
            'return',
            'static',
            'switch',
            'throw',
            'trait',
            'try',
            'unset',
            'use',
            'var',
            'while',
            'xor',
            'yield',
            'int',
            'float',
            'bool',
            'string',
            'true',
            'false',
            'null',
            'void',
            'iterable',
            'object',
            'resource',
            'mixed',
            'numeric',
            'never',

            // Not reserved keyword, but do have their own token in PHPCS.
            'parent',
            'self',
        ];

        $data = [];

        foreach ($reservedKeywords as $keyword) {
            $tokensTypes = [
                T_PARAM_NAME,
                T_STRING,
                T_GOTO_LABEL,
            ];
            $tokenName   = 'T_'.strtoupper($keyword);

            if ($keyword === 'and') {
                $tokensTypes[] = T_LOGICAL_AND;
            } else if ($keyword === 'die') {
                $tokensTypes[] = T_EXIT;
            } else if ($keyword === 'or') {
                $tokensTypes[] = T_LOGICAL_OR;
            } else if ($keyword === 'xor') {
                $tokensTypes[] = T_LOGICAL_XOR;
            } else if ($keyword === '__halt_compiler') {
                $tokensTypes[] = T_HALT_COMPILER;
            } else if (defined($tokenName) === true) {
                $tokensTypes[] = constant($tokenName);
            }

            $data[$keyword.'FirstParam'] = [
                '/* testReservedKeyword'.ucfirst($keyword).'1 */',
                $tokensTypes,
                $keyword,
            ];

            $data[$keyword.'SecondParam'] = [
                '/* testReservedKeyword'.ucfirst($keyword).'2 */',
                $tokensTypes,
                $keyword,
            ];
        }//end foreach

        return $data;

    }//end dataReservedKeywordsAsName()


}//end class
