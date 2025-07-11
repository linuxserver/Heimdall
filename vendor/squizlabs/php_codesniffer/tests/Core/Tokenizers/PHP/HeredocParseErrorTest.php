<?php
/**
 * Tests the tokenization for an unclosed heredoc construct.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

/**
 * Tests the tokenization for an unclosed heredoc construct.
 *
 * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
 */
final class HeredocParseErrorTest extends AbstractTokenizerTestCase
{


    /**
     * Verify that a heredoc (and nowdoc) start token is retokenized to T_STRING if no closer is found.
     *
     * @return void
     */
    public function testMergeConflict()
    {
        $tokens = $this->phpcsFile->getTokens();

        $token      = $this->getTargetToken('/* testUnclosedHeredoc */', [T_START_HEREDOC, T_STRING], '<<< HEAD'."\n");
        $tokenArray = $tokens[$token];

        $this->assertSame(T_STRING, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_START_HEREDOC (code)');
        $this->assertSame('T_STRING', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_START_HEREDOC (type)');

    }//end testMergeConflict()


}//end class
