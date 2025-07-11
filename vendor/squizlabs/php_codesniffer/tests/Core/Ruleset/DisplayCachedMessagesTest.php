<?php
/**
 * Test error handling for the Ruleset.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Ruleset;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHP_CodeSniffer\Tests\Core\Ruleset\AbstractRulesetTestCase;
use PHP_CodeSniffer\Util\MessageCollector;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Test error handling for the Ruleset.
 *
 * Note: this is purely a unit test of the `displayCachedMessages()` method.
 * The errors themselves are mocked.
 *
 * @covers \PHP_CodeSniffer\Ruleset::displayCachedMessages
 */
final class DisplayCachedMessagesTest extends AbstractRulesetTestCase
{


    /**
     * Test that no exception nor output is generated when there are no cached messsages.
     *
     * @return void
     */
    public function testDisplayCachedMessagesStaysSilentWithoutErrors()
    {
        $ruleset = $this->getPlainRuleset();

        $this->expectOutputString('');

        $this->invokeDisplayCachedMessages($ruleset);

    }//end testDisplayCachedMessagesStaysSilentWithoutErrors()


    /**
     * Verify that blocking errors encountered while loading the ruleset(s) result in an exception being thrown.
     *
     * @param array<string, int> $messages The messages encountered.
     * @param string             $expected The expected function output to screen (via an internally handled exception).
     *
     * @dataProvider dataBlockingErrorsAreDisplayedViaAnException
     *
     * @return void
     */
    public function testBlockingErrorsAreDisplayedViaAnException($messages, $expected)
    {
        $ruleset = $this->getPlainRuleset();
        $this->mockCachedMessages($ruleset, $messages);

        $this->expectRuntimeExceptionMessage($expected);

        $this->invokeDisplayCachedMessages($ruleset);

    }//end testBlockingErrorsAreDisplayedViaAnException()


    /**
     * Data provider.
     *
     * @see testBlockingErrorsAreDisplayedViaAnException()
     *
     * @return array<string, array<string, string|array<string, int>>>
     */
    public static function dataBlockingErrorsAreDisplayedViaAnException()
    {
        return [
            'One error'                               => [
                'messages' => ['This is a serious blocking issue' => MessageCollector::ERROR],
                'expected' => 'ERROR: This is a serious blocking issue'.PHP_EOL.PHP_EOL,
            ],
            'Multiple blocking errors'                => [
                'messages' => [
                    'This is a serious blocking issue'        => MessageCollector::ERROR,
                    'And here is another one'                 => MessageCollector::ERROR,
                    'OMG, why do you think that would work ?' => MessageCollector::ERROR,
                ],
                // phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound -- Test readability is more important.
                'expected' => 'ERROR: This is a serious blocking issue'.PHP_EOL
                    . 'ERROR: And here is another one'.PHP_EOL
                    . 'ERROR: OMG, why do you think that would work ?'.PHP_EOL.PHP_EOL,
                // phpcs:enable
            ],
            'Mix of blocking and non-blocking errors' => [
                'messages' => [
                    'This is a serious blocking issue'                              => MessageCollector::ERROR,
                    'Something something deprecated and will be removed in v x.x.x' => MessageCollector::DEPRECATED,
                    'Careful, this may not be correct'                              => MessageCollector::NOTICE,
                ],
                // phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound -- Test readability is more important.
                'expected' => 'ERROR: This is a serious blocking issue'.PHP_EOL
                    . 'NOTICE: Careful, this may not be correct'.PHP_EOL
                    . 'DEPRECATED: Something something deprecated and will be removed in v x.x.x'.PHP_EOL.PHP_EOL,
                // phpcs:enable
            ],
        ];

    }//end dataBlockingErrorsAreDisplayedViaAnException()


    /**
     * Test display of non-blocking messages encountered while loading the ruleset(s).
     *
     * @param array<string, int> $messages The messages encountered.
     * @param string             $expected The expected function output to screen.
     *
     * @dataProvider dataNonBlockingErrorsGenerateOutput
     *
     * @return void
     */
    public function testNonBlockingErrorsGenerateOutput($messages, $expected)
    {
        $ruleset = $this->getPlainRuleset();
        $this->mockCachedMessages($ruleset, $messages);

        $this->expectOutputString($expected);

        $this->invokeDisplayCachedMessages($ruleset);

    }//end testNonBlockingErrorsGenerateOutput()


