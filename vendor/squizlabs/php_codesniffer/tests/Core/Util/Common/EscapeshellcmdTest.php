<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Common::escapeshellcmd() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Common;

use PHP_CodeSniffer\Util\Common;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHP_CodeSniffer\Util\Common::escapeshellcmd() method.
 *
 * @covers \PHP_CodeSniffer\Util\Common::escapeshellcmd
 * @group  Windows
 */
final class EscapeshellcmdTest extends TestCase
{


    /**
     * Test escaping shell commands.
     *
     * @param string $command     The command provided.
     * @param string $expected    Expected function output.
     * @param string $expectedWin Optional. Expected function output on Windows.
     *                            Only needs to be passed if the output on Windows would be different.
     *
     * @dataProvider dataEscapeshellcmd
     *
     * @return void
     */
    public function testEscapeshellcmd($command, $expected, $expectedWin=null)
    {
        if (stripos(PHP_OS, 'WIN') === 0 && empty($expectedWin) === false) {
            $expected = $expectedWin;
        }

        $this->assertSame($expected, Common::escapeshellcmd($command));

    }//end testEscapeshellcmd()


    /**
     * Data provider.
     *
     * Note: we're only testing the PHPCS functionality, not the PHP native `escapeshellcmd()`
     * function (as that's not our responsibility).
     *
     * @see testEscapeshellcmd()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataEscapeshellcmd()
    {
        return [
            'Command is empty string'                                        => [
                'command'  => '',
                'expected' => '',
            ],
            'Command is simple string'                                       => [
                'command'  => 'csslint',
                'expected' => 'csslint',
            ],
            'Command containing characters which PHP escapes'                => [
                'command'     => '&#;`|*?~<>^()[]{}$\,%!',
                'expected'    => '\&\#\;\`\|\*\?\~\<\>\^\(\)\[\]\{\}\$\\\\,%!',
                'expectedWin' => '^&^#^;^`^|^*^?^~^<^>^^^(^)^[^]^{^}^$^\,^%^!',
            ],
            // @link https://github.com/squizlabs/PHP_CodeSniffer/pull/3214
            'Command containing spaces, which can cause problems on Windows' => [
                'command'     => 'C:\Program Files\nodejs\csslint.cmd',
                'expected'    => 'C:\\\\Program Files\\\\nodejs\\\\csslint.cmd',
                'expectedWin' => 'C:^\Program^ Files^\nodejs^\csslint.cmd',
            ],
            // @link https://github.com/php/doc-en/pull/511
            'Command containing spaces with additional arguments'            => [
                'command'     => 'php -f ./~home/path to/file.php',
                'expected'    => 'php -f ./\~home/path to/file.php',
                'expectedWin' => 'php^ -f^ ./^~home/path^ to/file.php',
            ],
        ];

    }//end dataEscapeshellcmd()


}//end class
