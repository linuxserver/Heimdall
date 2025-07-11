<?php
/**
 * Tests the tab replacement logic.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\Tokenizer;

use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHPUnit\Framework\TestCase;

/**
 * Miscellaneous tests for tab replacement.
 *
 * @covers PHP_CodeSniffer\Tokenizers\Tokenizer::replaceTabsInToken
 */
final class ReplaceTabsInTokenMiscTest extends TestCase
{


    /**
     * Test that when no tab width is set or passed, the tab width will be set to 1.
     *
     * @return void
     */
    public function testTabWidthNotSet()
    {
        $config  = new ConfigDouble();
        $ruleset = new Ruleset($config);

        $content   = <<<EOD
<?php
		echo 'foo';
EOD;
        $phpcsFile = new DummyFile($content, $ruleset, $config);
        $phpcsFile->parse();

        $tokens = $phpcsFile->getTokens();
        $target = $phpcsFile->findNext(T_WHITESPACE, 0);

        // Verify initial state.
        $this->assertTrue(is_int($target), 'Target token was not found');
        $this->assertSame('		', $tokens[$target]['content'], 'Content after initial parsing does not contain tabs');
        $this->assertSame(2, $tokens[$target]['length'], 'Length after initial parsing is not as expected');
        $this->assertArrayNotHasKey('orig_content', $tokens[$target], "Key 'orig_content' found in the initial token array.");

        $phpcsFile->tokenizer->replaceTabsInToken($tokens[$target]);

        // Verify tab replacement.
        $this->assertSame('  ', $tokens[$target]['content'], 'Content after tab replacement is not as expected');
        $this->assertSame(2, $tokens[$target]['length'], 'Length after tab replacement is not as expected');
        $this->assertArrayHasKey('orig_content', $tokens[$target], "Key 'orig_content' not found in the token array.");

    }//end testTabWidthNotSet()


    /**
     * Test that the length calculation handles text in non-ascii encodings correctly.
     *
     * @requires extension iconv
     *
     * @return void
     */
    public function testLengthSettingRespectsEncoding()
    {
        $config           = new ConfigDouble();
        $config->tabWidth = 4;
        $ruleset          = new Ruleset($config);

        $content   = <<<EOD
<?php
echo 'пасха		пасха';
EOD;
        $phpcsFile = new DummyFile($content, $ruleset, $config);
        $phpcsFile->parse();

        $tokens = $phpcsFile->getTokens();
        $target = $phpcsFile->findNext(T_CONSTANT_ENCAPSED_STRING, 0);

        $this->assertTrue(is_int($target), 'Target token was not found');
        $this->assertSame("'пасха     пасха'", $tokens[$target]['content'], 'Content is not as expected');
        $this->assertSame(17, $tokens[$target]['length'], 'Length is not as expected');
        $this->assertArrayHasKey('orig_content', $tokens[$target], "Key 'orig_content' not found in the token array.");
        $this->assertSame("'пасха		пасха'", $tokens[$target]['orig_content'], 'Orig_content is not as expected');

    }//end testLengthSettingRespectsEncoding()


    /**
     * Test that the length calculation falls back to byte length if iconv detects an illegal character.
     *
     * @requires extension iconv
     *
     * @return void
     */
    public function testLengthSettingFallsBackToBytesWhenTextContainsIllegalChars()
    {
        $config           = new ConfigDouble();
        $config->tabWidth = 4;
        $ruleset          = new Ruleset($config);

        $content   = <<<EOD
<?php
echo "aa\xC3\xC3	\xC3\xB8aa";
EOD;
        $phpcsFile = new DummyFile($content, $ruleset, $config);
        $phpcsFile->parse();

        $tokens = $phpcsFile->getTokens();
        $target = $phpcsFile->findNext(T_CONSTANT_ENCAPSED_STRING, 0);

        $this->assertTrue(is_int($target), 'Target token was not found');
        $this->assertSame(11, $tokens[$target]['length'], 'Length is not as expected');
        $this->assertArrayHasKey('orig_content', $tokens[$target], "Key 'orig_content' not found in the token array.");

    }//end testLengthSettingFallsBackToBytesWhenTextContainsIllegalChars()


}//end class
