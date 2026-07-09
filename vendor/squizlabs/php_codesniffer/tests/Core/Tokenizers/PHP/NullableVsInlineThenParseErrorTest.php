<?php
/**
 * Tests the retokenization of ? to T_NULLABLE or T_INLINE_THEN.
 *
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/HEAD/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

/**
 * Tests the retokenization of ? to T_NULLABLE or T_INLINE_THEN.
 *
 * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
 */
final class NullableVsInlineThenParseErrorTest extends AbstractTokenizerTestCase
{


    /**
     * Verify that a "?" as the last functional token in a file (live coding) is tokenized as `T_INLINE_THEN`
     * as it cannot yet be determined what the token would be once the code is finalized.
     *
     * @return void
     */
    public function testInlineThenAtEndOfFile()
    {
        $tokens     = $this->phpcsFile->getTokens();
        $target     = $this->getTargetToken('/* testLiveCoding */', [T_NULLABLE, T_INLINE_THEN]);
        $tokenArray = $tokens[$target];

        $this->assertSame(T_INLINE_THEN, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_INLINE_THEN (code)');
        $this->assertSame('T_INLINE_THEN', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_INLINE_THEN (type)');

    }//end testInlineThenAtEndOfFile()


}//end class
