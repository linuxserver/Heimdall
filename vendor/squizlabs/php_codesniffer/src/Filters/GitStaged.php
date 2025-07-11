<?php
/**
 * A filter to only include files that have been staged for commit in a Git repository.
 *
 * This filter is the ideal companion for your pre-commit git hook.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2018 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Filters;

use PHP_CodeSniffer\Util\Common;

class GitStaged extends ExactMatch
{


    /**
     * Get a list of file paths to exclude.
     *
     * @since 3.9.0
     *
     * @return array
     */
    protected function getDisallowedFiles()
    {
        return [];

    }//end getDisallowedFiles()


    /**
     * Get a list of file paths to exclude.
     *
     * @deprecated 3.9.0 Overload the `getDisallowedFiles()` method instead.
     *
     * @codeCoverageIgnore
     *
     * @return array
     */
    protected function getBlacklist()
    {
        return $this->getDisallowedFiles();

    }//end getBlacklist()


    /**
     * Get a list of file paths to include.
     *
     * @since 3.9.0
     *
     * @return array
     */
    protected function getAllowedFiles()
    {
        $modified = [];

        $cmd    = 'git diff --cached --name-only -- '.escapeshellarg($this->basedir);
        $output = $this->exec($cmd);

        $basedir = $this->basedir;
        if (is_dir($basedir) === false) {
            $basedir = dirname($basedir);
        }

        foreach ($output as $path) {
            $path = Common::realpath($path);
            if ($path === false) {
                // Skip deleted files.
                continue;
            }

            do {
                $modified[$path] = true;
                $path            = dirname($path);
            } while ($path !== $basedir);
        }

        return $modified;

    }//end getAllowedFiles()


    /**
     * Get a list of file paths to include.
     *
     * @deprecated 3.9.0 Overload the `getAllowedFiles()` method instead.
     *
     * @codeCoverageIgnore
     *
     * @return array
     */
    protected function getWhitelist()
    {
        return $this->getAllowedFiles();

    }//end getWhitelist()


    /**
     * Execute an external command.
     *
     * {@internal This method is only needed to allow for mocking the return value
     * to test the class logic.}
     *
     * @param string $cmd Command.
     *
     * @return array
     */
    protected function exec($cmd)
    {
        $output   = [];
        $lastLine = exec($cmd, $output);
        if ($lastLine === false) {
            return [];
        }

        return $output;

    }//end exec()


}//end class
