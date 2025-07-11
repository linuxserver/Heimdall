<?php
/**
 * Test fixture.
 *
 * This sniff deliberately causes a "failed to fix" situation by causing the fixer to run out of loops.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Fixer\FixFileReturnValueTest
 */

namespace Fixtures\TestStandard\Sniffs\FixFileReturnValue;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class NotEnoughLoopsSniff implements Sniff
{

    public function register()
    {
        return [T_ECHO];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE
            || $tokens[($stackPtr + 1)]['length'] > 60
        ) {
            return;
        }

        $error = 'There should be 60 spaces after an ECHO keyword';
        $fix   = $phpcsFile->addFixableError($error, ($stackPtr + 1), 'ShortSpace');
        if ($fix === true) {
            // The fixer deliberately only adds one space in each loop to ensure it runs out of loops before the file complies.
            $phpcsFile->fixer->addContent($stackPtr, ' ');
        }
    }
}
