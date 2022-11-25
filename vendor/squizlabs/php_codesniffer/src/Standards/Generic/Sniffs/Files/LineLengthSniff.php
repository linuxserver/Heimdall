<?php
/**
 * Checks the length of all lines in a file.
 *
 * Checks all lines in the file, and throws warnings if they are over 80
 * characters in length and errors if they are over 100. Both these
 * figures can be changed in a ruleset.xml file.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Files;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class LineLengthSniff implements Sniff
{

    /**
     * The limit that the length of a line should not exceed.
     *
     * @var integer
     */
    public $lineLimit = 80;

    /**
     * The limit that the length of a line must not exceed.
     *
     * Set to zero (0) to disable.
     *
     * @var integer
     */
    public $absoluteLineLimit = 100;

    /**
     * Whether or not to ignore trailing comments.
     *
     * This has the effect of also ignoring all lines
     * that only contain comments.
     *
     * @var boolean
     */
    public $ignoreComments = false;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_OPEN_TAG];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return int
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        for ($i = 1; $i < $phpcsFile->numTokens; $i++) {
            if ($tokens[$i]['column'] === 1) {
                $this->checkLineLength($phpcsFile, $tokens, $i);
            }
        }

        $this->checkLineLength($phpcsFile, $tokens, $i);

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);

    }//end process()


    /**
     * Checks if a line is too long.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param array                       $tokens    The token stack.
     * @param int                         $stackPtr  The first token on the next line.
     *
     * @return void
     */
    protected function checkLineLength($phpcsFile, $tokens, $stackPtr)
    {
        // The passed token is the first on the line.
        $stackPtr--;

        if ($tokens[$stackPtr]['column'] === 1
            && $tokens[$stackPtr]['length'] === 0
        ) {
            // Blank line.
            return;
        }

        if ($tokens[$stackPtr]['column'] !== 1
            && $tokens[$stackPtr]['content'] === $phpcsFile->eolChar
        ) {
            $stackPtr--;
        }

        $onlyComment = false;
        if (isset(Tokens::$commentTokens[$tokens[$stackPtr]['code']]) === true) {
            $prevNonWhiteSpace = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
            if ($tokens[$stackPtr]['line'] !== $tokens[$prevNonWhiteSpace]['line']) {
                $onlyComment = true;
            }
        }

        if ($onlyComment === true
            && isset(Tokens::$phpcsCommentTokens[$tokens[$stackPtr]['code']]) === true
        ) {
            // Ignore PHPCS annotation comments that are on a line by themselves.
            return;
        }

        $lineLength = ($tokens[$stackPtr]['column'] + $tokens[$stackPtr]['length'] - 1);

        if ($this->ignoreComments === true
            && isset(Tokens::$commentTokens[$tokens[$stackPtr]['code']]) === true
        ) {
            // Trailing comments are being ignored in line length calculations.
            if ($onlyComment === true) {
                // The comment is the only thing on the line, so no need to check length.
                return;
            }

            $lineLength -= $tokens[$stackPtr]['length'];
        }

        // Record metrics for common line length groupings.
        if ($lineLength <= 80) {
            $phpcsFile->recordMetric($stackPtr, 'Line length', '80 or less');
        } else if ($lineLength <= 120) {
            $phpcsFile->recordMetric($stackPtr, 'Line length', '81-120');
        } else if ($lineLength <= 150) {
            $phpcsFile->recordMetric($stackPtr, 'Line length', '121-150');
        } else {
            $phpcsFile->recordMetric($stackPtr, 'Line length', '151 or more');
        }

        if ($onlyComment === true) {
            // If this is a long comment, check if it can be broken up onto multiple lines.
            // Some comments contain unbreakable strings like URLs and so it makes sense
            // to ignore the line length in these cases if the URL would be longer than the max
            // line length once you indent it to the correct level.
            if ($lineLength > $this->lineLimit) {
                $oldLength = strlen($tokens[$stackPtr]['content']);
                $newLength = strlen(ltrim($tokens[$stackPtr]['content'], "/#\t "));
                $indent    = (($tokens[$stackPtr]['column'] - 1) + ($oldLength - $newLength));

                $nonBreakingLength = $tokens[$stackPtr]['length'];

                $space = strrpos($tokens[$stackPtr]['content'], ' ');
                if ($space !== false) {
                    $nonBreakingLength -= ($space + 1);
                }

                if (($nonBreakingLength + $indent) > $this->lineLimit) {
                    return;
                }
            }
        }//end if

        if ($this->absoluteLineLimit > 0
            && $lineLength > $this->absoluteLineLimit
        ) {
            $data = [
                $this->absoluteLineLimit,
                $lineLength,
            ];

            $error = 'Line exceeds maximum limit of %s characters; contains %s characters';
            $phpcsFile->addError($error, $stackPtr, 'MaxExceeded', $data);
        } else if ($lineLength > $this->lineLimit) {
            $data = [
                $this->lineLimit,
                $lineLength,
            ];

            $warning = 'Line exceeds %s characters; contains %s characters';
            $phpcsFile->addWarning($warning, $stackPtr, 'TooLong', $data);
        }

    }//end checkLineLength()


}//end class
