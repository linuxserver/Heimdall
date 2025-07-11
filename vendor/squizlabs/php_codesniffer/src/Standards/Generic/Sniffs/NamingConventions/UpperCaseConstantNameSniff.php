<?php
/**
 * Ensures that constant names are all uppercase.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class UpperCaseConstantNameSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array<int|string>
     */
    public function register()
    {
        return [
            T_STRING,
            T_CONST,
        ];

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

        if ($tokens[$stackPtr]['code'] === T_CONST) {
            // This is a constant declared with the "const" keyword.
            // This may be an OO constant, in which case it could be typed, so we need to
            // jump over a potential type to get to the name.
            $assignmentOperator = $phpcsFile->findNext([T_EQUAL, T_SEMICOLON], ($stackPtr + 1));
            if ($assignmentOperator === false || $tokens[$assignmentOperator]['code'] !== T_EQUAL) {
                // Parse error/live coding. Nothing to do. Rest of loop is moot.
                return;
            }

            $constant = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($assignmentOperator - 1), ($stackPtr + 1), true);
            if ($constant === false) {
                return;
            }

            $constName = $tokens[$constant]['content'];

            if (strtoupper($constName) !== $constName) {
                if (strtolower($constName) === $constName) {
                    $phpcsFile->recordMetric($constant, 'Constant name case', 'lower');
                } else {
                    $phpcsFile->recordMetric($constant, 'Constant name case', 'mixed');
                }

                $error = 'Class constants must be uppercase; expected %s but found %s';
                $data  = [
                    strtoupper($constName),
                    $constName,
                ];
                $phpcsFile->addError($error, $constant, 'ClassConstantNotUpperCase', $data);
            } else {
                $phpcsFile->recordMetric($constant, 'Constant name case', 'upper');
            }

            return;
        }//end if

        // Only interested in define statements now.
        if (strtolower($tokens[$stackPtr]['content']) !== 'define') {
            return;
        }

        // Make sure this is not a method call or class instantiation.
        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if ($tokens[$prev]['code'] === T_OBJECT_OPERATOR
            || $tokens[$prev]['code'] === T_DOUBLE_COLON
            || $tokens[$prev]['code'] === T_NULLSAFE_OBJECT_OPERATOR
            || $tokens[$prev]['code'] === T_NEW
        ) {
            return;
        }

        // Make sure this is not an attribute.
        if (empty($tokens[$stackPtr]['nested_attributes']) === false) {
            return;
        }

        // If the next non-whitespace token after this token
        // is not an opening parenthesis then it is not a function call.
        $openBracket = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($openBracket === false || $tokens[$openBracket]['code'] !== T_OPEN_PARENTHESIS) {
            return;
        }

        // Bow out if next non-empty token after the opening parenthesis is not a string (the
        // constant name). This could happen when live coding, if the constant is a variable or an
        // expression, or if handling a first-class callable or a function definition outside the
        // global scope.
        $constPtr = $phpcsFile->findNext(Tokens::$emptyTokens, ($openBracket + 1), null, true);
        if ($constPtr === false || $tokens[$constPtr]['code'] !== T_CONSTANT_ENCAPSED_STRING) {
            return;
        }

        $constName = $tokens[$constPtr]['content'];
        $prefix    = '';

        // Strip namespace from constant like \foo\bar\CONSTANT.
        $splitPos = strrpos($constName, '\\');
        if ($splitPos !== false) {
            $prefix    = substr($constName, 0, ($splitPos + 1));
            $constName = substr($constName, ($splitPos + 1));
        }

        if (strtoupper($constName) !== $constName) {
            if (strtolower($constName) === $constName) {
                $phpcsFile->recordMetric($constPtr, 'Constant name case', 'lower');
            } else {
                $phpcsFile->recordMetric($constPtr, 'Constant name case', 'mixed');
            }

            $error = 'Constants must be uppercase; expected %s but found %s';
            $data  = [
                $prefix.strtoupper($constName),
                $prefix.$constName,
            ];
            $phpcsFile->addError($error, $constPtr, 'ConstantNotUpperCase', $data);
        } else {
            $phpcsFile->recordMetric($constPtr, 'Constant name case', 'upper');
        }

    }//end process()


}//end class
