<?php
/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Ruleset\RegisterSniffsRejectsInvalidSniffTest
 */

namespace Fixtures\TestStandard\Sniffs\InvalidSniffError;

use PHP_CodeSniffer\Files\File;

final class NoImplementsNoRegisterSniff
{

    public function process(File $phpcsFile, $stackPtr)
    {
        // Do something.
    }
}
