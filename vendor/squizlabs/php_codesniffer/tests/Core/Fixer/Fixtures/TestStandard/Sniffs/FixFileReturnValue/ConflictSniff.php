<?php
/**
 * Test fixture.
 *
 * This sniff deliberately causes a fixer conflict **with no fixes applied in loop 50**.
 * This last part is important as that's the exact situation which needs testing.
 *
 * Loop 1 applies the fix for `BlankLineAfterOpen` and then bows out.
 * Loop 2 applies a fix for `NewlineEOF`.
 * Loop 3 applies a fix for `NoNewlineEOF`.
 * Loop 4 will try to apply the `NewlineEOF` fix again, but sees this causes a conflict and skips.
 * Loop 5 will try to apply the `NewlineEOF` fix again, but sees this causes a conflict and skips.
 * Loop 6 applies a fix for `NewlineEOF`.
 * Loop 7 will try to apply the `NoNewlineEOF` fix again, but sees this causes a conflict and skips.
 * Loop 8 applies a fix for `NoNewlineEOF`.
 * Loop 9 - 13 repeat loop 4 - 8.
 * Loop 14 - 18 repeat loop 4 - 8.
 * Loop 19 - 23 repeat loop 4 - 8.
 * Loop 24 - 28 repeat loop 4 - 8.
 * Loop 29 - 33 repeat loop 4 - 8.
 * Loop 34 - 38 repeat loop 4 - 8.
 * Loop 39 - 43 repeat loop 4 - 8.
 * Loop 44 - 48 repeat loop 4 - 8.
 * Loop 49 = loop 4.
 * Loop 50 = loop 5, i.e. applies no fixes.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Fixer\FixFileReturnValueTest
 */

namespace Fixtures\TestStandard\Sniffs\FixFileReturnValue;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ConflictSniff implements Sniff
{

    public function register()
    {
        return [T_OPEN_TAG];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Demand a blank line after the PHP open tag.
        // This error is here to ensure something will be fixed in the file.
        $nextNonWhitespace = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);

        if (($tokens[$nextNonWhitespace]['line'] - $tokens[$stackPtr]['line']) !== 2) {
            $error = 'There must be a single blank line after the PHP open tag';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'BlankLineAfterOpen');
            if ($fix === true) {
                $phpcsFile->fixer->addNewline($stackPtr);

                // This return is here deliberately to force a new loop.
                // This should ensure that loop 50 does *NOT* apply any fixes.
                return;
            }
        }

        // Skip to the end of the file.
        $stackPtr = ($phpcsFile->numTokens - 1);

        $eolCharLen = strlen($phpcsFile->eolChar);
        $lastChars  = substr($tokens[$stackPtr]['content'], ($eolCharLen * -1));

        // Demand a newline at the end of a file.
        if ($lastChars !== $phpcsFile->eolChar) {
            $error = 'File must end with a newline character';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoNewlineEOF');
            if ($fix === true) {
                $phpcsFile->fixer->addNewline($stackPtr);
            }
        }

        // Demand NO newline at the end of a file.
        if ($lastChars === $phpcsFile->eolChar) {
            $error = 'File must not end with a newline character';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NewlineEOF');
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();

                for ($i = $stackPtr; $i > 0; $i--) {
                    $newContent = rtrim($tokens[$i]['content'], $phpcsFile->eolChar);
                    $phpcsFile->fixer->replaceToken($i, $newContent);

                    if ($newContent !== '') {
                        break;
                    }
                }

                $phpcsFile->fixer->endChangeset();
            }
        }

        // Ignore the rest of the file.
        return $phpcsFile->numTokens;
    }
}
