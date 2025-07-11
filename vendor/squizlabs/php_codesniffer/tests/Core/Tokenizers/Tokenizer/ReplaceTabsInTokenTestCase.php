<?php
/**
 * Base class to test the tab replacement logic.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\Tokenizer;

use Exception;
use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

/**
 * Tab replacement test case.
 *
 * @covers PHP_CodeSniffer\Tokenizers\Tokenizer::replaceTabsInToken
 */
abstract class ReplaceTabsInTokenTestCase extends AbstractTokenizerTestCase
{

    /**
     * The name of the test case file used by this test.
     *
     * @var string
     */
    private static $caseFileName;


    /**
     * Make a copy the test case file we want to use for this test (as the file will be used by multiple tests).
     *
     * @beforeClass
     *
     * @return void
     *
     * @throws \Exception In case the base test case file would not be available.
     */
    public static function copyCaseFile()
    {
        $relativeCN         = str_replace(__NAMESPACE__.'\\', '', get_called_class());
        self::$caseFileName = __DIR__.DIRECTORY_SEPARATOR.$relativeCN.'.inc';

        $baseFileName = realpath(__DIR__.'/ReplaceTabsInTokenTest.inc');
        if (is_string($baseFileName) === false) {
            throw new Exception('Base test case file "ReplaceTabsInTokenTest.inc" not found');
        }

        if (copy($baseFileName, self::$caseFileName) === false) {
            throw new Exception(sprintf('Failed to copy test case file "ReplaceTabsInTokenTest.inc" to %s', self::$caseFileName));
        }

    }//end copyCaseFile()


    /**
     * Delete the copied test case file after use.
     *
     * @afterClass
     *
     * @return void
     */
    public static function deleteCaseFile()
    {
        @unlink(self::$caseFileName);

    }//end deleteCaseFile()


    /**
     * Verify that if a token not containing tabs would be passed to the replaceTabsInToken() method,
     * yes, the `orig_content` key is added, but no changes are made to the token `content` or `length` values.
     *
     * @param string                         $testMarker The comment prefacing the target token.
     * @param int|string                     $testTarget Token code to look for.
     * @param array<string, int|string|null> $expected   Expectations for the token array.
     * @param int                            $offset     Optional. Offset from the target token to get to the _real_ target.
     *                                                   This is specifically needed to target indentation whitespace.
     *
     * @dataProvider dataNoReplacementsAreMadeWhenNoTabsAreFound
     *
     * @return void
     */
    public function testNoReplacementsAreMadeWhenNoTabsAreFound($testMarker, $testTarget, $expected, $offset=0)
    {
        $tokens  = $this->phpcsFile->getTokens();
        $target  = $this->getTargetToken($testMarker, $testTarget);
        $target += $offset;

        foreach ($expected as $key => $value) {
            if ($key === 'orig_content' && $value === null) {
                $this->assertArrayNotHasKey($key, $tokens[$target], "Unexpected 'orig_content' key found in the token array.");
                continue;
            }

            $this->assertArrayHasKey($key, $tokens[$target], "Key $key not found in the token array.");
            $this->assertSame($value, $tokens[$target][$key], "Value for key $key does not match expectation.");
        }

    }//end testNoReplacementsAreMadeWhenNoTabsAreFound()


    /**
     * Data provider.
     *
     * @see testNoReplacementsAreMadeWhenNoTabsAreFound()
     *
     * @return array<string, array<string, int|string|array<string, int|string|null>>>
     */
    public static function dataNoReplacementsAreMadeWhenNoTabsAreFound()
    {
        return [
            'Indentation whitespace, only spaces'      => [
                'testMarker' => '/* testNoReplacementNeeded */',
                'testTarget' => T_WHITESPACE,
                'expected'   => [
                    'length'       => 4,
                    'content'      => '    ',
                    'orig_content' => null,
                ],
                'offset'     => 1,
            ],
            'Trailing comment not containing any tabs' => [
                'testMarker' => '/* testNoReplacementNeeded */',
                'testTarget' => T_COMMENT,
                'expected'   => [
                    'length'       => 35,
                    'content'      => '// Comment not containing any tabs.
',
                    'orig_content' => null,
                ],
            ],
        ];

    }//end dataNoReplacementsAreMadeWhenNoTabsAreFound()


