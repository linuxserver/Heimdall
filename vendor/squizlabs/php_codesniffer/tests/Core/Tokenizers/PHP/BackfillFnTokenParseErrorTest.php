<?php
/**
 * Tests the backfilling of the T_FN token to PHP < 7.4 for a specific parse error.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

final class BackfillFnTokenParseErrorTest extends AbstractTokenizerTestCase
{


    /**
     * Verify that un unfinished arrow function during live coding doesn't cause a "Undefined array key "parenthesis_closer"" error.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testUnfinishedArrowFunction()
    {
        $tokens = $this->phpcsFile->getTokens();

        $token      = $this->getTargetToken('/* testLiveCoding */', [T_STRING, T_FN], 'fn');
        $tokenArray = $tokens[$token];

        $this->assertSame('T_STRING', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING');

        $this->assertArrayNotHasKey('scope_condition', $tokenArray, 'Scope condition is set');
        $this->assertArrayNotHasKey('scope_opener', $tokenArray, 'Scope opener is set');
        $this->assertArrayNotHasKey('scope_closer', $tokenArray, 'Scope closer is set');
        $this->assertArrayNotHasKey('parenthesis_owner', $tokenArray, 'Parenthesis owner is set');
        $this->assertArrayNotHasKey('parenthesis_opener', $tokenArray, 'Parenthesis opener is set');
        $this->assertArrayNotHasKey('parenthesis_closer', $tokenArray, 'Parenthesis closer is set');

    }//end testUnfinishedArrowFunction()


}//end class
