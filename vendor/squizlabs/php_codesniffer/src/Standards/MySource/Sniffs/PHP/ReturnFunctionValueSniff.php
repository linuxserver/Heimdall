<?php
/**
 * Warns when function values are returned directly.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 *
 * @deprecated 3.9.0
 */

namespace PHP_CodeSniffer\Standards\MySource\Sniffs\PHP;

use PHP_CodeSniffer\Sniffs\DeprecatedSniff;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class ReturnFunctionValueSniff implements Sniff, DeprecatedSniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array<int|string>
     */
    public function register()
    {
        return [T_RETURN];

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
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

        $functionName = $phpcsFile->findNext(T_STRING, ($stackPtr + 1), null, false, null, true);

        while ($functionName !== false) {
            // Check if this is really a function.
            $bracket = $phpcsFile->findNext(T_WHITESPACE, ($functionName + 1), null, true);
            if ($tokens[$bracket]['code'] !== T_OPEN_PARENTHESIS) {
                // Not a function call.
                $functionName = $phpcsFile->findNext(T_STRING, ($functionName + 1), null, false, null, true);
                continue;
            }

            $error = 'The result of a function call should be assigned to a variable before being returned';
            $phpcsFile->addWarning($error, $stackPtr, 'NotAssigned');
            break;
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
        return 'The MySource standard will be removed completely in v4.0.0.';

    }//end getDeprecationMessage()


}//end class
