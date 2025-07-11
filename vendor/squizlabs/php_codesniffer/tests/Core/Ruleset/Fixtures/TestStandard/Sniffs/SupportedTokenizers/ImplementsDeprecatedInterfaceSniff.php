<?php
/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Ruleset\PopulateTokenListenersSupportedTokenizersTest
 */

namespace Fixtures\TestStandard\Sniffs\SupportedTokenizers;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\DeprecatedSniff;
use PHP_CodeSniffer\Sniffs\Sniff;

class ImplementsDeprecatedInterfaceSniff implements Sniff, DeprecatedSniff
{

    public $supportedTokenizers = [
        'CSS',
        'JS',
    ];

    public function getDeprecationVersion()
    {
        return 'v3.4.0';
    }

    public function getRemovalVersion()
    {
        return 'v4.0.0';
    }

    public function getDeprecationMessage()
    {
        return '';
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
