<?php
/**
 * Tests progress reporting in the Runner class.
 *
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Runner;

use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Runner;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHPUnit\Framework\TestCase;

/**
 * Tests progress reporting.
 *
 * @covers \PHP_CodeSniffer\Runner::printProgress
 */
final class PrintProgressDotsTest extends TestCase
{


    /**
     * Verify the correct progress indicator is used for a file in CS mode.
     *
     * @param bool   $colors   Whether to enable colors or not.
     * @param string $code     Code snippet to process.
     * @param string $sniffs   Comma-separated list of sniff(s) to run against the code snippet.
     * @param string $expected Expected output of the progress printer.
     *
     * @dataProvider dataProgressDotCs
     *
     * @return void
     */
    public function testProgressDotCs($colors, $code, $sniffs, $expected)
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $this->markTestSkipped('This test needs CS mode to run');
        }

        $this->checkProgressDot($colors, $code, $sniffs, $expected);

    }//end testProgressDotCs()


    /**
     * Data provider.
     *
     * @return array<string, array<string, bool|string>>
     */
    public static function dataProgressDotCs()
    {
        return [
            'No colors: Dot: no errors, no warnings' => [
                'colors'   => false,
                'code'     => '<?php'."\n".'$var = false;'."\n",
                'sniffs'   => 'Generic.PHP.LowerCaseConstant',
                'expected' => '.',
            ],
            'No colors: E: has error'                => [
                'colors'   => false,
                'code'     => '<?php'."\n".'if ($a && $b || $c) {}'."\n",
                'sniffs'   => 'Generic.CodeAnalysis.RequireExplicitBooleanOperatorPrecedence',
                'expected' => 'E',
            ],
            'No colors: W: has warning'              => [
                'colors'   => false,
                'code'     => '<?php'."\n".'// TODO: something'."\n",
                'sniffs'   => 'Generic.Commenting.Todo',
                'expected' => 'W',
            ],

            'Colors: Dot: no errors, no warnings'    => [
                'colors'   => true,
                'code'     => '<?php'."\n".'$var = false;'."\n",
                'sniffs'   => 'Generic.PHP.LowerCaseConstant',
                'expected' => '.',
            ],
            'Colors: E: has error (red)'             => [
                'colors'   => true,
                'code'     => '<?php'."\n".'if ($a && $b || $c) {}'."\n",
                'sniffs'   => 'Generic.CodeAnalysis.RequireExplicitBooleanOperatorPrecedence',
                'expected' => "\033[31m".'E'."\033[0m",
            ],
            'Colors: E: has fixable error (green)'   => [
                'colors'   => true,
                'code'     => '<?php'."\n".'$a = array();'."\n",
                'sniffs'   => 'Generic.Arrays.DisallowLongArraySyntax',
                'expected' => "\033[32m".'E'."\033[0m",
            ],
            'Colors: W: has warning (yellow)'        => [
                'colors'   => true,
                'code'     => '<?php'."\n".'// TODO: something'."\n",
                'sniffs'   => 'Generic.Commenting.Todo',
                'expected' => "\033[33m".'W'."\033[0m",
            ],
            'Colors: W: has fixable warning (green)' => [
                'colors'   => true,
                'code'     => '<?php'."\n".'echo \'hello\';;'."\n",
                'sniffs'   => 'Generic.CodeAnalysis.EmptyPHPStatement',
                'expected' => "\033[32m".'W'."\033[0m",
            ],
        ];

    }//end dataProgressDotCs()


    /**
     * Verify the correct progress indicator is used for a file in CBF mode.
     *
     * @param bool   $colors   Whether to enable colors or not.
     * @param string $code     Code snippet to process.
     * @param string $sniffs   Comma-separated list of sniff(s) to run against the code snippet.
     * @param string $expected Expected output of the progress printer.
     *
     * @dataProvider dataProgressDotCbf
     *
     * @group CBF
     *
     * @return void
     */
    public function testProgressDotCbf($colors, $code, $sniffs, $expected)
    {
        if (PHP_CODESNIFFER_CBF === false) {
            $this->markTestSkipped('This test needs CBF mode to run');
        }

        $this->checkProgressDot($colors, $code, $sniffs, $expected, true);

    }//end testProgressDotCbf()


    /**
     * Data provider.
     *
     * @return array<string, array<string, bool|string>>
     */
    public static function dataProgressDotCbf()
    {
        return [
            'No colors: Dot: no errors, no warnings'         => [
                'colors'   => false,
                'code'     => '<?php'."\n".'$var = false;'."\n",
                'sniffs'   => 'Generic.PHP.LowerCaseConstant',
                'expected' => '.',
            ],
            'No colors: F: fixes made'                       => [
                'colors'   => false,
                'code'     => '<?php'."\n".'$a = array();'."\n",
                'sniffs'   => 'Generic.Arrays.DisallowLongArraySyntax',
                'expected' => 'F',
            ],
            'No colors: E: has fixer conflict'               => [
                'colors'   => false,
                'code'     => '<?php'."\n".'$a = array();'."\n",
                'sniffs'   => 'Generic.Arrays.DisallowLongArraySyntax,Generic.Arrays.DisallowShortArraySyntax',
                'expected' => 'E',
            ],

            'Colors: Dot: no errors, no warnings (no color)' => [
                'colors'   => true,
                'code'     => '<?php'."\n".'$var = false;'."\n",
                'sniffs'   => 'Generic.PHP.LowerCaseConstant',
                'expected' => '.',
            ],
            'Colors: F: fixes made (green)'                  => [
                'colors'   => true,
                'code'     => '<?php'."\n".'$a = array();'."\n",
                'sniffs'   => 'Generic.Arrays.DisallowLongArraySyntax',
                'expected' => "\033[32m".'F'."\033[0m",
            ],
            'Colors: E: has fixer conflict (red)'            => [
                'colors'   => true,
                'code'     => '<?php'."\n".'$a = array();'."\n",
                'sniffs'   => 'Generic.Arrays.DisallowLongArraySyntax,Generic.Arrays.DisallowShortArraySyntax',
                'expected' => "\033[31m".'E'."\033[0m",
            ],
        ];

    }//end dataProgressDotCbf()


    /**
     * Verify the correct progress indicator is used for a file in CBF mode.
     *
     * @param bool   $colors      Whether to enable colors or not.
     * @param string $code        Code snippet to process.
     * @param string $sniffs      Comma-separated list of sniff(s) to run against the code snippet.
     * @param string $expected    Expected output of the progress printer.
     * @param bool   $enableFixer Whether to fix the code or not.
     *
     * @return void
     */
    private function checkProgressDot($colors, $code, $sniffs, $expected, $enableFixer=false)
    {
        $this->expectOutputString($expected);

        $config            = new ConfigDouble(['-p']);
        $config->colors    = $colors;
        $config->standards = ['Generic'];
        $config->sniffs    = explode(',', $sniffs);
        $ruleset           = new Ruleset($config);

        $runner         = new Runner();
        $runner->config = $config;

        $file = new DummyFile($code, $ruleset, $config);
        $file->process();

        if ($enableFixer === true) {
            $file->fixer->fixFile();
        }

        $runner->printProgress($file, 2, 1);

    }//end checkProgressDot()


}//end class
