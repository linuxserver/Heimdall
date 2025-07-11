<?php
/**
 * Test the Ruleset::getIncludePatterns() method.
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
 * Test the Ruleset::getIncludePatterns() method.
 *
 * @covers \PHP_CodeSniffer\Ruleset::getIncludePatterns
 */
final class GetIncludePatternsTest extends TestCase
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
        $standard      = __DIR__."/GetIncludePatternsTest.xml";
        $config        = new ConfigDouble(["--standard=$standard"]);
        self::$ruleset = new Ruleset($config);

    }//end initializeConfigAndRuleset()


    /**
     * Test retrieving include patterns.
     *
     * @param string|null                                 $listener The listener to get patterns for or null for all patterns.
     * @param array<string, string|array<string, string>> $expected The expected function output.
     *
     * @dataProvider dataGetIncludePatterns
     *
     * @return void
     */
    public function testGetIncludePatterns($listener, $expected)
    {
        $this->assertSame($expected, self::$ruleset->getIncludePatterns($listener));

    }//end testGetIncludePatterns()


    /**
     * Data provider.
     *
     * @see self::testGetIncludePatterns()
     *
     * @return array<string, array<string, string|array<string, string|array<string, string>>|null>>
     */
    public static function dataGetIncludePatterns()
    {
        return [
            'All include patterns'                                   => [
                'listener' => null,
                'expected' => [
                    'PSR1.Classes.ClassDeclaration'     => [
                        './src/*/file.php' => 'absolute',
                        './bin/'           => 'relative',
                    ],
                    'Generic.Formatting.SpaceAfterCast' => [
                        './src/*/test\\.php$' => 'absolute',
                    ],
                ],
            ],
            'Include patterns for PSR1.Classes.ClassDeclaration'     => [
                'listener' => 'PSR1.Classes.ClassDeclaration',
                'expected' => [
                    './src/*/file.php' => 'absolute',
                    './bin/'           => 'relative',
                ],
            ],
            'Include patterns for Generic.Formatting.SpaceAfterCast' => [
                'listener' => 'Generic.Formatting.SpaceAfterCast',
                'expected' => ['./src/*/test\\.php$' => 'absolute'],
            ],
            'Include patterns for sniff without include patterns'    => [
                'listener' => 'PSR1.Files.SideEffects',
                'expected' => [],
            ],
        ];

    }//end dataGetIncludePatterns()


}//end class
