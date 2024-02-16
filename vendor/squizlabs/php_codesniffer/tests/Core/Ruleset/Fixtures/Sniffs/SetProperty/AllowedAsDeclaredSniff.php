<?php
/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Ruleset\SetSniffPropertyTest
 */

namespace Fixtures\Sniffs\SetProperty;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class AllowedAsDeclaredSniff implements Sniff
{

    public $arbitrarystring;
    public $arbitraryarray;

    public function register()
    {
        return [T_WHITESPACE];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        // Do something.
    }
}
