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

class WithReplacementSniff implements Sniff,DeprecatedSniff
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
        return 'Use the Stnd.Category.OtherSniff sniff instead.';
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
