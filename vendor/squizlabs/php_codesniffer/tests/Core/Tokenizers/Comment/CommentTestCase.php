<?php
/**
 * Base class for testing DocBlock comment tokenization.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\Comment;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Base class for testing DocBlock comment tokenization.
 *
 * @covers PHP_CodeSniffer\Tokenizers\Comment
 */
abstract class CommentTestCase extends AbstractTokenizerTestCase
{


    /**
     * Test whether the docblock opener and closer have the expected extra keys.
     *
     * @param string     $marker       The comment prefacing the target token.
     * @param int        $closerOffset The offset of the closer from the opener.
     * @param array<int> $expectedTags The expected tags offsets array.
     *
     * @dataProvider dataDocblockOpenerCloser
     *
     * @return void
     */
    public function testDocblockOpenerCloser($marker, $closerOffset, $expectedTags)
    {
        $tokens = $this->phpcsFile->getTokens();
        $target = $this->getTargetToken($marker, [T_DOC_COMMENT_OPEN_TAG]);

        $opener = $tokens[$target];

        $this->assertArrayHasKey('comment_closer', $opener, 'Comment opener: comment_closer index is not set');
        $this->assertArrayHasKey('comment_tags', $opener, 'Comment opener: comment_tags index is not set');

        $expectedCloser = ($target + $closerOffset);
        $this->assertSame($expectedCloser, $opener['comment_closer'], 'Comment opener: comment_closer not set to the expected stack pointer');

        // Update the tags expectations.
        foreach ($expectedTags as $i => $ptr) {
            $expectedTags[$i] += $target;
        }

        $this->assertSame($expectedTags, $opener['comment_tags'], 'Comment opener: recorded tags do not match expected tags');

        $closer = $tokens[$opener['comment_closer']];

        $this->assertArrayHasKey('comment_opener', $closer, 'Comment closer: comment_opener index is not set');
        $this->assertSame($target, $closer['comment_opener'], 'Comment closer: comment_opener not set to the expected stack pointer');

    }//end testDocblockOpenerCloser()


    /**
     * Data provider.
     *
     * @see testDocblockOpenerCloser()
     *
     * @return array<string, array<string, string|int|array<int>>>
     */
    abstract public static function dataDocblockOpenerCloser();


    /**
     * Test helper. Check a token sequence complies with an expected token sequence.
     *
     * @param int                              $startPtr         The position in the file to start checking from.
     * @param array<array<int|string, string>> $expectedSequence The consecutive token constants and their contents to expect.
     *
     * @return void
     */
    protected function checkTokenSequence($startPtr, array $expectedSequence)
    {
        $tokens = $this->phpcsFile->getTokens();

        $sequenceKey   = 0;
        $sequenceCount = count($expectedSequence);

        for ($i = $startPtr; $sequenceKey < $sequenceCount; $i++, $sequenceKey++) {
            $currentItem     = $expectedSequence[$sequenceKey];
            $expectedCode    = key($currentItem);
            $expectedType    = Tokens::tokenName($expectedCode);
            $expectedContent = current($currentItem);
            $errorMsgSuffix  = PHP_EOL.'(StackPtr: '.$i.' | Position in sequence: '.$sequenceKey.' | Expected: '.$expectedType.')';

            $this->assertSame(
                $expectedCode,
                $tokens[$i]['code'],
                'Token tokenized as '.Tokens::tokenName($tokens[$i]['code']).', not '.$expectedType.' (code)'.$errorMsgSuffix
            );

            $this->assertSame(
                $expectedType,
                $tokens[$i]['type'],
                'Token tokenized as '.$tokens[$i]['type'].', not '.$expectedType.' (type)'.$errorMsgSuffix
            );

            $this->assertSame(
                $expectedContent,
                $tokens[$i]['content'],
                'Token content did not match expectations'.$errorMsgSuffix
            );
        }//end for

    }//end checkTokenSequence()


}//end class
