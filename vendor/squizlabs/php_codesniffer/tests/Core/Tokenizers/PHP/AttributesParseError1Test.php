<?php
/**
 * Tests the support of PHP 8 attributes
 *
 * @author    Alessandro Chitolina <alekitto@gmail.com>
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019-2023 Squiz Pty Ltd (ABN 77 084 670 600)
 * @copyright 2023 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/HEAD/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

final class AttributesParseError1Test extends AbstractTokenizerTestCase
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

        $attribute = $this->getTargetToken('/* testInvalidAttribute */', T_ATTRIBUTE);

        $this->assertArrayHasKey('attribute_closer', $tokens[$attribute]);
        $this->assertNull($tokens[$attribute]['attribute_closer']);

        $expectedTokenCodes = [
            'T_ATTRIBUTE',
            'T_STRING',
            'T_WHITESPACE',
            'T_FUNCTION',
        ];
        $length = count($expectedTokenCodes);

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
            array_slice($tokens, $attribute, $length)
        );

        $this->assertSame($expectedTokenCodes, $map);

    }//end testInvalidAttribute()


}//end class
