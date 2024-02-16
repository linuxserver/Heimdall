<?php
/**
 * Verifies that classes are instantiated with parentheses.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR12\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class ClassInstantiationSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array<int|string>
     */
    public function register()
    {
        return [T_NEW];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Find the class name.
        $allowed = [
            T_STRING                   => T_STRING,
            T_NS_SEPARATOR             => T_NS_SEPARATOR,
            T_SELF                     => T_SELF,
            T_STATIC                   => T_STATIC,
            T_PARENT                   => T_PARENT,
            T_VARIABLE                 => T_VARIABLE,
            T_DOLLAR                   => T_DOLLAR,
            T_OBJECT_OPERATOR          => T_OBJECT_OPERATOR,
            T_NULLSAFE_OBJECT_OPERATOR => T_NULLSAFE_OBJECT_OPERATOR,
            T_DOUBLE_COLON             => T_DOUBLE_COLON,
        ];

        $allowed += Tokens::$emptyTokens;

        $classNameEnd = null;
        for ($i = ($stackPtr + 1); $i < $phpcsFile->numTokens; $i++) {
            if (isset($allowed[$tokens[$i]['code']]) === true) {
                continue;
            }

            // Bow out when this is an anonymous class.
            // Anonymous classes are the only situation which would allow for an attribute
            // or for the readonly keyword between "new" and the class "name".
            if ($tokens[$i]['code'] === T_ATTRIBUTE
                || $tokens[$i]['code'] === T_READONLY
                || $tokens[$i]['code'] === T_ANON_CLASS
            ) {
                return;
            }

            if ($tokens[$i]['code'] === T_OPEN_SQUARE_BRACKET
                || $tokens[$i]['code'] === T_OPEN_CURLY_BRACKET
            ) {
                $i = $tokens[$i]['bracket_closer'];
                continue;
            }

            $classNameEnd = $i;
            break;
        }//end for

        if ($classNameEnd === null) {
            return;
        }

        if ($tokens[$classNameEnd]['code'] === T_OPEN_PARENTHESIS) {
            // Using parenthesis.
            return;
        }

        if ($classNameEnd === $stackPtr) {
            // Failed to find the class name.
            return;
        }

        $error = 'Parentheses must be used when instantiating a new class';
        $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'MissingParentheses');
        if ($fix === true) {
            $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($classNameEnd - 1), null, true);
            $phpcsFile->fixer->addContent($prev, '()');
        }

    }//end process()


}//end class
