<?php
/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Ruleset\RegisterSniffsMissingInterfaceTest
 */

namespace Fixtures\TestStandard\Sniffs\MissingInterface;

use PHP_CodeSniffer\Files\File;

final class InvalidImplementsWithoutImplementSniff
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
