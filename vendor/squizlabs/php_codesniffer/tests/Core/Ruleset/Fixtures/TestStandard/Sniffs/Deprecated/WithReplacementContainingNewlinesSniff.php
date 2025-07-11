<?php
/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Ruleset\SniffDeprecationTest
 */

namespace Fixtures\TestStandard\Sniffs\Deprecated;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\DeprecatedSniff;
use PHP_CodeSniffer\Sniffs\Sniff;

class WithReplacementContainingNewlinesSniff implements Sniff,DeprecatedSniff
{

    public function getDeprecationVersion()
    {
        return 'v3.8.0';
    }

    public function getRemovalVersion()
    {
        return 'v4.0.0';
    }

    public function getDeprecationMessage()
    {
        return 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'.PHP_EOL
            .'Fusce vel vestibulum nunc. Sed luctus dolor tortor, eu euismod purus pretium sed.'.PHP_EOL
            .'Fusce egestas congue massa semper cursus. Donec quis pretium tellus.'.PHP_EOL
            .'In lacinia, augue ut ornare porttitor, diam nunc faucibus purus, et accumsan eros sapien at sem.'.PHP_EOL
            .'Sed pulvinar aliquam malesuada. Aliquam erat volutpat. Mauris gravida rutrum lectus at egestas';
    }

    public function register()
    {
        return [T_WHITESPACE];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        // Do something.
    }
}
