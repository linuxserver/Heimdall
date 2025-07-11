<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Common::prepareForOutput() method.
 *
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Common;

use PHP_CodeSniffer\Util\Common;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHP_CodeSniffer\Util\Common::prepareForOutput() method.
 *
 * @covers \PHP_CodeSniffer\Util\Common::prepareForOutput
 * @group  Windows
 */
final class PrepareForOutputTest extends TestCase
{


    /**
     * Test formatting whitespace characters, on anything other than Windows.
     *
     * @param string   $content     The content to prepare.
     * @param string[] $exclude     A list of characters to leave invisible.
     * @param string   $expected    Expected function output.
     * @param string   $expectedWin Expected function output on Windows (unused in this test).
     *
     * @requires     OS ^(?!WIN).*
     * @dataProvider dataPrepareForOutput
     *
     * @return void
     */
    public function testPrepareForOutput($content, $exclude, $expected, $expectedWin)
    {
        $this->assertSame($expected, Common::prepareForOutput($content, $exclude));

    }//end testPrepareForOutput()


    /**
     * Test formatting whitespace characters, on Windows.
     *
     * @param string   $content     The content to prepare.
     * @param string[] $exclude     A list of characters to leave invisible.
     * @param string   $expected    Expected function output (unused in this test).
     * @param string   $expectedWin Expected function output on Windows.
     *
     * @requires     OS ^WIN.*.
     * @dataProvider dataPrepareForOutput
     *
     * @return void
     */
    public function testPrepareForOutputWindows($content, $exclude, $expected, $expectedWin)
    {
        $this->assertSame($expectedWin, Common::prepareForOutput($content, $exclude));

    }//end testPrepareForOutputWindows()


    /**
     * Data provider.
     *
     * @see testPrepareForOutput()
     * @see testPrepareForOutputWindows()
     *
     * @return array<string, array<string, mixed>>
     */
    public static function dataPrepareForOutput()
    {
        return [
            'Special characters are replaced with their escapes' => [
                'content'     => "\r\n\t",
                'exclude'     => [],
                'expected'    => "\033[30;1m\\r\033[0m\033[30;1m\\n\033[0m\033[30;1m\\t\033[0m",
                'expectedWin' => "\\r\\n\\t",
            ],
            'Spaces are replaced with a unique mark'             => [
                'content'     => "    ",
                'exclude'     => [],
                'expected'    => "\033[30;1m·\033[0m\033[30;1m·\033[0m\033[30;1m·\033[0m\033[30;1m·\033[0m",
                'expectedWin' => "    ",
            ],
            'Other characters are unaffected'                    => [
                'content'     => "{echo 1;}",
                'exclude'     => [],
                'expected'    => "{echo\033[30;1m·\033[0m1;}",
                'expectedWin' => "{echo 1;}",
            ],
            'No replacements'                                    => [
                'content'     => 'nothing-should-be-replaced',
                'exclude'     => [],
                'expected'    => 'nothing-should-be-replaced',
                'expectedWin' => 'nothing-should-be-replaced',
            ],
            'Excluded whitespace characters are unaffected'      => [
                'content'     => "\r\n\t ",
                'exclude'     => [
                    "\r",
                    "\n",
                ],
                'expected'    => "\r\n\033[30;1m\\t\033[0m\033[30;1m·\033[0m",
                'expectedWin' => "\r\n\\t ",
            ],
        ];

    }//end dataPrepareForOutput()


}//end class
