<?php
/**
 * Tests the tokenization for heredoc/nowdoc constructs.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Tests the tokenization for heredoc/nowdoc constructs.
 *
 * Verifies that:
 * - Nowdoc opener/closers are retokenized from `T_[START_|END_]HEREDOC` to `T_[START_|END_]NOWDOC`.
 * - The contents of the heredoc/nowdoc is tokenized as `T_HEREDOC`/`T_NOWDOC`.
 * - Each line of the contents has its own token, which includes the new line char.
 *
 * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
 */
final class HeredocNowdocTest extends AbstractTokenizerTestCase
{


    /**
     * Verify tokenization a heredoc construct.
     *
     * @phpcs:disable Squiz.Arrays.ArrayDeclaration.SpaceBeforeDoubleArrow -- Readability is better with alignment.
     *
     * @return void
     */
    public function testHeredocSingleLine()
    {
        $expectedSequence = [
            [T_START_HEREDOC => '<<<EOD'."\n"],
            [T_HEREDOC       => 'Some $var text'."\n"],
            [T_END_HEREDOC   => 'EOD'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_START_HEREDOC);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testHeredocSingleLine()


    /**
     * Verify tokenization a nowdoc construct.
     *
     * @phpcs:disable Squiz.Arrays.ArrayDeclaration.SpaceBeforeDoubleArrow -- Readability is better with alignment.
     *
     * @return void
     */
    public function testNowdocSingleLine()
    {
        $expectedSequence = [
            [T_START_NOWDOC => "<<<'MARKER'\n"],
            [T_NOWDOC       => 'Some text'."\n"],
            [T_END_NOWDOC   => 'MARKER'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_START_NOWDOC);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testNowdocSingleLine()


    /**
     * Verify tokenization a multiline heredoc construct.
     *
     * @phpcs:disable Squiz.Arrays.ArrayDeclaration.SpaceBeforeDoubleArrow -- Readability is better with alignment.
     *
     * @return void
     */
    public function testHeredocMultiLine()
    {
        $expectedSequence = [
            [T_START_HEREDOC => '<<<"😬"'."\n"],
            [T_HEREDOC       => 'Lorum ipsum'."\n"],
            [T_HEREDOC       => 'Some $var text'."\n"],
            [T_HEREDOC       => 'dolor sit amet'."\n"],
            [T_END_HEREDOC   => '😬'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_START_HEREDOC);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testHeredocMultiLine()


    /**
     * Verify tokenization a multiline testNowdocSingleLine construct.
     *
     * @phpcs:disable Squiz.Arrays.ArrayDeclaration.SpaceBeforeDoubleArrow -- Readability is better with alignment.
     *
     * @return void
     */
    public function testNowdocMultiLine()
    {
        $expectedSequence = [
            [T_START_NOWDOC => "<<<'multi_line'\n"],
            [T_NOWDOC       => 'Lorum ipsum'."\n"],
            [T_NOWDOC       => 'Some text'."\n"],
            [T_NOWDOC       => 'dolor sit amet'."\n"],
            [T_END_NOWDOC   => 'multi_line'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_START_NOWDOC);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testNowdocMultiLine()


    /**
     * Verify tokenization a multiline heredoc construct.
     *
     * @phpcs:disable Squiz.Arrays.ArrayDeclaration.SpaceBeforeDoubleArrow -- Readability is better with alignment.
     *
     * @return void
     */
    public function testHeredocEndsOnBlankLine()
    {
        $expectedSequence = [
            [T_START_HEREDOC => '<<<EOD'."\n"],
            [T_HEREDOC       => 'Lorum ipsum'."\n"],
            [T_HEREDOC       => 'dolor sit amet'."\n"],
            [T_HEREDOC       => "\n"],
            [T_END_HEREDOC   => 'EOD'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_START_HEREDOC);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testHeredocEndsOnBlankLine()


    /**
     * Verify tokenization a multiline testNowdocSingleLine construct.
     *
     * @phpcs:disable Squiz.Arrays.ArrayDeclaration.SpaceBeforeDoubleArrow -- Readability is better with alignment.
     *
     * @return void
     */
    public function testNowdocEndsOnBlankLine()
    {
        $expectedSequence = [
            [T_START_NOWDOC => "<<<'EOD'\n"],
            [T_NOWDOC       => 'Lorum ipsum'."\n"],
            [T_NOWDOC       => 'dolor sit amet'."\n"],
            [T_NOWDOC       => "\n"],
            [T_END_NOWDOC   => 'EOD'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_START_NOWDOC);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testNowdocEndsOnBlankLine()


    /**
     * Test helper. Check a token sequence complies with an expected token sequence.
     *
     * @param int                              $startPtr         The position in the file to start checking from.
     * @param array<array<int|string, string>> $expectedSequence The consecutive token constants and their contents to expect.
     *
     * @return void
     */
    private function checkTokenSequence($startPtr, array $expectedSequence)
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
