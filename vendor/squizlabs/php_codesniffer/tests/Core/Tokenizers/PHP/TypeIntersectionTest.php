<?php
/**
 * Tests the conversion of bitwise and tokens to type intersection tokens.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @author    Jaroslav Hanslík <kukulich@kukulich.cz>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

final class TypeIntersectionTest extends AbstractTokenizerTestCase
{


    /**
     * Test that non-intersection type bitwise and tokens are still tokenized as bitwise and.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataBitwiseAnd
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testBitwiseAnd($testMarker)
    {
        $tokens     = $this->phpcsFile->getTokens();
        $target     = $this->getTargetToken($testMarker, [T_BITWISE_AND, T_TYPE_INTERSECTION]);
        $tokenArray = $tokens[$target];

        $this->assertSame(T_BITWISE_AND, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_BITWISE_AND (code)');
        $this->assertSame('T_BITWISE_AND', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_BITWISE_AND (type)');

    }//end testBitwiseAnd()


    /**
     * Data provider.
     *
     * @see testBitwiseAnd()
     *
     * @return array<string, array<string>>
     */
    public static function dataBitwiseAnd()
    {
        return [
            'in simple assignment 1'                     => ['/* testBitwiseAnd1 */'],
            'in simple assignment 2'                     => ['/* testBitwiseAnd2 */'],
            'in OO constant default value'               => ['/* testBitwiseAndOOConstDefaultValue */'],
            'in property default value'                  => ['/* testBitwiseAndPropertyDefaultValue */'],
            'in method parameter default value'          => ['/* testBitwiseAndParamDefaultValue */'],
            'reference for method parameter'             => ['/* testBitwiseAnd3 */'],
            'in return statement'                        => ['/* testBitwiseAnd4 */'],
            'reference for function parameter'           => ['/* testBitwiseAnd5 */'],
            'in OO constant default value DNF-like'      => ['/* testBitwiseAndOOConstDefaultValueDNF */'],
            'in property default value DNF-like'         => ['/* testBitwiseAndPropertyDefaultValueDNF */'],
            'in method parameter default value DNF-like' => ['/* testBitwiseAndParamDefaultValueDNF */'],
            'in closure parameter default value'         => ['/* testBitwiseAndClosureParamDefault */'],
            'in arrow function parameter default value'  => ['/* testBitwiseAndArrowParamDefault */'],
            'in arrow function return expression'        => ['/* testBitwiseAndArrowExpression */'],
            'in long array key'                          => ['/* testBitwiseAndInArrayKey */'],
            'in long array value'                        => ['/* testBitwiseAndInArrayValue */'],
            'in short array key'                         => ['/* testBitwiseAndInShortArrayKey */'],
            'in short array value'                       => ['/* testBitwiseAndInShortArrayValue */'],
            'in parameter in function call'              => ['/* testBitwiseAndNonArrowFnFunctionCall */'],
            'function return by reference'               => ['/* testBitwiseAnd6 */'],
            'live coding / undetermined'                 => ['/* testLiveCoding */'],
        ];

    }//end dataBitwiseAnd()


    /**
     * Test that bitwise and tokens when used as part of a intersection type are tokenized as `T_TYPE_INTERSECTION`.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataTypeIntersection
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testTypeIntersection($testMarker)
    {
        $tokens     = $this->phpcsFile->getTokens();
        $target     = $this->getTargetToken($testMarker, [T_BITWISE_AND, T_TYPE_INTERSECTION]);
        $tokenArray = $tokens[$target];

        $this->assertSame(T_TYPE_INTERSECTION, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_TYPE_INTERSECTION (code)');
        $this->assertSame('T_TYPE_INTERSECTION', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_TYPE_INTERSECTION (type)');

    }//end testTypeIntersection()


    /**
     * Data provider.
     *
     * @see testTypeIntersection()
     *
     * @return array<string, array<string>>
     */
    public static function dataTypeIntersection()
    {
        return [
            'type for OO constant'                                        => ['/* testTypeIntersectionOOConstSimple */'],
            'type for OO constant, reversed modifier order'               => ['/* testTypeIntersectionOOConstReverseModifierOrder */'],
            'type for OO constant, first of multi-intersect'              => ['/* testTypeIntersectionOOConstMulti1 */'],
            'type for OO constant, middle of multi-intersect + comments'  => ['/* testTypeIntersectionOOConstMulti2 */'],
            'type for OO constant, last of multi-intersect'               => ['/* testTypeIntersectionOOConstMulti3 */'],
            'type for OO constant, using namespace relative names'        => ['/* testTypeIntersectionOOConstNamespaceRelative */'],
            'type for OO constant, using partially qualified names'       => ['/* testTypeIntersectionOOConstPartiallyQualified */'],
            'type for OO constant, using fully qualified names'           => ['/* testTypeIntersectionOOConstFullyQualified */'],
            'type for static property'                                    => ['/* testTypeIntersectionPropertySimple */'],
            'type for static property, reversed modifier order'           => ['/* testTypeIntersectionPropertyReverseModifierOrder */'],
            'type for property, first of multi-intersect'                 => ['/* testTypeIntersectionPropertyMulti1 */'],
            'type for property, middle of multi-intersect, also comments' => ['/* testTypeIntersectionPropertyMulti2 */'],
            'type for property, last of multi-intersect'                  => ['/* testTypeIntersectionPropertyMulti3 */'],
            'type for property using namespace relative names'            => ['/* testTypeIntersectionPropertyNamespaceRelative */'],
            'type for property using partially qualified names'           => ['/* testTypeIntersectionPropertyPartiallyQualified */'],
            'type for property using fully qualified names'               => ['/* testTypeIntersectionPropertyFullyQualified */'],
            'type for readonly property'                                  => ['/* testTypeIntersectionPropertyWithReadOnlyKeyword */'],
            'type for static readonly property'                           => ['/* testTypeIntersectionPropertyWithStaticKeyword */'],
            'type for final property'                                     => ['/* testTypeIntersectionWithPHP84FinalKeyword */'],
            'type for final property reversed modifier order'             => ['/* testTypeIntersectionWithPHP84FinalKeywordFirst */'],
            'type for asymmetric visibility (private(set)) property'      => ['/* testTypeIntersectionPropertyWithPrivateSet */'],
            'type for asymmetric visibility (public private(set)) prop'   => ['/* testTypeIntersectionPropertyWithPublicPrivateSet */'],
            'type for asymmetric visibility (protected(set)) property'    => ['/* testTypeIntersectionPropertyWithProtectedSet */'],
            'type for asymmetric visibility (public protected(set)) prop' => ['/* testTypeIntersectionPropertyWithPublicProtectedSet */'],
            'type for method parameter'                                   => ['/* testTypeIntersectionParam1 */'],
            'type for method parameter, first in multi-intersect'         => ['/* testTypeIntersectionParam2 */'],
            'type for method parameter, last in multi-intersect'          => ['/* testTypeIntersectionParam3 */'],
            'type for method parameter with namespace relative names'     => ['/* testTypeIntersectionParamNamespaceRelative */'],
            'type for method parameter with partially qualified names'    => ['/* testTypeIntersectionParamPartiallyQualified */'],
            'type for method parameter with fully qualified names'        => ['/* testTypeIntersectionParamFullyQualified */'],
            'type for property in constructor property promotion'         => ['/* testTypeIntersectionConstructorPropertyPromotion */'],
            'return type for method'                                      => ['/* testTypeIntersectionReturnType */'],
            'return type for method, first of multi-intersect'            => ['/* testTypeIntersectionAbstractMethodReturnType1 */'],
            'return type for method, last of multi-intersect'             => ['/* testTypeIntersectionAbstractMethodReturnType2 */'],
            'return type for method with namespace relative names'        => ['/* testTypeIntersectionReturnTypeNamespaceRelative */'],
            'return type for method with partially qualified names'       => ['/* testTypeIntersectionReturnPartiallyQualified */'],
            'return type for method with fully qualified names'           => ['/* testTypeIntersectionReturnFullyQualified */'],
            'type for function parameter with reference'                  => ['/* testTypeIntersectionWithReference */'],
            'type for function parameter with spread operator'            => ['/* testTypeIntersectionWithSpreadOperator */'],
            'DNF type for OO constant, union before DNF'                  => ['/* testTypeIntersectionConstantTypeUnionBeforeDNF */'],
            'DNF type for property, union after DNF'                      => ['/* testTypeIntersectionPropertyTypeUnionAfterDNF */'],
            'DNF type for function param, union before and after DNF'     => ['/* testTypeIntersectionParamUnionBeforeAndAfterDNF */'],
            'DNF type for function return, union after DNF with null'     => ['/* testTypeIntersectionReturnTypeUnionAfterDNF */'],
            'type for closure parameter with illegal nullable'            => ['/* testTypeIntersectionClosureParamIllegalNullable */'],
            'return type for closure'                                     => ['/* testTypeIntersectionClosureReturn */'],
            'type for arrow function parameter'                           => ['/* testTypeIntersectionArrowParam */'],
            'return type for arrow function'                              => ['/* testTypeIntersectionArrowReturnType */'],
            'type for function parameter, return by ref'                  => ['/* testTypeIntersectionNonArrowFunctionDeclaration */'],
            'type for function parameter with invalid types'              => ['/* testTypeIntersectionWithInvalidTypes */'],
        ];

    }//end dataTypeIntersection()


}//end class