    /**
     * Data provider.
     *
     * @see testNonBlockingErrorsGenerateOutput()
     *
     * @return array<string, array<string, string|array<string, int>>>
     */
    public static function dataNonBlockingErrorsGenerateOutput()
    {
        return [
            'One deprecation'              => [
                'messages' => ['My deprecation message' => MessageCollector::DEPRECATED],
                'expected' => 'DEPRECATED: My deprecation message'.PHP_EOL.PHP_EOL,
            ],
            'One notice'                   => [
                'messages' => ['My notice message' => MessageCollector::NOTICE],
                'expected' => 'NOTICE: My notice message'.PHP_EOL.PHP_EOL,
            ],
            'One warning'                  => [
                'messages' => ['My warning message' => MessageCollector::WARNING],
                'expected' => 'WARNING: My warning message'.PHP_EOL.PHP_EOL,
            ],
            'Multiple non-blocking errors' => [
                'messages' => [
                    'Something something deprecated and will be removed in v x.x.x' => MessageCollector::DEPRECATED,
                    'Something is not supported and support may be removed'         => MessageCollector::WARNING,
                    'Some other deprecation notice'                                 => MessageCollector::DEPRECATED,
                    'Careful, this may not be correct'                              => MessageCollector::NOTICE,
                ],
                // phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound -- Test readability is more important.
                'expected' => 'WARNING: Something is not supported and support may be removed'.PHP_EOL
                    .'NOTICE: Careful, this may not be correct'.PHP_EOL
                    .'DEPRECATED: Something something deprecated and will be removed in v x.x.x'.PHP_EOL
                    .'DEPRECATED: Some other deprecation notice'.PHP_EOL.PHP_EOL,
                // phpcs:enable
            ],
        ];

    }//end dataNonBlockingErrorsGenerateOutput()


    /**
     * Test that blocking errors will always show, independently of specific command-line options being used.
     *
     * @param array<string> $configArgs Arguments to pass to the Config.
     *
     * @dataProvider dataSelectiveDisplayOfMessages
     *
     * @return void
     */
    public function testBlockingErrorsAlwaysShow($configArgs)
    {
        $config  = new ConfigDouble($configArgs);
        $ruleset = new Ruleset($config);

        $message = 'Some serious error';
        $errors  = [$message => MessageCollector::ERROR];
        $this->mockCachedMessages($ruleset, $errors);

        $this->expectRuntimeExceptionMessage('ERROR: '.$message.PHP_EOL);

        $this->invokeDisplayCachedMessages($ruleset);

    }//end testBlockingErrorsAlwaysShow()


    /**
     * Test that non-blocking messsages will not show when specific command-line options are being used.
     *
     * @param array<string> $configArgs Arguments to pass to the Config.
     *
     * @dataProvider dataSelectiveDisplayOfMessages
     *
     * @return void
     */
    public function testNonBlockingErrorsDoNotShowUnderSpecificCircumstances($configArgs)
    {
        $config  = new ConfigDouble($configArgs);
        $ruleset = new Ruleset($config);
        $this->mockCachedMessages($ruleset, ['Deprecation notice' => MessageCollector::DEPRECATED]);

        $this->expectOutputString('');

        $this->invokeDisplayCachedMessages($ruleset);

    }//end testNonBlockingErrorsDoNotShowUnderSpecificCircumstances()


    /**
     * Data provider.
     *
     * @see testBlockingErrorsAlwaysShow()
     * @see testNonBlockingErrorsDoNotShow()
     *
     * @return array<string, array<string, string|array<string>>>
     */
    public static function dataSelectiveDisplayOfMessages()
    {
        $data = [
            'Explain mode' => [
                'configArgs' => ['-e'],
            ],
            'Quiet mode'   => [
                'configArgs' => ['-q'],
            ],
        ];

        // Setting the `--generator` arg is only supported when running `phpcs`.
        if (PHP_CODESNIFFER_CBF === false) {
            $data['Documentation is requested'] = [
                'configArgs' => ['--generator=text'],
            ];
        }

        return $data;

    }//end dataSelectiveDisplayOfMessages()


    /**
     * Test Helper.
     *
     * @return \PHP_CodeSniffer\Ruleset
     */
    private function getPlainRuleset()
    {
        static $ruleset;

        if (isset($ruleset) === false) {
            $config  = new ConfigDouble();
            $ruleset = new Ruleset($config);
        }

        return $ruleset;

    }//end getPlainRuleset()


    /**
     * Add mock messages to the message cache.
     *
     * @param \PHP_CodeSniffer\Ruleset $ruleset  The ruleset object.
     * @param array<string, int>       $messages The messages to add to the message cache.
     *
     * @return void
     */
    private function mockCachedMessages(Ruleset $ruleset, $messages)
    {
        $reflProperty = new ReflectionProperty($ruleset, 'msgCache');
        $reflProperty->setAccessible(true);

        $msgCache = $reflProperty->getValue($ruleset);
        foreach ($messages as $msg => $type) {
            $msgCache->add($msg, $type);
        }

        $reflProperty->setAccessible(false);

    }//end mockCachedMessages()


    /**
     * Invoke the display of the cached messages.
     *
     * @param \PHP_CodeSniffer\Ruleset $ruleset The ruleset object.
     *
     * @return void
     */
    private function invokeDisplayCachedMessages(Ruleset $ruleset)
    {
        $reflMethod = new ReflectionMethod($ruleset, 'displayCachedMessages');
        $reflMethod->setAccessible(true);
        $reflMethod->invoke($ruleset);
        $reflMethod->setAccessible(false);

    }//end invokeDisplayCachedMessages()


}//end class
