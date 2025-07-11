<?php
/**
 * Ensures all language constructs contain a single space between themselves and their content.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2017 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Common;
use PHP_CodeSniffer\Util\Tokens;

class LanguageConstructSpacingSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array<int|string>
     */
    public function register()
    {
        return [
            T_ECHO,
            T_PRINT,
            T_RETURN,
            T_INCLUDE,
            T_INCLUDE_ONCE,
            T_REQUIRE,
            T_REQUIRE_ONCE,
            T_NEW,
            T_YIELD,
            T_YIELD_FROM,
            T_THROW,
            T_NAMESPACE,
            T_USE,
            T_GOTO,
        ];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return int|void Integer stack pointer to skip forward or void to continue
     *                  normal file processing.
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $nextToken = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
        if ($nextToken === false) {
            // Skip when at end of file.
            return;
        }

        if ($tokens[($stackPtr + 1)]['code'] === T_SEMICOLON) {
            // No content for this language construct.
            return;
        }

        $content = $tokens[$stackPtr]['content'];
        if ($tokens[$stackPtr]['code'] === T_NAMESPACE) {
            $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
            if ($nextNonEmpty !== false && $tokens[$nextNonEmpty]['code'] === T_NS_SEPARATOR) {
                // Namespace keyword used as operator, not as the language construct.
                return;
            }
        }

        if ($tokens[$stackPtr]['code'] === T_YIELD_FROM
            && strtolower($content) !== 'yield from'
        ) {
            $found        = $content;
            $hasComment   = false;
            $yieldFromEnd = $stackPtr;

            // Handle potentially multi-line/multi-token "yield from" expressions.
            if (preg_match('`yield\s+from`i', $content) !== 1) {
                for ($i = ($stackPtr + 1); $i < $phpcsFile->numTokens; $i++) {
                    if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === false
                        && $tokens[$i]['code'] !== T_YIELD_FROM
                    ) {
                        break;
                    }

                    if (isset(Tokens::$commentTokens[$tokens[$i]['code']]) === true) {
                        $hasComment = true;
                    }

                    $found .= $tokens[$i]['content'];

                    if ($tokens[$i]['code'] === T_YIELD_FROM
                        && strtolower(trim($tokens[$i]['content'])) === 'from'
                    ) {
                        break;
                    }
                }

                $yieldFromEnd = $i;
            }//end if

            $error = 'Language constructs must be followed by a single space; expected 1 space between YIELD FROM found "%s"';
            $data  = [Common::prepareForOutput($found)];

            if ($hasComment === true) {
                $phpcsFile->addError($error, $stackPtr, 'IncorrectYieldFromWithComment', $data);
            } else {
                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'IncorrectYieldFrom', $data);
                if ($fix === true) {
                    preg_match('/yield/i', $found, $yield);
                    preg_match('/from/i', $found, $from);
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->replaceToken($stackPtr, $yield[0].' '.$from[0]);

                    for ($i = ($stackPtr + 1); $i <= $yieldFromEnd; $i++) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->endChangeset();
                }
            }//end if

            return ($yieldFromEnd + 1);
        }//end if

        if ($tokens[($stackPtr + 1)]['code'] === T_WHITESPACE) {
            $content = $tokens[($stackPtr + 1)]['content'];
            if ($content !== ' ') {
                $error = 'Language constructs must be followed by a single space; expected 1 space but found "%s"';
                $data  = [Common::prepareForOutput($content)];
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'IncorrectSingle', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($stackPtr + 1), ' ');
                }
            }
        } else if ($tokens[($stackPtr + 1)]['code'] !== T_OPEN_PARENTHESIS) {
            $error = 'Language constructs must be followed by a single space; expected "%s" but found "%s"';
            $data  = [
                $tokens[$stackPtr]['content'].' '.$tokens[($stackPtr + 1)]['content'],
                $tokens[$stackPtr]['content'].$tokens[($stackPtr + 1)]['content'],
            ];
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'Incorrect', $data);
            if ($fix === true) {
                $phpcsFile->fixer->addContent($stackPtr, ' ');
            }
        }//end if

    }//end process()


}//end class
