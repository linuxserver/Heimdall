<?php
/**
 * Checks that arguments in function declarations are spaced correctly.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Common;
use PHP_CodeSniffer\Util\Tokens;

class FunctionDeclarationArgumentSpacingSniff implements Sniff
{

    /**
     * How many spaces should surround the equals signs.
     *
     * @var integer
     */
    public $equalsSpacing = 0;

    /**
     * How many spaces should follow the opening bracket.
     *
     * @var integer
     */
    public $requiredSpacesAfterOpen = 0;

    /**
     * How many spaces should precede the closing bracket.
     *
     * @var integer
     */
    public $requiredSpacesBeforeClose = 0;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array<int|string>
     */
    public function register()
    {
        return [
            T_FUNCTION,
            T_CLOSURE,
            T_FN,
        ];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['parenthesis_opener']) === false
            || isset($tokens[$stackPtr]['parenthesis_closer']) === false
            || $tokens[$stackPtr]['parenthesis_opener'] === null
            || $tokens[$stackPtr]['parenthesis_closer'] === null
        ) {
            return;
        }

        $this->equalsSpacing           = (int) $this->equalsSpacing;
        $this->requiredSpacesAfterOpen = (int) $this->requiredSpacesAfterOpen;
        $this->requiredSpacesBeforeClose = (int) $this->requiredSpacesBeforeClose;

        $this->processBracket($phpcsFile, $tokens[$stackPtr]['parenthesis_opener']);

        if ($tokens[$stackPtr]['code'] === T_CLOSURE) {
            $use = $phpcsFile->findNext(T_USE, ($tokens[$stackPtr]['parenthesis_closer'] + 1), $tokens[$stackPtr]['scope_opener']);
            if ($use !== false) {
                $openBracket = $phpcsFile->findNext(T_OPEN_PARENTHESIS, ($use + 1), null);
                $this->processBracket($phpcsFile, $openBracket);
            }
        }

    }//end process()


    /**
     * Processes the contents of a single set of brackets.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file being scanned.
     * @param int                         $openBracket The position of the open bracket
     *                                                 in the stack.
     *
     * @return void
     */
    public function processBracket($phpcsFile, $openBracket)
    {
        $tokens       = $phpcsFile->getTokens();
        $closeBracket = $tokens[$openBracket]['parenthesis_closer'];
        $multiLine    = ($tokens[$openBracket]['line'] !== $tokens[$closeBracket]['line']);

        if (isset($tokens[$openBracket]['parenthesis_owner']) === true) {
            $stackPtr = $tokens[$openBracket]['parenthesis_owner'];
        } else {
            $stackPtr = $phpcsFile->findPrevious(T_USE, ($openBracket - 1));
        }

        $params = $phpcsFile->getMethodParameters($stackPtr);

        if (empty($params) === true) {
            // Check spacing around parenthesis.
            $next = $phpcsFile->findNext(T_WHITESPACE, ($openBracket + 1), $closeBracket, true);
            if ($next === false) {
                if (($closeBracket - $openBracket) !== 1) {
                    if ($tokens[$openBracket]['line'] !== $tokens[$closeBracket]['line']) {
                        $found = 'newline';
                    } else {
                        $found = $tokens[($openBracket + 1)]['length'];
                    }

                    $error = 'Expected 0 spaces between parenthesis of function declaration; %s found';
                    $data  = [$found];
                    $fix   = $phpcsFile->addFixableError($error, $openBracket, 'SpacingBetween', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = ($openBracket + 1); $tokens[$i]['code'] === T_WHITESPACE; $i++) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->endChangeset();
                    }
                }

                // No params, so we don't check normal spacing rules.
                return;
            }//end if
        }//end if

        foreach ($params as $paramNumber => $param) {
            if ($param['pass_by_reference'] === true) {
                $refToken = $param['reference_token'];

                if ($tokens[($refToken + 1)]['code'] === T_WHITESPACE) {
                    $gap = $tokens[($refToken + 1)]['length'];
                    if ($tokens[$refToken]['line'] !== $tokens[($refToken + 2)]['line']) {
                        $gap = 'newline';
                    }

                    $error = 'Expected 0 spaces after reference operator for argument "%s"; %s found';
                    $data  = [
                        $param['name'],
                        $gap,
                    ];
                    $fix   = $phpcsFile->addFixableError($error, $refToken, 'SpacingAfterReference', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = ($refToken + 1); $tokens[$i]['code'] === T_WHITESPACE; $i++) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->endChangeset();
                    }
                }//end if
            }//end if

            if ($param['variable_length'] === true) {
                $variadicToken = $param['variadic_token'];

                if ($tokens[($variadicToken + 1)]['code'] === T_WHITESPACE) {
                    $gap = $tokens[($variadicToken + 1)]['length'];
                    if ($tokens[$variadicToken]['line'] !== $tokens[($variadicToken + 2)]['line']) {
                        $gap = 'newline';
                    }

                    $error = 'Expected 0 spaces after variadic operator for argument "%s"; %s found';
                    $data  = [
                        $param['name'],
                        $gap,
                    ];
                    $fix   = $phpcsFile->addFixableError($error, $variadicToken, 'SpacingAfterVariadic', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = ($variadicToken + 1); $tokens[$i]['code'] === T_WHITESPACE; $i++) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->endChangeset();
                    }
                }//end if
            }//end if

            if (isset($param['default_equal_token']) === true) {
                $equalToken = $param['default_equal_token'];

                $spacesBefore = 0;
                if ($tokens[$param['token']]['line'] !== $tokens[$equalToken]['line']) {
                    $spacesBefore = 'newline';
                } else if ($tokens[($param['token'] + 1)]['code'] === T_WHITESPACE) {
                    $spacesBefore = $tokens[($param['token'] + 1)]['length'];
                }

                if ($spacesBefore !== $this->equalsSpacing) {
                    $error = 'Incorrect spacing between argument "%s" and equals sign; expected '.$this->equalsSpacing.' but found %s';
                    $data  = [
                        $param['name'],
                        $spacesBefore,
                    ];

                    $nextNonWhitespace = $phpcsFile->findNext(T_WHITESPACE, ($param['token'] + 1), $equalToken, true);
                    if ($nextNonWhitespace !== false) {
                        $phpcsFile->addError($error, $equalToken, 'SpaceBeforeEquals', $data);
                    } else {
                        $fix = $phpcsFile->addFixableError($error, $equalToken, 'SpaceBeforeEquals', $data);
                        if ($fix === true) {
                            $padding = str_repeat(' ', $this->equalsSpacing);

                            $phpcsFile->fixer->beginChangeset();
                            $phpcsFile->fixer->addContent($param['token'], $padding);

                            for ($i = ($param['token'] + 1); $tokens[$i]['code'] === T_WHITESPACE; $i++) {
                                $phpcsFile->fixer->replaceToken($i, '');
                            }

                            $phpcsFile->fixer->endChangeset();
                        }
                    }
                }//end if

                $spacesAfter = 0;
                if ($tokens[$equalToken]['line'] !== $tokens[$param['default_token']]['line']) {
                    $spacesAfter = 'newline';
                } else if ($tokens[($equalToken + 1)]['code'] === T_WHITESPACE) {
                    $spacesAfter = $tokens[($equalToken + 1)]['length'];
                }

                if ($spacesAfter !== $this->equalsSpacing) {
                    $error = 'Incorrect spacing between default value and equals sign for argument "%s"; expected '.$this->equalsSpacing.' but found %s';
                    $data  = [
                        $param['name'],
                        $spacesAfter,
                    ];

                    $nextNonWhitespace = $phpcsFile->findNext(T_WHITESPACE, ($equalToken + 1), $param['default_token'], true);
                    if ($nextNonWhitespace !== false) {
                        $phpcsFile->addError($error, $equalToken, 'SpaceAfterEquals', $data);
                    } else {
                        $fix = $phpcsFile->addFixableError($error, $equalToken, 'SpaceAfterEquals', $data);
                        if ($fix === true) {
                            $padding = str_repeat(' ', $this->equalsSpacing);

                            $phpcsFile->fixer->beginChangeset();
                            $phpcsFile->fixer->addContent($equalToken, $padding);

                            for ($i = ($equalToken + 1); $tokens[$i]['code'] === T_WHITESPACE; $i++) {
                                $phpcsFile->fixer->replaceToken($i, '');
                            }

                            $phpcsFile->fixer->endChangeset();
                        }
                    }
                }//end if
            }//end if

            if ($param['type_hint_token'] !== false) {
                $typeHintToken = $param['type_hint_end_token'];

                $gap = '';
                $i   = $typeHintToken;
                while ($tokens[++$i]['code'] === T_WHITESPACE) {
                    $gap .= $tokens[$i]['content'];
                }

                if ($gap !== ' ') {
                    $error = 'Expected 1 space between type hint and argument "%s"; %s found';
                    $data  = [
                        $param['name'],
                    ];
                    if (trim($gap, ' ') === '') {
                        // Gap contains only space characters: report the number of spaces.
                        $data[] = strlen($gap);
                    } else {
                        // Gap contains more than just spaces: render these for better clarity.
                        $data[] = '"'.Common::prepareForOutput($gap).'"';
                    }

                    $fix = $phpcsFile->addFixableError($error, $typeHintToken, 'SpacingAfterHint', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        $phpcsFile->fixer->addContent($typeHintToken, ' ');

                        for ($i = ($typeHintToken + 1); $tokens[$i]['code'] === T_WHITESPACE; $i++) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->endChangeset();
                    }
                }//end if
            }//end if

            if (isset($param['visibility_token']) === true && $param['visibility_token'] !== false) {
                $visibilityToken      = $param['visibility_token'];
                $afterVisibilityToken = $phpcsFile->findNext(T_WHITESPACE, ($visibilityToken + 1), $param['token'], true);

                $spacesAfter = 0;
                if ($afterVisibilityToken !== false
                    && $tokens[$visibilityToken]['line'] !== $tokens[$afterVisibilityToken]['line']
                ) {
                    $spacesAfter = 'newline';
                } else if ($tokens[($visibilityToken + 1)]['code'] === T_WHITESPACE) {
                    $spacesAfter = $tokens[($visibilityToken + 1)]['length'];
                }

                if ($spacesAfter !== 1) {
                    $error = 'Expected 1 space after visibility modifier "%s"; %s found';
                    $data  = [
                        $tokens[$visibilityToken]['content'],
                        $spacesAfter,
                    ];

                    $fix = $phpcsFile->addFixableError($error, $visibilityToken, 'SpacingAfterVisbility', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        $phpcsFile->fixer->addContent($visibilityToken, ' ');

                        for ($i = ($visibilityToken + 1); $tokens[$i]['code'] === T_WHITESPACE; $i++) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->endChangeset();
                    }
                }//end if
            }//end if

            if (isset($param['set_visibility_token']) === true && $param['set_visibility_token'] !== false) {
                $visibilityToken      = $param['set_visibility_token'];
                $afterVisibilityToken = $phpcsFile->findNext(T_WHITESPACE, ($visibilityToken + 1), $param['token'], true);

                $spacesAfter = 0;
                if ($afterVisibilityToken !== false
                    && $tokens[$visibilityToken]['line'] !== $tokens[$afterVisibilityToken]['line']
                ) {
                    $spacesAfter = 'newline';
                } else if ($tokens[($visibilityToken + 1)]['code'] === T_WHITESPACE) {
                    $spacesAfter = $tokens[($visibilityToken + 1)]['length'];
                }

                if ($spacesAfter !== 1) {
                    $error = 'Expected 1 space after set-visibility modifier "%s"; %s found';
                    $data  = [
                        $tokens[$visibilityToken]['content'],
                        $spacesAfter,
                    ];

                    $fix = $phpcsFile->addFixableError($error, $visibilityToken, 'SpacingAfterSetVisibility', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        $phpcsFile->fixer->addContent($visibilityToken, ' ');

                        for ($i = ($visibilityToken + 1); $tokens[$i]['code'] === T_WHITESPACE; $i++) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->endChangeset();
                    }
                }//end if
            }//end if

            if (isset($param['readonly_token']) === true) {
                $readonlyToken      = $param['readonly_token'];
                $afterReadonlyToken = $phpcsFile->findNext(T_WHITESPACE, ($readonlyToken + 1), $param['token'], true);

                $spacesAfter = 0;
                if ($afterReadonlyToken !== false
                    && $tokens[$readonlyToken]['line'] !== $tokens[$afterReadonlyToken]['line']
                ) {
                    $spacesAfter = 'newline';
                } else if ($tokens[($readonlyToken + 1)]['code'] === T_WHITESPACE) {
                    $spacesAfter = $tokens[($readonlyToken + 1)]['length'];
                }

                if ($spacesAfter !== 1) {
                    $error = 'Expected 1 space after readonly modifier; %s found';
                    $data  = [$spacesAfter];

                    $fix = $phpcsFile->addFixableError($error, $readonlyToken, 'SpacingAfterReadonly', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        $phpcsFile->fixer->addContent($readonlyToken, ' ');

                        for ($i = ($readonlyToken + 1); $tokens[$i]['code'] === T_WHITESPACE; $i++) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->endChangeset();
                    }
                }//end if
            }//end if

            $commaToken = false;
            if ($paramNumber > 0 && $params[($paramNumber - 1)]['comma_token'] !== false) {
                $commaToken = $params[($paramNumber - 1)]['comma_token'];
            }

            if ($commaToken !== false) {
                $endOfPreviousParam = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($commaToken - 1), null, true);

                $spaceBeforeComma = 0;
                if ($tokens[$endOfPreviousParam]['line'] !== $tokens[$commaToken]['line']) {
                    $spaceBeforeComma = 'newline';
                } else if ($tokens[($commaToken - 1)]['code'] === T_WHITESPACE) {
                    $spaceBeforeComma = $tokens[($commaToken - 1)]['length'];
                }

                if ($spaceBeforeComma !== 0) {
                    $error = 'Expected 0 spaces between argument "%s" and comma; %s found';
                    $data  = [
                        $params[($paramNumber - 1)]['name'],
                        $spaceBeforeComma,
                    ];

                    $fix = $phpcsFile->addFixableError($error, $commaToken, 'SpaceBeforeComma', $data);
                    if ($fix === true) {
                        $startOfCurrentParam = $phpcsFile->findNext(Tokens::$emptyTokens, ($commaToken + 1), null, true);

                        $phpcsFile->fixer->beginChangeset();
                        $phpcsFile->fixer->addContent($endOfPreviousParam, ',');
                        $phpcsFile->fixer->replaceToken($commaToken, '');

                        if ($tokens[$commaToken]['line'] === $tokens[$startOfCurrentParam]['line']) {
                            for ($i = ($commaToken + 1); $tokens[$i]['code'] === T_WHITESPACE; $i++) {
                                $phpcsFile->fixer->replaceToken($i, '');
                            }
                        } else {
                            for ($i = ($commaToken - 1);
                                $tokens[$i]['code'] === T_WHITESPACE && $tokens[$endOfPreviousParam]['line'] !== $tokens[$i]['line'];
                                $i--
                            ) {
                                $phpcsFile->fixer->replaceToken($i, '');
                            }

                            for ($i = ($commaToken + 1);
                                $tokens[$i]['code'] === T_WHITESPACE && $tokens[$commaToken]['line'] === $tokens[$i]['line'];
                                $i++
                            ) {
                                $phpcsFile->fixer->replaceToken($i, '');
                            }
                        }

                        $phpcsFile->fixer->endChangeset();
                    }//end if
                }//end if

                // Don't check spacing after the comma if it is the last content on the line.
                $checkComma = true;
                if ($multiLine === true) {
                    $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($commaToken + 1), $closeBracket, true);
                    if ($tokens[$next]['line'] !== $tokens[$commaToken]['line']) {
                        $checkComma = false;
                    }
                }

                if ($checkComma === true) {
                    $typeOfNext      = 'argument';
                    $typeOfNextShort = 'Arg';
                    $contentOfNext   = $param['name'];

                    if (isset($param['property_visibility']) === true) {
                        $typeOfNext      = 'property modifier';
                        $typeOfNextShort = 'PropertyModifier';
                        $modifier        = $phpcsFile->findNext(Tokens::$emptyTokens, ($commaToken + 1), $param['token'], true);
                        $contentOfNext   = $tokens[$modifier]['content'];
                    } else if ($param['type_hint_token'] !== false) {
                        $typeOfNext      = 'type hint';
                        $typeOfNextShort = 'Hint';
                        $contentOfNext   = $param['type_hint'];
                    }

                    $spacesAfter = 0;
                    if ($tokens[($commaToken + 1)]['code'] === T_WHITESPACE) {
                        $spacesAfter = $tokens[($commaToken + 1)]['length'];
                    }

                    if ($spacesAfter === 0) {
                        $error     = 'Expected 1 space between comma and %s "%s"; 0 found';
                        $errorCode = 'NoSpaceBefore'.$typeOfNextShort;
                        $data      = [
                            $typeOfNext,
                            $contentOfNext,
                        ];

                        $fix = $phpcsFile->addFixableError($error, $commaToken, $errorCode, $data);
                        if ($fix === true) {
                            $phpcsFile->fixer->addContent($commaToken, ' ');
                        }
                    } else if ($spacesAfter !== 1) {
                        $error     = 'Expected 1 space between comma and %s "%s"; %s found';
                        $errorCode = 'SpacingBefore'.$typeOfNextShort;
                        $data      = [
                            $typeOfNext,
                            $contentOfNext,
                            $spacesAfter,
                        ];

                        $fix = $phpcsFile->addFixableError($error, $commaToken, $errorCode, $data);
                        if ($fix === true) {
                            $phpcsFile->fixer->replaceToken(($commaToken + 1), ' ');
                        }
                    }//end if
                }//end if
            }//end if
        }//end foreach

        // Only check spacing around parenthesis for single line definitions.
        if ($multiLine === true) {
            return;
        }

        $gap = 0;
        if ($tokens[($closeBracket - 1)]['code'] === T_WHITESPACE) {
            $gap = $tokens[($closeBracket - 1)]['length'];
        }

        if ($gap !== $this->requiredSpacesBeforeClose) {
            $error = 'Expected %s spaces before closing parenthesis; %s found';
            $data  = [
                $this->requiredSpacesBeforeClose,
                $gap,
            ];
            $fix   = $phpcsFile->addFixableError($error, $closeBracket, 'SpacingBeforeClose', $data);
            if ($fix === true) {
                $padding = str_repeat(' ', $this->requiredSpacesBeforeClose);
                if ($gap === 0) {
                    $phpcsFile->fixer->addContentBefore($closeBracket, $padding);
                } else {
                    $phpcsFile->fixer->replaceToken(($closeBracket - 1), $padding);
                }
            }
        }

        $gap = 0;
        if ($tokens[($openBracket + 1)]['code'] === T_WHITESPACE) {
            $gap = $tokens[($openBracket + 1)]['length'];
        }

        if ($gap !== $this->requiredSpacesAfterOpen) {
            $error = 'Expected %s spaces after opening parenthesis; %s found';
            $data  = [
                $this->requiredSpacesAfterOpen,
                $gap,
            ];
            $fix   = $phpcsFile->addFixableError($error, $openBracket, 'SpacingAfterOpen', $data);
            if ($fix === true) {
                $padding = str_repeat(' ', $this->requiredSpacesAfterOpen);
                if ($gap === 0) {
                    $phpcsFile->fixer->addContent($openBracket, $padding);
                } else {
                    $phpcsFile->fixer->replaceToken(($openBracket + 1), $padding);
                }
            }
        }

    }//end processBracket()


}//end class
