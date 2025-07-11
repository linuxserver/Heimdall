<?php
/**
 * Tests the retokenization of ? to T_NULLABLE or T_INLINE_THEN.
 *
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
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
            'property declaration, readonly, no visibility'  => ['/* testNullableReadonlyOnly */'],
            'property declaration, private set'              => ['/* testNullablePrivateSet */'],
            'property declaration, public and protected set' => ['/* testNullablePublicProtectedSet */'],
        ];

    }//end dataNullable()


    /**
     * Test that "readonly" when not used as the keyword is still tokenized as `T_STRING`.
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
            'ternary in property default value' => ['/* testInlineThenInPropertyDefaultValue */'],
        ];

    }//end dataInlineThen()


}//end class
