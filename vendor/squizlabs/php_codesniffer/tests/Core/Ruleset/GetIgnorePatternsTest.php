<?php
/**
 * Test the Ruleset::getIgnorePatterns() method.
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
 * Test the Ruleset::getIgnorePatterns() method.
 *
 * @covers \PHP_CodeSniffer\Ruleset::getIgnorePatterns
 */
final class GetIgnorePatternsTest extends TestCase
{

    /**
     * The Ruleset object.
     *
     * @var \PHP_CodeSniffer\Ruleset
     */
    private static $ruleset;


    /**
     * Initialize the config and ruleset objects for this test.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function initializeConfigAndRuleset()
    {
        // Set up the ruleset.
        $standard      = __DIR__."/GetIgnorePatternsTest.xml";
        $config        = new ConfigDouble(["--standard=$standard"]);
        self::$ruleset = new Ruleset($config);

    }//end initializeConfigAndRuleset()


    /**
     * Test retrieving ignore patterns.
     *
     * @param string|null                                 $listener The listener to get patterns for or null for all patterns.
     * @param array<string, string|array<string, string>> $expected The expected function output.
     *
     * @dataProvider dataGetIgnorePatterns
     *
     * @return void
     */
    public function testGetIgnorePatterns($listener, $expected)
    {
        $this->assertSame($expected, self::$ruleset->getIgnorePatterns($listener));

    }//end testGetIgnorePatterns()


    /**
     * Data provider.
     *
     * @see self::testGetIgnorePatterns()
     *
     * @return array<string, array<string, string|array<string, string|array<string, string>>|null>>
     */
    public static function dataGetIgnorePatterns()
    {
        return [
            'All ignore patterns'                                   => [
                'listener' => null,
                'expected' => [
                    'PSR1.Classes.ClassDeclaration'     => [
                        './src/*/file.php' => 'absolute',
                        './bin/'           => 'relative',
                    ],
                    'Generic.Formatting.SpaceAfterCast' => [
                        './src/*/test\\.php$' => 'absolute',
                    ],
                    './tests/'                          => 'absolute',
                    './vendor/*'                        => 'absolute',
                    '*/node-modules/*'                  => 'relative',
                ],
            ],
            'Ignore patterns for PSR1.Classes.ClassDeclaration'     => [
                'listener' => 'PSR1.Classes.ClassDeclaration',
                'expected' => [
                    './src/*/file.php' => 'absolute',
                    './bin/'           => 'relative',
                ],
            ],
            'Ignore patterns for Generic.Formatting.SpaceAfterCast' => [
                'listener' => 'Generic.Formatting.SpaceAfterCast',
                'expected' => ['./src/*/test\\.php$' => 'absolute'],
            ],
            'Ignore patterns for sniff without ignore patterns'     => [
                'listener' => 'PSR1.Files.SideEffects',
                'expected' => [],
            ],
        ];

    }//end dataGetIgnorePatterns()


}//end class
