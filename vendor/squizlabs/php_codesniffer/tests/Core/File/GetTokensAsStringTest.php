<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File::getTokensAsString method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2022-2024 PHPCSStandards Contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\File;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

/**
 * Tests for the \PHP_CodeSniffer\Files\File:getTokensAsString method.
 *
 * @covers \PHP_CodeSniffer\Files\File::getTokensAsString
 */
final class GetTokensAsStringTest extends AbstractMethodUnitTest
{


    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectRunTimeException('The $start position for getTokensAsString() must exist in the token stack');

        self::$phpcsFile->getTokensAsString(100000, 10);

    }//end testNonExistentToken()


    /**
     * Test passing a non integer `$start`, like the result of a failed $phpcsFile->findNext().
     *
     * @return void
     */
    public function testNonIntegerStart()
    {
        $this->expectRunTimeException('The $start position for getTokensAsString() must exist in the token stack');

        self::$phpcsFile->getTokensAsString(false, 10);

    }//end testNonIntegerStart()


    /**
     * Test passing a non integer `$length`.
     *
     * @return void
     */
    public function testNonIntegerLength()
    {
        $result = self::$phpcsFile->getTokensAsString(10, false);
        $this->assertSame('', $result);

        $result = self::$phpcsFile->getTokensAsString(10, 1.5);
        $this->assertSame('', $result);

    }//end testNonIntegerLength()


    /**
     * Test passing a zero or negative `$length`.
     *
     * @return void
     */
    public function testLengthEqualToOrLessThanZero()
    {
        $result = self::$phpcsFile->getTokensAsString(10, -10);
        $this->assertSame('', $result);

        $result = self::$phpcsFile->getTokensAsString(10, 0);
        $this->assertSame('', $result);

    }//end testLengthEqualToOrLessThanZero()


    /**
     * Test passing a `$length` beyond the end of the file.
     *
     * @return void
     */
    public function testLengthBeyondEndOfFile()
    {
        $semicolon = $this->getTargetToken('/* testEndOfFile */', T_SEMICOLON);
        $result    = self::$phpcsFile->getTokensAsString($semicolon, 20);
        $this->assertSame(
            ';
',
            $result
        );

    }//end testLengthBeyondEndOfFile()


    /**
     * Test getting a token set as a string.
     *
     * @param string     $testMarker     The comment which prefaces the target token in the test file.
     * @param int|string $startTokenType The type of token(s) to look for for the start of the string.
     * @param int        $length         Token length to get.
     * @param string     $expected       The expected function return value.
     *
     * @dataProvider dataGetTokensAsString()
     *
     * @return void
     */
    public function testGetTokensAsString($testMarker, $startTokenType, $length, $expected)
    {
        $start  = $this->getTargetToken($testMarker, $startTokenType);
        $result = self::$phpcsFile->getTokensAsString($start, $length);
        $this->assertSame($expected, $result);

    }//end testGetTokensAsString()


    /**
     * Data provider.
     *
     * @see testGetTokensAsString() For the array format.
     *
     * @return array<string, array<string, string|int>>
     */
    public static function dataGetTokensAsString()
    {
        return [
            'length-0'          => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => T_LNUMBER,
                'length'         => 0,
                'expected'       => '',
            ],
            'length-1'          => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => T_LNUMBER,
                'length'         => 1,
                'expected'       => '1',
            ],
            'length-2'          => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => T_LNUMBER,
                'length'         => 2,
                'expected'       => '1 ',
            ],
            'length-3'          => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => T_LNUMBER,
                'length'         => 3,
                'expected'       => '1 +',
            ],
            'length-4'          => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => T_LNUMBER,
                'length'         => 4,
                'expected'       => '1 + ',
            ],
            'length-5'          => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => T_LNUMBER,
                'length'         => 5,
                'expected'       => '1 + 2',
            ],
            'length-6'          => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => T_LNUMBER,
                'length'         => 6,
                'expected'       => '1 + 2 ',
            ],
            'length-7'          => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => T_LNUMBER,
                'length'         => 7,
                'expected'       => '1 + 2 +',
            ],
            'length-8'          => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => T_LNUMBER,
                'length'         => 8,
                'expected'       => '1 + 2 +
