<?php
/**
 * Ensure that each style definition is on a line by itself.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 *
 * @deprecated 3.9.0
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\CSS;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\DeprecatedSniff;
use PHP_CodeSniffer\Sniffs\Sniff;

class DisallowMultipleStyleDefinitionsSniff implements Sniff, DeprecatedSniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var string[]
     */
    public $supportedTokenizers = ['CSS'];


    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return array<int|string>
     */
    public function register()
    {
        return [T_STYLE];

    }//end register()


    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where the token was found.
     * @param int                         $stackPtr  The position in the stack where
     *                                               the token was found.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $next   = $phpcsFile->findNext(T_STYLE, ($stackPtr + 1));
        if ($next === false) {
            return;
        }

        if ($tokens[$next]['content'] === 'progid') {
            // Special case for IE filters.
            return;
        }

        if ($tokens[$next]['line'] === $tokens[$stackPtr]['line']) {
            $error = 'Each style definition must be on a line by itself';
            $fix   = $phpcsFile->addFixableError($error, $next, 'Found');
            if ($fix === true) {
                $phpcsFile->fixer->addNewlineBefore($next);
            }
        }

    }//end process()


    /**
     * Provide the version number in which the sniff was deprecated.
     *
     * @return string
     */
    public function getDeprecationVersion()
    {
        return 'v3.9.0';

    }//end getDeprecationVersion()


    /**
     * Provide the version number in which the sniff will be removed.
     *
     * @return string
     */
    public function getRemovalVersion()
    {
        return 'v4.0.0';

    }//end getRemovalVersion()


    /**
     * Provide a custom message to display with the deprecation.
     *
     * @return string
     */
    public function getDeprecationMessage()
    {
        return 'Support for scanning CSS files will be removed completely in v4.0.0.';

    }//end getDeprecationMessage()


}//end class
