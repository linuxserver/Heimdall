<?php
/**
 * Tests the support of PHP 8 attributes
 *
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/HEAD/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

final class AttributesParseError4Test extends AbstractTokenizerTestCase
{


    /**
     * Test that invalid attribute (or comment starting with #[ and without ]) are parsed correctly
     * and that tokens "within" the attribute are not removed.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
     * @covers PHP_CodeSniffer\Tokenizers\PHP::findCloser
     * @covers PHP_CodeSniffer\Tokenizers\PHP::parsePhpAttribute
     *
     * @return void
     */
    public function testInvalidAttribute()
    {
        $tokens = $this->phpcsFile->getTokens();

        $attribute = $this->getTargetToken('/* testLiveCoding */', T_ATTRIBUTE);

        $expectedTokenCodesAttribute1 = [
            'T_ATTRIBUTE',
            'T_STRING',
            'T_ATTRIBUTE_END',
            'T_WHITESPACE',
        ];

        $lengthAttribute1 = count($expectedTokenCodesAttribute1);

        $map = array_map(
            function ($token) use ($attribute, $lengthAttribute1) {
                if ($token['code'] !== T_WHITESPACE) {
                    $this->assertArrayHasKey('attribute_closer', $token);
                    $this->assertSame(($attribute + 2), $token['attribute_closer']);
                }

                return $token['type'];
            },
            array_slice($tokens, $attribute, $lengthAttribute1)
        );

        $this->assertSame($expectedTokenCodesAttribute1, $map);

        $expectedTokenCodesAttribute2 = [
            'T_ATTRIBUTE',
            'T_STRING',
            'T_WHITESPACE',
            'T_FUNCTION',
        ];

        $lengthAttribute2 = count($expectedTokenCodesAttribute2);

        $map = array_map(
            function ($token) {
                if ($token['code'] === T_ATTRIBUTE) {
                    $this->assertArrayHasKey('attribute_closer', $token);
                    $this->assertNull($token['attribute_closer']);
                } else {
                    $this->assertArrayNotHasKey('attribute_closer', $token);
                }

                return $token['type'];
            },
            array_slice($tokens, ($attribute + $lengthAttribute1), $lengthAttribute2)
        );

        $this->assertSame($expectedTokenCodesAttribute2, $map);

    }//end testInvalidAttribute()


}//end class
