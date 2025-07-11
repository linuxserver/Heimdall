<?php
/**
 * Tests the conversion of bitwise or tokens to type union tokens.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

final class BitwiseOrTest extends AbstractTokenizerTestCase
{


    /**
     * Test that non-union type bitwise or tokens are still tokenized as bitwise or.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataBitwiseOr
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testBitwiseOr($testMarker)
    {
        $tokens     = $this->phpcsFile->getTokens();
        $target     = $this->getTargetToken($testMarker, [T_BITWISE_OR, T_TYPE_UNION]);
        $tokenArray = $tokens[$target];

        $this->assertSame(T_BITWISE_OR, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_BITWISE_OR (code)');
        $this->assertSame('T_BITWISE_OR', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_BITWISE_OR (type)');

    }//end testBitwiseOr()


    /**
     * Data provider.
     *
     * @see testBitwiseOr()
     *
     * @return array<string, array<string>>
     */
    public static function dataBitwiseOr()
    {
        return [
            'in simple assignment 1'                     => ['/* testBitwiseOr1 */'],
            'in simple assignment 2'                     => ['/* testBitwiseOr2 */'],
            'in OO constant default value'               => ['/* testBitwiseOrOOConstDefaultValue */'],
            'in property default value'                  => ['/* testBitwiseOrPropertyDefaultValue */'],
            'in method parameter default value'          => ['/* testBitwiseOrParamDefaultValue */'],
            'in return statement'                        => ['/* testBitwiseOr3 */'],
            'in closure parameter default value'         => ['/* testBitwiseOrClosureParamDefault */'],
            'in OO constant default value DNF-like'      => ['/* testBitwiseOrOOConstDefaultValueDNF */'],
            'in property default value DNF-like'         => ['/* testBitwiseOrPropertyDefaultValueDNF */'],
            'in method parameter default value DNF-like' => ['/* testBitwiseOrParamDefaultValueDNF */'],
            'in arrow function parameter default value'  => ['/* testBitwiseOrArrowParamDefault */'],
            'in arrow function return expression'        => ['/* testBitwiseOrArrowExpression */'],
            'in long array key'                          => ['/* testBitwiseOrInArrayKey */'],
            'in long array value'                        => ['/* testBitwiseOrInArrayValue */'],
            'in short array key'                         => ['/* testBitwiseOrInShortArrayKey */'],
            'in short array value'                       => ['/* testBitwiseOrInShortArrayValue */'],
            'in catch condition'                         => ['/* testBitwiseOrTryCatch */'],
            'in parameter in function call'              => ['/* testBitwiseOrNonArrowFnFunctionCall */'],
            'live coding / undetermined'                 => ['/* testLiveCoding */'],
        ];

    }//end dataBitwiseOr()


    /**
     * Test that bitwise or tokens when used as part of a union type are tokenized as `T_TYPE_UNION`.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataTypeUnion
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testTypeUnion($testMarker)
    {
        $tokens     = $this->phpcsFile->getTokens();
        $target     = $this->getTargetToken($testMarker, [T_BITWISE_OR, T_TYPE_UNION]);
        $tokenArray = $tokens[$target];

        $this->assertSame(T_TYPE_UNION, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_TYPE_UNION (code)');
        $this->assertSame('T_TYPE_UNION', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_TYPE_UNION (type)');

    }//end testTypeUnion()


    /**
     * Data provider.
     *
     * @see testTypeUnion()
     *
     * @return array<string, array<string>>
     */
    public static function dataTypeUnion()
    {
        return [
            'type for OO constant'                                     => ['/* testTypeUnionOOConstSimple */'],
            'type for OO constant, reversed modifier order'            => ['/* testTypeUnionOOConstReverseModifierOrder */'],
            'type for OO constant, first of multi-union'               => ['/* testTypeUnionOOConstMulti1 */'],
            'type for OO constant, middle of multi-union + comments'   => ['/* testTypeUnionOOConstMulti2 */'],
            'type for OO constant, last of multi-union'                => ['/* testTypeUnionOOConstMulti3 */'],
            'type for OO constant, using namespace relative names'     => ['/* testTypeUnionOOConstNamespaceRelative */'],
            'type for OO constant, using partially qualified names'    => ['/* testTypeUnionOOConstPartiallyQualified */'],
            'type for OO constant, using fully qualified names'        => ['/* testTypeUnionOOConstFullyQualified */'],
            'type for static property'                                 => ['/* testTypeUnionPropertySimple */'],
            'type for static property, reversed modifier order'        => ['/* testTypeUnionPropertyReverseModifierOrder */'],
            'type for property, first of multi-union'                  => ['/* testTypeUnionPropertyMulti1 */'],
            'type for property, middle of multi-union, also comments'  => ['/* testTypeUnionPropertyMulti2 */'],
            'type for property, last of multi-union'                   => ['/* testTypeUnionPropertyMulti3 */'],
            'type for property using namespace relative names'         => ['/* testTypeUnionPropertyNamespaceRelative */'],
            'type for property using partially qualified names'        => ['/* testTypeUnionPropertyPartiallyQualified */'],
            'type for property using fully qualified names'            => ['/* testTypeUnionPropertyFullyQualified */'],
            'type for readonly property'                               => ['/* testTypeUnionPropertyWithReadOnlyKeyword */'],
            'type for static readonly property'                        => ['/* testTypeUnionPropertyWithStaticAndReadOnlyKeywords */'],
            'type for readonly property using var keyword'             => ['/* testTypeUnionPropertyWithVarAndReadOnlyKeywords */'],
            'type for readonly property, reversed modifier order'      => ['/* testTypeUnionPropertyWithReadOnlyKeywordFirst */'],
            'type for readonly property, no visibility'                => ['/* testTypeUnionPropertyWithOnlyReadOnlyKeyword */'],
            'type for static property, no visibility'                  => ['/* testTypeUnionPropertyWithOnlyStaticKeyword */'],
            'type for final property, no visibility'                   => ['/* testTypeUnionWithPHP84FinalKeyword */'],
            'type for final property, reversed modifier order'         => ['/* testTypeUnionWithPHP84FinalKeywordFirst */'],
            'type for final property, no visibility, FQN type'         => ['/* testTypeUnionWithPHP84FinalKeywordAndFQN */'],
            'type for private(set) property'                           => ['/* testTypeUnionPropertyPrivateSet */'],
            'type for public private(set) property'                    => ['/* testTypeUnionPropertyPublicPrivateSet */'],
            'type for protected(set) property'                         => ['/* testTypeUnionPropertyProtected */'],
            'type for public protected(set) property'                  => ['/* testTypeUnionPropertyPublicProtected */'],
            'type for method parameter'                                => ['/* testTypeUnionParam1 */'],
            'type for method parameter, first in multi-union'          => ['/* testTypeUnionParam2 */'],
            'type for method parameter, last in multi-union'           => ['/* testTypeUnionParam3 */'],
            'type for method parameter with namespace relative names'  => ['/* testTypeUnionParamNamespaceRelative */'],
            'type for method parameter with partially qualified names' => ['/* testTypeUnionParamPartiallyQualified */'],
            'type for method parameter with fully qualified names'     => ['/* testTypeUnionParamFullyQualified */'],
            'type for property in constructor property promotion'      => ['/* testTypeUnionConstructorPropertyPromotion */'],
            'return type for method'                                   => ['/* testTypeUnionReturnType */'],
            'return type for method, first of multi-union'             => ['/* testTypeUnionAbstractMethodReturnType1 */'],
            'return type for method, last of multi-union'              => ['/* testTypeUnionAbstractMethodReturnType2 */'],
            'return type for method with namespace relative names'     => ['/* testTypeUnionReturnTypeNamespaceRelative */'],
            'return type for method with partially qualified names'    => ['/* testTypeUnionReturnPartiallyQualified */'],
            'return type for method with fully qualified names'        => ['/* testTypeUnionReturnFullyQualified */'],
            'type for function parameter with reference'               => ['/* testTypeUnionWithReference */'],
            'type for function parameter with spread operator'         => ['/* testTypeUnionWithSpreadOperator */'],
            'DNF type for OO constant, union before DNF'               => ['/* testTypeUnionConstantTypeUnionBeforeDNF */'],
            'DNF type for property, union after DNF'                   => ['/* testTypeUnionPropertyTypeUnionAfterDNF */'],
            'DNF type for function param, union before and after DNF'  => ['/* testTypeUnionParamUnionBeforeAndAfterDNF */'],
            'DNF type for function return, union after DNF with null'  => ['/* testTypeUnionReturnTypeUnionAfterDNF */'],
            'type for closure parameter with illegal nullable'         => ['/* testTypeUnionClosureParamIllegalNullable */'],
            'return type for closure'                                  => ['/* testTypeUnionClosureReturn */'],
            'type for arrow function parameter'                        => ['/* testTypeUnionArrowParam */'],
            'return type for arrow function'                           => ['/* testTypeUnionArrowReturnType */'],
            'type for function parameter, return by ref'               => ['/* testTypeUnionNonArrowFunctionDeclaration */'],
            'type for function param with true type first'             => ['/* testTypeUnionPHP82TrueFirst */'],
            'return type for function with true type middle'           => ['/* testTypeUnionPHP82TrueMiddle */'],
            'return type for closure with true type last'              => ['/* testTypeUnionPHP82TrueLast */'],
        ];

    }//end dataTypeUnion()


}//end class
