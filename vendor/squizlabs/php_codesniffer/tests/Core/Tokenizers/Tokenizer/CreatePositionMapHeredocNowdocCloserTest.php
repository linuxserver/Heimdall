<?php
/**
 * Tests the tokenization of heredoc/nowdoc closer tokens.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\Tokenizer;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

/**
 * Heredoc/nowdoc closer token test.
 *
 * @requires PHP 7.3
 *
 * @covers PHP_CodeSniffer\Tokenizers\Tokenizer::createPositionMap
 */
final class CreatePositionMapHeredocNowdocCloserTest extends AbstractTokenizerTestCase
{


    /**
     * Verify that leading (indent) whitespace in a heredoc/nowdoc closer token get the tab replacement treatment.
     *
     * @param string                         $testMarker The comment prefacing the target token.
     * @param array<string, int|string|null> $expected   Expectations for the token array.
     *
     * @dataProvider dataHeredocNowdocCloserTabReplacement
     *
     * @return void
     */
    public function testHeredocNowdocCloserTabReplacement($testMarker, $expected)
    {
        $tokens = $this->phpcsFile->getTokens();

        $closer = $this->getTargetToken($testMarker, [T_END_HEREDOC, T_END_NOWDOC]);

        foreach ($expected as $key => $value) {
            if ($key === 'orig_content' && $value === null) {
                $this->assertArrayNotHasKey($key, $tokens[$closer], "Unexpected 'orig_content' key found in the token array.");
                continue;
            }

            $this->assertArrayHasKey($key, $tokens[$closer], "Key $key not found in the token array.");
            $this->assertSame($value, $tokens[$closer][$key], "Value for key $key does not match expectation.");
        }

    }//end testHeredocNowdocCloserTabReplacement()


    /**
     * Data provider.
     *
     * @see testHeredocNowdocCloserTabReplacement()
     *
     * @return array<string, array<string, string|array<string, int|string|null>>>
     */
    public static function dataHeredocNowdocCloserTabReplacement()
    {
        return [
            'Heredoc closer without indent'      => [
                'testMarker' => '/* testHeredocCloserNoIndent */',
                'expected'   => [
                    'length'       => 3,
                    'content'      => 'EOD',
                    'orig_content' => null,
                ],
            ],
            'Nowdoc closer without indent'       => [
                'testMarker' => '/* testNowdocCloserNoIndent */',
                'expected'   => [
                    'length'       => 3,
                    'content'      => 'EOD',
                    'orig_content' => null,
                ],
            ],
            'Heredoc closer with indent, spaces' => [
                'testMarker' => '/* testHeredocCloserSpaceIndent */',
                'expected'   => [
                    'length'       => 7,
                    'content'      => '    END',
                    'orig_content' => null,
                ],
            ],
            'Nowdoc closer with indent, spaces'  => [
                'testMarker' => '/* testNowdocCloserSpaceIndent */',
                'expected'   => [
                    'length'       => 8,
                    'content'      => '     END',
                    'orig_content' => null,
                ],
            ],
            'Heredoc closer with indent, tabs'   => [
                'testMarker' => '/* testHeredocCloserTabIndent */',
                'expected'   => [
                    'length'       => 8,
                    'content'      => '     END',
                    'orig_content' => '	 END',
                ],
            ],
            'Nowdoc closer with indent, tabs'    => [
                'testMarker' => '/* testNowdocCloserTabIndent */',
                'expected'   => [
                    'length'       => 7,
                    'content'      => '    END',
                    'orig_content' => '	END',
                ],
            ],
        ];

    }//end dataHeredocNowdocCloserTabReplacement()


}//end class
