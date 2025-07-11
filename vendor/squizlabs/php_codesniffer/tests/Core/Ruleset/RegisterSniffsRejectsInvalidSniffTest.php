<?php
/**
 * Tests that invalid sniffs will be rejected with an informative error message.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Ruleset;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHP_CodeSniffer\Tests\Core\Ruleset\AbstractRulesetTestCase;

/**
 * Tests that invalid sniffs will be rejected with an informative error message.
 *
 * @covers \PHP_CodeSniffer\Ruleset::registerSniffs
 */
final class RegisterSniffsRejectsInvalidSniffTest extends AbstractRulesetTestCase
{


    /**
     * Verify that an error is thrown if an invalid sniff class is loaded.
     *
     * @param string $standard   The standard to use for the test.
     * @param string $methodName The name of the missing method.
     *
     * @dataProvider dataExceptionIsThrownOnMissingInterfaceMethod
     *
     * @return void
     */
    public function testExceptionIsThrownOnMissingInterfaceMethod($standard, $methodName)
    {
        // Set up the ruleset.
        $standard = __DIR__.'/'.$standard;
        $config   = new ConfigDouble(["--standard=$standard"]);

        $regex = "`(^|\R)ERROR: Sniff class \S+Sniff is missing required method $methodName\(\)\.\R`";
        $this->expectRuntimeExceptionRegex($regex);

        new Ruleset($config);

    }//end testExceptionIsThrownOnMissingInterfaceMethod()


    /**
     * Data provider.
     *
     * @see testExceptionIsThrownOnMissingInterfaceMethod()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataExceptionIsThrownOnMissingInterfaceMethod()
    {
        return [
            'Missing register() method'                => [
                'standard'   => 'RegisterSniffsRejectsInvalidSniffNoImplementsNoRegisterTest.xml',
                'methodName' => 'register',
            ],
            'Missing process() method'                 => [
                'standard'   => 'RegisterSniffsRejectsInvalidSniffNoImplementsNoProcessTest.xml',
                'methodName' => 'process',
            ],
            'Missing both, checking register() method' => [
                'standard'   => 'RegisterSniffsRejectsInvalidSniffNoImplementsNoRegisterOrProcessTest.xml',
                'methodName' => 'register',
            ],
            'Missing both, checking process() method'  => [
                'standard'   => 'RegisterSniffsRejectsInvalidSniffNoImplementsNoRegisterOrProcessTest.xml',
                'methodName' => 'process',
            ],
        ];

    }//end dataExceptionIsThrownOnMissingInterfaceMethod()


}//end class
