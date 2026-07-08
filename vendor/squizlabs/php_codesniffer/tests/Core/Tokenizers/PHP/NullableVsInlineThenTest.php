<?php
/**
 * Tests the retokenization of ? to T_NULLABLE or T_INLINE_THEN.
 *
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/HEAD/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

/**
 * Tests the retokenization of ? to T_NULLABLE or T_INLINE_THEN.
 *
 * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
 */
final class NullableVsInlineThenTest extends AbstractTokenizerTestCase
{


    /**
     * Test that the ? at the start of type declarations is retokenized to T_NULLABLE.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataNullable
     *
     * @return void
     */
    public function testNullable($testMarker)
    {
        $tokens     = $this->phpcsFile->getTokens();
        $target     = $this->getTargetToken($testMarker, [T_NULLABLE, T_INLINE_THEN]);
        $tokenArray = $tokens[$target];

        $this->assertSame(T_NULLABLE, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_NULLABLE (code)');
        $this->assertSame('T_NULLABLE', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_NULLABLE (type)');

    }//end testNullable()


    /**
     * Data provider.
     *
     * @see testNullable()
     *
     * @return array<string, array<string>>
     */
    public static function dataNullable()
    {
        return [
            'property declaration, readonly, no visibility'                 => ['/* testNullableReadonlyOnly */'],
            'property declaration, private set'                             => ['/* testNullablePrivateSet */'],
            'property declaration, public and protected set'                => ['/* testNullablePublicProtectedSet */'],
            'property declaration, final, no visibility'                    => ['/* testNullableFinalOnly */'],
            'property declaration, abstract, no visibility'                 => ['/* testNullableAbstractOnly */'],

            'closure param type, nullable int'                              => ['/* testClosureParamTypeNullableInt */'],
            'closure param type, nullable callable'                         => ['/* testClosureParamTypeNullableCallable */'],
            'closure param type, nullable string with comment, issue #1216' => ['/* testClosureParamTypeNullableStringWithAttributeAndSlashComment */'],
            'closure return type, nullable int'                             => ['/* testClosureReturnTypeNullableInt */'],
            'function return type, nullable callable'                       => ['/* testFunctionReturnTypeNullableCallable */'],
        ];

    }//end dataNullable()


    /**
     * Test that a "?" when used as part of a ternary expression is tokenized as `T_INLINE_THEN`.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataInlineThen
     *
     * @return void
     */
    public function testInlineThen($testMarker)
    {
        $tokens     = $this->phpcsFile->getTokens();
        $target     = $this->getTargetToken($testMarker, [T_NULLABLE, T_INLINE_THEN]);
        $tokenArray = $tokens[$target];

        $this->assertSame(T_INLINE_THEN, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_INLINE_THEN (code)');
        $this->assertSame('T_INLINE_THEN', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_INLINE_THEN (type)');

    }//end testInlineThen()


    /**
     * Data provider.
     *
     * @see testInlineThen()
     *
     * @return array<string, array<string>>
     */
    public static function dataInlineThen()
    {
        return [
            'ternary in property default value'                            => ['/* testInlineThenInPropertyDefaultValue */'],

            'ternary ? followed by array declaration'                      => ['/* testInlineThenWithArrayDeclaration */'],

            'ternary ? followed by unqualified constant'                   => ['/* testInlineThenWithUnqualifiedNameAndNothingElse */'],
            'ternary ? followed by unqualified function call'              => ['/* testInlineThenWithUnqualifiedNameAndParens */'],
            'ternary ? followed by unqualified static method call'         => ['/* testInlineThenWithUnqualifiedNameAndDoubleColon */'],

            'ternary ? followed by fully qualified constant'               => ['/* testInlineThenWithFullyQualifiedNameAndNothingElse */'],
            'ternary ? followed by fully qualified function call'          => ['/* testInlineThenWithFullyQualifiedNameAndParens */'],
            'ternary ? followed by fully qualified static method call'     => ['/* testInlineThenWithFullyQualifiedNameAndDoubleColon */'],

            'ternary ? followed by partially qualified constant'           => ['/* testInlineThenWithPartiallyQualifiedNameAndNothingElse */'],
            'ternary ? followed by partially qualified function call'      => ['/* testInlineThenWithPartiallyQualifiedNameAndParens */'],
            'ternary ? followed by partially qualified static method call' => ['/* testInlineThenWithPartiallyQualifiedNameAndDoubleColon */'],

            'ternary ? followed by namespace relative constant'            => ['/* testInlineThenWithNamespaceRelativeNameAndNothingElse */'],
            'ternary ? followed by namespace relative function call'       => ['/* testInlineThenWithNamespaceRelativeNameAndParens */'],
            'ternary ? followed by namespace relative static method call'  => ['/* testInlineThenWithNamespaceRelativeNameAndDoubleColon */'],
        ];

    }//end dataInlineThen()


}//end class
