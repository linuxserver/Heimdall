<?php
/**
 * Tests to verify that the "help" command functions as expected.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Help;

use PHP_CodeSniffer\Tests\ConfigDouble;
use PHP_CodeSniffer\Util\Help;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Test the Help class.
 *
 * @covers \PHP_CodeSniffer\Util\Help
 */
final class HelpTest extends TestCase
{


    /**
     * QA check: verify that the category names are at most the minimum screen width
     * and that option argument names are always at most half the length of the minimum screen width.
     *
     * If this test would start failing, either wrapping of argument info would need to be implemented
     * or the minimum screen width needs to be upped.
     *
     * @coversNothing
     *
     * @return void
     */
    public function testQaArgumentNamesAreWithinAcceptableBounds()
    {
        $help = new Help(new ConfigDouble(), []);

        $reflMethod = new ReflectionMethod($help, 'getAllOptions');
        $reflMethod->setAccessible(true);
        $allOptions = $reflMethod->invoke($help);
        $reflMethod->setAccessible(false);

        $this->assertGreaterThan(0, count($allOptions), 'No categories found');

        $minScreenWidth = Help::MIN_WIDTH;
        $maxArgWidth    = ($minScreenWidth / 2);

        foreach ($allOptions as $category => $options) {
            $this->assertLessThanOrEqual(
                Help::MIN_WIDTH,
                strlen($category),
                "Category name $category is longer than the minimum screen width of $minScreenWidth"
            );

            foreach ($options as $option) {
                if (isset($option['argument']) === false) {
                    continue;
                }

                $this->assertLessThanOrEqual(
                    $maxArgWidth,
                    strlen($option['argument']),
                    "Option name {$option['argument']} is longer than the half the minimum screen width of $minScreenWidth"
                );
            }
        }

    }//end testQaArgumentNamesAreWithinAcceptableBounds()


    /**
     * QA check: verify that each option only contains a spacer, text or argument + description combo.
     *
     * @coversNothing
     *
     * @return void
     */
    public function testQaValidCategoryOptionDefinitions()
    {
        $help = new Help(new ConfigDouble(), []);

        $reflMethod = new ReflectionMethod($help, 'getAllOptions');
        $reflMethod->setAccessible(true);
        $allOptions = $reflMethod->invoke($help);
        $reflMethod->setAccessible(false);

        $this->assertGreaterThan(0, count($allOptions), 'No categories found');

        foreach ($allOptions as $category => $options) {
            $this->assertGreaterThan(0, count($options), "No options found in category $category");

            foreach ($options as $name => $option) {
                if (isset($option['spacer']) === true) {
                    $this->assertStringStartsWith('blank-line', $name, 'The name for spacer items should start with "blank-line"');
                }

                $this->assertFalse(
                    isset($option['spacer'], $option['text']),
                    "Option $name: spacer and text should not be combined in one option"
                );
                $this->assertFalse(
                    isset($option['spacer'], $option['argument']),
                    "Option $name: spacer and argument should not be combined in one option"
                );
                $this->assertFalse(
                    isset($option['spacer'], $option['description']),
                    "Option $name: spacer and description should not be combined in one option"
                );
                $this->assertFalse(
                    isset($option['text'], $option['argument']),
                    "Option $name: text and argument should not be combined in one option"
                );
                $this->assertFalse(
                    isset($option['text'], $option['description']),
                    "Option $name: text and description should not be combined in one option"
                );

                if (isset($option['argument']) === true) {
                    $this->assertArrayHasKey(
                        'description',
                        $option,
                        "Option $name: an argument should always be accompanied by a description"
                    );
                }

                if (isset($option['description']) === true) {
                    $this->assertArrayHasKey(
                        'argument',
                        $option,
                        "Option $name: a description should always be accompanied by an argument"
                    );
                }
            }//end foreach
        }//end foreach

    }//end testQaValidCategoryOptionDefinitions()


    /**
     * Test receiving an expected exception when the shortOptions parameter is not passed a string value.
     *
     * @return void
     */
    public function testConstructorInvalidArgumentException()
    {
        $exception = 'InvalidArgumentException';
        $message   = 'The $shortOptions parameter must be a string';

        if (method_exists($this, 'expectException') === true) {
            // PHPUnit 5+.
            $this->expectException($exception);
            $this->expectExceptionMessage($message);
        } else {
            // PHPUnit 4.
            $this->setExpectedException($exception, $message);
        }

        new Help(new ConfigDouble(), [], []);

    }//end testConstructorInvalidArgumentException()


