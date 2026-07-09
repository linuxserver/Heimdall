<?php
/**
 * Ensures PHP believes the syntax is clean.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Blaine Schmeisser <blainesch@gmail.com>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/HEAD/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\PHP;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Common;

class SyntaxSniff implements Sniff
{

    /**
     * The path to the PHP version we are checking with.
     *
     * @var string
     */
    private $phpPath = null;


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
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return int
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->phpPath === null) {
            $this->phpPath = Config::getExecutablePath('php');
        }

        $cmd     = $this->getPhpLintCommand($phpcsFile);
        $output  = shell_exec($cmd);
        $matches = [];
        if (preg_match('/^.*error:(.*) in .* on line ([0-9]+)/m', trim($output), $matches) === 1) {
            $error = trim($matches[1]);
            $line  = (int) $matches[2];
            $phpcsFile->addErrorOnLine("PHP syntax error: $error", $line, 'PHPSyntax');
        }

        // Ignore the rest of the file.
        return $phpcsFile->numTokens;

    }//end process()


    /**
     * Returns the command used to lint PHP code.
     *
     * Uses a different command when the content is provided via STDIN.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The File object.
     *
     * @return string The command used to lint PHP code.
     */
    private function getPhpLintCommand(File $phpcsFile)
    {
        if ($phpcsFile->getFilename() === 'STDIN') {
            $content = $phpcsFile->getTokensAsString(0, $phpcsFile->numTokens);
            return sprintf(
                "echo %s | %s -l -d display_errors=1 -d error_prepend_string='' 2>&1",
                escapeshellarg($content),
                Common::escapeshellcmd($this->phpPath)
            );
        }

        $fileName = escapeshellarg($phpcsFile->getFilename());
        return sprintf(
            "%s -l -d display_errors=1 -d error_prepend_string='' %s 2>&1",
            Common::escapeshellcmd($this->phpPath),
            $fileName
        );

    }//end getPhpLintCommand()


}//end class
