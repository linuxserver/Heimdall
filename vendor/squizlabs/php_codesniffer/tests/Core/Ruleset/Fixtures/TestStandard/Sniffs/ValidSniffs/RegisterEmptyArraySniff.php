<?php
/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Ruleset\PopulateTokenListenersTest
 */

namespace Fixtures\TestStandard\Sniffs\ValidSniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class RegisterEmptyArraySniff implements Sniff
{

    public function register()
    {
        return [];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        // Do something.
    }
}