    /**
     * Test filtering of the options by requested options.
     *
     * Tests that:
     * - Options not explicitly requested are removed.
     * - Short options passed via the longOptions array are still respected.
     * - A category gets removed if all options are removed, even if the category still has spacers.
     *
     * @param array<string>      $longOptions  The long options which should be displayed.
     * @param string             $shortOptions The short options which should be displayed.
     * @param array<string, int> $expected     The categories expected after filtering with the number
     *                                         of expected help items per category.
     *
     * @dataProvider dataOptionFiltering
     *
     * @return void
     */
    public function testOptionFiltering($longOptions, $shortOptions, $expected)
    {
        $help = new Help(new ConfigDouble(), $longOptions, $shortOptions);

        $reflProperty = new ReflectionProperty($help, 'activeOptions');
        $reflProperty->setAccessible(true);
        $activeOptions = $reflProperty->getValue($help);
        $reflProperty->setAccessible(false);

        // Simplify the value to make it comparible.
        foreach ($activeOptions as $category => $options) {
            $activeOptions[$category] = count($options);
        }

        $this->assertSame($expected, $activeOptions, 'Option count per category does not match');

    }//end testOptionFiltering()


    /**
     * Data provider.
     *
     * @return array<string, array<string, string|array<string>|array<string, int>>>
     */
    public static function dataOptionFiltering()
    {
        $allLongOptions   = explode(',', Help::DEFAULT_LONG_OPTIONS);
        $allLongOptions[] = 'cache';
        $allLongOptions[] = 'no-cache';
        $allLongOptions[] = 'report';
        $allLongOptions[] = 'report-file';
        $allLongOptions[] = 'report-report';
        $allLongOptions[] = 'runtime-set';
        $allLongOptions[] = 'config-explain';
        $allLongOptions[] = 'config-set';
        $allLongOptions[] = 'config-delete';
        $allLongOptions[] = 'config-show';
        $allLongOptions[] = 'generator';
        $allLongOptions[] = 'suffix';

        $allShortOptions = Help::DEFAULT_SHORT_OPTIONS.'saem';

        return [
            'No options'                                      => [
                'longOptions'  => [],
                'shortOptions' => '',
                'expected'     => [],
            ],
            'Invalid options have no influence'               => [
                'longOptions'  => [
                    'doesnotexist',
                    'invalid',
                ],
                'shortOptions' => 'bjrz',
                'expected'     => [],
            ],
            'Short options passed as long options works fine' => [
                'longOptions'  => [
                    's',
                    'suffix',
                    'a',
                    'e',
                    'colors',
                ],
                'shortOptions' => '',
                'expected'     => [
                    'Rule Selection Options' => 1,
                    'Run Options'            => 2,
                    'Reporting Options'      => 2,
                ],
            ],
            'All options'                                     => [
                'longOptions'  => $allLongOptions,
                'shortOptions' => $allShortOptions,
                'expected'     => [
                    'Scan targets'           => 8,
                    'Rule Selection Options' => 7,
                    'Run Options'            => 8,
                    'Reporting Options'      => 19,
                    'Configuration Options'  => 8,
                    'Miscellaneous Options'  => 5,
                ],
            ],
            'Default options only'                            => [
                'longOptions'  => explode(',', Help::DEFAULT_LONG_OPTIONS),
                'shortOptions' => Help::DEFAULT_SHORT_OPTIONS,
                'expected'     => [
                    'Scan targets'           => 8,
                    'Rule Selection Options' => 5,
                    'Run Options'            => 4,
                    'Reporting Options'      => 14,
                    'Configuration Options'  => 4,
                    'Miscellaneous Options'  => 5,
                ],
            ],
            'Only one category'                               => [
                'longOptions'  => [
                    'file',
                    'stdin-path',
                    'file-list',
                    'filter',
                    'ignore',
                    'extensions',
                ],
                'shortOptions' => '-l',
                'expected'     => [
                    'Scan targets' => 8,
                ],
            ],
            'All except one category'                         => [
                'longOptions'  => array_diff($allLongOptions, ['version', 'vv', 'vvv']),
                'shortOptions' => str_replace(['h', 'v'], '', $allShortOptions),
                'expected'     => [
                    'Scan targets'           => 8,
                    'Rule Selection Options' => 7,
                    'Run Options'            => 8,
                    'Reporting Options'      => 19,
                    'Configuration Options'  => 8,
                ],
            ],
        ];

    }//end dataOptionFiltering()


