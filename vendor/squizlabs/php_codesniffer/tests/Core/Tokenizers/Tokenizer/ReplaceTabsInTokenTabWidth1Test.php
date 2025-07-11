<?php
/**
 * Tests the tab replacement logic.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\Tokenizer;

/**
 * Tab replacement test using tab width 1.
 *
 * @covers PHP_CodeSniffer\Tokenizers\Tokenizer::replaceTabsInToken
 */
final class ReplaceTabsInTokenTabWidth1Test extends ReplaceTabsInTokenTestCase
{

    /**
     * The tab width setting to use when tokenizing the file.
     *
     * @var integer
     */
    protected $tabWidth = 1;


    /**
     * Data provider helper.
     *
     * @see ReplaceTabsInTokenTestCase::dataTabReplacement()
     *
     * @return array<string, array<string, int|string>>
     */
    public static function getTabReplacementExpected()
    {
        return [
            'Tab indentation'                                                      => [
                'length'       => 2,
                'content'      => '  ',
                'orig_content' => '		',
            ],
            'Mixed tab/space indentation'                                          => [
                'length'       => 3,
                'content'      => '   ',
                'orig_content' => '	  ',
            ],
            'Inline: single tab in text string'                                    => [
                'length'       => 15,
                'content'      => "'tab separated'",
                'orig_content' => "'tab	separated'",
            ],
            'Inline: single tab between each word in text string'                  => [
                'length'       => 24,
                'content'      => '"tab $between each word"',
                'orig_content' => '"tab	$between	each	word"',
            ],
            'Inline: multiple tabs in heredoc'                                     => [
                'length'       => 15,
                'content'      => 'tab   separated
',
                'orig_content' => 'tab			separated
',
            ],
            'Inline: multiple tabs between each word in nowdoc'                    => [
                'length'       => 27,
                'content'      => 'tab  between    each   word
',
                'orig_content' => 'tab		between				each			word
',
            ],
            'Inline: mixed spaces/tabs in text string'                             => [
                'length'       => 20,
                'content'      => "'tab      separated'",
                'orig_content' => "'tab 	  		separated'",
            ],
            'Inline: mixed spaces/tabs between each word in text string'           => [
                'length'       => 31,
                'content'      => '"tab  $between   each     word"',
                'orig_content' => '"tab	 $between  	each	   	word"',
            ],
            'Inline: tab becomes single space in comment (with tabwidth 4)'        => [
                'length'       => 50,
                'content'      => '// -123 With tabwidth 4, the tab size should be 1.
',
                'orig_content' => '// -123	With tabwidth 4, the tab size should be 1.
',
            ],
            'Inline: tab becomes 2 spaces in comment (with tabwidth 4)'            => [
                'length'       => 52,
                'content'      => '/* -12 With tabwidth 4, the tab size should be 2. */',
                'orig_content' => '/* -12	With tabwidth 4, the tab size should be 2. */',
            ],
            'Inline: tab becomes 3 spaces in doc comment string (with tabwidth 4)' => [
                'length'       => 45,
                'content'      => '-1 With tabwidth 4, the tab size should be 3.',
                'orig_content' => '-1	With tabwidth 4, the tab size should be 3.',
            ],
            'Inline: tab becomes 4 spaces in comment (with tabwidth 4)'            => [
                'length'       => 47,
                'content'      => '// - With tabwidth 4, the tab size should be 4.
',
                'orig_content' => '// -	With tabwidth 4, the tab size should be 4.
',
            ],
        ];

    }//end getTabReplacementExpected()


}//end class
