<?php
/**
 * Tests that switch "case" statements get scope indexes, while enum "case" statements do not.
 *
 * @author    Jaroslav Hanslík <kukulich@kukulich.cz>
 * @copyright 2021 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\Tokenizer;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

final class RecurseScopeMapCaseKeywordConditionsTest extends AbstractTokenizerTestCase
{


    /**
     * Test that enum "case" tokens does not get scope indexes.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataEnumCases
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::recurseScopeMap
     *
     * @return void
     */
    public function testEnumCases($testMarker)
    {
        $tokens     = $this->phpcsFile->getTokens();
        $enumCase   = $this->getTargetToken($testMarker, [T_ENUM_CASE, T_CASE]);
        $tokenArray = $tokens[$enumCase];

        // Make sure we're looking at the right token.
        $this->assertSame(T_ENUM_CASE, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_ENUM_CASE (code)');

        $this->assertArrayNotHasKey('scope_condition', $tokenArray, 'Scope condition is set');
        $this->assertArrayNotHasKey('scope_opener', $tokenArray, 'Scope opener is set');
        $this->assertArrayNotHasKey('scope_closer', $tokenArray, 'Scope closer is set');

    }//end testEnumCases()


    /**
     * Data provider.
     *
     * @see testEnumCases()
     *
     * @return array<string, array<string>>
     */
    public static function dataEnumCases()
    {
        return [
            'enum case, no value'                                        => ['/* testPureEnumCase */'],
            'enum case, integer value'                                   => ['/* testBackingIntegerEnumCase */'],
            'enum case, string value'                                    => ['/* testBackingStringEnumCase */'],
            'enum case, integer value in more complex enum'              => ['/* testEnumCaseInComplexEnum */'],
            'enum case, keyword in mixed case'                           => ['/* testEnumCaseIsCaseInsensitive */'],
            'enum case, after switch statement'                          => ['/* testEnumCaseAfterSwitch */'],
            'enum case, after switch statement using alternative syntax' => ['/* testEnumCaseAfterSwitchWithEndSwitch */'],
        ];

    }//end dataEnumCases()


    /**
     * Test that switch "case" tokens do get the scope indexes.
     *
     * @param string                    $testMarker       The comment which prefaces the target token in the test file.
     * @param array<string, int|string> $expectedTokens   The expected token codes for the scope opener/closer.
     * @param string|null               $testCloserMarker Optional. The comment which prefaces the scope closer if different
     *                                                    from the test marker.
     *
     * @dataProvider dataNotEnumCases
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::recurseScopeMap
     *
     * @return void
     */
    public function testNotEnumCases($testMarker, $expectedTokens, $testCloserMarker=null)
    {
        $tokens     = $this->phpcsFile->getTokens();
        $caseIndex  = $this->getTargetToken($testMarker, [T_ENUM_CASE, T_CASE]);
        $tokenArray = $tokens[$caseIndex];

        $scopeCloserMarker = $testMarker;
        if (isset($testCloserMarker) === true) {
            $scopeCloserMarker = $testCloserMarker;
        }

        $expectedScopeCondition = $caseIndex;
        $expectedScopeOpener    = $this->getTargetToken($testMarker, $expectedTokens['scope_opener']);
        $expectedScopeCloser    = $this->getTargetToken($scopeCloserMarker, $expectedTokens['scope_closer']);

        // Make sure we're looking at the right token.
        $this->assertSame(T_CASE, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_CASE (code)');

        $this->assertArrayHasKey('scope_condition', $tokenArray, 'Scope condition is not set');
        $this->assertSame(
            $expectedScopeCondition,
            $tokenArray['scope_condition'],
            sprintf(
                'Scope condition not set correctly; expected T_CASE, found %s.',
                $tokens[$tokenArray['scope_condition']]['type']
            )
        );

        $this->assertArrayHasKey('scope_opener', $tokenArray, 'Scope opener is not set');
        $this->assertSame(
            $expectedScopeOpener,
            $tokenArray['scope_opener'],
            sprintf(
                'Scope opener not set correctly; expected %s, found %s.',
                $tokens[$expectedScopeOpener]['type'],
                $tokens[$tokenArray['scope_opener']]['type']
            )
        );

        $this->assertArrayHasKey('scope_closer', $tokenArray, 'Scope closer is not set');
        $this->assertSame(
            $expectedScopeCloser,
            $tokenArray['scope_closer'],
            sprintf(
                'Scope closer not set correctly; expected %s, found %s.',
                $tokens[$expectedScopeCloser]['type'],
                $tokens[$tokenArray['scope_closer']]['type']
            )
        );

    }//end testNotEnumCases()


    /**
     * Data provider.
     *
     * @see testNotEnumCases()
     *
     * @return array<string, array<string, string|array<string, int|string>>>
     */
    public static function dataNotEnumCases()
    {
        return [
            'switch case with constant, semicolon condition end'                => [
                'testMarker'     => '/* testCaseWithSemicolonIsNotEnumCase */',
                'expectedTokens' => [
                    'scope_opener' => T_SEMICOLON,
                    'scope_closer' => T_CLOSE_CURLY_BRACKET,
                ],
            ],
            'switch case with constant, colon condition end'                    => [
                'testMarker'       => '/* testCaseWithConstantIsNotEnumCase */',
                'expectedTokens'   => [
                    'scope_opener' => T_COLON,
                    'scope_closer' => T_CLOSE_CURLY_BRACKET,
                ],
                'testCloserMarker' => '/* testCaseConstantCloserMarker */',
            ],
            'switch case with constant, comparison'                             => [
                'testMarker'       => '/* testCaseWithConstantAndIdenticalIsNotEnumCase */',
                'expectedTokens'   => [
                    'scope_opener' => T_COLON,
                    'scope_closer' => T_CLOSE_CURLY_BRACKET,
                ],
                'testCloserMarker' => '/* testCaseConstantCloserMarker */',
            ],
            'switch case with constant, assignment'                             => [
                'testMarker'       => '/* testCaseWithAssignmentToConstantIsNotEnumCase */',
                'expectedTokens'   => [
                    'scope_opener' => T_COLON,
                    'scope_closer' => T_CLOSE_CURLY_BRACKET,
                ],
                'testCloserMarker' => '/* testCaseConstantCloserMarker */',
            ],
            'switch case with constant, keyword in mixed case'                  => [
                'testMarker'       => '/* testIsNotEnumCaseIsCaseInsensitive */',
                'expectedTokens'   => [
                    'scope_opener' => T_COLON,
                    'scope_closer' => T_CLOSE_CURLY_BRACKET,
                ],
                'testCloserMarker' => '/* testCaseConstantCloserMarker */',
            ],
            'switch case, body in curlies declares enum'                        => [
                'testMarker'       => '/* testCaseInSwitchWhenCreatingEnumInSwitch1 */',
                'expectedTokens'   => [
                    'scope_opener' => T_OPEN_CURLY_BRACKET,
                    'scope_closer' => T_CLOSE_CURLY_BRACKET,
                ],
                'testCloserMarker' => '/* testCaseInSwitchWhenCreatingEnumInSwitch1CloserMarker */',
            ],
            'switch case, body after semicolon declares enum'                   => [
                'testMarker'       => '/* testCaseInSwitchWhenCreatingEnumInSwitch2 */',
                'expectedTokens'   => [
                    'scope_opener' => T_SEMICOLON,
                    'scope_closer' => T_BREAK,
                ],
                'testCloserMarker' => '/* testCaseInSwitchWhenCreatingEnumInSwitch2CloserMarker */',
            ],
            'switch case, shared closer with switch'                            => [
                'testMarker'     => '/* testSwitchCaseScopeCloserSharedWithSwitch */',
                'expectedTokens' => [
                    'scope_opener' => T_COLON,
                    'scope_closer' => T_ENDSWITCH,
                ],
            ],
            'switch case, nested inline if/elseif/else with and without braces' => [
                'testMarker'     => '/* testSwitchCaseNestedIfWithAndWithoutBraces */',
                'expectedTokens' => [
                    'scope_opener' => T_COLON,
                    'scope_closer' => T_BREAK,
                ],
            ],
            'switch case, nested inline if'                                     => [
                'testMarker'     => '/* testSwitchCaseNestedInlineIfWithMoreThanThreeLines */',
                'expectedTokens' => [
                    'scope_opener' => T_COLON,
                    'scope_closer' => T_BREAK,
                ],
            ],
        ];

    }//end dataNotEnumCases()


    /**
     * Test that a "case" keyword which is not a switch or enum case, does not get the scope indexes.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataKeywordAsEnumCaseNameShouldBeString
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::recurseScopeMap
     *
     * @return void
     */
    public function testKeywordAsEnumCaseNameShouldBeString($testMarker)
    {
        $tokens       = $this->phpcsFile->getTokens();
        $enumCaseName = $this->getTargetToken($testMarker, [T_STRING, T_INTERFACE, T_TRAIT, T_ENUM, T_FUNCTION, T_FALSE, T_DEFAULT, T_ARRAY]);
        $tokenArray   = $tokens[$enumCaseName];

        // Make sure we're looking at the right token.
        $this->assertSame(T_STRING, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (code)');

        $this->assertArrayNotHasKey('scope_condition', $tokenArray, 'Scope condition is set');
        $this->assertArrayNotHasKey('scope_opener', $tokenArray, 'Scope opener is set');
        $this->assertArrayNotHasKey('scope_closer', $tokenArray, 'Scope closer is set');

    }//end testKeywordAsEnumCaseNameShouldBeString()


    /**
     * Data provider.
     *
     * @see testKeywordAsEnumCaseNameShouldBeString()
     *
     * @return array<string, array<string>>
     */
    public static function dataKeywordAsEnumCaseNameShouldBeString()
    {
        return [
            '"interface" as case name' => ['/* testKeywordAsEnumCaseNameShouldBeString1 */'],
            '"trait" as case name'     => ['/* testKeywordAsEnumCaseNameShouldBeString2 */'],
            '"enum" as case name'      => ['/* testKeywordAsEnumCaseNameShouldBeString3 */'],
            '"function" as case name'  => ['/* testKeywordAsEnumCaseNameShouldBeString4 */'],
            '"false" as case name'     => ['/* testKeywordAsEnumCaseNameShouldBeString5 */'],
            '"default" as case name'   => ['/* testKeywordAsEnumCaseNameShouldBeString6 */'],
            '"array" as case name'     => ['/* testKeywordAsEnumCaseNameShouldBeString7 */'],
        ];

    }//end dataKeywordAsEnumCaseNameShouldBeString()


}//end class