    /**
     * Test filtering of the options by requested options does not leave stray spacers at the start
     * or end of a category and that a category does not contain two consecutive spacers.
     *
     * {@internal Careful! This test may need updates to still test what it is supposed to test
     *            if/when the defined options in Help::getAllOptions() change.}
     *
     * @param array<string> $longOptions  The long options which should be displayed.
     * @param string        $shortOptions The short options which should be displayed.
     *
     * @dataProvider dataOptionFilteringSpacerHandling
     *
     * @return void
     */
    public function testOptionFilteringSpacerHandling($longOptions, $shortOptions)
    {
        $help = new Help(new ConfigDouble(), $longOptions, $shortOptions);

        $reflProperty = new ReflectionProperty($help, 'activeOptions');
        $reflProperty->setAccessible(true);
        $activeOptions = $reflProperty->getValue($help);
        $reflProperty->setAccessible(false);

        $this->assertNotEmpty($activeOptions, 'Active options is empty, test is invalid');

        foreach ($activeOptions as $options) {
            $first = reset($options);
            $this->assertArrayNotHasKey('spacer', $first, 'Found spacer at start of category');

            $last = end($options);
            $this->assertArrayNotHasKey('spacer', $last, 'Found spacer at end of category');

            $previousWasSpacer = false;
            foreach ($options as $option) {
                $this->assertFalse((isset($option['spacer']) && $previousWasSpacer === true), 'Consecutive spacers found');
                $previousWasSpacer = isset($option['spacer']);
            }
        }

    }//end testOptionFilteringSpacerHandling()


    /**
     * Data provider.
     *
     * @return array<string, array<string, string|array<string>>>
     */
    public static function dataOptionFilteringSpacerHandling()
    {
        return [
            'No spacer at start of category'         => [
                'longOptions'  => ['generator'],
                'shortOptions' => 'ie',
            ],
            'No spacer at end of category'           => [
                'longOptions'  => [
                    'encoding',
                    'tab-width',
                ],
                'shortOptions' => '',
            ],
            'No consecutive spacers within category' => [
                'longOptions'  => [
                    'report',
                    'report-file',
                    'report-report',
                    'report-width',
                    'basepath',
                    'ignore-annotations',
                    'colors',
                    'no-colors',
                ],
                'shortOptions' => 'spqm',
            ],
        ];

    }//end dataOptionFilteringSpacerHandling()


