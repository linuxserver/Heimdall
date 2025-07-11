<?php
/**
 * Checks that all PHP types are lowercase.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\PHP;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class LowerCaseTypeSniff implements Sniff
{

    /**
     * Native types supported by PHP.
     *
     * @var array
     */
    private $phpTypes = [
        'self'     => true,
        'parent'   => true,
        'array'    => true,
        'callable' => true,
        'bool'     => true,
        'float'    => true,
        'int'      => true,
        'string'   => true,
        'iterable' => true,
        'void'     => true,
        'object'   => true,
        'mixed'    => true,
        'static'   => true,
        'false'    => true,
        'true'     => true,
        'null'     => true,
        'never'    => true,
    ];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array<int|string>
     */
    public function register()
    {
        $tokens   = Tokens::$castTokens;
        $tokens  += Tokens::$ooScopeTokens;
        $tokens[] = T_FUNCTION;
        $tokens[] = T_CLOSURE;
        $tokens[] = T_FN;
        return $tokens;

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
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

        if (isset(Tokens::$castTokens[$tokens[$stackPtr]['code']]) === true) {
            // A cast token.
            $this->processType(
                $phpcsFile,
                $stackPtr,
                $tokens[$stackPtr]['content'],
                'PHP type casts must be lowercase; expected "%s" but found "%s"',
                'TypeCastFound'
            );

            return;
        }

        /*
         * Check OO constant and property types.
         */

        if (isset(Tokens::$ooScopeTokens[$tokens[$stackPtr]['code']]) === true) {
            if (isset($tokens[$stackPtr]['scope_opener'], $tokens[$stackPtr]['scope_closer']) === false) {
                return;
            }

            for ($i = ($tokens[$stackPtr]['scope_opener'] + 1); $i < $tokens[$stackPtr]['scope_closer']; $i++) {
                // Skip over potentially large docblocks.
                if ($tokens[$i]['code'] === T_DOC_COMMENT_OPEN_TAG
                    && isset($tokens[$i]['comment_closer']) === true
                ) {
                    $i = $tokens[$i]['comment_closer'];
                    continue;
                }

                // Skip over function declarations and everything nested within.
                if ($tokens[$i]['code'] === T_FUNCTION
                    && isset($tokens[$i]['scope_closer']) === true
                ) {
                    $i = $tokens[$i]['scope_closer'];
                    continue;
                }

                if ($tokens[$i]['code'] === T_CONST) {
                    $ignore = Tokens::$emptyTokens;
                    $ignore[T_NULLABLE] = T_NULLABLE;

                    $startOfType = $phpcsFile->findNext($ignore, ($i + 1), null, true);
                    if ($startOfType === false) {
                        // Parse error/live coding. Nothing to do. Rest of loop is moot.
                        return;
                    }

                    $assignmentOperator = $phpcsFile->findNext([T_EQUAL, T_SEMICOLON], ($startOfType + 1));
                    if ($assignmentOperator === false || $tokens[$assignmentOperator]['code'] !== T_EQUAL) {
                        // Parse error/live coding. Nothing to do. Rest of loop is moot.
                        return;
                    }

                    $constName = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($assignmentOperator - 1), null, true);
                    if ($startOfType !== $constName) {
                        $endOfType = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($constName - 1), null, true);

                        $error     = 'PHP constant type declarations must be lowercase; expected "%s" but found "%s"';
                        $errorCode = 'ConstantTypeFound';

                        if ($startOfType !== $endOfType) {
                            // Multi-token type.
                            $this->processUnionType(
                                $phpcsFile,
                                $startOfType,
                                $endOfType,
                                $error,
                                $errorCode
                            );
                        } else {
                            $type = $tokens[$startOfType]['content'];
                            if (isset($this->phpTypes[strtolower($type)]) === true) {
                                $this->processType($phpcsFile, $startOfType, $type, $error, $errorCode);
                            }
                        }
                    }//end if

                    continue;
                }//end if

                if ($tokens[$i]['code'] !== T_VARIABLE) {
                    continue;
                }

                try {
                    $props = $phpcsFile->getMemberProperties($i);
                } catch (RuntimeException $e) {
                    // Not an OO property.
                    continue;
                }

                if (empty($props) === true) {
                    // Parse error - property in interface or enum. Ignore.
                    return;
                }

                // Strip off potential nullable indication.
                $type = ltrim($props['type'], '?');

                if ($type !== '') {
                    $error     = 'PHP property type declarations must be lowercase; expected "%s" but found "%s"';
                    $errorCode = 'PropertyTypeFound';

                    if ($props['type_token'] !== $props['type_end_token']) {
                        // Multi-token type.
                        $this->processUnionType(
                            $phpcsFile,
                            $props['type_token'],
                            $props['type_end_token'],
                            $error,
                            $errorCode
                        );
                    } else if (isset($this->phpTypes[strtolower($type)]) === true) {
                        $this->processType($phpcsFile, $props['type_token'], $type, $error, $errorCode);
                    }
                }
            }//end for

            return;
        }//end if

        /*
         * Check function return type.
         */

        $props = $phpcsFile->getMethodProperties($stackPtr);

        // Strip off potential nullable indication.
        $returnType = ltrim($props['return_type'], '?');

        if ($returnType !== '') {
            $error     = 'PHP return type declarations must be lowercase; expected "%s" but found "%s"';
            $errorCode = 'ReturnTypeFound';

            if ($props['return_type_token'] !== $props['return_type_end_token']) {
                // Multi-token type.
                $this->processUnionType(
                    $phpcsFile,
                    $props['return_type_token'],
                    $props['return_type_end_token'],
                    $error,
                    $errorCode
                );
            } else if (isset($this->phpTypes[strtolower($returnType)]) === true) {
                $this->processType($phpcsFile, $props['return_type_token'], $returnType, $error, $errorCode);
            }
        }

        /*
         * Check function parameter types.
         */

        $params = $phpcsFile->getMethodParameters($stackPtr);
        if (empty($params) === true) {
            return;
        }

        foreach ($params as $param) {
            // Strip off potential nullable indication.
            $typeHint = ltrim($param['type_hint'], '?');

            if ($typeHint !== '') {
                $error     = 'PHP parameter type declarations must be lowercase; expected "%s" but found "%s"';
                $errorCode = 'ParamTypeFound';

                if ($param['type_hint_token'] !== $param['type_hint_end_token']) {
                    // Multi-token type.
                    $this->processUnionType(
                        $phpcsFile,
                        $param['type_hint_token'],
                        $param['type_hint_end_token'],
                        $error,
                        $errorCode
                    );
                } else if (isset($this->phpTypes[strtolower($typeHint)]) === true) {
                    $this->processType($phpcsFile, $param['type_hint_token'], $typeHint, $error, $errorCode);
                }
            }
        }//end foreach

    }//end process()


    /**
     * Processes a multi-token type declaration.
     *
     * {@internal The method name is superseded by the reality, but changing it would be a BC-break.}
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile     The file being scanned.
     * @param int                         $typeDeclStart The position of the start of the type token.
     * @param int                         $typeDeclEnd   The position of the end of the type token.
     * @param string                      $error         Error message template.
     * @param string                      $errorCode     The error code.
     *
     * @return void
     */
    protected function processUnionType(File $phpcsFile, $typeDeclStart, $typeDeclEnd, $error, $errorCode)
    {
        $tokens         = $phpcsFile->getTokens();
        $typeTokenCount = 0;
        $typeStart      = null;
        $type           = '';

        for ($i = $typeDeclStart; $i <= $typeDeclEnd; $i++) {
            if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === true) {
                continue;
            }

            if ($tokens[$i]['code'] === T_TYPE_UNION
                || $tokens[$i]['code'] === T_TYPE_INTERSECTION
                || $tokens[$i]['code'] === T_TYPE_OPEN_PARENTHESIS
                || $tokens[$i]['code'] === T_TYPE_CLOSE_PARENTHESIS
            ) {
                if ($typeTokenCount === 1
                    && $type !== ''
                    && isset($this->phpTypes[strtolower($type)]) === true
                ) {
                    $this->processType($phpcsFile, $typeStart, $type, $error, $errorCode);
                }

                // Reset for the next type in the type string.
                $typeTokenCount = 0;
                $typeStart      = null;
                $type           = '';

                continue;
            }

            if (isset($typeStart) === false) {
                $typeStart = $i;
            }

            ++$typeTokenCount;
            $type .= $tokens[$i]['content'];
        }//end for

        // Handle type at end of type string.
        if ($typeTokenCount === 1
            && $type !== ''
            && isset($this->phpTypes[strtolower($type)]) === true
        ) {
            $this->processType($phpcsFile, $typeStart, $type, $error, $errorCode);
        }

    }//end processUnionType()


    /**
     * Processes a type cast or a singular type declaration.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the type token.
     * @param string                      $type      The type found.
     * @param string                      $error     Error message template.
     * @param string                      $errorCode The error code.
     *
     * @return void
     */
    protected function processType(File $phpcsFile, $stackPtr, $type, $error, $errorCode)
    {
        $typeLower = strtolower($type);

        if ($typeLower === $type) {
            $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'lower');
            return;
        }

        if ($type === strtoupper($type)) {
            $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'upper');
        } else {
            $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'mixed');
        }

        $data = [
            $typeLower,
            $type,
        ];

        $fix = $phpcsFile->addFixableError($error, $stackPtr, $errorCode, $data);
        if ($fix === true) {
            $phpcsFile->fixer->replaceToken($stackPtr, $typeLower);
        }

    }//end processType()


}//end class
