<?php
/**
 * Test the Ruleset::expandSniffDirectory() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Ruleset;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHPUnit\Framework\TestCase;

/**
 * Test handling of sniffs not following the PHPCS naming conventions in the Ruleset::populateTokenListeners() method.
 *
 * @covers \PHP_CodeSniffer\Ruleset::populateTokenListeners
 */
final class PopulateTokenListenersNamingConventionsTest extends TestCase
{


    /**
     * Verify a warning is shown for sniffs not complying with the PHPCS naming conventions.
     *
     * Including sniffs which do not comply with the PHPCS naming conventions is soft deprecated since
     * PHPCS 3.12.0, hard deprecated since PHPCS 3.13.0 and support will be removed in PHPCS 4.0.0.
     *
     * @return void
     */
    public function testBrokenNamingConventions()
    {
        // Set up the ruleset.
        $standard = __DIR__.'/PopulateTokenListenersNamingConventionsTest.xml';
        $config   = new ConfigDouble(["--standard=$standard"]);
        $ruleset  = new Ruleset($config);

        // The "Generic.PHP.BacktickOperator" sniff is the only valid sniff.
        $expectedSniffCodes = [
            '..NoNamespace'                                       => 'NoNamespaceSniff',
            '.Sniffs.MissingCategoryDir'                          => 'BrokenNamingConventions\\Sniffs\\MissingCategoryDirSniff',
            '.Sniffs.PartialNamespace'                            => 'Sniffs\\PartialNamespaceSniff',
            'BrokenNamingConventions.Category.'                   => 'BrokenNamingConventions\\Sniffs\\Category\\Sniff',
            'BrokenNamingConventions.Sniffs.CategoryCalledSniffs' => 'BrokenNamingConventions\\Sniffs\\Sniffs\\CategoryCalledSniffsSniff',
            'Generic.PHP.BacktickOperator'                        => 'PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\PHP\\BacktickOperatorSniff',
            'Sniffs.SubDir.TooDeeplyNested'                       => 'BrokenNamingConventions\\Sniffs\\Category\\SubDir\\TooDeeplyNestedSniff',
        ];

        // Sort the value to make the tests stable as different OSes will read directories
        // in a different order and the order is not relevant for these tests. Just the values.
        $actual = $ruleset->sniffCodes;
        ksort($actual);

        $this->assertSame($expectedSniffCodes, $actual, 'Registered sniffs do not match expectation');

        $expectedMessage  = 'DEPRECATED: The sniff BrokenNamingConventions\\Sniffs\\MissingCategoryDirSniff does not comply';
        $expectedMessage .= ' with the PHP_CodeSniffer naming conventions. This will no longer be supported in PHPCS 4.0.'.PHP_EOL;
        $expectedMessage .= 'Contact the sniff author to fix the sniff.'.PHP_EOL;
        $expectedMessage .= 'DEPRECATED: The sniff NoNamespaceSniff does not comply with the PHP_CodeSniffer naming conventions.';
        $expectedMessage .= ' This will no longer be supported in PHPCS 4.0.'.PHP_EOL;
        $expectedMessage .= 'Contact the sniff author to fix the sniff.'.PHP_EOL;
        $expectedMessage .= 'DEPRECATED: The sniff Sniffs\\PartialNamespaceSniff does not comply with the PHP_CodeSniffer naming conventions.';
        $expectedMessage .= ' This will no longer be supported in PHPCS 4.0.'.PHP_EOL;
        $expectedMessage .= 'Contact the sniff author to fix the sniff.'.PHP_EOL;
        $expectedMessage .= 'DEPRECATED: The sniff BrokenNamingConventions\\Sniffs\\Category\\Sniff does not comply';
        $expectedMessage .= ' with the PHP_CodeSniffer naming conventions. This will no longer be supported in PHPCS 4.0.'.PHP_EOL;
        $expectedMessage .= 'Contact the sniff author to fix the sniff.'.PHP_EOL;
        $expectedMessage .= 'DEPRECATED: The sniff BrokenNamingConventions\\Sniffs\\Sniffs\\CategoryCalledSniffsSniff does not';
        $expectedMessage .= ' comply with the PHP_CodeSniffer naming conventions. This will no longer be supported in PHPCS 4.0.'.PHP_EOL;
        $expectedMessage .= 'Contact the sniff author to fix the sniff.'.PHP_EOL;
        $expectedMessage .= 'DEPRECATED: The sniff BrokenNamingConventions\\Sniffs\\Category\\SubDir\\TooDeeplyNestedSniff';
        $expectedMessage .= ' does not comply with the PHP_CodeSniffer naming conventions. This will no longer be supported in PHPCS 4.0.'.PHP_EOL;
        $expectedMessage .= 'Contact the sniff author to fix the sniff.'.PHP_EOL.PHP_EOL;

        $this->expectOutputString($expectedMessage);

    }//end testBrokenNamingConventions()


}//end class
