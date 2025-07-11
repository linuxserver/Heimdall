<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Common::stripColors() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Common;

use PHP_CodeSniffer\Util\Common;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHP_CodeSniffer\Util\Common::stripColors() method.
 *
 * @covers \PHP_CodeSniffer\Util\Common::stripColors
 */
final class StripColorsTest extends TestCase
{


    /**
     * Test stripping color codes from a text.
     *
     * @param string $text     The text provided.
     * @param string $expected Expected function output.
     *
     * @dataProvider dataStripColors
     *
     * @return void
     */
    public function testStripColors($text, $expected)
    {
        $this->assertSame($expected, Common::stripColors($text));

    }//end testStripColors()


    /**
     * Data provider.
     *
     * @see testStripColors()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataStripColors()
    {
        return [
            'Text is empty'                                                      => [
                'text'     => '',
                'expected' => '',
            ],
            'Text enclosed in color code'                                        => [
                'text'     => "\033[36mSome text\033[0m",
                'expected' => 'Some text',
            ],
            'Text containing color code'                                         => [
                'text'     => "Some text \033[33mSome other text",
                'expected' => 'Some text Some other text',
            ],
            'Text enclosed in color code, bold'                                  => [
                'text'     => "\033[1;32mSome text\033[0m",
                'expected' => 'Some text',
            ],
            'Text enclosed in color code, with escaped text'                     => [
                'text'     => "\033[30;1m\\n\033[0m",
                'expected' => '\n',
            ],
            'Text enclosed in color code, bold, dark, italic'                    => [
                'text'     => "\033[1;2;3mtext\033[0m",
                'expected' => 'text',
            ],
            'Text enclosed in color code, foreground color'                      => [
                'text'     => "\033[38;5;255mtext\033[0m",
                'expected' => 'text',
            ],
            'Text enclosed in color code, foreground color and background color' => [
                'text'     => "\033[38;5;200;48;5;255mtext\033[0m",
                'expected' => 'text',
            ],
            'Multiline text containing multiple color codes'                     => [
                'text'     => "First \033[36mSecond\033[0m
Third \033[1;2;3mFourth
Next line\033[0m Last",
                'expected' => 'First Second
Third Fourth
Next line Last',
            ],
        ];

    }//end dataStripColors()


}//end class
