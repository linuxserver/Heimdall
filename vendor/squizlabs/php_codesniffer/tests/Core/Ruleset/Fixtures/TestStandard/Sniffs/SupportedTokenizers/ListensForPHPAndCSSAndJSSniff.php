<?php
/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Ruleset\PopulateTokenListenersSupportedTokenizersTest
 */

namespace Fixtures\TestStandard\Sniffs\SupportedTokenizers;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ListensForPHPAndCSSAndJSSniff implements Sniff
{

    public $supportedTokenizers = [
        'PHP',
        'JS',
        'CSS',
    ];

    public function register()
    {
        return [
            T_OPEN_TAG,
            T_OPEN_TAG_WITH_ECHO
        ];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        // Do something.
    }
}