',
            ],
            'length-9'          => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => T_LNUMBER,
                'length'         => 9,
                'expected'       => '1 + 2 +
        ',
            ],
            'length-10'         => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => T_LNUMBER,
                'length'         => 10,
                'expected'       => '1 + 2 +
        // Comment.
',
            ],
            'length-11'         => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => T_LNUMBER,
                'length'         => 11,
                'expected'       => '1 + 2 +
        // Comment.
        ',
            ],
            'length-12'         => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => T_LNUMBER,
                'length'         => 12,
                'expected'       => '1 + 2 +
        // Comment.
        3',
            ],
            'length-13'         => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => T_LNUMBER,
                'length'         => 13,
                'expected'       => '1 + 2 +
        // Comment.
        3 ',
            ],
            'length-14'         => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => T_LNUMBER,
                'length'         => 14,
                'expected'       => '1 + 2 +
        // Comment.
        3 +',
            ],
            'length-34'         => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => T_LNUMBER,
                'length'         => 34,
                'expected'       => '1 + 2 +
        // Comment.
        3 + 4
        + 5 + 6 + 7 > 20;',
            ],
            'namespace'         => [
                'testMarker'     => '/* testNamespace */',
                'startTokenType' => T_NAMESPACE,
                'length'         => 8,
                'expected'       => 'namespace Foo\Bar\Baz;',
            ],
            'use-with-comments' => [
                'testMarker'     => '/* testUseWithComments */',
                'startTokenType' => T_USE,
                'length'         => 17,
                'expected'       => 'use Foo /*comment*/ \ Bar
    // phpcs:ignore Stnd.Cat.Sniff --    For reasons.
    \ Bah;',
            ],
            'echo-with-tabs'    => [
                'testMarker'     => '/* testEchoWithTabs */',
                'startTokenType' => T_ECHO,
                'length'         => 13,
                'expected'       => 'echo \'foo\',
    \'bar\'   ,
        \'baz\';',
            ],
            'end-of-file'       => [
                'testMarker'     => '/* testEndOfFile */',
                'startTokenType' => T_ECHO,
                'length'         => 4,
                'expected'       => 'echo   $foo;',
            ],
        ];

    }//end dataGetTokensAsString()


    /**
     * Test getting a token set as a string with the original, non tab-replaced content.
     *
     * @param string     $testMarker     The comment which prefaces the target token in the test file.
     * @param int|string $startTokenType The type of token(s) to look for for the start of the string.
     * @param int        $length         Token length to get.
     * @param string     $expected       The expected function return value.
     *
     * @dataProvider dataGetOrigContent()
     *
     * @return void
     */
    public function testGetOrigContent($testMarker, $startTokenType, $length, $expected)
    {
        $start  = $this->getTargetToken($testMarker, $startTokenType);
        $result = self::$phpcsFile->getTokensAsString($start, $length, true);
        $this->assertSame($expected, $result);

    }//end testGetOrigContent()


    /**
     * Data provider.
     *
     * @see testGetOrigContent() For the array format.
     *
     * @return array<string, array<string, string|int>>
     */
    public static function dataGetOrigContent()
    {
        return [
            'use-with-comments' => [
                'testMarker'     => '/* testUseWithComments */',
                'startTokenType' => T_USE,
                'length'         => 17,
                'expected'       => 'use Foo /*comment*/ \ Bar
	// phpcs:ignore Stnd.Cat.Sniff --	 For reasons.
	\ Bah;',
            ],
            'echo-with-tabs'    => [
                'testMarker'     => '/* testEchoWithTabs */',
                'startTokenType' => T_ECHO,
                'length'         => 13,
                'expected'       => 'echo \'foo\',
	\'bar\'	,
		\'baz\';',
            ],
            'end-of-file'       => [
                'testMarker'     => '/* testEndOfFile */',
                'startTokenType' => T_ECHO,
                'length'         => 4,
                'expected'       => 'echo   $foo;',
            ],
        ];

    }//end dataGetOrigContent()


}//end class
