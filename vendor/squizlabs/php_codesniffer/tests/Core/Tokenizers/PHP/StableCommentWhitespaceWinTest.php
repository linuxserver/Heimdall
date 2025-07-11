<?php
/**
 * Tests the comment tokenization with Windows line endings.
 *
 * Basically the same as the StableCommentWhitespaceTest, but now for
 * Windows line endings.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;
use PHP_CodeSniffer\Util\Tokens;

final class StableCommentWhitespaceWinTest extends AbstractTokenizerTestCase
{


    /**
     * Test that comment tokenization with new lines at the end of the comment is stable.
     *
     * @param string                       $testMarker     The comment prefacing the test.
     * @param array<array<string, string>> $expectedTokens The tokenization expected.
     *
     * @dataProvider dataCommentTokenization
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testCommentTokenization($testMarker, $expectedTokens)
    {
        $tokens  = $this->phpcsFile->getTokens();
        $comment = $this->getTargetToken($testMarker, Tokens::$commentTokens);

        foreach ($expectedTokens as $tokenInfo) {
            $this->assertSame(
                constant($tokenInfo['type']),
                $tokens[$comment]['code'],
                'Token tokenized as '.Tokens::tokenName($tokens[$comment]['code']).', not '.$tokenInfo['type'].' (code)'
            );
            $this->assertSame(
                $tokenInfo['type'],
                $tokens[$comment]['type'],
                'Token tokenized as '.$tokens[$comment]['type'].', not '.$tokenInfo['type'].' (type)'
            );
            $this->assertSame($tokenInfo['content'], $tokens[$comment]['content']);

            ++$comment;
        }

    }//end testCommentTokenization()


    /**
     * Data provider.
     *
     * @see testCommentTokenization()
     *
     * @return array<string, array<string, string|array<array<string, string>>>>
     */
    public static function dataCommentTokenization()
    {
        return [
            'slash comment, single line'                                  => [
                'testMarker'     => '/* testSingleLineSlashComment */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            'slash comment, single line, trailing'                        => [
                'testMarker'     => '/* testSingleLineSlashCommentTrailing */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            'slash ignore annotation, single line'                        => [
                'testMarker'     => '/* testSingleLineSlashAnnotation */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_PHPCS_DISABLE',
                        'content' => '// phpcs:disable Stnd.Cat
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            'slash comment, multi-line'                                   => [
                'testMarker'     => '/* testMultiLineSlashComment */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment1
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment2
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment3
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            'slash comment, multi-line, indented'                         => [
                'testMarker'     => '/* testMultiLineSlashCommentWithIndent */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment1
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment2
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment3
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            'slash comment, multi-line, ignore annotation as first line'  => [
                'testMarker'     => '/* testMultiLineSlashCommentWithAnnotationStart */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_PHPCS_IGNORE',
                        'content' => '// phpcs:ignore Stnd.Cat
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment2
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment3
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            'slash comment, multi-line, ignore annotation as middle line' => [
                'testMarker'     => '/* testMultiLineSlashCommentWithAnnotationMiddle */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment1
',
                    ],
                    [
                        'type'    => 'T_PHPCS_IGNORE',
                        'content' => '// @phpcs:ignore Stnd.Cat
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment3
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            'slash comment, multi-line, ignore annotation as last line'   => [
                'testMarker'     => '/* testMultiLineSlashCommentWithAnnotationEnd */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment1
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment2
',
                    ],
                    [
                        'type'    => 'T_PHPCS_IGNORE',
                        'content' => '// phpcs:ignore Stnd.Cat
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            'slash comment, single line, without new line at end'         => [
                'testMarker'     => '/* testSingleLineSlashCommentNoNewLineAtEnd */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Slash ',
                    ],
                    [
                        'type'    => 'T_CLOSE_TAG',
                        'content' => '?>
',
                    ],
                ],
            ],
            'hash comment, single line'                                   => [
                'testMarker'     => '/* testSingleLineHashComment */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '# Comment
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            'hash comment, single line, trailing'                         => [
                'testMarker'     => '/* testSingleLineHashCommentTrailing */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '# Comment
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            'hash comment, multi-line'                                    => [
                'testMarker'     => '/* testMultiLineHashComment */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '# Comment1
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '# Comment2
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '# Comment3
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            'hash comment, multi-line, indented'                          => [
                'testMarker'     => '/* testMultiLineHashCommentWithIndent */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '# Comment1
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '# Comment2
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '# Comment3
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            'hash comment, single line, without new line at end'          => [
                'testMarker'     => '/* testSingleLineHashCommentNoNewLineAtEnd */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '# Hash ',
                    ],
                    [
                        'type'    => 'T_CLOSE_TAG',
                        'content' => '?>
',
                    ],
                ],
            ],
            'unclosed star comment at end of file'                        => [
                'testMarker'     => '/* testCommentAtEndOfFile */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '/* Comment',
                    ],
                ],
            ],
        ];

    }//end dataCommentTokenization()


}//end class
