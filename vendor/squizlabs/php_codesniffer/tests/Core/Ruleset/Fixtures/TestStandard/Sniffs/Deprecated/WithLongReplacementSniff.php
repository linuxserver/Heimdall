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

class WithLongReplacementSniff implements Sniff,DeprecatedSniff
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
        return 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce vel vestibulum nunc. Sed luctus dolor tortor, eu euismod purus pretium sed. Fusce egestas congue massa semper cursus. Donec quis pretium tellus. In lacinia, augue ut ornare porttitor, diam nunc faucibus purus, et accumsan eros sapien at sem. Sed pulvinar aliquam malesuada. Aliquam erat volutpat. Mauris gravida rutrum lectus at egestas. Fusce tempus elit in tincidunt dictum. Suspendisse dictum egestas sapien, eget ullamcorper metus elementum semper. Vestibulum sem justo, consectetur ac tincidunt et, finibus eget libero.';
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