    /**
     * Test tab replacement in tokens.
     *
     * @param string                         $testMarker The comment prefacing the target token.
     * @param int|string                     $testTarget Token code to look for.
     * @param array<string, int|string|null> $expected   Expectations for the token array.
     * @param int                            $offset     Optional. Offset from the target token to get to the _real_ target.
     *                                                   This is specifically needed to target indentation whitespace.
     *
     * @dataProvider dataTabReplacement
     *
     * @return void
     */
    public function testTabReplacement($testMarker, $testTarget, $expected, $offset=0)
    {
        $tokens  = $this->phpcsFile->getTokens();
        $target  = $this->getTargetToken($testMarker, $testTarget);
        $target += $offset;

        foreach ($expected as $key => $value) {
            if ($key === 'orig_content' && $value === null) {
                $this->assertArrayNotHasKey($key, $tokens[$target], "Unexpected 'orig_content' key found in the token array.");
                continue;
            }

            $this->assertArrayHasKey($key, $tokens[$target], "Key $key not found in the token array.");
            $this->assertSame($value, $tokens[$target][$key], "Value for key $key does not match expectation.");
        }

    }//end testTabReplacement()


    /**
     * Data provider.
     *
     * @see testTabReplacement()
     *
     * @return array<string, array<string, int|string|array<string, int|string>>>
     *
     * @throws \Exception When the getTabReplacementExpected() method doesn't provide data in the correct format.
     */
    public static function dataTabReplacement()
    {
        $data = [
            'Tab indentation'                                                      => [
                'testMarker' => '/* testTabIndentation */',
                'testTarget' => T_WHITESPACE,
            ],
            'Mixed tab/space indentation'                                          => [
                'testMarker' => '/* testMixedIndentation */',
                'testTarget' => T_WHITESPACE,
            ],
            'Inline: single tab in text string'                                    => [
                'testMarker' => '/* testInlineSingleTab */',
                'testTarget' => T_CONSTANT_ENCAPSED_STRING,
            ],
            'Inline: single tab between each word in text string'                  => [
                'testMarker' => '/* testInlineSingleTabBetweenEachWord */',
                'testTarget' => T_DOUBLE_QUOTED_STRING,
            ],
            'Inline: multiple tabs in heredoc'                                     => [
                'testMarker' => '/* testInlineMultiTab */',
                'testTarget' => T_HEREDOC,
            ],
            'Inline: multiple tabs between each word in nowdoc'                    => [
                'testMarker' => '/* testInlineMultipleTabsBetweenEachWord */',
                'testTarget' => T_NOWDOC,
            ],
            'Inline: mixed spaces/tabs in text string'                             => [
                'testMarker' => '/* testInlineMixedSpacesTabs */',
                'testTarget' => T_CONSTANT_ENCAPSED_STRING,
            ],
            'Inline: mixed spaces/tabs between each word in text string'           => [
                'testMarker' => '/* testInlineMixedSpacesTabsBetweenEachWord */',
                'testTarget' => T_DOUBLE_QUOTED_STRING,
            ],
            'Inline: tab becomes single space in comment (with tabwidth 4)'        => [
                'testMarker' => '/* testInlineSize1 */',
                'testTarget' => T_COMMENT,
            ],
            'Inline: tab becomes 2 spaces in comment (with tabwidth 4)'            => [
                'testMarker' => '/* testInlineSize2 */',
                'testTarget' => T_COMMENT,
            ],
            'Inline: tab becomes 3 spaces in doc comment string (with tabwidth 4)' => [
                'testMarker' => '/* testInlineSize3 */',
                'testTarget' => T_DOC_COMMENT_STRING,
            ],
            'Inline: tab becomes 4 spaces in comment (with tabwidth 4)'            => [
                'testMarker' => '/* testInlineSize4 */',
                'testTarget' => T_COMMENT,
            ],
        ];

        $expectations = static::getTabReplacementExpected();

        foreach ($data as $key => $value) {
            if (isset($expectations[$key]) === false || is_array($expectations[$key]) === false) {
                throw new Exception(
                    sprintf('Invalid getTabReplacementExpected() method. Missing expectation array for the "%s" test case', $key)
                );
            }

            if (isset($expectations[$key]['length'], $expectations[$key]['content']) === false
                || array_key_exists('orig_content', $expectations[$key]) === false
            ) {
                throw new Exception(
                    sprintf('Invalid expectation array for the "%s" test case. The array must contain the "length", "content" and "orig_content" keys', $key)
                );
            }

            $data[$key]['expected'] = $expectations[$key];
        }

        // Set offset for test cases targetting whitespace.
        $data['Tab indentation']['offset'] = 1;
        $data['Mixed tab/space indentation']['offset'] = 1;

        return $data;

    }//end dataTabReplacement()


    /**
     * Data provider helper.
     *
     * Should be declared in child classes to set the expectations for the token array.
     *
     * @see dataTabReplacement()
     *
     * @return array<string, array<string, int|string|null>>
     */
    abstract public static function getTabReplacementExpected();


}//end class
