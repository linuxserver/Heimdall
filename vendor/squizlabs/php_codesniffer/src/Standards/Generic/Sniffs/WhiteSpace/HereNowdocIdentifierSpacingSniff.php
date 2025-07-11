<?php
/**
 * Ensures heredoc/nowdoc identifiers do not have any whitespace before them.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class HereNowdocIdentifierSpacingSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array<int|string>
     */
    public function register()
    {
        return [
            T_START_HEREDOC,
            T_START_NOWDOC,
        ];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (strpos($tokens[$stackPtr]['content'], ' ') === false
            && strpos($tokens[$stackPtr]['content'], "\t") === false
        ) {
            // Nothing to do.
            $phpcsFile->recordMetric($stackPtr, 'Heredoc/nowdoc identifier', 'no space between <<< and ID');
            return;
        }

        $phpcsFile->recordMetric($stackPtr, 'Heredoc/nowdoc identifier', 'space between <<< and ID');

        $error = 'There should be no space between the <<< and the heredoc/nowdoc identifier string. Found: %s';
        $data  = [$tokens[$stackPtr]['content']];

        $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceFound', $data);
        if ($fix === true) {
            $replacement = str_replace([' ', "\t"], '', $tokens[$stackPtr]['content']);
            $phpcsFile->fixer->replaceToken($stackPtr, $replacement);
        }

    }//end process()


}//end class
