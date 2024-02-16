<?php
/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Ruleset\SniffDeprecationTest
 */

namespace Fixtures\Sniffs\DeprecatedInvalid;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\DeprecatedSniff;
use PHP_CodeSniffer\Sniffs\Sniff;
use stdClass;

class InvalidDeprecationMessageSniff implements Sniff,DeprecatedSniff
{

    public function getDeprecationVersion()
    {
        return 'dummy';
    }

    public function getRemovalVersion()
    {
        return 'dummy';
    }

    public function getDeprecationMessage()
    {
        return new stdClass;
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
