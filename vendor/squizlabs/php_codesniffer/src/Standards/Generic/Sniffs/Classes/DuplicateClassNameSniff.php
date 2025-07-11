<?php
/**
 * Reports errors if the same class or interface name is used in multiple files.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class DuplicateClassNameSniff implements Sniff
{

    /**
     * List of classes that have been found during checking.
     *
     * @var array
     */
    protected $foundClasses = [];


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return array<int|string>
     */
    public function register()
    {
        return [T_OPEN_TAG];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return int
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $namespace  = '';
        $findTokens = [
            T_CLASS,
            T_INTERFACE,
            T_TRAIT,
            T_ENUM,
            T_NAMESPACE,
        ];

        $stackPtr = $phpcsFile->findNext($findTokens, ($stackPtr + 1));
        while ($stackPtr !== false) {
            // Keep track of what namespace we are in.
            if ($tokens[$stackPtr]['code'] === T_NAMESPACE) {
                $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
                if ($nextNonEmpty !== false
                    // Ignore namespace keyword used as operator.
                    && $tokens[$nextNonEmpty]['code'] !== T_NS_SEPARATOR
                ) {
                    $namespace = '';
                    for ($i = $nextNonEmpty; $i < $phpcsFile->numTokens; $i++) {
                        if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === true) {
                            continue;
                        }

                        if ($tokens[$i]['code'] !== T_STRING && $tokens[$i]['code'] !== T_NS_SEPARATOR) {
                            break;
                        }

                        $namespace .= $tokens[$i]['content'];
                    }

                    $stackPtr = $i;
                }
            } else {
                $name = $phpcsFile->getDeclarationName($stackPtr);
                if (empty($name) === false) {
                    if ($namespace !== '') {
                        $name = $namespace.'\\'.$name;
                    }

                    $compareName = strtolower($name);
                    if (isset($this->foundClasses[$compareName]) === true) {
                        $type  = strtolower($tokens[$stackPtr]['content']);
                        $file  = $this->foundClasses[$compareName]['file'];
                        $line  = $this->foundClasses[$compareName]['line'];
                        $error = 'Duplicate %s name "%s" found; first defined in %s on line %s';
                        $data  = [
                            $type,
                            $name,
                            $file,
                            $line,
                        ];
                        $phpcsFile->addWarning($error, $stackPtr, 'Found', $data);
                    } else {
                        $this->foundClasses[$compareName] = [
                            'file' => $phpcsFile->getFilename(),
                            'line' => $tokens[$stackPtr]['line'],
                        ];
                    }
                }//end if

                if (isset($tokens[$stackPtr]['scope_closer']) === true) {
                    $stackPtr = $tokens[$stackPtr]['scope_closer'];
                }
            }//end if

            $stackPtr = $phpcsFile->findNext($findTokens, ($stackPtr + 1));
        }//end while

        return $phpcsFile->numTokens;

    }//end process()


}//end class
