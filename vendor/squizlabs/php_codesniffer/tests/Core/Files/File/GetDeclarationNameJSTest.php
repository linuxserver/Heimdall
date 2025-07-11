<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File::getDeclarationName method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2022-2024 PHPCSStandards Contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Files\File;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

/**
 * Tests for the \PHP_CodeSniffer\Files\File:getDeclarationName method.
 *
 * @covers \PHP_CodeSniffer\Files\File::getDeclarationName
 */
final class GetDeclarationNameJSTest extends AbstractMethodUnitTest
{

    /**
     * The file extension of the test case file (without leading dot).
     *
     * @var string
     */
    protected static $fileExtension = 'js';


    /**
     * Test receiving an expected exception when a non-supported token is passed.
     *
     * @return void
     */
    public function testInvalidTokenPassed()
    {
        $this->expectRunTimeException('Token type "T_STRING" is not T_FUNCTION, T_CLASS, T_INTERFACE, T_TRAIT or T_ENUM');

        $target = $this->getTargetToken('/* testInvalidTokenPassed */', T_STRING);
        self::$phpcsFile->getDeclarationName($target);

    }//end testInvalidTokenPassed()


    /**
     * Test receiving "null" when passed an anonymous construct or in case of a parse error.
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $targetType Token type of the token to get as stackPtr.
     *
     * @dataProvider dataGetDeclarationNameNull
     *
     * @return void
     */
    public function testGetDeclarationNameNull($testMarker, $targetType)
    {
        $target = $this->getTargetToken($testMarker, $targetType);
        $result = self::$phpcsFile->getDeclarationName($target);
        $this->assertNull($result);

    }//end testGetDeclarationNameNull()


    /**
     * Data provider.
     *
     * @see GetDeclarationNameTest::testGetDeclarationNameNull()
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataGetDeclarationNameNull()
    {
        return [
            'closure' => [
                'testMarker' => '/* testClosure */',
                'targetType' => T_CLOSURE,
            ],
        ];

    }//end dataGetDeclarationNameNull()


    /**
     * Test retrieving the name of a function or OO structure.
     *
     * @param string                 $testMarker The comment which prefaces the target token in the test file.
     * @param string                 $expected   Expected function output.
     * @param array<int|string>|null $targetType Token type of the token to get as stackPtr.
     *
     * @dataProvider dataGetDeclarationName
     *
     * @return void
     */
    public function testGetDeclarationName($testMarker, $expected, $targetType=null)
    {
        if (isset($targetType) === false) {
            $targetType = [
                T_CLASS,
                T_INTERFACE,
                T_TRAIT,
                T_ENUM,
                T_FUNCTION,
            ];
        }

        $target = $this->getTargetToken($testMarker, $targetType);
        $result = self::$phpcsFile->getDeclarationName($target);
        $this->assertSame($expected, $result);

    }//end testGetDeclarationName()


    /**
     * Data provider.
     *
     * @see GetDeclarationNameTest::testGetDeclarationName()
     *
     * @return array<string, array<string, string|array<int|string>>>
     */
    public static function dataGetDeclarationName()
    {
        return [
            'function'              => [
                'testMarker' => '/* testFunction */',
                'expected'   => 'functionName',
            ],
            'class'                 => [
                'testMarker' => '/* testClass */',
                'expected'   => 'ClassName',
                'targetType' => [
                    T_CLASS,
                    T_STRING,
                ],
            ],
            'function-unicode-name' => [
                'testMarker' => '/* testFunctionUnicode */',
                'expected'   => 'π',
            ],
        ];

    }//end dataGetDeclarationName()


    /**
     * Test retrieving the name of JS ES6 class method.
     *
     * @return void
     */
    public function testGetDeclarationNameES6Method()
    {
        $target = $this->getTargetToken('/* testMethod */', [T_CLASS, T_INTERFACE, T_TRAIT, T_FUNCTION]);
        $result = self::$phpcsFile->getDeclarationName($target);
        $this->assertSame('methodName', $result);

    }//end testGetDeclarationNameES6Method()


}//end class
