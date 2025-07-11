<?php
/**
 * Tests the tokenization of "yield from" tokens.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\Tokenizer;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

/**
 * Yield from token test.
 *
 * @covers PHP_CodeSniffer\Tokenizers\Tokenizer::createPositionMap
 */
final class CreatePositionMapYieldFromTest extends AbstractTokenizerTestCase
{


    /**
     * Verify that spaces/tabs in "yield from" tokens get the tab replacement treatment.
     *
     * @param string                         $testMarker The comment prefacing the target token.
     * @param array<string, int|string|null> $expected   Expectations for the token array.
     * @param string                         $content    Optional. The test token content to search for.
     *                                                   Defaults to null.
     *
     * @dataProvider dataYieldFromTabReplacement
     *
     * @return void
     */
    public function testYieldFromTabReplacement($testMarker, $expected, $content=null)
    {
        $tokens = $this->phpcsFile->getTokens();
        $target = $this->getTargetToken($testMarker, [T_YIELD_FROM], $content);

        foreach ($expected as $key => $value) {
            if ($key === 'orig_content' && $value === null) {
                $this->assertArrayNotHasKey($key, $tokens[$target], "Unexpected 'orig_content' key found in the token array.");
                continue;
            }

            $this->assertArrayHasKey($key, $tokens[$target], "Key $key not found in the token array.");
            $this->assertSame($value, $tokens[$target][$key], "Value for key $key does not match expectation.");
        }

    }//end testYieldFromTabReplacement()


    /**
     * Data provider.
     *
     * @see testYieldFromTabReplacement()
     *
     * @return array<string, array<string, string|array<string, int|string|null>>>
     */
    public static function dataYieldFromTabReplacement()
    {
        return [
            'Yield from, single line, single space'           => [
                'testMarker' => '/* testYieldFromHasSingleSpace */',
                'expected'   => [
                    'length'       => 10,
                    'content'      => 'yield from',
                    'orig_content' => null,
                ],
            ],
            'Yield from, single line, multiple spaces'        => [
                'testMarker' => '/* testYieldFromHasMultiSpace */',
                'expected'   => [
                    'length'       => 14,
                    'content'      => 'yield     from',
                    'orig_content' => null,
                ],
            ],
            'Yield from, single line, has tabs'               => [
                'testMarker' => '/* testYieldFromHasTabs */',
                'expected'   => [
                    'length'       => 16,
                    'content'      => 'yield       from',
                    'orig_content' => 'yield		from',
                ],
            ],
            'Yield from, single line, mix of tabs and spaces' => [
                'testMarker' => '/* testYieldFromMixedTabsSpaces */',
                'expected'   => [
                    'length'       => 20,
                    'content'      => 'Yield           From',
                    'orig_content' => 'Yield	   	 	From',
                ],
            ],
        ];

    }//end dataYieldFromTabReplacement()


}//end class
