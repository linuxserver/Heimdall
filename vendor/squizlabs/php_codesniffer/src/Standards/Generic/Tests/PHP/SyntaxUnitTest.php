<?php
/**
 * Unit test class for the Syntax sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Blaine Schmeisser <blainesch@gmail.com>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/HEAD/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\PHP;

use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the Syntax sniff.
 *
 * @covers \PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\SyntaxSniff
 */
final class SyntaxUnitTest extends AbstractSniffUnitTest
{


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @param string $testFile The name of the file being tested.
     *
     * @return array<int, int>
     */
    public function getErrorList($testFile='')
    {
        switch ($testFile) {
        case 'SyntaxUnitTest.1.inc':
        case 'SyntaxUnitTest.2.inc':
            return [3 => 1];

        default:
            return [];
        }

    }//end getErrorList()


    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array<int, int>
     */
    public function getWarningList()
    {
        return [];

    }//end getWarningList()


    /**
     * Test the sniff checks syntax when file contents are passed via STDIN.
     *
     * Note: this test doesn't run on Windows as PHPCS currently doesn't support STDIN on this OS.
     *
     * @param string $content        The content to test.
     * @param int    $errorCount     The expected number of errors.
     * @param array  $expectedErrors The expected errors.
     *
     * @dataProvider dataStdIn
     * @requires     OS ^(?!WIN).*
     *
     * @return void
     */
    public function testStdIn($content, $errorCount, $expectedErrors)
    {
        $config            = new ConfigDouble();
        $config->standards = ['Generic'];
        $config->sniffs    = ['Generic.PHP.Syntax'];

        $ruleset = new Ruleset($config);

        $file = new DummyFile($content, $ruleset, $config);
        $file->process();

        $this->assertSame(
            $errorCount,
            $file->getErrorCount(),
            'Error count does not match expected value'
        );
        $this->assertSame(
            0,
            $file->getWarningCount(),
            'Warning count does not match expected value'
        );
        $this->assertSame(
            $expectedErrors,
            $file->getErrors(),
            'Error list does not match expected errors'
        );

    }//end testStdIn()


    /**
     * Data provider for testStdIn().
     *
     * @return array<string, array<string|int|array>>
     */
    public function dataStdIn()
    {
        // The error message changed in PHP 8+.
        if (PHP_VERSION_ID >= 80000) {
            $errorMessage = 'PHP syntax error: syntax error, unexpected token ";", expecting "]"';
        } else {
            $errorMessage = 'PHP syntax error: syntax error, unexpected \';\', expecting \']\'';
        }

        return [
            'No syntax errors'                                                                => [
                '<?php $array = [1, 2, 3];',
                0,
                [],
            ],
            'One syntax error'                                                                => [
                '<?php $array = [1, 2, 3; // Missing closing bracket.',
                1,
                [
                    1 => [
                        1 => [
                            0 => [
                                'message'  => $errorMessage,
                                'source'   => 'Generic.PHP.Syntax.PHPSyntax',
                                'listener' => 'PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\PHP\\SyntaxSniff',
                                'severity' => 5,
                                'fixable'  => false,
                            ],
                        ],
                    ],
                ],
            ],
            'Single error reported even when there is more than one syntax error in the file' => [
                '<?php $array = [1, 2, 3; // Missing closing bracket.
                $anotherArray = [4, 5, 6; // Another missing closing bracket.',
                1,
                [
                    1 => [
                        1 => [
                            0 => [
                                'message'  => $errorMessage,
                                'source'   => 'Generic.PHP.Syntax.PHPSyntax',
                                'listener' => 'PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\PHP\\SyntaxSniff',
                                'severity' => 5,
                                'fixable'  => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];

    }//end dataStdIn()


}//end class
