<?php
/**
 * Tests for the \PHP_CodeSniffer\Config --extensions argument.
 *
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Config;

use PHP_CodeSniffer\Tests\ConfigDouble;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHP_CodeSniffer\Config --extensions argument.
 *
 * @covers \PHP_CodeSniffer\Config::processLongArgument
 */
final class ExtensionsArgTest extends TestCase
{


    /**
     * Ensure that the extension property is set when the parameter is passed a valid value.
     *
     * @param string $passedValue Extensions as passed on the command line.
     * @param string $expected    Expected value for the extensions property.
     *
     * @dataProvider dataValidExtensions
     *
     * @return void
     */
    public function testValidExtensions($passedValue, $expected)
    {
        $config = new ConfigDouble(["--extensions=$passedValue"]);

        $this->assertSame($expected, $config->extensions);

    }//end testValidExtensions()


    /**
     * Data provider.
     *
     * @see self::testValidExtensions()
     *
     * @return array<string, array<string, string|array<string, string>>>
     */
    public static function dataValidExtensions()
    {
        return [
            // Passing an empty extensions list is not useful, as it will result in no files being scanned,
            // but that's the responsibility of the user.
            'Empty extensions list'                                                          => [
                'passedValue' => '',
                'expected'    => [],
            ],
            'Single extension passed: php'                                                   => [
                'passedValue' => 'php',
                'expected'    => [
                    'php' => 'PHP',
                ],
            ],
            // This would cause PHPCS to scan python files as PHP, which will probably cause very weird scan results,
            // but that's the responsibility of the user.
            'Single extension passed: py'                                                    => [
                'passedValue' => 'py',
                'expected'    => [
                    'py' => 'PHP',
                ],
            ],
            // This would likely result in a problem when PHPCS can't find a "PY" tokenizer class,
            // but that's not our concern at this moment. Support for non-PHP tokenizers is being dropped soon anyway.
            'Single extension passed with language: py/py'                                   => [
                'passedValue' => 'py/py',
                'expected'    => [
                    'py' => 'PY',
                ],
            ],
            'Multiple extensions passed: php,js,css'                                         => [
                'passedValue' => 'php,js,css',
                'expected'    => [
                    'php' => 'PHP',
                    'js'  => 'JS',
                    'css' => 'CSS',
                ],
            ],
            'Multiple extensions passed, some with language: php,inc/php,phpt/php,js'        => [
                'passedValue' => 'php,inc/php,phpt/php,js',
                'expected'    => [
                    'php'  => 'PHP',
                    'inc'  => 'PHP',
                    'phpt' => 'PHP',
                    'js'   => 'JS',
                ],
            ],
            'File extensions are set case sensitively (and filtering is case sensitive too)' => [
                'passedValue' => 'PHP,php',
                'expected'    => [
                    'PHP' => 'PHP',
                    'php' => 'PHP',
                ],
            ],
        ];

    }//end dataValidExtensions()


    /**
     * Ensure that only the first argument is processed and others are ignored.
     *
     * @return void
     */
    public function testOnlySetOnce()
    {
        $config = new ConfigDouble(
            [
                '--extensions=php',
                '--extensions=inc,module',
            ]
        );

        $this->assertSame(['php' => 'PHP'], $config->extensions);

    }//end testOnlySetOnce()


}//end class
