<?php
/**
 * Ensure colours are defined in upper-case and use shortcuts where possible.
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

class ColourDefinitionSniff implements Sniff, DeprecatedSniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = ['CSS'];


    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return array<int|string>
     */
    public function register()
    {
        return [T_COLOUR];

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
        $colour = $tokens[$stackPtr]['content'];

        $expected = strtoupper($colour);
        if ($colour !== $expected) {
            $error = 'CSS colours must be defined in uppercase; expected %s but found %s';
            $data  = [
                $expected,
                $colour,
            ];

            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'NotUpper', $data);
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken($stackPtr, $expected);
            }
        }

        // Now check if shorthand can be used.
        if (strlen($colour) !== 7) {
            return;
        }

        if ($colour[1] === $colour[2] && $colour[3] === $colour[4] && $colour[5] === $colour[6]) {
            $expected = '#'.$colour[1].$colour[3].$colour[5];
            $error    = 'CSS colours must use shorthand if available; expected %s but found %s';
            $data     = [
                $expected,
                $colour,
            ];

            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Shorthand', $data);
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken($stackPtr, $expected);
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
