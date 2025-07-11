<?php
/**
 * Forbid mixing different binary boolean operators within a single expression without making precedence
 * clear using parentheses.
 *
 * <code>
 * $one = false;
 * $two = false;
 * $three = true;
 *
 * $result = $one && $two || $three;
 * $result3 = $one && !$two xor $three;
 * </code>
 *
 * {@internal The unary `!` operator is not handled, because its high precedence matches its visuals of
 * applying only to the sub-expression right next to it, making it unlikely that someone would
 * misinterpret its precedence. Requiring parentheses around it would reduce the readability of
 * expressions due to the additional characters, especially if multiple subexpressions / variables
 * need to be negated.}
 *
 * Sister-sniff to the `Squiz.ControlStructures.InlineIfDeclaration` and
 * `Squiz.Formatting.OperatorBracket.MissingBrackets` sniffs.
 *
 * @author    Tim Duesterhus <duesterhus@woltlab.com>
 * @copyright 2021-2023 WoltLab GmbH.
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class RequireExplicitBooleanOperatorPrecedenceSniff implements Sniff
{

    /**
     * Array of tokens this test searches for to find either a boolean
     * operator or the start of the current (sub-)expression. Used for
     * performance optimization purposes.
     *
     * @var array<int|string>
     */
    private $searchTargets = [];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array<int|string>
     */
    public function register()
    {
        $this->searchTargets = Tokens::$booleanOperators;
        $this->searchTargets[T_INLINE_THEN] = T_INLINE_THEN;
        $this->searchTargets[T_INLINE_ELSE] = T_INLINE_ELSE;

        return Tokens::$booleanOperators;

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $start = $phpcsFile->findStartOfStatement($stackPtr);

        $previous = $phpcsFile->findPrevious(
            $this->searchTargets,
            ($stackPtr - 1),
            $start,
            false,
            null,
            true
        );

        if ($previous === false) {
            // No token found.
            return;
        }

        if ($tokens[$previous]['code'] === $tokens[$stackPtr]['code']) {
            // Identical operator found.
            return;
        }

        if (in_array($tokens[$previous]['code'], [T_INLINE_THEN, T_INLINE_ELSE], true) === true) {
            // Beginning of the expression found for the ternary conditional operator.
            return;
        }

        // We found a mismatching operator, thus we must report the error.
        $error  = 'Mixing different binary boolean operators within an expression';
        $error .= ' without using parentheses to clarify precedence is not allowed.';
        $phpcsFile->addError($error, $stackPtr, 'MissingParentheses');

    }//end process()


}//end class
