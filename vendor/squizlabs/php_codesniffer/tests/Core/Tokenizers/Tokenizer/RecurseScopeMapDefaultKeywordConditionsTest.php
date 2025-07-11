<?php
/**
 * Tests that switch "default" statements get scope indexes, while match "default" statements do not.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020-2021 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\Tokenizer;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

final class RecurseScopeMapDefaultKeywordConditionsTest extends AbstractTokenizerTestCase
{

    /**
     * Condition stop tokens when `default` is used with curlies.
     *
     * @var array<int>
     */
    protected $conditionStopTokens = [
        T_BREAK,
        T_CONTINUE,
        T_EXIT,
        T_GOTO,
        T_RETURN,
        T_THROW,
    ];


    /**
     * Test that match "default" tokens does not get scope indexes.
     *
     * Note: Cases and default structures within a match structure do *NOT* get case/default scope
     * conditions, in contrast to case and default structures in switch control structures.
     *
     * @param string $testMarker  The comment prefacing the target token.
     * @param string $testContent The token content to look for.
     *
     * @dataProvider dataMatchDefault
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::recurseScopeMap
     *
     * @return void
     */
    public function testMatchDefault($testMarker, $testContent='default')
    {
        $tokens = $this->phpcsFile->getTokens();

        $token      = $this->getTargetToken($testMarker, [T_MATCH_DEFAULT, T_DEFAULT, T_STRING], $testContent);
        $tokenArray = $tokens[$token];

        // Make sure we're looking at the right token.
        $this->assertSame(
            T_MATCH_DEFAULT,
            $tokenArray['code'],
            sprintf('Token tokenized as %s, not T_MATCH_DEFAULT (code). Marker: %s.', $tokenArray['type'], $testMarker)
        );

        $this->assertArrayNotHasKey(
            'scope_condition',
            $tokenArray,
            sprintf('Scope condition is set. Marker: %s.', $testMarker)
        );
        $this->assertArrayNotHasKey(
            'scope_opener',
            $tokenArray,
            sprintf('Scope opener is set. Marker: %s.', $testMarker)
        );
        $this->assertArrayNotHasKey(
            'scope_closer',
            $tokenArray,
            sprintf('Scope closer is set. Marker: %s.', $testMarker)
        );

    }//end testMatchDefault()


    /**
     * Data provider.
     *
     * @see testMatchDefault()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataMatchDefault()
    {
        return [
            'simple_match_default'                                    => [
                'testMarker' => '/* testSimpleMatchDefault */',
            ],
            'match_default_in_switch_case_1'                          => [
                'testMarker' => '/* testMatchDefaultNestedInSwitchCase1 */',
            ],
            'match_default_in_switch_case_2'                          => [
                'testMarker' => '/* testMatchDefaultNestedInSwitchCase2 */',
            ],
            'match_default_in_switch_default'                         => [
                'testMarker' => '/* testMatchDefaultNestedInSwitchDefault */',
            ],
            'match_default_containing_switch'                         => [
                'testMarker' => '/* testMatchDefault */',
            ],

            'match_default_with_nested_long_array_and_default_key'    => [
                'testMarker'  => '/* testMatchDefaultWithNestedLongArrayWithClassConstantKey */',
                'testContent' => 'DEFAULT',
            ],
            'match_default_with_nested_long_array_and_default_key_2'  => [
                'testMarker'  => '/* testMatchDefaultWithNestedLongArrayWithClassConstantKeyLevelDown */',
                'testContent' => 'DEFAULT',
            ],
            'match_default_with_nested_short_array_and_default_key'   => [
                'testMarker'  => '/* testMatchDefaultWithNestedShortArrayWithClassConstantKey */',
                'testContent' => 'DEFAULT',
            ],
            'match_default_with_nested_short_array_and_default_key_2' => [
                'testMarker'  => '/* testMatchDefaultWithNestedShortArrayWithClassConstantKeyLevelDown */',
                'testContent' => 'DEFAULT',
            ],
            'match_default_in_long_array'                             => [
                'testMarker'  => '/* testMatchDefaultNestedInLongArray */',
                'testContent' => 'DEFAULT',
            ],
            'match_default_in_short_array'                            => [
                'testMarker'  => '/* testMatchDefaultNestedInShortArray */',
                'testContent' => 'DEFAULT',
            ],
        ];

    }//end dataMatchDefault()


    /**
     * Test that switch "default" tokens do get the scope indexes.
     *
     * Note: Cases and default structures within a switch control structure *do* get case/default scope
     * conditions.
     *
     * @param string      $testMarker          The comment prefacing the target token.
     * @param string      $openerMarker        The comment prefacing the scope opener token.
     * @param string      $closerMarker        The comment prefacing the scope closer token.
     * @param string|null $conditionStopMarker The expected offset in relation to the testMarker, after which tokens stop
     *                                         having T_DEFAULT as a scope condition.
     * @param string      $testContent         The token content to look for.
     * @param bool        $sharedScopeCloser   Whether to skip checking for the `scope_condition` of the
     *                                         scope closer. Needed when the default and switch
     *                                         structures share a scope closer. See
     *                                         https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/810.
     *
     * @dataProvider dataSwitchDefault
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::recurseScopeMap
     *
     * @return void
     */
    public function testSwitchDefault($testMarker, $openerMarker, $closerMarker, $conditionStopMarker=null, $testContent='default', $sharedScopeCloser=false)
    {
        $tokens = $this->phpcsFile->getTokens();

        $token      = $this->getTargetToken($testMarker, [T_MATCH_DEFAULT, T_DEFAULT, T_STRING], $testContent);
        $tokenArray = $tokens[$token];
        $expectedScopeOpener = $this->getTargetToken($openerMarker, [T_COLON, T_OPEN_CURLY_BRACKET, T_SEMICOLON]);
        $expectedScopeCloser = $this->getTargetToken($closerMarker, [T_BREAK, T_CLOSE_CURLY_BRACKET, T_RETURN, T_ENDSWITCH]);

        // Make sure we're looking at the right token.
        $this->assertSame(
            T_DEFAULT,
            $tokenArray['code'],
            sprintf('Token tokenized as %s, not T_DEFAULT (code). Marker: %s.', $tokenArray['type'], $testMarker)
        );

        $this->assertArrayHasKey(
            'scope_condition',
            $tokenArray,
            sprintf('Scope condition is not set. Marker: %s.', $testMarker)
        );
        $this->assertArrayHasKey(
            'scope_opener',
            $tokenArray,
            sprintf('Scope opener is not set. Marker: %s.', $testMarker)
        );
        $this->assertArrayHasKey(
            'scope_closer',
            $tokenArray,
            sprintf('Scope closer is not set. Marker: %s.', $testMarker)
        );
        $this->assertSame(
            $token,
            $tokenArray['scope_condition'],
            sprintf('Scope condition is not the T_DEFAULT token. Marker: %s.', $testMarker)
        );
        $this->assertSame(
            $expectedScopeOpener,
            $tokenArray['scope_opener'],
            sprintf('Scope opener of the T_DEFAULT token incorrect. Marker: %s.', $testMarker)
        );
        $this->assertSame(
            $expectedScopeCloser,
            $tokenArray['scope_closer'],
            sprintf('Scope closer of the T_DEFAULT token incorrect. Marker: %s.', $testMarker)
        );

        $opener = $tokenArray['scope_opener'];
        $this->assertArrayHasKey(
            'scope_condition',
            $tokens[$opener],
            sprintf('Opener scope condition is not set. Marker: %s.', $openerMarker)
        );
        $this->assertArrayHasKey(
            'scope_opener',
            $tokens[$opener],
            sprintf('Opener scope opener is not set. Marker: %s.', $openerMarker)
        );
        $this->assertArrayHasKey(
            'scope_closer',
            $tokens[$opener],
            sprintf('Opener scope closer is not set. Marker: %s.', $openerMarker)
        );
        $this->assertSame(
            $token,
            $tokens[$opener]['scope_condition'],
            sprintf('Opener scope condition is not the T_DEFAULT token. Marker: %s.', $openerMarker)
        );
        $this->assertSame(
            $expectedScopeOpener,
            $tokens[$opener]['scope_opener'],
            sprintf('T_DEFAULT opener scope opener token incorrect. Marker: %s.', $openerMarker)
        );
        $this->assertSame(
            $expectedScopeCloser,
            $tokens[$opener]['scope_closer'],
            sprintf('T_DEFAULT opener scope closer token incorrect. Marker: %s.', $openerMarker)
        );

        $closer = $expectedScopeCloser;

        if ($sharedScopeCloser === false) {
            $closer = $tokenArray['scope_closer'];
            $this->assertArrayHasKey(
                'scope_condition',
                $tokens[$closer],
                sprintf('Closer scope condition is not set. Marker: %s.', $closerMarker)
            );
            $this->assertArrayHasKey(
                'scope_opener',
                $tokens[$closer],
                sprintf('Closer scope opener is not set. Marker: %s.', $closerMarker)
            );
            $this->assertArrayHasKey(
                'scope_closer',
                $tokens[$closer],
                sprintf('Closer scope closer is not set. Marker: %s.', $closerMarker)
            );
            $this->assertSame(
                $token,
                $tokens[$closer]['scope_condition'],
                sprintf('Closer scope condition is not the T_DEFAULT token. Marker: %s.', $closerMarker)
            );
            $this->assertSame(
                $expectedScopeOpener,
                $tokens[$closer]['scope_opener'],
                sprintf('T_DEFAULT closer scope opener token incorrect. Marker: %s.', $closerMarker)
            );
            $this->assertSame(
                $expectedScopeCloser,
                $tokens[$closer]['scope_closer'],
                sprintf('T_DEFAULT closer scope closer token incorrect. Marker: %s.', $closerMarker)
            );
        }//end if

        if (($opener + 1) !== $closer) {
            $end = $closer;
            if (isset($conditionStopMarker) === true) {
                $end = ( $this->getTargetToken($conditionStopMarker, $this->conditionStopTokens) + 1);
            }

            for ($i = ($opener + 1); $i < $end; $i++) {
                $this->assertArrayHasKey(
                    $token,
                    $tokens[$i]['conditions'],
                    sprintf('T_DEFAULT condition not added for token belonging to the T_DEFAULT structure. Marker: %s.', $testMarker)
                );
            }
        }//end if

    }//end testSwitchDefault()


    /**
     * Data provider.
     *
     * @see testSwitchDefault()
     *
     * @return array<string, array<string, string|int>>
     */
    public static function dataSwitchDefault()
    {
        return [
            'simple_switch_default'                                     => [
                'testMarker'   => '/* testSimpleSwitchDefault */',
                'openerMarker' => '/* testSimpleSwitchDefault */',
                'closerMarker' => '/* testSimpleSwitchDefault */',
            ],
            'simple_switch_default_with_curlies'                        => [
                // For a default structure with curly braces, the scope opener
                // will be the open curly and the closer the close curly.
                // However, scope conditions will not be set for open to close,
                // but only for the open token up to the "break/return/continue" etc.
                'testMarker'          => '/* testSimpleSwitchDefaultWithCurlies */',
                'openerMarker'        => '/* testSimpleSwitchDefaultWithCurliesScopeOpener */',
                'closerMarker'        => '/* testSimpleSwitchDefaultWithCurliesScopeCloser */',
                'conditionStopMarker' => '/* testSimpleSwitchDefaultWithCurliesConditionStop */',
            ],
            'switch_default_toplevel'                                   => [
                'testMarker'   => '/* testSwitchDefault */',
                'openerMarker' => '/* testSwitchDefault */',
                'closerMarker' => '/* testSwitchDefaultCloserMarker */',
            ],
            'switch_default_nested_in_match_case'                       => [
                'testMarker'   => '/* testSwitchDefaultNestedInMatchCase */',
                'openerMarker' => '/* testSwitchDefaultNestedInMatchCase */',
                'closerMarker' => '/* testSwitchDefaultNestedInMatchCase */',
            ],
            'switch_default_nested_in_match_default'                    => [
                'testMarker'   => '/* testSwitchDefaultNestedInMatchDefault */',
                'openerMarker' => '/* testSwitchDefaultNestedInMatchDefault */',
                'closerMarker' => '/* testSwitchDefaultNestedInMatchDefault */',
            ],
            'switch_and_default_sharing_scope_closer'                   => [
                'testMarker'          => '/* testSwitchAndDefaultSharingScopeCloser */',
                'openerMarker'        => '/* testSwitchAndDefaultSharingScopeCloser */',
                'closerMarker'        => '/* testSwitchAndDefaultSharingScopeCloserScopeCloser */',
                'conditionStopMarker' => null,
                'testContent'         => 'default',
                'sharedScopeCloser'   => true,
            ],
            'switch_and_default_with_nested_if_with_and_without_braces' => [
                'testMarker'   => '/* testSwitchDefaultNestedIfWithAndWithoutBraces */',
                'openerMarker' => '/* testSwitchDefaultNestedIfWithAndWithoutBraces */',
                'closerMarker' => '/* testSwitchDefaultNestedIfWithAndWithoutBracesScopeCloser */',
            ],
        ];

    }//end dataSwitchDefault()


    /**
     * Test that a "default" keyword which is not a switch or match default, does not get the scope indexes.
     *
     * @param string $testMarker  The comment prefacing the target token.
     * @param string $testContent The token content to look for.
     *
     * @dataProvider dataNotDefaultKeyword
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::recurseScopeMap
     *
     * @return void
     */
    public function testNotDefaultKeyword($testMarker, $testContent='DEFAULT')
    {
        $tokens = $this->phpcsFile->getTokens();

        $token      = $this->getTargetToken($testMarker, [T_MATCH_DEFAULT, T_DEFAULT, T_STRING], $testContent);
        $tokenArray = $tokens[$token];

        // Make sure we're looking at the right token.
        $this->assertSame(
            T_STRING,
            $tokenArray['code'],
            sprintf('Token tokenized as %s, not T_STRING (code). Marker: %s.', $tokenArray['type'], $testMarker)
        );

        $this->assertArrayNotHasKey(
            'scope_condition',
            $tokenArray,
            sprintf('Scope condition is set. Marker: %s.', $testMarker)
        );
        $this->assertArrayNotHasKey(
            'scope_opener',
            $tokenArray,
            sprintf('Scope opener is set. Marker: %s.', $testMarker)
        );
        $this->assertArrayNotHasKey(
            'scope_closer',
            $tokenArray,
            sprintf('Scope closer is set. Marker: %s.', $testMarker)
        );

    }//end testNotDefaultKeyword()


    /**
     * Data provider.
     *
     * @see testNotDefaultKeyword()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataNotDefaultKeyword()
    {
        return [
            'class-constant-as-short-array-key'                   => [
                'testMarker' => '/* testClassConstantAsShortArrayKey */',
            ],
            'class-property-as-short-array-key'                   => [
                'testMarker' => '/* testClassPropertyAsShortArrayKey */',
            ],
            'namespaced-constant-as-short-array-key'              => [
                'testMarker' => '/* testNamespacedConstantAsShortArrayKey */',
            ],
            'fqn-global-constant-as-short-array-key'              => [
                'testMarker' => '/* testFQNGlobalConstantAsShortArrayKey */',
            ],
            'class-constant-as-long-array-key'                    => [
                'testMarker' => '/* testClassConstantAsLongArrayKey */',
            ],
            'class-constant-as-yield-key'                         => [
                'testMarker' => '/* testClassConstantAsYieldKey */',
            ],

            'class-constant-as-long-array-key-nested-in-match'    => [
                'testMarker' => '/* testClassConstantAsLongArrayKeyNestedInMatch */',
            ],
            'class-constant-as-long-array-key-nested-in-match-2'  => [
                'testMarker' => '/* testClassConstantAsLongArrayKeyNestedInMatchLevelDown */',
            ],
            'class-constant-as-short-array-key-nested-in-match'   => [
                'testMarker' => '/* testClassConstantAsShortArrayKeyNestedInMatch */',
            ],
            'class-constant-as-short-array-key-nested-in-match-2' => [
                'testMarker' => '/* testClassConstantAsShortArrayKeyNestedInMatchLevelDown */',
            ],
            'class-constant-as-long-array-key-with-nested-match'  => [
                'testMarker' => '/* testClassConstantAsLongArrayKeyWithNestedMatch */',
            ],
            'class-constant-as-short-array-key-with-nested-match' => [
                'testMarker' => '/* testClassConstantAsShortArrayKeyWithNestedMatch */',
            ],

            'class-constant-in-switch-case'                       => [
                'testMarker' => '/* testClassConstantInSwitchCase */',
            ],
            'class-property-in-switch-case'                       => [
                'testMarker' => '/* testClassPropertyInSwitchCase */',
            ],
            'namespaced-constant-in-switch-case'                  => [
                'testMarker' => '/* testNamespacedConstantInSwitchCase */',
            ],
            'namespace-relative-constant-in-switch-case'          => [
                'testMarker' => '/* testNamespaceRelativeConstantInSwitchCase */',
            ],

            'class-constant-declaration'                          => [
                'testMarker' => '/* testClassConstant */',
            ],
            'class-method-declaration'                            => [
                'testMarker'  => '/* testMethodDeclaration */',
                'testContent' => 'default',
            ],
        ];

    }//end dataNotDefaultKeyword()


    /**
     * Test a specific edge case where a scope opener would be incorrectly set.
     *
     * @link https://github.com/squizlabs/PHP_CodeSniffer/issues/3326
     *
     * @covers PHP_CodeSniffer\Tokenizers\Tokenizer::recurseScopeMap
     *
     * @return void
     */
    public function testIssue3326()
    {
        $tokens = $this->phpcsFile->getTokens();

        $token      = $this->getTargetToken('/* testClassConstant */', [T_SEMICOLON]);
        $tokenArray = $tokens[$token];

        $this->assertArrayNotHasKey('scope_condition', $tokenArray, 'Scope condition is set');
        $this->assertArrayNotHasKey('scope_opener', $tokenArray, 'Scope opener is set');
        $this->assertArrayNotHasKey('scope_closer', $tokenArray, 'Scope closer is set');

    }//end testIssue3326()


}//end class