    /**
     * Test that if no short/long options are passed, only usage information is displayed (CS mode).
     *
     * @param array<string> $cliArgs       Command line arguments.
     * @param string        $expectedRegex Regex to validate expected output.
     *
     * @dataProvider dataDisplayUsage
     *
     * @return void
     */
    public function testDisplayUsageCS($cliArgs, $expectedRegex)
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $this->markTestSkipped('This test needs CS mode to run');
        }

        $expectedRegex = str_replace('phpc(bf|s)', 'phpcs', $expectedRegex);
        $this->verifyDisplayUsage($cliArgs, $expectedRegex);

    }//end testDisplayUsageCS()


    /**
     * Test that if no short/long options are passed, only usage information is displayed (CBF mode).
     *
     * @param array<string> $cliArgs       Command line arguments.
     * @param string        $expectedRegex Regex to validate expected output.
     *
     * @dataProvider dataDisplayUsage
     * @group        CBF
     *
     * @return void
     */
    public function testDisplayUsageCBF($cliArgs, $expectedRegex)
    {
        if (PHP_CODESNIFFER_CBF === false) {
            $this->markTestSkipped('This test needs CBF mode to run');
        }

        $expectedRegex = str_replace('phpc(bf|s)', 'phpcbf', $expectedRegex);
        $this->verifyDisplayUsage($cliArgs, $expectedRegex);

    }//end testDisplayUsageCBF()


    /**
     * Helper method to test that if no short/long options are passed, only usage information is displayed
     * (and displayed correctly).
     *
     * @param array<string> $cliArgs       Command line arguments.
     * @param string        $expectedRegex Regex to validate expected output.
     *
     * @return void
     */
    private function verifyDisplayUsage($cliArgs, $expectedRegex)
    {
        $help = new Help(new ConfigDouble($cliArgs), []);

        $this->expectOutputRegex($expectedRegex);

        $help->display();

    }//end verifyDisplayUsage()


    /**
     * Data provider.
     *
     * @return array<string, array<string, string|array<string>>>
     */
    public static function dataDisplayUsage()
    {
        return [
            'Usage without colors' => [
                'cliArgs'       => ['--no-colors'],
                'expectedRegex' => '`^\s*Usage:\s+phpc(bf|s) \[options\] \<file\|directory\>\s+$`',
            ],
            'Usage with colors'    => [
                'cliArgs'       => ['--colors'],
                'expectedRegex' => '`^\s*\\033\[33mUsage:\\033\[0m\s+phpc(bf|s) \[options\] \<file\|directory\>\s+$`',
            ],
        ];

    }//end dataDisplayUsage()


    /**
     * Test the column width calculations.
     *
     * This tests the following aspects:
     * 1. That the report width is never less than Help::MIN_WIDTH, even when a smaller width is passed.
     * 2. That the first column width is calculated correctly and is based on the longest argument.
     * 3. That the word wrapping of the description respects the maximum report width.
     * 4. That if the description is being wrapped, the indent for the second line is calculated correctly.
     *
     * @param int           $reportWidth    Report width for the test.
     * @param array<string> $longOptions    The long options which should be displayed.
     * @param string        $expectedOutput Expected output.
     *
     * @dataProvider dataReportWidthCalculations
     *
     * @return void
     */
    public function testReportWidthCalculations($reportWidth, $longOptions, $expectedOutput)
    {
        $config = new ConfigDouble(["--report-width=$reportWidth", '--no-colors']);
        $help   = new Help($config, $longOptions);

        $reflMethod = new ReflectionMethod($help, 'printCategories');
        $reflMethod->setAccessible(true);
        $reflMethod->invoke($help);
        $reflMethod->setAccessible(false);

        $this->expectOutputString($expectedOutput);

    }//end testReportWidthCalculations()


    /**
     * Data provider.
     *
     * @return array<string, array<string, int|string|array<string>>>
     */
    public static function dataReportWidthCalculations()
    {
        $longOptions = [
            'e',
            'generator',
        ];

        // phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound -- Test readability is more important.
        return [
            'Report width small: 40; forces report width to minimum width of 60'                                                    => [
                'reportWidth'    => 40,
                'longOptions'    => $longOptions,
                'expectedOutput' => PHP_EOL.'Rule Selection Options:'.PHP_EOL
                    .'  -e                      Explain a standard by showing the'.PHP_EOL
                    .'                          names of all the sniffs it'.PHP_EOL
                    .'                          includes.'.PHP_EOL
                    .'  --generator=<generator> Show documentation for a standard.'.PHP_EOL
                    .'                          Use either the "HTML", "Markdown"'.PHP_EOL
                    .'                          or "Text" generator.'.PHP_EOL,
            ],
            'Report width is minimum: 60 (= self::MIN_WIDTH)'                                                                       => [
                'reportWidth'    => Help::MIN_WIDTH,
                'longOptions'    => $longOptions,
                'expectedOutput' => PHP_EOL.'Rule Selection Options:'.PHP_EOL
                    .'  -e                      Explain a standard by showing the'.PHP_EOL
                    .'                          names of all the sniffs it'.PHP_EOL
                    .'                          includes.'.PHP_EOL
                    .'  --generator=<generator> Show documentation for a standard.'.PHP_EOL
                    .'                          Use either the "HTML", "Markdown"'.PHP_EOL
                    .'                          or "Text" generator.'.PHP_EOL,
            ],
            'Report width matches length for one line, not the other: 96; only one should wrap'                                     => [
                'reportWidth'    => 96,
                'longOptions'    => $longOptions,
                'expectedOutput' => PHP_EOL.'Rule Selection Options:'.PHP_EOL
                    .'  -e                      Explain a standard by showing the names of all the sniffs it includes.'.PHP_EOL
                    .'  --generator=<generator> Show documentation for a standard. Use either the "HTML", "Markdown"'.PHP_EOL
                    .'                          or "Text" generator.'.PHP_EOL,
            ],
            'Report width matches longest line: 119; the messages should not wrap and there should be no stray new line at the end' => [
                'reportWidth'    => 119,
                'longOptions'    => $longOptions,
                'expectedOutput' => PHP_EOL.'Rule Selection Options:'.PHP_EOL
                    .'  -e                      Explain a standard by showing the names of all the sniffs it includes.'.PHP_EOL
                    .'  --generator=<generator> Show documentation for a standard. Use either the "HTML", "Markdown" or "Text" generator.'.PHP_EOL,
            ],
        ];
        // phpcs:enable

    }//end dataReportWidthCalculations()


    /**
     * Verify that variable elements in an argument specification get colorized correctly.
     *
     * @param string $input    String to colorize.
     * @param string $expected Expected function output.
     *
     * @dataProvider dataColorizeVariableInput
     *
     * @return void
     */
    public function testColorizeVariableInput($input, $expected)
    {
        $help = new Help(new ConfigDouble(), []);

        $reflMethod = new ReflectionMethod($help, 'colorizeVariableInput');
        $reflMethod->setAccessible(true);
        $result = $reflMethod->invoke($help, $input);
        $reflMethod->setAccessible(false);

        $this->assertSame($expected, $result);

    }//end testColorizeVariableInput()


    /**
     * Data provider.
     *
     * @return array<string, array<string, string|array<string>>>
     */
    public static function dataColorizeVariableInput()
    {
        return [
            'Empty string'                                     => [
                'input'    => '',
                'expected' => '',
            ],
            'String without variable element(s)'               => [
                'input'    => 'This is text',
                'expected' => 'This is text',
            ],
            'String with variable element'                     => [
                'input'    => 'This <is> text',
                'expected' => "This \033[36m<is>\033[32m text",
            ],
            'String with multiple variable elements'           => [
                'input'    => '<This> is <text>',
                'expected' => "\033[36m<This>\033[32m is \033[36m<text>\033[32m",
            ],
            'String with unclosed variable element'            => [
                'input'    => 'This <is text',
                'expected' => 'This <is text',
            ],
            'String with nested elements'                      => [
                'input'    => '<This <is> text>',
                'expected' => "\033[36m<This <is> text>\033[32m",
            ],
            'String with nested elements and surrounding text' => [
                'input'    => 'Start <This <is> text> end',
                'expected' => "Start \033[36m<This <is> text>\033[32m end",
            ],
        ];

    }//end dataColorizeVariableInput()


    /**
     * Test the various option types within a category get displayed correctly.
     *
     * @param array<string, array<string, string>> $input         The options to print.
     * @param array<string, string>                $expectedRegex Regexes to validate expected output.
     *
     * @dataProvider dataPrintCategoryOptions
     *
     * @return void
     */
    public function testPrintCategoryOptionsNoColor($input, $expectedRegex)
    {
        $config = new ConfigDouble(['--no-colors']);
        $help   = new Help($config, []);

        $reflProperty = new ReflectionProperty($help, 'activeOptions');
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($help, ['cat' => $input]);
        $reflProperty->setAccessible(false);

        $reflMethod = new ReflectionMethod($help, 'setMaxOptionNameLength');
        $reflMethod->setAccessible(true);
        $reflMethod->invoke($help);
        $reflMethod->setAccessible(false);

        $reflMethod = new ReflectionMethod($help, 'printCategoryOptions');
        $reflMethod->setAccessible(true);
        $reflMethod->invoke($help, $input);
        $reflMethod->setAccessible(false);

        $this->expectOutputRegex($expectedRegex['no-color']);

    }//end testPrintCategoryOptionsNoColor()


    /**
     * Test the various option types within a category get displayed correctly.
     *
     * @param array<string, array<string, string>> $input         The options to print.
     * @param array<string, string>                $expectedRegex Regexes to validate expected output.
     *
     * @dataProvider dataPrintCategoryOptions
     *
     * @return void
     */
    public function testPrintCategoryOptionsColor($input, $expectedRegex)
    {
        $config = new ConfigDouble(['--colors']);
        $help   = new Help($config, []);

        $reflProperty = new ReflectionProperty($help, 'activeOptions');
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($help, ['cat' => $input]);
        $reflProperty->setAccessible(false);

        $reflMethod = new ReflectionMethod($help, 'setMaxOptionNameLength');
        $reflMethod->setAccessible(true);
        $reflMethod->invoke($help);
        $reflMethod->setAccessible(false);

        $reflMethod = new ReflectionMethod($help, 'printCategoryOptions');
        $reflMethod->setAccessible(true);
        $reflMethod->invoke($help, $input);
        $reflMethod->setAccessible(false);

        $this->expectOutputRegex($expectedRegex['color']);

    }//end testPrintCategoryOptionsColor()


    /**
     * Data provider.
     *
     * @return array<string, array<string, array<string, array<string, string>>|array<string, string>>>
     */
    public static function dataPrintCategoryOptions()
    {
        $indentLength = strlen(Help::INDENT);
        $gutterLength = strlen(Help::GUTTER);

        // phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound -- Test readability is more important.
        // phpcs:disable Generic.Strings.UnnecessaryStringConcat.Found -- Test readability is more important.
        return [
            'Input: arg, spacer, arg; new lines in description get preserved' => [
                'input'         => [
                    'short-option'                       => [
                        'argument'    => '-a',
                        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    ],
                    'blank-line'                         => [
                        'spacer' => '',
                    ],
                    'long-option-multi-line-description' => [
                        'argument'    => '--something=<var>',
                        'description' => 'Proin sit amet malesuada libero, finibus bibendum tortor. Nulla vitae quam nec orci finibus pharetra.'
                            ."\n".'Nam eget blandit dui.',
                    ],
                ],
                'expectedRegex' => [
                    'no-color' => '`^ {'.$indentLength.'}-a {15} {'.$gutterLength.'}Lorem ipsum dolor sit amet, consectetur adipiscing elit\.\R'
                        .'\R'
                        .' {'.$indentLength.'}--something=<var> {'.$gutterLength.'}Proin sit amet malesuada libero, finibus bibendum tortor\.\R'
                        .' {'.($indentLength + 17).'} {'.$gutterLength.'}Nulla vitae quam nec orci finibus pharetra\.\R'
                        .' {'.($indentLength + 17).'} {'.$gutterLength.'}Nam eget blandit dui\.\R$`',
                    'color'    => '`^ {'.$indentLength.'}\\033\[32m-a {15}\\033\[0m {'.$gutterLength.'}Lorem ipsum dolor sit amet, consectetur adipiscing elit\.\R'
                        .'\R'
                        .' {'.$indentLength.'}\\033\[32m--something=\\033\[36m<var>\\033\[32m\\033\[0m {'.$gutterLength.'}Proin sit amet malesuada libero, finibus bibendum tortor\.\R'
                        .' {'.($indentLength + 17).'} {'.$gutterLength.'}Nulla vitae quam nec orci finibus pharetra\.\R'
                        .' {'.($indentLength + 17).'} {'.$gutterLength.'}Nam eget blandit dui\.\R$`',
                ],
            ],
            'Input: text, arg, text; multi-line text gets wrapped'            => [
                'input'         => [
                    'single-line-text'             => [
                        'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    ],
                    'argument-description'         => [
                        'argument'    => '--something',
                        'description' => 'Fusce dapibus sodales est eu sodales.',
                    ],
                    'multi-line-text-gets-wrapped' => [
                        'text' => 'Maecenas vulputate ligula vel feugiat finibus. Mauris sem dui, pretium in turpis auctor, consectetur ultrices lorem.',
                    ],
                ],
                'expectedRegex' => [
                    'no-color' => '`^ {'.$indentLength.'}Lorem ipsum dolor sit amet, consectetur adipiscing elit\.\R'
                        .' {'.$indentLength.'}--something {'.$gutterLength.'}Fusce dapibus sodales est eu sodales\.\R'
                        .' {'.$indentLength.'}Maecenas vulputate ligula vel feugiat finibus. Mauris sem dui, pretium in\R'
                        .' {'.$indentLength.'}turpis auctor, consectetur ultrices lorem\.\R$`',
                    'color'    => '`^ {'.$indentLength.'}Lorem ipsum dolor sit amet, consectetur adipiscing elit\.\R'
                        .' {'.$indentLength.'}\\033\[32m--something\\033\[0m {'.$gutterLength.'}Fusce dapibus sodales est eu sodales\.\R'
                        .' {'.$indentLength.'}Maecenas vulputate ligula vel feugiat finibus. Mauris sem dui, pretium in\R'
                        .' {'.$indentLength.'}turpis auctor, consectetur ultrices lorem\.\R$`',
                ],
            ],
        ];
        // phpcs:enable

    }//end dataPrintCategoryOptions()


}//end class
