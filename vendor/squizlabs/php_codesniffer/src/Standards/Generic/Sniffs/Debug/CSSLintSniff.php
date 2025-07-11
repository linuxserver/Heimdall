<?php
/**
 * Runs csslint on the file.
 *
 * @author    Roman Levishchenko <index.0h@gmail.com>
 * @copyright 2013-2014 Roman Levishchenko
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 *
 * @deprecated 3.9.0
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Debug;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\DeprecatedSniff;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Common;

class CSSLintSniff implements Sniff, DeprecatedSniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = ['CSS'];


    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return array<int|string>
     */
    public function register()
    {
        return [T_OPEN_TAG];

    }//end register()


    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where the token was found.
     * @param int                         $stackPtr  The position in the stack where
     *                                               the token was found.
     *
     * @return int
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $csslintPath = Config::getExecutablePath('csslint');
        if ($csslintPath === null) {
            return $phpcsFile->numTokens;
        }

        $fileName = $phpcsFile->getFilename();

        $cmd = Common::escapeshellcmd($csslintPath).' '.escapeshellarg($fileName).' 2>&1';
        exec($cmd, $output, $retval);

        if (is_array($output) === false) {
            return $phpcsFile->numTokens;
        }

        $count = count($output);

        for ($i = 0; $i < $count; $i++) {
            $matches    = [];
            $numMatches = preg_match(
                '/(error|warning) at line (\d+)/',
                $output[$i],
                $matches
            );

            if ($numMatches === 0) {
                continue;
            }

            $line    = (int) $matches[2];
            $message = 'csslint says: '.$output[($i + 1)];
            // First line is message with error line and error code.
            // Second is error message.
            // Third is wrong line in file.
            // Fourth is empty line.
            $i += 4;

            $phpcsFile->addWarningOnLine($message, $line, 'ExternalTool');
        }//end for

        // Ignore the rest of the file.
        return $phpcsFile->numTokens;

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
