<?php
/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Ruleset\PopulateTokenListenersNamingConventionsTest
 */

namespace Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class PartialNamespaceSniff implements Sniff
{
    public function register()
    {
        return [T_OPEN_TAG];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        // Do something.
    }
}
