<?php
/**
 * Checks that all uses of true, false and null are lowercase.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class LowerCaseConstantSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = [
        'PHP',
        'JS',
    ];

    /**
     * The tokens this sniff is targetting.
     *
     * @var array
     */
    private $targets = [
        T_TRUE  => T_TRUE,
        T_FALSE => T_FALSE,
        T_NULL  => T_NULL,
    ];

    /**
     * Token types which can be encountered in a property type declaration.
     *
     * @var array<int|string, int|string>
     */
    private $propertyTypeTokens = [
        T_CALLABLE             => T_CALLABLE,
        T_SELF                 => T_SELF,
        T_PARENT               => T_PARENT,
        T_FALSE                => T_FALSE,
        T_TRUE                 => T_TRUE,
        T_NULL                 => T_NULL,
        T_STRING               => T_STRING,
        T_NAME_QUALIFIED       => T_NAME_QUALIFIED,
        T_NAME_FULLY_QUALIFIED => T_NAME_FULLY_QUALIFIED,
        T_NAME_RELATIVE        => T_NAME_RELATIVE,
        T_NS_SEPARATOR         => T_NS_SEPARATOR,
        T_NAMESPACE            => T_NAMESPACE,
        T_TYPE_UNION           => T_TYPE_UNION,
        T_TYPE_INTERSECTION    => T_TYPE_INTERSECTION,
        T_NULLABLE             => T_NULLABLE,
    ];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array<int|string>
     */
    public function register()
    {
        $targets = $this->targets;

        // Register scope modifiers to filter out property type declarations.
        $targets  += Tokens::$scopeModifiers;
        $targets[] = T_VAR;
        $targets[] = T_STATIC;
        $targets[] = T_READONLY;

        // Register function keywords to filter out param/return type declarations.
        $targets[] = T_FUNCTION;
        $targets[] = T_CLOSURE;
        $targets[] = T_FN;

        // Register constant keyword to filter out type declarations.
        $targets[] = T_CONST;

        return $targets;

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void|int Optionally returns a stack pointer. The sniff will not be
     *                  called again on the current file until the returned stack
     *                  pointer is reached.
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Skip over potential type declarations for constants.
        if ($tokens[$stackPtr]['code'] === T_CONST) {
            // Constant must always have a value assigned to it, so we can just look for the assignment
            // operator. Anything between the const keyword and the assignment can be safely ignored.
            $skipTo = $phpcsFile->findNext(T_EQUAL, ($stackPtr + 1));
            if ($skipTo !== false) {
                return $skipTo;
            }

            // If we're at the end of the file, just return.
            return;
        }

        /*
         * Skip over type declarations for properties.
         *
         * Note: for other uses of the visibility modifiers (functions, constants, trait use),
         * nothing relevant will be skipped as the next non-empty token will be an "non-skippable"
         * one.
         * Functions are handled separately below (and then skip to their scope opener), so
         * this should also not cause any confusion for constructor property promotion.
         *
         * For other uses of the "static" keyword, it also shouldn't be problematic as the only
         * time the next non-empty token will be a "skippable" token will be in return type
         * declarations, in which case, it is correct to skip over them.
         */

        if (isset(Tokens::$scopeModifiers[$tokens[$stackPtr]['code']]) === true
            || $tokens[$stackPtr]['code'] === T_VAR
            || $tokens[$stackPtr]['code'] === T_STATIC
            || $tokens[$stackPtr]['code'] === T_READONLY
        ) {
            $skipOver = (Tokens::$emptyTokens + $this->propertyTypeTokens);
            $skipTo   = $phpcsFile->findNext($skipOver, ($stackPtr + 1), null, true);
            if ($skipTo !== false) {
                return $skipTo;
            }

            // If we're at the end of the file, just return.
            return;
        }

        // Handle function declarations separately as they may contain the keywords in type declarations.
        if ($tokens[$stackPtr]['code'] === T_FUNCTION
            || $tokens[$stackPtr]['code'] === T_CLOSURE
            || $tokens[$stackPtr]['code'] === T_FN
        ) {
            if (isset($tokens[$stackPtr]['parenthesis_closer']) === false) {
                return;
            }

            // Make sure to skip over return type declarations.
            $end = $tokens[$stackPtr]['parenthesis_closer'];
            if (isset($tokens[$stackPtr]['scope_opener']) === true) {
                $end = $tokens[$stackPtr]['scope_opener'];
            } else {
                $skipTo = $phpcsFile->findNext([T_SEMICOLON, T_OPEN_CURLY_BRACKET], ($end + 1), null, false, null, true);
                if ($skipTo !== false) {
                    $end = $skipTo;
                }
            }

            // Do a quick check if any of the targets exist in the declaration.
            $found = $phpcsFile->findNext($this->targets, $tokens[$stackPtr]['parenthesis_opener'], $end);
            if ($found === false) {
                // Skip forward, no need to examine these tokens again.
                return $end;
            }

            // Handle the whole function declaration in one go.
            $params = $phpcsFile->getMethodParameters($stackPtr);
            foreach ($params as $param) {
                if (isset($param['default_token']) === false) {
                    continue;
                }

                $paramEnd = $param['comma_token'];
                if ($param['comma_token'] === false) {
                    $paramEnd = $tokens[$stackPtr]['parenthesis_closer'];
                }

                for ($i = $param['default_token']; $i < $paramEnd; $i++) {
                    if (isset($this->targets[$tokens[$i]['code']]) === true) {
                        $this->processConstant($phpcsFile, $i);
                    }
                }
            }

            // Skip over return type declarations.
            return $end;
        }//end if

        // Handle everything else.
        $this->processConstant($phpcsFile, $stackPtr);

    }//end process()


    /**
     * Processes a non-type declaration constant.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    protected function processConstant(File $phpcsFile, $stackPtr)
    {
        $tokens   = $phpcsFile->getTokens();
        $keyword  = $tokens[$stackPtr]['content'];
        $expected = strtolower($keyword);

        if ($keyword !== $expected) {
            if ($keyword === strtoupper($keyword)) {
                $phpcsFile->recordMetric($stackPtr, 'PHP constant case', 'upper');
            } else {
                $phpcsFile->recordMetric($stackPtr, 'PHP constant case', 'mixed');
            }

            $error = 'TRUE, FALSE and NULL must be lowercase; expected "%s" but found "%s"';
            $data  = [
                $expected,
                $keyword,
            ];

            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Found', $data);
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken($stackPtr, $expected);
            }
        } else {
            $phpcsFile->recordMetric($stackPtr, 'PHP constant case', 'lower');
        }

    }//end processConstant()


}//end class
