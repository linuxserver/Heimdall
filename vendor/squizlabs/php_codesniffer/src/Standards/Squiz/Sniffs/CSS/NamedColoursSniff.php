<?php
/**
 * Ensure colour names are not used.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 *
 * @deprecated 3.9.0
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\CSS;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\DeprecatedSniff;
use PHP_CodeSniffer\Sniffs\Sniff;

class NamedColoursSniff implements Sniff, DeprecatedSniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = ['CSS'];

    /**
     * A list of named colours.
     *
     * This is the list of standard colours defined in the CSS specification.
     *
     * @var array
     */
    protected $colourNames = [
        'aqua'    => 'aqua',
        'black'   => 'black',
        'blue'    => 'blue',
        'fuchsia' => 'fuchsia',
        'gray'    => 'gray',
        'green'   => 'green',
        'lime'    => 'lime',
        'maroon'  => 'maroon',
        'navy'    => 'navy',
        'olive'   => 'olive',
        'orange'  => 'orange',
        'purple'  => 'purple',
        'red'     => 'red',
        'silver'  => 'silver',
        'teal'    => 'teal',
        'white'   => 'white',
        'yellow'  => 'yellow',
    ];


    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return array<int|string>
     */
    public function register()
    {
        return [T_STRING];

    }//end register()


    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where the token was found.
     * @param int                         $stackPtr  The position in the stack where
     *                                               the token was found.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[($stackPtr - 1)]['code'] === T_HASH
            || $tokens[($stackPtr - 1)]['code'] === T_STRING_CONCAT
        ) {
            // Class name.
            return;
        }

        if (isset($this->colourNames[strtolower($tokens[$stackPtr]['content'])]) === true) {
            $error = 'Named colours are forbidden; use hex, rgb, or rgba values instead';
            $phpcsFile->addError($error, $stackPtr, 'Forbidden');
        }

    }//end process()


    /**
     * Provide the version number in which the sniff was deprecated.
     *
     * @return string
     */
    public function getDeprecationVersion()
    {
        return 'v3.9.0';

    }//end getDeprecationVersion()


    /**
     * Provide the version number in which the sniff will be removed.
     *
     * @return string
     */
    public function getRemovalVersion()
    {
        return 'v4.0.0';

    }//end getRemovalVersion()


    /**
     * Provide a custom message to display with the deprecation.
     *
     * @return string
     */
    public function getDeprecationMessage()
    {
        return 'Support for scanning CSS files will be removed completely in v4.0.0.';

    }//end getDeprecationMessage()


}//end class
