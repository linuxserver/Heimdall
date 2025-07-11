<?php
/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Fixer\FixFileReturnValueTest
 */

namespace Fixtures\TestStandard\Sniffs\FixFileReturnValue;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class AllGoodSniff implements Sniff
{

    public function register()
    {
        return [T_ECHO];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE
            || $tokens[($stackPtr + 1)]['length'] > 51
        ) {
            return;
        }

        $error = 'There should be 52 spaces after an ECHO keyword';
        $fix   = $phpcsFile->addFixableError($error, ($stackPtr + 1), 'ShortSpace');
        if ($fix === true) {
            $phpcsFile->fixer->replaceToken(($stackPtr + 1), str_repeat(' ', 52));
        }
    }
}
