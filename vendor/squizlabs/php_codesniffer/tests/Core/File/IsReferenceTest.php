<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File::isReference method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\File;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

/**
 * Tests for the \PHP_CodeSniffer\Files\File::isReference method.
 *
 * @covers \PHP_CodeSniffer\Files\File::isReference
 */
final class IsReferenceTest extends AbstractMethodUnitTest
{


    /**
     * Test that false is returned when a non-"bitwise and" token is passed.
     *
     * @return void
     */
    public function testNotBitwiseAndToken()
    {
        $target = $this->getTargetToken('/* testBitwiseAndA */', T_STRING);
        $this->assertFalse(self::$phpcsFile->isReference($target));

    }//end testNotBitwiseAndToken()


    /**
     * Test correctly identifying whether a "bitwise and" token is a reference or not.
     *
     * @param string $identifier Comment which precedes the test case.
     * @param bool   $expected   Expected function output.
     *
     * @dataProvider dataIsReference
     *
     * @return void
     */
    public function testIsReference($identifier, $expected)
    {
        $bitwiseAnd = $this->getTargetToken($identifier, T_BITWISE_AND);
        $result     = self::$phpcsFile->isReference($bitwiseAnd);
        $this->assertSame($expected, $result);

    }//end testIsReference()


    /**
     * Data provider for the IsReference test.
     *
     * @see testIsReference()
     *
     * @return array<string, array<string, string|bool>>
     */
    public static function dataIsReference()
    {
        return [
            'issue-1971-list-first-in-file'                                                                     => [
                'testMarker' => '/* testTokenizerIssue1971PHPCSlt330gt271A */',
                'expected'   => true,
            ],
            'issue-1971-list-first-in-file-nested'                                                              => [
                'testMarker' => '/* testTokenizerIssue1971PHPCSlt330gt271B */',
                'expected'   => true,
            ],
            'bitwise and: param in function call'                                                               => [
                'testMarker' => '/* testBitwiseAndA */',
                'expected'   => false,
            ],
            'bitwise and: in unkeyed short array, first value'                                                  => [
                'testMarker' => '/* testBitwiseAndB */',
                'expected'   => false,
            ],
            'bitwise and: in unkeyed short array, last value'                                                   => [
                'testMarker' => '/* testBitwiseAndC */',
                'expected'   => false,
            ],
            'bitwise and: in unkeyed long array, last value'                                                    => [
                'testMarker' => '/* testBitwiseAndD */',
                'expected'   => false,
            ],
            'bitwise and: in keyed short array, last value'                                                     => [
                'testMarker' => '/* testBitwiseAndE */',
                'expected'   => false,
            ],
            'bitwise and: in keyed long array, last value'                                                      => [
                'testMarker' => '/* testBitwiseAndF */',
                'expected'   => false,
            ],
            'bitwise and: in assignment'                                                                        => [
                'testMarker' => '/* testBitwiseAndG */',
                'expected'   => false,
            ],
            'bitwise and: in param default value in function declaration'                                       => [
                'testMarker' => '/* testBitwiseAndH */',
                'expected'   => false,
            ],
            'bitwise and: in param default value in closure declaration'                                        => [
                'testMarker' => '/* testBitwiseAndI */',
                'expected'   => false,
            ],
            'reference: function declared to return by reference'                                               => [
                'testMarker' => '/* testFunctionReturnByReference */',
                'expected'   => true,
            ],
            'reference: only param in function declaration, pass by reference'                                  => [
                'testMarker' => '/* testFunctionPassByReferenceA */',
                'expected'   => true,
            ],
            'reference: last param in function declaration, pass by reference'                                  => [
                'testMarker' => '/* testFunctionPassByReferenceB */',
                'expected'   => true,
            ],
            'reference: only param in closure declaration, pass by reference'                                   => [
                'testMarker' => '/* testFunctionPassByReferenceC */',
                'expected'   => true,
            ],
            'reference: last param in closure declaration, pass by reference'                                   => [
                'testMarker' => '/* testFunctionPassByReferenceD */',
                'expected'   => true,
            ],
            'reference: typed param in function declaration, pass by reference'                                 => [
                'testMarker' => '/* testFunctionPassByReferenceE */',
                'expected'   => true,
            ],
            'reference: typed param in closure declaration, pass by reference'                                  => [
                'testMarker' => '/* testFunctionPassByReferenceF */',
                'expected'   => true,
            ],
            'reference: variadic param in function declaration, pass by reference'                              => [
                'testMarker' => '/* testFunctionPassByReferenceG */',
                'expected'   => true,
            ],
            'reference: foreach value'                                                                          => [
                'testMarker' => '/* testForeachValueByReference */',
                'expected'   => true,
            ],
            'reference: foreach key'                                                                            => [
                'testMarker' => '/* testForeachKeyByReference */',
                'expected'   => true,
            ],
            'reference: keyed short array, first value, value by reference'                                     => [
                'testMarker' => '/* testArrayValueByReferenceA */',
                'expected'   => true,
            ],
            'reference: keyed short array, last value, value by reference'                                      => [
                'testMarker' => '/* testArrayValueByReferenceB */',
                'expected'   => true,
            ],
            'reference: unkeyed short array, only value, value by reference'                                    => [
                'testMarker' => '/* testArrayValueByReferenceC */',
                'expected'   => true,
            ],
            'reference: unkeyed short array, last value, value by reference'                                    => [
                'testMarker' => '/* testArrayValueByReferenceD */',
                'expected'   => true,
            ],
            'reference: keyed long array, first value, value by reference'                                      => [
                'testMarker' => '/* testArrayValueByReferenceE */',
                'expected'   => true,
            ],
            'reference: keyed long array, last value, value by reference'                                       => [
                'testMarker' => '/* testArrayValueByReferenceF */',
                'expected'   => true,
            ],
            'reference: unkeyed long array, only value, value by reference'                                     => [
                'testMarker' => '/* testArrayValueByReferenceG */',
                'expected'   => true,
            ],
            'reference: unkeyed long array, last value, value by reference'                                     => [
                'testMarker' => '/* testArrayValueByReferenceH */',
                'expected'   => true,
            ],
            'reference: variable, assign by reference'                                                          => [
                'testMarker' => '/* testAssignByReferenceA */',
                'expected'   => true,
            ],
            'reference: variable, assign by reference, spacing variation'                                       => [
                'testMarker' => '/* testAssignByReferenceB */',
                'expected'   => true,
            ],
            'reference: variable, assign by reference, concat assign'                                           => [
                'testMarker' => '/* testAssignByReferenceC */',
                'expected'   => true,
            ],
            'reference: property, assign by reference'                                                          => [
                'testMarker' => '/* testAssignByReferenceD */',
                'expected'   => true,
            ],
            'reference: function return value, assign by reference'                                             => [
                'testMarker' => '/* testAssignByReferenceE */',
                'expected'   => true,
            ],
            'reference: function return value, assign by reference, null coalesce assign'                       => [
                'testMarker' => '/* testAssignByReferenceF */',
                'expected'   => true,
            ],
            'reference: unkeyed short list, first var, assign by reference'                                     => [
                'testMarker' => '/* testShortListAssignByReferenceNoKeyA */',
                'expected'   => true,
            ],
            'reference: unkeyed short list, second var, assign by reference'                                    => [
                'testMarker' => '/* testShortListAssignByReferenceNoKeyB */',
                'expected'   => true,
            ],
            'reference: unkeyed short list, nested var, assign by reference'                                    => [
                'testMarker' => '/* testNestedShortListAssignByReferenceNoKey */',
                'expected'   => true,
            ],
            'reference: unkeyed long list, second var, assign by reference'                                     => [
                'testMarker' => '/* testLongListAssignByReferenceNoKeyA */',
                'expected'   => true,
            ],
            'reference: unkeyed long list, first nested var, assign by reference'                               => [
                'testMarker' => '/* testLongListAssignByReferenceNoKeyB */',
                'expected'   => true,
            ],
            'reference: unkeyed long list, last nested var, assign by reference'                                => [
                'testMarker' => '/* testLongListAssignByReferenceNoKeyC */',
                'expected'   => true,
            ],
            'reference: keyed short list, first nested var, assign by reference'                                => [
                'testMarker' => '/* testNestedShortListAssignByReferenceWithKeyA */',
                'expected'   => true,
            ],
            'reference: keyed short list, last nested var, assign by reference'                                 => [
                'testMarker' => '/* testNestedShortListAssignByReferenceWithKeyB */',
                'expected'   => true,
            ],
            'reference: keyed long list, only var, assign by reference'                                         => [
                'testMarker' => '/* testLongListAssignByReferenceWithKeyA */',
                'expected'   => true,
            ],
            'reference: first param in function call, pass by reference'                                        => [
                'testMarker' => '/* testPassByReferenceA */',
                'expected'   => true,
            ],
            'reference: last param in function call, pass by reference'                                         => [
                'testMarker' => '/* testPassByReferenceB */',
                'expected'   => true,
            ],
            'reference: property in function call, pass by reference'                                           => [
                'testMarker' => '/* testPassByReferenceC */',
                'expected'   => true,
            ],
            'reference: hierarchical self property in function call, pass by reference'                         => [
                'testMarker' => '/* testPassByReferenceD */',
                'expected'   => true,
            ],
            'reference: hierarchical parent property in function call, pass by reference'                       => [
                'testMarker' => '/* testPassByReferenceE */',
                'expected'   => true,
            ],
            'reference: hierarchical static property in function call, pass by reference'                       => [
                'testMarker' => '/* testPassByReferenceF */',
                'expected'   => true,
            ],
            'reference: static property in function call, pass by reference'                                    => [
                'testMarker' => '/* testPassByReferenceG */',
                'expected'   => true,
            ],
            'reference: static property in function call, first with FQN, pass by reference'                    => [
                'testMarker' => '/* testPassByReferenceH */',
                'expected'   => true,
            ],
            'reference: static property in function call, last with FQN, pass by reference'                     => [
                'testMarker' => '/* testPassByReferenceI */',
                'expected'   => true,
            ],
            'reference: static property in function call, last with namespace relative name, pass by reference' => [
                'testMarker' => '/* testPassByReferenceJ */',
                'expected'   => true,
            ],
            'reference: static property in function call, last with PQN, pass by reference'                     => [
                'testMarker' => '/* testPassByReferencePartiallyQualifiedName */',
                'expected'   => true,
            ],
            'reference: new by reference'                                                                       => [
                'testMarker' => '/* testNewByReferenceA */',
                'expected'   => true,
            ],
            'reference: new by reference as function call param'                                                => [
                'testMarker' => '/* testNewByReferenceB */',
                'expected'   => true,
            ],
            'reference: closure use by reference'                                                               => [
                'testMarker' => '/* testUseByReference */',
                'expected'   => true,
            ],
            'reference: closure use by reference, first param, with comment'                                    => [
                'testMarker' => '/* testUseByReferenceWithCommentFirstParam */',
                'expected'   => true,
            ],
            'reference: closure use by reference, last param, with comment'                                     => [
                'testMarker' => '/* testUseByReferenceWithCommentSecondParam */',
                'expected'   => true,
            ],
            'reference: arrow fn declared to return by reference'                                               => [
                'testMarker' => '/* testArrowFunctionReturnByReference */',
                'expected'   => true,
            ],
            'bitwise and: first param default value in closure declaration'                                     => [
                'testMarker' => '/* testBitwiseAndExactParameterA */',
                'expected'   => false,
            ],
            'reference: param in closure declaration, pass by reference'                                        => [
                'testMarker' => '/* testPassByReferenceExactParameterB */',
                'expected'   => true,
            ],
            'reference: variadic param in closure declaration, pass by reference'                               => [
                'testMarker' => '/* testPassByReferenceExactParameterC */',
                'expected'   => true,
            ],
            'bitwise and: last param default value in closure declaration'                                      => [
                'testMarker' => '/* testBitwiseAndExactParameterD */',
                'expected'   => false,
            ],
            'reference: typed param in arrow fn declaration, pass by reference'                                 => [
                'testMarker' => '/* testArrowFunctionPassByReferenceA */',
                'expected'   => true,
            ],
            'reference: variadic param in arrow fn declaration, pass by reference'                              => [
                'testMarker' => '/* testArrowFunctionPassByReferenceB */',
                'expected'   => true,
            ],
            'reference: closure declared to return by reference'                                                => [
                'testMarker' => '/* testClosureReturnByReference */',
                'expected'   => true,
            ],
            'bitwise and: param default value in arrow fn declaration'                                          => [
                'testMarker' => '/* testBitwiseAndArrowFunctionInDefault */',
                'expected'   => false,
            ],
            'issue-1284-short-list-directly-after-close-curly-control-structure'                                => [
                'testMarker' => '/* testTokenizerIssue1284PHPCSlt280A */',
                'expected'   => true,
            ],
            'issue-1284-short-list-directly-after-close-curly-control-structure-second-item'                    => [
                'testMarker' => '/* testTokenizerIssue1284PHPCSlt280B */',
                'expected'   => true,
            ],
            'issue-1284-short-array-directly-after-close-curly-control-structure'                               => [
                'testMarker' => '/* testTokenizerIssue1284PHPCSlt280C */',
                'expected'   => true,
            ],
        ];

    }//end dataIsReference()


}//end class
