<?php
/**
 * Tests the tokenization of PHP open tags.
 *
 * Prior to PHP 7.4, PHP didn't support stand-alone PHP open tags at the end of a file (without a new line),
 * so we need to make sure that the tokenization in PHPCS is consistent and correct.
 *
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Tests the tokenization of PHP open tags.
 *
 * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
 */
final class PHPOpenTagEOF1Test extends AbstractTokenizerTestCase
{


    /**
     * Test that the tokenization of a long PHP open tag at the very end of a file is correct and consistent.
     *
     * @return void
     */
    public function testLongOpenTagAtEndOfFile()
    {
        $tokens   = $this->phpcsFile->getTokens();
        $stackPtr = $this->getTargetToken('/* testLongOpenTagEndOfFileSpaceNoNewLine */', [T_OPEN_TAG, T_STRING, T_INLINE_HTML]);

        $this->assertSame(
            T_OPEN_TAG,
            $tokens[$stackPtr]['code'],
            'Token tokenized as '.Tokens::tokenName($tokens[$stackPtr]['code']).', not T_OPEN_TAG (code)'
        );
        $this->assertSame(
            'T_OPEN_TAG',
            $tokens[$stackPtr]['type'],
            'Token tokenized as '.$tokens[$stackPtr]['type'].', not T_OPEN_TAG (type)'
        );
        $this->assertSame('<?php ', $tokens[$stackPtr]['content']);

        // Now make sure that this is the very last token in the file and there are no tokens after it.
        $this->assertArrayNotHasKey(($stackPtr + 1), $tokens);

    }//end testLongOpenTagAtEndOfFile()


}//end class
