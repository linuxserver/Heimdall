<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File:findStartOfStatement method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @copyright 2019-2024 PHPCSStandards Contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\File;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

/**
 * Tests for the \PHP_CodeSniffer\Files\File:findStartOfStatement method.
 *
 * @covers \PHP_CodeSniffer\Files\File::findStartOfStatement
 */
final class FindStartOfStatementTest extends AbstractMethodUnitTest
{


    /**
     * Test a simple assignment.
     *
     * @return void
     */
    public function testSimpleAssignment()
    {
        $start = $this->getTargetToken('/* testSimpleAssignment */', T_SEMICOLON);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 5), $found);

    }//end testSimpleAssignment()


    /**
     * Test a function call.
     *
     * @return void
     */
    public function testFunctionCall()
    {
        $start = $this->getTargetToken('/* testFunctionCall */', T_CLOSE_PARENTHESIS);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 6), $found);

    }//end testFunctionCall()


    /**
     * Test a function call.
     *
     * @return void
     */
    public function testFunctionCallArgument()
    {
        $start = $this->getTargetToken('/* testFunctionCallArgument */', T_VARIABLE, '$b');
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame($start, $found);

    }//end testFunctionCallArgument()


    /**
     * Test a direct call to a control structure.
     *
     * @return void
     */
    public function testControlStructure()
    {
        $start = $this->getTargetToken('/* testControlStructure */', T_CLOSE_CURLY_BRACKET);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 6), $found);

    }//end testControlStructure()


    /**
     * Test the assignment of a closure.
     *
     * @return void
     */
    public function testClosureAssignment()
    {
        $start = $this->getTargetToken('/* testClosureAssignment */', T_CLOSE_CURLY_BRACKET);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 12), $found);

    }//end testClosureAssignment()


    /**
     * Test using a heredoc in a function argument.
     *
     * @return void
     */
    public function testHeredocFunctionArg()
    {
        // Find the start of the function.
        $start = $this->getTargetToken('/* testHeredocFunctionArg */', T_SEMICOLON);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 10), $found);

        // Find the start of the heredoc.
        $start -= 4;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 4), $found);

        // Find the start of the last arg.
        $start += 2;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame($start, $found);

    }//end testHeredocFunctionArg()


    /**
     * Test parts of a switch statement.
     *
     * @return void
     */
    public function testSwitch()
    {
        // Find the start of the switch.
        $start = $this->getTargetToken('/* testSwitch */', T_CLOSE_CURLY_BRACKET);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 47), $found);

        // Find the start of default case.
        $start -= 5;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 6), $found);

        // Find the start of the second case.
        $start -= 12;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 5), $found);

        // Find the start of the first case.
        $start -= 13;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 8), $found);

        // Test inside the first case.
        $start--;
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 1), $found);

    }//end testSwitch()


    /**
     * Test statements that are array values.
     *
     * @return void
     */
    public function testStatementAsArrayValue()
    {
        // Test short array syntax.
        $start = $this->getTargetToken('/* testStatementAsArrayValue */', T_STRING, 'Datetime');
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 2), $found);

        // Test long array syntax.
        $start += 12;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 2), $found);

        // Test same statement outside of array.
        $start++;
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 9), $found);

        // Test with an array index.
        $start += 17;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 5), $found);

    }//end testStatementAsArrayValue()


    /**
     * Test a use group.
     *
     * @return void
     */
    public function testUseGroup()
    {
        $start = $this->getTargetToken('/* testUseGroup */', T_SEMICOLON);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 23), $found);

    }//end testUseGroup()


    /**
     * Test arrow function as array value.
     *
     * @return void
     */
    public function testArrowFunctionArrayValue()
    {
        $start = $this->getTargetToken('/* testArrowFunctionArrayValue */', T_COMMA);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 9), $found);

    }//end testArrowFunctionArrayValue()


    /**
     * Test static arrow function.
     *
     * @return void
     */
    public function testStaticArrowFunction()
    {
        $start = $this->getTargetToken('/* testStaticArrowFunction */', T_SEMICOLON);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 11), $found);

    }//end testStaticArrowFunction()


    /**
     * Test arrow function with return value.
     *
     * @return void
     */
    public function testArrowFunctionReturnValue()
    {
        $start = $this->getTargetToken('/* testArrowFunctionReturnValue */', T_SEMICOLON);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 18), $found);

    }//end testArrowFunctionReturnValue()


    /**
     * Test arrow function used as a function argument.
     *
     * @return void
     */
    public function testArrowFunctionAsArgument()
    {
        $start  = $this->getTargetToken('/* testArrowFunctionAsArgument */', T_FN);
        $start += 8;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 8), $found);

    }//end testArrowFunctionAsArgument()


    /**
     * Test arrow function with arrays used as a function argument.
     *
     * @return void
     */
    public function testArrowFunctionWithArrayAsArgument()
    {
        $start  = $this->getTargetToken('/* testArrowFunctionWithArrayAsArgument */', T_FN);
        $start += 17;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 17), $found);

    }//end testArrowFunctionWithArrayAsArgument()


    /**
     * Test simple match expression case.
     *
     * @return void
     */
    public function testMatchCase()
    {
        $start = $this->getTargetToken('/* testMatchCase */', T_COMMA);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 1), $found);

    }//end testMatchCase()


    /**
     * Test simple match expression default case.
     *
     * @return void
     */
    public function testMatchDefault()
    {
        $start = $this->getTargetToken('/* testMatchDefault */', T_CONSTANT_ENCAPSED_STRING, "'bar'");
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame($start, $found);

    }//end testMatchDefault()


    /**
     * Test multiple comma-separated match expression case values.
     *
     * @return void
     */
    public function testMatchMultipleCase()
    {
        $start = $this->getTargetToken('/* testMatchMultipleCase */', T_MATCH_ARROW);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 6), $found);

        $start += 6;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 4), $found);

    }//end testMatchMultipleCase()


    /**
     * Test match expression default case with trailing comma.
     *
     * @return void
     */
    public function testMatchDefaultComma()
    {
        $start = $this->getTargetToken('/* testMatchDefaultComma */', T_MATCH_ARROW);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 3), $found);

        $start += 2;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame($start, $found);

    }//end testMatchDefaultComma()


    /**
     * Test match expression with function call.
     *
     * @return void
     */
    public function testMatchFunctionCall()
    {
        $start = $this->getTargetToken('/* testMatchFunctionCall */', T_CLOSE_PARENTHESIS);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 6), $found);

    }//end testMatchFunctionCall()


    /**
     * Test match expression with function call in the arm.
     *
     * @return void
     */
    public function testMatchFunctionCallArm()
    {
        // Check the first case.
        $start = $this->getTargetToken('/* testMatchFunctionCallArm */', T_MATCH_ARROW);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 18), $found);

        // Check the second case.
        $start += 24;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 18), $found);

    }//end testMatchFunctionCallArm()


    /**
     * Test match expression with closure.
     *
     * @return void
     */
    public function testMatchClosure()
    {
        $start  = $this->getTargetToken('/* testMatchClosure */', T_LNUMBER);
        $start += 14;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 10), $found);

        $start += 17;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 10), $found);

    }//end testMatchClosure()


    /**
     * Test match expression with array declaration.
     *
     * @return void
     */
    public function testMatchArray()
    {
        // Start of first case statement.
        $start = $this->getTargetToken('/* testMatchArray */', T_LNUMBER);
        $found = self::$phpcsFile->findStartOfStatement($start);
        $this->assertSame($start, $found);

        // Comma after first statement.
        $start += 11;
        $found  = self::$phpcsFile->findStartOfStatement($start);
        $this->assertSame(($start - 7), $found);

        // Start of second case statement.
        $start += 3;
        $found  = self::$phpcsFile->findStartOfStatement($start);
        $this->assertSame($start, $found);

        // Comma after first statement.
        $start += 30;
        $found  = self::$phpcsFile->findStartOfStatement($start);
        $this->assertSame(($start - 26), $found);

    }//end testMatchArray()


    /**
     * Test nested match expressions.
     *
     * @return void
     */
    public function testNestedMatch()
    {
        $start  = $this->getTargetToken('/* testNestedMatch */', T_LNUMBER);
        $start += 30;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 26), $found);

        $start -= 4;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 1), $found);

        $start -= 3;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 2), $found);

    }//end testNestedMatch()


    /**
     * Test PHP open tag.
     *
     * @return void
     */
    public function testOpenTag()
    {
        $start  = $this->getTargetToken('/* testOpenTag */', T_OPEN_TAG);
        $start += 2;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 1), $found);

    }//end testOpenTag()


    /**
     * Test PHP short open echo tag.
     *
     * @return void
     */
    public function testOpenTagWithEcho()
    {
        $start  = $this->getTargetToken('/* testOpenTagWithEcho */', T_OPEN_TAG_WITH_ECHO);
        $start += 3;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 1), $found);

    }//end testOpenTagWithEcho()


    /**
     * Test object call on result of static function call with arrow function as parameter and wrapped within an array.
     *
     * @link https://github.com/squizlabs/PHP_CodeSniffer/issues/2849
     * @link https://github.com/squizlabs/PHP_CodeSniffer/commit/fbf67efc3fc0c2a355f5585d49f4f6fe160ff2f9
     *
     * @return void
     */
    public function testObjectCallPrecededByArrowFunctionAsFunctionCallParameterInArray()
    {
        $expected = $this->getTargetToken('/* testPrecededByArrowFunctionInArray - Expected */', T_STRING, 'Url');

        $start = $this->getTargetToken('/* testPrecededByArrowFunctionInArray */', T_STRING, 'onlyOnDetail');
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame($expected, $found);

    }//end testObjectCallPrecededByArrowFunctionAsFunctionCallParameterInArray()


    /**
     * Test finding the start of a statement inside a switch control structure case/default statement.
     *
     * @param string     $testMarker     The comment which prefaces the target token in the test file.
     * @param int|string $targets        The token to search for after the test marker.
     * @param string|int $expectedTarget Token code of the expected start of statement stack pointer.
     *
     * @link https://github.com/squizlabs/php_codesniffer/issues/3192
     * @link https://github.com/squizlabs/PHP_CodeSniffer/pull/3186/commits/18a0e54735bb9b3850fec266e5f4c50dacf618ea
     *
     * @dataProvider dataFindStartInsideSwitchCaseDefaultStatements
     *
     * @return void
     */
    public function testFindStartInsideSwitchCaseDefaultStatements($testMarker, $targets, $expectedTarget)
    {
        $testToken = $this->getTargetToken($testMarker, $targets);
        $expected  = $this->getTargetToken($testMarker, $expectedTarget);

        $found = self::$phpcsFile->findStartOfStatement($testToken);

        $this->assertSame($expected, $found);

    }//end testFindStartInsideSwitchCaseDefaultStatements()


    /**
     * Data provider.
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataFindStartInsideSwitchCaseDefaultStatements()
    {
        return [
            'Case keyword should be start of case statement - case itself'                            => [
                'testMarker'     => '/* testCaseStatement */',
                'targets'        => T_CASE,
                'expectedTarget' => T_CASE,
            ],
            'Case keyword should be start of case statement - number (what\'s being compared)'        => [
                'testMarker'     => '/* testCaseStatement */',
                'targets'        => T_LNUMBER,
                'expectedTarget' => T_CASE,
            ],
            'Variable should be start of arbitrary assignment statement - variable itself'            => [
                'testMarker'     => '/* testInsideCaseStatement */',
                'targets'        => T_VARIABLE,
                'expectedTarget' => T_VARIABLE,
            ],
            'Variable should be start of arbitrary assignment statement - equal sign'                 => [
                'testMarker'     => '/* testInsideCaseStatement */',
                'targets'        => T_EQUAL,
                'expectedTarget' => T_VARIABLE,
            ],
            'Variable should be start of arbitrary assignment statement - function call'              => [
                'testMarker'     => '/* testInsideCaseStatement */',
                'targets'        => T_STRING,
                'expectedTarget' => T_VARIABLE,
            ],
            'Break should be start for contents of the break statement - contents'                    => [
                'testMarker'     => '/* testInsideCaseBreakStatement */',
                'targets'        => T_LNUMBER,
                'expectedTarget' => T_BREAK,
            ],
            'Continue should be start for contents of the continue statement - contents'              => [
                'testMarker'     => '/* testInsideCaseContinueStatement */',
                'targets'        => T_LNUMBER,
                'expectedTarget' => T_CONTINUE,
            ],
            'Return should be start for contents of the return statement - contents'                  => [
                'testMarker'     => '/* testInsideCaseReturnStatement */',
                'targets'        => T_FALSE,
                'expectedTarget' => T_RETURN,
            ],
            'Exit should be start for contents of the exit statement - close parenthesis'             => [
                // Note: not sure if this is actually correct - should this be the open parenthesis ?
                'testMarker'     => '/* testInsideCaseExitStatement */',
                'targets'        => T_CLOSE_PARENTHESIS,
                'expectedTarget' => T_EXIT,
            ],
            'Throw should be start for contents of the throw statement - new keyword'                 => [
                'testMarker'     => '/* testInsideCaseThrowStatement */',
                'targets'        => T_NEW,
                'expectedTarget' => T_THROW,
            ],
            'Throw should be start for contents of the throw statement - exception name'              => [
                'testMarker'     => '/* testInsideCaseThrowStatement */',
                'targets'        => T_STRING,
                'expectedTarget' => T_THROW,
            ],
            'Throw should be start for contents of the throw statement - close parenthesis'           => [
                'testMarker'     => '/* testInsideCaseThrowStatement */',
                'targets'        => T_CLOSE_PARENTHESIS,
                'expectedTarget' => T_THROW,
            ],
            'Default keyword should be start of default statement - default itself'                   => [
                'testMarker'     => '/* testDefaultStatement */',
                'targets'        => T_DEFAULT,
                'expectedTarget' => T_DEFAULT,
            ],
            'Return should be start for contents of the return statement (inside default) - variable' => [
                'testMarker'     => '/* testInsideDefaultContinueStatement */',
                'targets'        => T_VARIABLE,
                'expectedTarget' => T_CONTINUE,
            ],
        ];

    }//end dataFindStartInsideSwitchCaseDefaultStatements()


}//end class
