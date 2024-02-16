<?php
/**
 * Tests that embedded variables and expressions in heredoc strings are tokenized
 * as one heredoc string token.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2022 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

final class HeredocStringTest extends AbstractTokenizerTestCase
{


    /**
     * Test that heredoc strings contain the complete interpolated string.
     *
     * @param string $testMarker      The comment which prefaces the target token in the test file.
     * @param string $expectedContent The expected content of the heredoc string.
     *
     * @dataProvider dataHeredocString
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testHeredocString($testMarker, $expectedContent)
    {
        $tokens = $this->phpcsFile->getTokens();

        $target = $this->getTargetToken($testMarker, T_HEREDOC);
        $this->assertSame($expectedContent."\n", $tokens[$target]['content']);

    }//end testHeredocString()


    /**
     * Test that heredoc strings contain the complete interpolated string when combined with other texts.
     *
     * @param string $testMarker      The comment which prefaces the target token in the test file.
     * @param string $expectedContent The expected content of the heredoc string.
     *
     * @dataProvider dataHeredocString
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testHeredocStringWrapped($testMarker, $expectedContent)
    {
        $tokens = $this->phpcsFile->getTokens();

        $testMarker = substr($testMarker, 0, -3).'Wrapped */';
        $target     = $this->getTargetToken($testMarker, T_HEREDOC);
        $this->assertSame('Do '.$expectedContent." Something\n", $tokens[$target]['content']);

    }//end testHeredocStringWrapped()


    /**
     * Data provider.
     *
     * Type reference:
     * 1. Directly embedded variables.
     * 2. Braces outside the variable.
     * 3. Braces after the dollar sign.
     * 4. Variable variables and expressions.
     *
     * @link https://wiki.php.net/rfc/deprecate_dollar_brace_string_interpolation
     *
     * @see testHeredocString()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataHeredocString()
    {
        return [
            'Type 1: simple variable'                                                  => [
                'testMarker'      => '/* testSimple1 */',
                'expectedContent' => '$foo',
            ],
            'Type 2: simple variable'                                                  => [
                'testMarker'      => '/* testSimple2 */',
                'expectedContent' => '{$foo}',
            ],
            'Type 3: simple variable'                                                  => [
                'testMarker'      => '/* testSimple3 */',
                'expectedContent' => '${foo}',
            ],
            'Type 1: array offset'                                                     => [
                'testMarker'      => '/* testDIM1 */',
                'expectedContent' => '$foo[bar]',
            ],
            'Type 2: array offset'                                                     => [
                'testMarker'      => '/* testDIM2 */',
                'expectedContent' => '{$foo[\'bar\']}',
            ],
            'Type 3: array offset'                                                     => [
                'testMarker'      => '/* testDIM3 */',
                'expectedContent' => '${foo[\'bar\']}',
            ],
            'Type 1: object property'                                                  => [
                'testMarker'      => '/* testProperty1 */',
                'expectedContent' => '$foo->bar',
            ],
            'Type 2: object property'                                                  => [
                'testMarker'      => '/* testProperty2 */',
                'expectedContent' => '{$foo->bar}',
            ],
            'Type 2: object method call'                                               => [
                'testMarker'      => '/* testMethod1 */',
                'expectedContent' => '{$foo->bar()}',
            ],
            'Type 2: closure function call'                                            => [
                'testMarker'      => '/* testClosure1 */',
                'expectedContent' => '{$foo()}',
            ],
            'Type 2: chaining various syntaxes'                                        => [
                'testMarker'      => '/* testChain1 */',
                'expectedContent' => '{$foo[\'bar\']->baz()()}',
            ],
            'Type 4: variable variables'                                               => [
                'testMarker'      => '/* testVariableVar1 */',
                'expectedContent' => '${$bar}',
            ],
            'Type 4: variable constants'                                               => [
                'testMarker'      => '/* testVariableVar2 */',
                'expectedContent' => '${(foo)}',
            ],
            'Type 4: object property'                                                  => [
                'testMarker'      => '/* testVariableVar3 */',
                'expectedContent' => '${foo->bar}',
            ],
            'Type 4: variable variable nested in array offset'                         => [
                'testMarker'      => '/* testNested1 */',
                'expectedContent' => '${foo["${bar}"]}',
            ],
            'Type 4: variable array offset nested in array offset'                     => [
                'testMarker'      => '/* testNested2 */',
                'expectedContent' => '${foo["${bar[\'baz\']}"]}',
            ],
            'Type 4: variable object property'                                         => [
                'testMarker'      => '/* testNested3 */',
                'expectedContent' => '${foo->{$baz}}',
            ],
            'Type 4: variable object property - complex with single quotes'            => [
                'testMarker'      => '/* testNested4 */',
                'expectedContent' => '${foo->{${\'a\'}}}',
            ],
            'Type 4: variable object property - complex with single and double quotes' => [
                'testMarker'      => '/* testNested5 */',
                'expectedContent' => '${foo->{"${\'a\'}"}}',
            ],
        ];

    }//end dataHeredocString()


}//end class
