<?php
/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Ruleset\SetSniffPropertyTest
 */

namespace Fixtures\TestStandard\Sniffs\SetProperty;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use stdClass;

class AllowedViaStdClassSniff extends stdClass implements Sniff
{

    public function register()
    {
        return [T_WHITESPACE];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        // Do something.
    }
}
