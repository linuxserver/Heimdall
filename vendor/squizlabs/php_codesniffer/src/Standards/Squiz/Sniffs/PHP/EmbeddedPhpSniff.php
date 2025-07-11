<?php
/**
 * Checks the indentation of embedded PHP code segments.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class EmbeddedPhpSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array<int|string>
     */
    public function register()
    {
        return [
            T_OPEN_TAG,
            T_OPEN_TAG_WITH_ECHO,
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

        // If the close php tag is on the same line as the opening
        // then we have an inline embedded PHP block.
        $closeTag = $phpcsFile->findNext(T_CLOSE_TAG, $stackPtr);
        if ($closeTag === false || $tokens[$stackPtr]['line'] !== $tokens[$closeTag]['line']) {
            $this->validateMultilineEmbeddedPhp($phpcsFile, $stackPtr, $closeTag);
        } else {
            $this->validateInlineEmbeddedPhp($phpcsFile, $stackPtr, $closeTag);
        }

    }//end process()


    /**
     * Validates embedded PHP that exists on multiple lines.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile  The file being scanned.
     * @param int                         $stackPtr   The position of the current token in the
     *                                                stack passed in $tokens.
     * @param int|false                   $closingTag The position of the PHP close tag in the
     *                                                stack passed in $tokens.
     *
     * @return void
     */
    private function validateMultilineEmbeddedPhp($phpcsFile, $stackPtr, $closingTag)
    {
        $tokens = $phpcsFile->getTokens();

        $prevTag = $phpcsFile->findPrevious($this->register(), ($stackPtr - 1));
        if ($prevTag === false) {
            // This is the first open tag.
            return;
        }

        $firstContent = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
        if ($firstContent === false) {
            // Unclosed PHP open tag at the end of a file. Nothing to do.
            return;
        }

        if ($closingTag !== false) {
            $firstContentAfterBlock = $phpcsFile->findNext(T_WHITESPACE, ($closingTag + 1), $phpcsFile->numTokens, true);
            if ($firstContentAfterBlock === false) {
                // Final closing tag. It will be handled elsewhere.
                return;
            }

            // We have an opening and a closing tag, that lie within other content.
            if ($firstContent === $closingTag) {
                $this->reportEmptyTagSet($phpcsFile, $stackPtr, $closingTag);
                return;
            }
        }//end if

        if ($tokens[$firstContent]['line'] === $tokens[$stackPtr]['line']) {
            $error = 'Opening PHP tag must be on a line by itself';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'ContentAfterOpen');
            if ($fix === true) {
                $padding = $this->calculateLineIndent($phpcsFile, $stackPtr);

                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->replaceToken($stackPtr, rtrim($tokens[$stackPtr]['content']));
                $phpcsFile->fixer->addNewline($stackPtr);
                $phpcsFile->fixer->addContent($stackPtr, str_repeat(' ', $padding));

                if ($tokens[($stackPtr + 1)]['code'] === T_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken(($stackPtr + 1), '');
                }

                $phpcsFile->fixer->endChangeset();
            }
        } else {
            // Check the indent of the first line, except if it is a scope closer.
            if (isset($tokens[$firstContent]['scope_closer']) === false
                || $tokens[$firstContent]['scope_closer'] !== $firstContent
            ) {
                // Check for a blank line at the top.
                if ($tokens[$firstContent]['line'] > ($tokens[$stackPtr]['line'] + 1)) {
                    // Find a token on the blank line to throw the error on.
                    $i = $stackPtr;
                    do {
                        $i++;
                    } while ($tokens[$i]['line'] !== ($tokens[$stackPtr]['line'] + 1));

                    $error = 'Blank line found at start of embedded PHP content';
                    $fix   = $phpcsFile->addFixableError($error, $i, 'SpacingBefore');
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = ($stackPtr + 1); $i < $firstContent; $i++) {
                            if ($tokens[$i]['line'] === $tokens[$firstContent]['line']
                                || $tokens[$i]['line'] === $tokens[$stackPtr]['line']
                            ) {
                                continue;
                            }

                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->endChangeset();
                    }
                }//end if

                $indent        = $this->calculateLineIndent($phpcsFile, $stackPtr);
                $contentColumn = ($tokens[$firstContent]['column'] - 1);
                if ($contentColumn !== $indent) {
                    $error = 'First line of embedded PHP code must be indented %s spaces; %s found';
                    $data  = [
                        $indent,
                        $contentColumn,
                    ];
                    $fix   = $phpcsFile->addFixableError($error, $firstContent, 'Indent', $data);
                    if ($fix === true) {
                        $padding = str_repeat(' ', $indent);
                        if ($contentColumn === 0) {
                            $phpcsFile->fixer->addContentBefore($firstContent, $padding);
                        } else {
                            $phpcsFile->fixer->replaceToken(($firstContent - 1), $padding);
                        }
                    }
                }
            }//end if
        }//end if

        $lastContentBeforeBlock = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        if ($tokens[$lastContentBeforeBlock]['line'] === $tokens[$stackPtr]['line']
            && (($tokens[$lastContentBeforeBlock]['code'] === T_INLINE_HTML
            && trim($tokens[$lastContentBeforeBlock]['content']) !== '')
            || ($tokens[($lastContentBeforeBlock - 1)]['code'] !== T_INLINE_HTML
            && $tokens[($lastContentBeforeBlock - 1)]['line'] === $tokens[$stackPtr]['line']))
        ) {
            $error = 'Opening PHP tag must be on a line by itself';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'ContentBeforeOpen');
            if ($fix === true) {
                $padding = $this->calculateLineIndent($phpcsFile, $lastContentBeforeBlock);

                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->addContentBefore($stackPtr, $phpcsFile->eolChar.str_repeat(' ', $padding));

                // Make sure we don't leave trailing whitespace behind.
                if ($tokens[($stackPtr - 1)]['code'] === T_INLINE_HTML
                    && trim($tokens[($stackPtr - 1)]['content']) === ''
                ) {
                    $phpcsFile->fixer->replaceToken(($stackPtr - 1), '');
                }

                $phpcsFile->fixer->endChangeset();
            }//end if
        } else {
            // Find the first token on the first non-empty line we find.
            for ($first = ($lastContentBeforeBlock - 1); $first > 0; $first--) {
                if ($tokens[$first]['line'] === $tokens[$stackPtr]['line']) {
                    continue;
                } else if (trim($tokens[$first]['content']) !== '') {
                    $first = $phpcsFile->findFirstOnLine([], $first, true);
                    break;
                }
            }

            $expected  = $this->calculateLineIndent($phpcsFile, $first);
            $expected += 4;
            $found     = ($tokens[$stackPtr]['column'] - 1);
            if ($found > $expected) {
                $error = 'Opening PHP tag indent incorrect; expected no more than %s spaces but found %s';
                $data  = [
                    $expected,
                    $found,
                ];
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'OpenTagIndent', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($stackPtr - 1), str_repeat(' ', $expected));
                }
            }
        }//end if

        if ($closingTag === false) {
            return;
        }

        $lastContent            = $phpcsFile->findPrevious(T_WHITESPACE, ($closingTag - 1), ($stackPtr + 1), true);
        $firstContentAfterBlock = $phpcsFile->findNext(T_WHITESPACE, ($closingTag + 1), null, true);

        if ($tokens[$lastContent]['line'] === $tokens[$closingTag]['line']) {
            $error = 'Closing PHP tag must be on a line by itself';
            $fix   = $phpcsFile->addFixableError($error, $closingTag, 'ContentBeforeEnd');
            if ($fix === true) {
                // Calculate the indent for the close tag.
                // If the close tag is on the same line as the first content, re-use the indent
                // calculated for the first content line to prevent the indent being based on an
                // "old" indent, not the _new_ (fixed) indent.
                if ($tokens[$firstContent]['line'] === $tokens[$lastContent]['line']
                    && isset($indent) === true
                ) {
                    $closerIndent = $indent;
                } else {
                    $closerIndent = $this->calculateLineIndent($phpcsFile, $closingTag);
                }

                $phpcsFile->fixer->beginChangeset();

                if ($tokens[($closingTag - 1)]['code'] === T_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken(($closingTag - 1), '');
                }

                $phpcsFile->fixer->addContentBefore($closingTag, str_repeat(' ', $closerIndent));
                $phpcsFile->fixer->addNewlineBefore($closingTag);
                $phpcsFile->fixer->endChangeset();
            }//end if
        } else if ($firstContentAfterBlock !== false
            && $tokens[$firstContentAfterBlock]['line'] === $tokens[$closingTag]['line']
        ) {
            $error = 'Closing PHP tag must be on a line by itself';
            $fix   = $phpcsFile->addFixableError($error, $closingTag, 'ContentAfterEnd');
            if ($fix === true) {
                $indent = $this->calculateLineIndent($phpcsFile, $closingTag);
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->addNewline($closingTag);
                $phpcsFile->fixer->addContent($closingTag, str_repeat(' ', $indent));

                if ($tokens[$firstContentAfterBlock]['code'] === T_INLINE_HTML) {
                    $trimmedHtmlContent = ltrim($tokens[$firstContentAfterBlock]['content']);
                    if ($trimmedHtmlContent === '') {
                        // HTML token contains only whitespace and the next token after is PHP, not HTML, so remove the whitespace.
                        $phpcsFile->fixer->replaceToken($firstContentAfterBlock, '');
                    } else {
                        // The HTML token has content, so remove leading whitespace in favour of the indent.
                        $phpcsFile->fixer->replaceToken($firstContentAfterBlock, $trimmedHtmlContent);
                    }
                }

                if ($tokens[$firstContentAfterBlock]['code'] === T_OPEN_TAG
                    || $tokens[$firstContentAfterBlock]['code'] === T_OPEN_TAG_WITH_ECHO
                ) {
                    // Next token is a PHP open tag which will also have thrown an error.
                    // Prevent both fixers running in the same loop by making sure the token is "touched" during this loop.
                    // This prevents a stray new line being added between the close and open tags.
                    $phpcsFile->fixer->replaceToken($firstContentAfterBlock, $tokens[$firstContentAfterBlock]['content']);
                }

                $phpcsFile->fixer->endChangeset();
            }//end if
        }//end if

        $next = $phpcsFile->findNext($this->register(), ($closingTag + 1));
        if ($next === false) {
            return;
        }

        // Check for a blank line at the bottom.
        $lastNonEmpty = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($closingTag - 1), ($stackPtr + 1), true);
        if ((isset($tokens[$lastNonEmpty]['scope_closer']) === false
            || $tokens[$lastNonEmpty]['scope_closer'] !== $lastNonEmpty)
            && $tokens[$lastContent]['line'] < ($tokens[$closingTag]['line'] - 1)
        ) {
            // Find a token on the blank line to throw the error on.
            $i = $closingTag;
            do {
                $i--;
            } while ($tokens[$i]['line'] !== ($tokens[$closingTag]['line'] - 1));

            $error = 'Blank line found at end of embedded PHP content';
            $fix   = $phpcsFile->addFixableError($error, $i, 'SpacingAfter');
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = ($lastContent + 1); $i < $closingTag; $i++) {
                    if ($tokens[$i]['line'] === $tokens[$lastContent]['line']
                        || $tokens[$i]['line'] === $tokens[$closingTag]['line']
                    ) {
                        continue;
                    }

                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $phpcsFile->fixer->endChangeset();
            }
        }//end if

    }//end validateMultilineEmbeddedPhp()


    /**
     * Validates embedded PHP that exists on one line.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     * @param int                         $closeTag  The position of the PHP close tag in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    private function validateInlineEmbeddedPhp($phpcsFile, $stackPtr, $closeTag)
    {
        $tokens = $phpcsFile->getTokens();

        $firstContent = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), $closeTag, true);

        if ($firstContent === false) {
            $this->reportEmptyTagSet($phpcsFile, $stackPtr, $closeTag);
            return;
        }

        // Check that there is one, and only one space at the start of the statement.
        $leadingSpace  = 0;
        $isLongOpenTag = false;
        if ($tokens[$stackPtr]['code'] === T_OPEN_TAG
            && stripos($tokens[$stackPtr]['content'], '<?php') === 0
        ) {
            // The long open tag token in a single line tag set always contains a single space after it.
            $leadingSpace  = 1;
            $isLongOpenTag = true;
        }

        if ($tokens[($stackPtr + 1)]['code'] === T_WHITESPACE) {
            $leadingSpace += $tokens[($stackPtr + 1)]['length'];
        }

        if ($leadingSpace !== 1) {
            $error = 'Expected 1 space after opening PHP tag; %s found';
            $data  = [$leadingSpace];
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingAfterOpen', $data);
            if ($fix === true) {
                if ($isLongOpenTag === true) {
                    $phpcsFile->fixer->replaceToken(($stackPtr + 1), '');
                } else if ($tokens[($stackPtr + 1)]['code'] === T_WHITESPACE) {
                    // Short open tag with too much whitespace.
                    $phpcsFile->fixer->replaceToken(($stackPtr + 1), ' ');
                } else {
                    // Short open tag without whitespace.
                    $phpcsFile->fixer->addContent($stackPtr, ' ');
                }
            }
        }

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($closeTag - 1), $stackPtr, true);
        if ($prev !== $stackPtr) {
            if ((isset($tokens[$prev]['scope_opener']) === false
                || $tokens[$prev]['scope_opener'] !== $prev)
                && (isset($tokens[$prev]['scope_closer']) === false
                || $tokens[$prev]['scope_closer'] !== $prev)
                && $tokens[$prev]['code'] !== T_SEMICOLON
            ) {
                $error = 'Inline PHP statement must end with a semicolon';
                $code  = 'NoSemicolon';
                if ($tokens[$stackPtr]['code'] === T_OPEN_TAG_WITH_ECHO) {
                    $code = 'ShortOpenEchoNoSemicolon';
                }

                $fix = $phpcsFile->addFixableError($error, $stackPtr, $code);
                if ($fix === true) {
                    $phpcsFile->fixer->addContent($prev, ';');
                }
            } else if ($tokens[$prev]['code'] === T_SEMICOLON) {
                $statementCount = 1;
                for ($i = ($stackPtr + 1); $i < $prev; $i++) {
                    if ($tokens[$i]['code'] === T_SEMICOLON) {
                        $statementCount++;
                    }
                }

                if ($statementCount > 1) {
                    $error = 'Inline PHP statement must contain a single statement; %s found';
                    $data  = [$statementCount];
                    $phpcsFile->addError($error, $stackPtr, 'MultipleStatements', $data);
                }
            }//end if
        }//end if

        $trailingSpace = 0;
        if ($tokens[($closeTag - 1)]['code'] === T_WHITESPACE) {
            $trailingSpace = $tokens[($closeTag - 1)]['length'];
        } else if (($tokens[($closeTag - 1)]['code'] === T_COMMENT
            || isset(Tokens::$phpcsCommentTokens[$tokens[($closeTag - 1)]['code']]) === true)
            && substr($tokens[($closeTag - 1)]['content'], -1) === ' '
        ) {
            $trailingSpace = (strlen($tokens[($closeTag - 1)]['content']) - strlen(rtrim($tokens[($closeTag - 1)]['content'])));
        }

        if ($trailingSpace !== 1) {
            $error = 'Expected 1 space before closing PHP tag; %s found';
            $data  = [$trailingSpace];
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingBeforeClose', $data);
            if ($fix === true) {
                if ($trailingSpace === 0) {
                    $phpcsFile->fixer->addContentBefore($closeTag, ' ');
                } else if ($tokens[($closeTag - 1)]['code'] === T_COMMENT
                    || isset(Tokens::$phpcsCommentTokens[$tokens[($closeTag - 1)]['code']]) === true
                ) {
                    $phpcsFile->fixer->replaceToken(($closeTag - 1), rtrim($tokens[($closeTag - 1)]['content']).' ');
                } else {
                    $phpcsFile->fixer->replaceToken(($closeTag - 1), ' ');
                }
            }
        }

    }//end validateInlineEmbeddedPhp()


    /**
     * Report and fix a set of empty PHP tags.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     * @param int                         $closeTag  The position of the PHP close tag in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    private function reportEmptyTagSet(File $phpcsFile, $stackPtr, $closeTag)
    {
        $tokens = $phpcsFile->getTokens();
        $error  = 'Empty embedded PHP tag found';
        $fix    = $phpcsFile->addFixableError($error, $stackPtr, 'Empty');
        if ($fix === true) {
            $phpcsFile->fixer->beginChangeset();
            for ($i = $stackPtr; $i <= $closeTag; $i++) {
                $phpcsFile->fixer->replaceToken($i, '');
            }

            // Prevent leaving indentation whitespace behind when the empty tag set is the only thing on the affected lines.
            if (isset($tokens[($closeTag + 1)]) === true
                && $tokens[($closeTag + 1)]['line'] !== $tokens[$closeTag]['line']
                && $tokens[($stackPtr - 1)]['code'] === T_INLINE_HTML
                && $tokens[($stackPtr - 1)]['line'] === $tokens[$stackPtr]['line']
                && $tokens[($stackPtr - 1)]['column'] === 1
                && trim($tokens[($stackPtr - 1)]['content']) === ''
            ) {
                $phpcsFile->fixer->replaceToken(($stackPtr - 1), '');
            }

            $phpcsFile->fixer->endChangeset();
        }

    }//end reportEmptyTagSet()


    /**
     * Calculate the indent of the line containing the stackPtr.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return int
     */
    private function calculateLineIndent(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        for ($firstOnLine = $stackPtr; $tokens[$firstOnLine]['column'] !== 1; $firstOnLine--);

        // Check if this is a subsequent line in a star-slash comment containing leading indent.
        // In that case, we'll need the first line of the comment to correctly determine the indent.
        while ($tokens[$firstOnLine]['code'] === T_COMMENT
            && $tokens[$firstOnLine]['content'] !== ltrim($tokens[$firstOnLine]['content'])
        ) {
            for (--$firstOnLine; $tokens[$firstOnLine]['column'] !== 1; $firstOnLine--);
        }

        $indent = 0;
        if ($tokens[$firstOnLine]['code'] === T_WHITESPACE) {
            $indent = ($tokens[($firstOnLine + 1)]['column'] - 1);
        } else if ($tokens[$firstOnLine]['code'] === T_INLINE_HTML
            || $tokens[$firstOnLine]['code'] === T_END_HEREDOC
            || $tokens[$firstOnLine]['code'] === T_END_NOWDOC
        ) {
            $indent = (strlen($tokens[$firstOnLine]['content']) - strlen(ltrim($tokens[$firstOnLine]['content'])));
        } else if ($tokens[$firstOnLine]['code'] === T_DOC_COMMENT_WHITESPACE) {
            $indent = (strlen($tokens[$firstOnLine]['content']) - strlen(ltrim($tokens[$firstOnLine]['content'])) - 1);
        }

        return $indent;

    }//end calculateLineIndent()


}//end class
