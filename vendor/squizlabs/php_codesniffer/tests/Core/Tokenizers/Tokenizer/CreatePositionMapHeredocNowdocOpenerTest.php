<?php
/**
 * Tests the tokenization of heredoc/nowdoc opener tokens.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\Tokenizer;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

/**
 * Heredoc/nowdoc opener token test.
 *
 * @covers PHP_CodeSniffer\Tokenizers\Tokenizer::createPositionMap
 */
final class CreatePositionMapHeredocNowdocOpenerTest extends AbstractTokenizerTestCase
{


    /**
     * Verify that spaces/tabs in a heredoc/nowdoc opener token get the tab replacement treatment.
     *
     * @param string                         $testMarker The comment prefacing the target token.
     * @param array<string, int|string|null> $expected   Expectations for the token array.
     *
     * @dataProvider dataHeredocNowdocOpenerTabReplacement
     *
     * @return void
     */
    public function testHeredocNowdocOpenerTabReplacement($testMarker, $expected)
    {
        $tokens = $this->phpcsFile->getTokens();
        $opener = $this->getTargetToken($testMarker, [T_START_HEREDOC, T_START_NOWDOC]);

        foreach ($expected as $key => $value) {
            if ($key === 'orig_content' && $value === null) {
                $this->assertArrayNotHasKey($key, $tokens[$opener], "Unexpected 'orig_content' key found in the token array.");
                continue;
            }

            $this->assertArrayHasKey($key, $tokens[$opener], "Key $key not found in the token array.");
            $this->assertSame($value, $tokens[$opener][$key], "Value for key $key does not match expectation.");
        }

    }//end testHeredocNowdocOpenerTabReplacement()


    /**
     * Data provider.
     *
     * @see testHeredocNowdocOpenerTabReplacement()
     *
     * @return array<string, array<string, string|array<string, int|string|null>>>
     */
    public static function dataHeredocNowdocOpenerTabReplacement()
    {
        return [
            'Heredoc opener without space' => [
                'testMarker' => '/* testHeredocOpenerNoSpace */',
                'expected'   => [
                    'length'       => 6,
                    'content'      => '<<<EOD
',
                    'orig_content' => null,
                ],
            ],
            'Nowdoc opener without space'  => [
                'testMarker' => '/* testNowdocOpenerNoSpace */',
                'expected'   => [
                    'length'       => 8,
                    'content'      => "<<<'EOD'
",
                    'orig_content' => null,
                ],
            ],
            'Heredoc opener with space(s)' => [
                'testMarker' => '/* testHeredocOpenerHasSpace */',
                'expected'   => [
                    'length'       => 7,
                    'content'      => '<<< END
',
                    'orig_content' => null,
                ],
            ],
            'Nowdoc opener with space(s)'  => [
                'testMarker' => '/* testNowdocOpenerHasSpace */',
                'expected'   => [
                    'length'       => 21,
                    'content'      => "<<<             'END'
",
                    'orig_content' => null,
                ],
            ],
            'Heredoc opener with tab(s)'   => [
                'testMarker' => '/* testHeredocOpenerHasTab */',
                'expected'   => [
                    'length'       => 18,
                    'content'      => '<<<          "END"
',
                    'orig_content' => '<<<			"END"
',
                ],
            ],
            'Nowdoc opener with tab(s)'    => [
                'testMarker' => '/* testNowdocOpenerHasTab */',
                'expected'   => [
                    'length'       => 11,
                    'content'      => "<<<   'END'
",
                    'orig_content' => "<<<	'END'
",
                ],
            ],
        ];

    }//end dataHeredocNowdocOpenerTabReplacement()


}//end class
