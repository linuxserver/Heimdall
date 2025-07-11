<?php
/**
 * Tests the conversion of parentheses tokens to type parentheses tokens.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;
use PHP_CodeSniffer\Util\Tokens;

final class DNFTypesTest extends AbstractTokenizerTestCase
{


    /**
     * Test that parentheses when **not** used in a type declaration are correctly tokenized.
     *
     * @param string $testMarker      The comment prefacing the target token.
     * @param bool   $skipCheckInside Optional. Skip checking correct token type inside the parentheses.
     *                                Use judiciously for combined normal + DNF tests only.
     *
     * @dataProvider dataNormalParentheses
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testNormalParentheses($testMarker, $skipCheckInside=false)
    {
        $tokens = $this->phpcsFile->getTokens();

        $openPtr = $this->getTargetToken($testMarker, [T_OPEN_PARENTHESIS, T_TYPE_OPEN_PARENTHESIS]);
        $opener  = $tokens[$openPtr];

        $this->assertSame('(', $opener['content'], 'Content of type open parenthesis is not "("');
        $this->assertSame(T_OPEN_PARENTHESIS, $opener['code'], 'Token tokenized as '.$opener['type'].', not T_OPEN_PARENTHESIS (code)');
        $this->assertSame('T_OPEN_PARENTHESIS', $opener['type'], 'Token tokenized as '.$opener['type'].', not T_OPEN_PARENTHESIS (type)');

        $closePtr = $opener['parenthesis_closer'];
        $closer   = $tokens[$closePtr];

        $this->assertSame(')', $closer['content'], 'Content of type close parenthesis is not ")"');
        $this->assertSame(T_CLOSE_PARENTHESIS, $closer['code'], 'Token tokenized as '.$closer['type'].', not T_CLOSE_PARENTHESIS (code)');
        $this->assertSame('T_CLOSE_PARENTHESIS', $closer['type'], 'Token tokenized as '.$closer['type'].', not T_CLOSE_PARENTHESIS (type)');

        if ($skipCheckInside === false) {
            for ($i = ($openPtr + 1); $i < $closePtr; $i++) {
                // If there are ampersands, make sure these are tokenized as bitwise and.
                if ($tokens[$i]['content'] === '&') {
                    $this->assertSame(T_BITWISE_AND, $tokens[$i]['code'], 'Token tokenized as '.$tokens[$i]['type'].', not T_BITWISE_AND (code)');
                    $this->assertSame('T_BITWISE_AND', $tokens[$i]['type'], 'Token tokenized as '.$tokens[$i]['type'].', not T_BITWISE_AND (type)');
                }

                // If there are pipes, make sure these are tokenized as bitwise or.
                if ($tokens[$i]['content'] === '|') {
                    $this->assertSame(T_BITWISE_OR, $tokens[$i]['code'], 'Token tokenized as '.$tokens[$i]['type'].', not T_BITWISE_OR (code)');
                    $this->assertSame('T_BITWISE_OR', $tokens[$i]['type'], 'Token tokenized as '.$tokens[$i]['type'].', not T_BITWISE_OR (type)');
                }
            }
        }

        $before = $this->phpcsFile->findPrevious(Tokens::$emptyTokens, ($openPtr - 1), null, true);
        if ($before !== false && $tokens[$before]['content'] === '|') {
            $this->assertSame(
                T_BITWISE_OR,
                $tokens[$before]['code'],
                'Token before tokenized as '.$tokens[$before]['type'].', not T_BITWISE_OR (code)'
            );
            $this->assertSame(
                'T_BITWISE_OR',
                $tokens[$before]['type'],
                'Token before tokenized as '.$tokens[$before]['type'].', not T_BITWISE_OR (type)'
            );
        }

        $after = $this->phpcsFile->findNext(Tokens::$emptyTokens, ($closePtr + 1), null, true);
        if ($after !== false && $tokens[$after]['content'] === '|') {
            $this->assertSame(
                T_BITWISE_OR,
                $tokens[$after]['code'],
                'Token after tokenized as '.$tokens[$after]['type'].', not T_BITWISE_OR (code)'
            );
            $this->assertSame(
                'T_BITWISE_OR',
                $tokens[$after]['type'],
                'Token after tokenized as '.$tokens[$after]['type'].', not T_BITWISE_OR (type)'
            );
        }

    }//end testNormalParentheses()


    /**
     * Data provider.
     *
     * @see testNormalParentheses()
     *
     * @return array<string, array<string, string|bool>>
     */
    public static function dataNormalParentheses()
    {
        // "Owner" offsets are relative to the open parenthesis.
        return [
            'parens without owner'                                            => [
                'testMarker' => '/* testParensNoOwner */',
            ],
            'parens without owner in ternary then'                            => [
                'testMarker' => '/* testParensNoOwnerInTernary */',
            ],
            'parens without owner in short ternary'                           => [
                'testMarker' => '/* testParensNoOwnerInShortTernary */',
            ],
            'parens without owner in ternary then (fn call in inline then)'   => [
                'testMarker' => '/* testFnCallParensNoOwnerInTernaryA */',
            ],
            'parens without owner in ternary then (fn call in inline else)'   => [
                'testMarker' => '/* testFnCallParensNoOwnerInTernaryB */',
            ],
            'parens without owner in short ternary (fn call)'                 => [
                'testMarker' => '/* testPFnCallarensNoOwnerInShortTernary */',
            ],
            'parens with owner: function; & in default value'                 => [
                'testMarker' => '/* testParensOwnerFunctionAmpersandInDefaultValue */',
            ],
            'parens with owner: closure; param declared by & ref'             => [
                'testMarker' => '/* testParensOwnerClosureAmpersandParamRef */',
            ],
            'parens with owner: if'                                           => [
                'testMarker' => '/* testParensOwnerIf */',
            ],
            'parens without owner in if condition'                            => [
                'testMarker' => '/* testParensNoOwnerInIfCondition */',
            ],
            'parens with owner: for'                                          => [
                'testMarker' => '/* testParensOwnerFor */',
            ],
            'parens without owner in for condition'                           => [
                'testMarker' => '/* testParensNoOwnerInForCondition */',
            ],
            'parens with owner: match'                                        => [
                'testMarker' => '/* testParensOwnerMatch */',
            ],
            'parens with owner: array'                                        => [
                'testMarker' => '/* testParensOwnerArray */',
            ],
            'parens without owner in array; function call with & in callable' => [
                'testMarker' => '/* testParensNoOwnerFunctionCallWithAmpersandInCallable */',
            ],
            'parens with owner: fn; & in return value'                        => [
                'testMarker' => '/* testParensOwnerArrowFn */',
            ],
            'parens with owner: list with reference vars'                     => [
                'testMarker' => '/* testParensOwnerListWithRefVars */',
            ],
            'parens without owner, function call with DNF look-a-like param'  => [
                'testMarker' => '/* testParensNoOwnerFunctionCallwithDNFLookALikeParam */',
            ],
            'parens without owner, function call, named param'                => [
                'testMarker' => '/* testParensNoOwnerFunctionCallWithDNFLookALikeNamedParamPlain */',
            ],
            'parens without owner, function call, named param + bitwise or'   => [
                'testMarker' => '/* testParensNoOwnerFunctionCallWithDNFLookALikeNamedParamUnion */',
            ],
            'parens without owner, function call, named param + bitwise and'  => [
                'testMarker' => '/* testParensNoOwnerFunctionCallWithDNFLookALikeNamedParamIntersect */',
            ],
            'parens without owner, function call in named param'              => [
                'testMarker' => '/* testParensNoOwnerFunctionCallInNamedParam */',
            ],
            'parens with owner: fn; in named param'                           => [
                'testMarker' => '/* testParensOwnerArrowFunctionInNamedParam */',
            ],
            'parens without owner, function call in named param arrow return' => [
                'testMarker' => '/* testParensNoOwnerFunctionCallInArrowFnReturn */',
            ],
            'parens with owner: closure; in named param'                      => [
                'testMarker' => '/* testParensOwnerClosureInNamedParam */',
            ],
            'parens without owner, function call, named param closure return' => [
                'testMarker' => '/* testParensNoOwnerFunctionCallInClosureReturn */',
            ],
            'parens with owner: switch condition'                             => [
                'testMarker' => '/* testSwitchControlStructureCondition */',
            ],
            'parens without owner in switch-case condition'                   => [
                'testMarker' => '/* testFunctionCallInSwitchCaseCondition */',
            ],
            'parens without owner in switch-case body'                        => [
                'testMarker' => '/* testFunctionCallInSwitchCaseBody */',
            ],
            'parens without owner in switch-default body'                     => [
                'testMarker' => '/* testFunctionCallInSwitchDefaultBody */',
            ],
            'parens with owner: if condition, alternative syntax'             => [
                'testMarker' => '/* testIfAlternativeSyntaxCondition */',
            ],
            'parens without owner in if body, alternative syntax'             => [
                'testMarker' => '/* testFunctionCallInIfBody */',
            ],
            'parens with owner: elseif condition, alternative syntax'         => [
                'testMarker' => '/* testElseIfAlternativeSyntaxCondition */',
            ],
            'parens without owner in elseif body, alternative syntax'         => [
                'testMarker' => '/* testFunctionCallInElseIfBody */',
            ],
            'parens without owner in else body, alternative syntax'           => [
                'testMarker' => '/* testFunctionCallInElseBody */',
            ],
            'parens without owner in goto body'                               => [
                'testMarker' => '/* testFunctionCallInGotoBody */',
            ],
            'parens with owner: while condition, alternative syntax'          => [
                'testMarker' => '/* testWhileAlternativeSyntaxCondition */',
            ],
            'parens without owner in while body, alternative syntax'          => [
                'testMarker' => '/* testFunctionCallInWhileBody */',
            ],
            'parens with owner: for condition, alternative syntax'            => [
                'testMarker' => '/* testForAlternativeSyntaxCondition */',
            ],
            'parens without owner in for body, alternative syntax'            => [
                'testMarker' => '/* testFunctionCallInForBody */',
            ],
            'parens with owner: foreach condition, alternative syntax'        => [
                'testMarker' => '/* testForEachAlternativeSyntaxCondition */',
            ],
            'parens without owner in foreach body, alternative syntax'        => [
                'testMarker' => '/* testFunctionCallInForeachBody */',
            ],

            'parens without owner in OO const default value'                  => [
                'testMarker' => '/* testParensNoOwnerOOConstDefaultValue */',
            ],
            'parens without owner in property default 1'                      => [
                'testMarker' => '/* testParensNoOwnerPropertyDefaultValue1 */',
            ],
            'parens without owner in property default 2'                      => [
                'testMarker' => '/* testParensNoOwnerPropertyDefaultValue2 */',
            ],
            'parens without owner in param default value'                     => [
                'testMarker' => '/* testParensNoOwnerParamDefaultValue */',
            ],
            'parens without owner in return statement 1'                      => [
                'testMarker' => '/* testParensNoOwnerInReturnValue1 */',
            ],
            'parens without owner in return statement 2'                      => [
                'testMarker' => '/* testParensNoOwnerInReturnValue2 */',
            ],
            'parens without owner in return statement 3'                      => [
                'testMarker' => '/* testParensNoOwnerInReturnValue3 */',
            ],
            'parens with owner: closure; & in default value'                  => [
                'testMarker' => '/* testParensOwnerClosureAmpersandInDefaultValue */',
            ],
            'parens with owner: fn; dnf used within'                          => [
                'testMarker'      => '/* testParensOwnerArrowDNFUsedWithin */',
                'skipCheckInside' => true,
            ],
            'parens without owner: default value for param in arrow function' => [
                'testMarker' => '/* testParensNoOwnerAmpersandInDefaultValue */',
            ],
            'parens without owner in arrow function return expression'        => [
                'testMarker' => '/* testParensNoOwnerInArrowReturnExpression */',
            ],
        ];

    }//end dataNormalParentheses()


    /**
     * Test that parentheses when used in a DNF type declaration are correctly tokenized.
     *
     * Includes verifying that:
     * - the tokens between the parentheses all have a "nested_parenthesis" key.
     * - all ampersands between the parentheses are tokenized as T_TYPE_INTERSECTION.
     *
     * @param string $testMarker The comment prefacing the target token.
     *
     * @dataProvider dataDNFTypeParentheses
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testDNFTypeParentheses($testMarker)
    {
        $tokens = $this->phpcsFile->getTokens();

        $openPtr = $this->getTargetToken($testMarker, [T_OPEN_PARENTHESIS, T_TYPE_OPEN_PARENTHESIS]);
        $opener  = $tokens[$openPtr];

        $this->assertSame('(', $opener['content'], 'Content of type open parenthesis is not "("');
        $this->assertSame(T_TYPE_OPEN_PARENTHESIS, $opener['code'], 'Token tokenized as '.$opener['type'].', not T_TYPE_OPEN_PARENTHESIS (code)');
        $this->assertSame('T_TYPE_OPEN_PARENTHESIS', $opener['type'], 'Token tokenized as '.$opener['type'].', not T_TYPE_OPEN_PARENTHESIS (type)');

        $closePtr = $opener['parenthesis_closer'];
        $closer   = $tokens[$closePtr];

        $this->assertSame(')', $closer['content'], 'Content of type close parenthesis is not ")"');
        $this->assertSame(T_TYPE_CLOSE_PARENTHESIS, $closer['code'], 'Token tokenized as '.$closer['type'].', not T_TYPE_CLOSE_PARENTHESIS (code)');
        $this->assertSame('T_TYPE_CLOSE_PARENTHESIS', $closer['type'], 'Token tokenized as '.$closer['type'].', not T_TYPE_CLOSE_PARENTHESIS (type)');

        $intersectionCount = 0;
        for ($i = ($openPtr + 1); $i < $closePtr; $i++) {
            if ($tokens[$i]['content'] === '&') {
                $this->assertSame(
                    T_TYPE_INTERSECTION,
                    $tokens[$i]['code'],
                    'Token tokenized as '.$tokens[$i]['type'].', not T_TYPE_INTERSECTION (code)'
                );
                $this->assertSame(
                    'T_TYPE_INTERSECTION',
                    $tokens[$i]['type'],
                    'Token tokenized as '.$tokens[$i]['type'].', not T_TYPE_INTERSECTION (type)'
                );
                ++$intersectionCount;
            }

            // Not valid, but that's irrelevant for the tokenization.
            if ($tokens[$i]['content'] === '|') {
                $this->assertSame(T_TYPE_UNION, $tokens[$i]['code'], 'Token tokenized as '.$tokens[$i]['type'].', not T_TYPE_UNION (code)');
                $this->assertSame('T_TYPE_UNION', $tokens[$i]['type'], 'Token tokenized as '.$tokens[$i]['type'].', not T_TYPE_UNION (type)');

                // For the purposes of this test, presume it was intended as an intersection.
                ++$intersectionCount;
            }
        }//end for

        $this->assertGreaterThanOrEqual(1, $intersectionCount, 'Did not find an intersection "&" between the DNF type parentheses');

        $before = $this->phpcsFile->findPrevious(Tokens::$emptyTokens, ($openPtr - 1), null, true);
        if ($before !== false && $tokens[$before]['content'] === '|') {
            $this->assertSame(
                T_TYPE_UNION,
                $tokens[$before]['code'],
                'Token before tokenized as '.$tokens[$before]['type'].', not T_TYPE_UNION (code)'
            );
            $this->assertSame(
                'T_TYPE_UNION',
                $tokens[$before]['type'],
                'Token before tokenized as '.$tokens[$before]['type'].', not T_TYPE_UNION (type)'
            );
        }

        // Invalid, but that's not relevant for the tokenization.
        if ($before !== false && $tokens[$before]['content'] === '?') {
            $this->assertSame(
                T_NULLABLE,
                $tokens[$before]['code'],
                'Token before tokenized as '.$tokens[$before]['type'].', not T_NULLABLE (code)'
            );
            $this->assertSame(
                'T_NULLABLE',
                $tokens[$before]['type'],
                'Token before tokenized as '.$tokens[$before]['type'].', not T_NULLABLE (type)'
            );
        }

        $after = $this->phpcsFile->findNext(Tokens::$emptyTokens, ($closePtr + 1), null, true);
        if ($after !== false && $tokens[$after]['content'] === '|') {
            $this->assertSame(
                T_TYPE_UNION,
                $tokens[$after]['code'],
                'Token after tokenized as '.$tokens[$after]['type'].', not T_TYPE_UNION (code)'
            );
            $this->assertSame(
                'T_TYPE_UNION',
                $tokens[$after]['type'],
                'Token after tokenized as '.$tokens[$after]['type'].', not T_TYPE_UNION (type)'
            );
        }

    }//end testDNFTypeParentheses()


    /**
     * Data provider.
     *
     * @see testDNFTypeParentheses()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataDNFTypeParentheses()
    {
        return [
            'arrow function return type: in named parameter'              => [
                'testMarker' => '/* testDNFTypeArrowFnReturnInNamedParam */',
            ],
            'closure return type: in named parameter'                     => [
                'testMarker' => '/* testDNFTypeClosureReturnInNamedParam */',
            ],

            'OO const type: unqualified classes'                          => [
                'testMarker' => '/* testDNFTypeOOConstUnqualifiedClasses */',
            ],
            'OO const type: modifiers in reverse order'                   => [
                'testMarker' => '/* testDNFTypeOOConstReverseModifierOrder */',
            ],
            'OO const type: multi-dnf part 1'                             => [
                'testMarker' => '/* testDNFTypeOOConstMulti1 */',
            ],
            'OO const type: multi-dnf part 2'                             => [
                'testMarker' => '/* testDNFTypeOOConstMulti2 */',
            ],
            'OO const type: multi-dnf part 3'                             => [
                'testMarker' => '/* testDNFTypeOOConstMulti3 */',
            ],
            'OO const type: namespace relative classes'                   => [
                'testMarker' => '/* testDNFTypeOOConstNamespaceRelative */',
            ],
            'OO const type: partially qualified classes'                  => [
                'testMarker' => '/* testDNFTypeOOConstPartiallyQualified */',
            ],
            'OO const type: fully qualified classes'                      => [
                'testMarker' => '/* testDNFTypeOOConstFullyQualified */',
            ],

            'OO property type: unqualified classes'                       => [
                'testMarker' => '/* testDNFTypePropertyUnqualifiedClasses */',
            ],
            'OO property type: modifiers in reverse order'                => [
                'testMarker' => '/* testDNFTypePropertyReverseModifierOrder */',
            ],
            'OO property type: multi-dnf namespace relative classes'      => [
                'testMarker' => '/* testDNFTypePropertyMultiNamespaceRelative */',
            ],
            'OO property type: multi-dnf partially qualified classes'     => [
                'testMarker' => '/* testDNFTypePropertyMultiPartiallyQualified */',
            ],
            'OO property type: multi-dnf fully qualified classes'         => [
                'testMarker' => '/* testDNFTypePropertyMultiFullyQualified */',
            ],
            'OO property type: multi-dnf with readonly keyword 1'         => [
                'testMarker' => '/* testDNFTypePropertyWithReadOnlyKeyword1 */',
            ],
            'OO property type: multi-dnf with readonly keyword 2'         => [
                'testMarker' => '/* testDNFTypePropertyWithReadOnlyKeyword2 */',
            ],
            'OO property type: with static and readonly keywords'         => [
                'testMarker' => '/* testDNFTypePropertyWithStaticAndReadOnlyKeywords */',
            ],
            'OO property type: with only static keyword'                  => [
                'testMarker' => '/* testDNFTypePropertyWithOnlyStaticKeyword */',
            ],
            'OO property type: with only final keyword'                   => [
                'testMarker' => '/* testDNFTypeWithPHP84FinalKeyword */',
            ],
            'OO property type: with final and static keyword'             => [
                'testMarker' => '/* testDNFTypeWithPHP84FinalKeywordAndStatic */',
            ],
            'OO property type: asymmetric visibility, private(set)'       => [
                'testMarker'  => '/* testDNFTypePropertyWithPrivateSet */',
            ],
            'OO property type: asymmetric vis, public private(set)'       => [
                'testMarker'  => '/* testDNFTypePropertyWithPublicPrivateSet */',
            ],
            'OO property type: asymmetric visibility, protected(set)'     => [
                'testMarker'  => '/* testDNFTypePropertyWithProtectedSet */',
            ],
            'OO property type: asymmetric vis, public protected(set)'     => [
                'testMarker'  => '/* testDNFTypePropertyWithPublicProtectedSet */',
            ],
            'OO method param type: first param'                           => [
                'testMarker' => '/* testDNFTypeParam1WithAttribute */',
            ],
            'OO method param type: second param, first DNF'               => [
                'testMarker' => '/* testDNFTypeParam2 */',
            ],
            'OO method param type: second param, second DNF'              => [
                'testMarker' => '/* testDNFTypeParam3 */',
            ],
            'OO method param type: namespace relative classes'            => [
                'testMarker' => '/* testDNFTypeParamNamespaceRelative */',
            ],
            'OO method param type: partially qualified classes'           => [
                'testMarker' => '/* testDNFTypeParamPartiallyQualified */',
            ],
            'OO method param type: fully qualified classes'               => [
                'testMarker' => '/* testDNFTypeParamFullyQualified */',
            ],
            'Constructor property promotion with multi DNF 1'             => [
                'testMarker' => '/* testDNFTypeConstructorPropertyPromotion1 */',
            ],
            'Constructor property promotion with multi DNF 2'             => [
                'testMarker' => '/* testDNFTypeConstructorPropertyPromotion2 */',
            ],
            'OO method return type: multi DNF 1'                          => [
                'testMarker' => '/* testDNFTypeReturnType1 */',
            ],
            'OO method return type: multi DNF 2'                          => [
                'testMarker' => '/* testDNFTypeReturnType2 */',
            ],
            'OO abstract method return type: multi DNF 1'                 => [
                'testMarker' => '/* testDNFTypeAbstractMethodReturnType1 */',
            ],
            'OO abstract method return type: multi DNF 2'                 => [
                'testMarker' => '/* testDNFTypeAbstractMethodReturnType2 */',
            ],
            'OO method return type: namespace relative classes'           => [
                'testMarker' => '/* testDNFTypeReturnTypeNamespaceRelative */',
            ],
            'OO method return type: partially qualified classes'          => [
                'testMarker' => '/* testDNFTypeReturnPartiallyQualified */',
            ],
            'OO method return type: fully qualified classes'              => [
                'testMarker' => '/* testDNFTypeReturnFullyQualified */',
            ],
            'function param type: with reference'                         => [
                'testMarker' => '/* testDNFTypeWithReference */',
            ],
            'function param type: with spread'                            => [
                'testMarker' => '/* testDNFTypeWithSpreadOperator */',
            ],
            'closure param type: with illegal nullable'                   => [
                'testMarker' => '/* testDNFTypeClosureParamIllegalNullable */',
            ],
            'closure return type'                                         => [
                'testMarker' => '/* testDNFTypeClosureReturn */',
            ],
            'closure with use return type'                                => [
                'testMarker' => '/* testDNFTypeClosureWithUseReturn */',
            ],

            'arrow function param type'                                   => [
                'testMarker' => '/* testDNFTypeArrowParam */',
            ],
            'arrow function return type'                                  => [
                'testMarker' => '/* testDNFTypeArrowReturnType */',
            ],
            'arrow function param type with return by ref'                => [
                'testMarker' => '/* testDNFTypeArrowParamWithReturnByRef */',
            ],

            'illegal syntax: unnecessary parentheses (no union)'          => [
                'testMarker' => '/* testDNFTypeParamIllegalUnnecessaryParens */',
            ],
            'illegal syntax: union within parentheses, intersect outside' => [
                'testMarker' => '/* testDNFTypeParamIllegalIntersectUnionReversed */',
            ],
            'illegal syntax: nested parentheses'                          => [
                'testMarker' => '/* testDNFTypeParamIllegalNestedParens */',
            ],
        ];

    }//end dataDNFTypeParentheses()


}//end class
