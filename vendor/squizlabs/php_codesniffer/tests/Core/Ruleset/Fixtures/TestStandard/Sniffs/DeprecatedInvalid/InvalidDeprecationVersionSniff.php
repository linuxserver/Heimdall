<?php
/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Ruleset\SniffDeprecationTest
 */

namespace Fixtures\TestStandard\Sniffs\DeprecatedInvalid;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\DeprecatedSniff;
use PHP_CodeSniffer\Sniffs\Sniff;

class InvalidDeprecationVersionSniff implements Sniff,DeprecatedSniff
{

    public function getDeprecationVersion()
    {
        return 3.8;
    }

    public function getRemovalVersion()
    {
        return 'dummy';
    }

    public function getDeprecationMessage()
    {
        return 'dummy';
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
