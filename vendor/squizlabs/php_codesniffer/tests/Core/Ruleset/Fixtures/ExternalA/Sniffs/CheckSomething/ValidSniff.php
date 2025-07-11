<?php
/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Ruleset\ExpandRulesetReferenceTest
 */

namespace Fixtures\ExternalA\Sniffs\CheckSomething;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ValidSniff implements Sniff
{

    public function register()
    {
        return [T_CLASS];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        // Do something.
    }
}
