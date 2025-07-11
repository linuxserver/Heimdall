<?php
/**
 * Tests deprecation of support for sniffs not implementing the PHPCS `Sniff` interface.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Ruleset;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHPUnit\Framework\TestCase;

/**
 * Tests deprecation of support for sniffs not implementing the PHPCS `Sniff` interface.
 *
 * @covers \PHP_CodeSniffer\Ruleset::registerSniffs
 */
final class RegisterSniffsMissingInterfaceTest extends TestCase
{


    /**
     * Test that no deprecation is shown when sniffs implement the `PHP_CodeSniffer\Sniffs\Sniff` interface.
     *
     * @return void
     */
    public function testNoNoticesForSniffsImplementingInterface()
    {
        // Set up the ruleset.
        $standard = __DIR__.'/RegisterSniffsMissingInterfaceValidTest.xml';
        $config   = new ConfigDouble(["--standard=$standard"]);

        $this->expectOutputString('');

        new Ruleset($config);

    }//end testNoNoticesForSniffsImplementingInterface()


    /**
     * Test that a deprecation notice is shown if a sniff doesn't implement the Sniff interface.
     *
     * @return void
     */
    public function testDeprecationNoticeWhenSniffDoesntImplementInterface()
    {
        // Set up the ruleset.
        $standard = __DIR__.'/RegisterSniffsMissingInterfaceInvalidTest.xml';
        $config   = new ConfigDouble(["--standard=$standard"]);

        $expected  = 'DEPRECATED: All sniffs must implement the PHP_CodeSniffer\\Sniffs\\Sniff interface.'.PHP_EOL;
        $expected .= 'Interface not implemented for sniff Fixtures\\TestStandard\\Sniffs\\MissingInterface\\InvalidImplementsWithoutImplementSniff.'.PHP_EOL;
        $expected .= 'Contact the sniff author to fix the sniff.'.PHP_EOL.PHP_EOL;

        $this->expectOutputString($expected);

        new Ruleset($config);

    }//end testDeprecationNoticeWhenSniffDoesntImplementInterface()


}//end class
