<?php
/**
 * Test the Ruleset::populateTokenListeners() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Ruleset;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHP_CodeSniffer\Tests\Core\Ruleset\AbstractRulesetTestCase;

/**
 * Test the Ruleset::populateTokenListeners() method shows a deprecation notice for sniffs supporting JS and/or CSS tokenizers.
 *
 * @covers \PHP_CodeSniffer\Ruleset::populateTokenListeners
 */
final class PopulateTokenListenersSupportedTokenizersTest extends AbstractRulesetTestCase
{

    /**
     * The Config object.
     *
     * @var \PHP_CodeSniffer\Config
     */
    private static $config;


    /**
     * Initialize the config and ruleset objects for this test.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function initializeConfig()
    {
        // Set up the ruleset.
        $standard     = __DIR__.'/PopulateTokenListenersSupportedTokenizersTest.xml';
        self::$config = new ConfigDouble(["--standard=$standard"]);

    }//end initializeConfig()


    /**
     * Verify that a deprecation notice is shown if a non-deprecated sniff supports the JS/CSS tokenizer(s).
     *
     * Additionally, this test verifies that:
     * - No deprecation notice is thrown if the complete sniff is deprecated.
     * - No deprecation notice is thrown when the sniff _also_ supports PHP.
     * - No deprecation notice is thrown when no tokenizers are supported (not sure why anyone would do that, but :shrug:).
     *
     * {@internal The test uses a data provider to verify the messages as the _order_ of the messages depends
     * on the OS on which the tests are run (order in which files are retrieved), which makes the order within the
     * complete message too unpredictable to test in one go.}
     *
     * @param string $expected The expected message output in regex format.
     *
     * @dataProvider dataDeprecatedTokenizersTriggerDeprecationNotice
     *
     * @return void
     */
    public function testDeprecatedTokenizersTriggerDeprecationNotice($expected)
    {
        $this->expectOutputRegex($expected);

        new Ruleset(self::$config);

    }//end testDeprecatedTokenizersTriggerDeprecationNotice()


    /**
     * Data provider.
     *
     * @see testDeprecatedTokenizersTriggerDeprecationNotice()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataDeprecatedTokenizersTriggerDeprecationNotice()
    {
        $cssJsDeprecated  = '`DEPRECATED: Scanning CSS/JS files is deprecated and support will be removed in PHP_CodeSniffer 4\.0\.\R';
        $cssJsDeprecated .= 'The %1$s sniff is listening for %2$s\.\R`';

        $customTokenizer  = '`DEPRECATED: Support for custom tokenizers will be removed in PHP_CodeSniffer 4\.0\.\R';
        $customTokenizer .= 'The %1$s sniff is listening for %2$s\.\R`';

        return [
            'Listens for CSS'                            => [
                'expected' => sprintf($cssJsDeprecated, 'TestStandard.SupportedTokenizers.ListensForCSS', 'CSS'),
            ],
            'Listens for JS'                             => [
                'expected' => sprintf($cssJsDeprecated, 'TestStandard.SupportedTokenizers.ListensForJS', 'JS'),
            ],
            'Listens for both CSS and JS'                => [
                'expected' => sprintf($cssJsDeprecated, 'TestStandard.SupportedTokenizers.ListensForCSSAndJS', 'CSS, JS'),
            ],
            'Listens for CSS and something unrecognized' => [
                'expected' => sprintf($cssJsDeprecated, 'TestStandard.SupportedTokenizers.ListensForCSSAndUnrecognized', 'CSS, Unrecognized'),
            ],
            'Listens for only unrecognized tokenizers'   => [
                'expected' => sprintf($customTokenizer, 'TestStandard.SupportedTokenizers.ListensForUnrecognizedTokenizers', 'SCSS, TypeScript'),
            ],
        ];

    }//end dataDeprecatedTokenizersTriggerDeprecationNotice()


}//end class
