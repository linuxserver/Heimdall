<?php
/**
 * Tests the message collecting functionality.
 *
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\MessageCollector;

use PHP_CodeSniffer\Util\MessageCollector;
use PHPUnit\Framework\TestCase;

/**
 * Tests the message caching and display functionality.
 *
 * @covers \PHP_CodeSniffer\Util\MessageCollector
 */
final class MessageCollectorTest extends TestCase
{


    /**
     * Verify that non-string "messages" are rejected with an exception.
     *
     * @param mixed $message The invalid "message" to add.
     *
     * @dataProvider dataAddingNonStringMessageResultsInException
     *
     * @return void
     */
    public function testAddingNonStringMessageResultsInException($message)
    {
        $exception    = 'InvalidArgumentException';
        $exceptionMsg = 'The $message should be of type string. Received: ';
        if (method_exists($this, 'expectException') === true) {
            // PHPUnit 5+.
            $this->expectException($exception);
            $this->expectExceptionMessage($exceptionMsg);
        } else {
            // PHPUnit 4.
            $this->setExpectedException($exception, $exceptionMsg);
        }

        $msgCollector = new MessageCollector();
        $msgCollector->add($message);

    }//end testAddingNonStringMessageResultsInException()


    /**
     * Data provider.
     *
     * @see testAddingNonStringMessageResultsInException()
     *
     * @return array<string, array<mixed>>
     */
    public static function dataAddingNonStringMessageResultsInException()
    {
        return [
            'null'    => [null],
            'boolean' => [true],
            'integer' => [10],
            'array'   => [['something' => 'incorrect']],
        ];

    }//end dataAddingNonStringMessageResultsInException()


    /**
     * Verify that passing a message type which is not one of the predefined types is rejected with an exception.
     *
     * @param mixed $type The invalid "type" to pass.
     *
     * @dataProvider dataAddingMessageWithUnsupportedMessageTypeResultsInException
     *
     * @return void
     */
    public function testAddingMessageWithUnsupportedMessageTypeResultsInException($type)
    {
        $exception    = 'InvalidArgumentException';
        $exceptionMsg = 'The message $type should be one of the predefined MessageCollector constants. Received: ';
        if (method_exists($this, 'expectException') === true) {
            // PHPUnit 5+.
            $this->expectException($exception);
            $this->expectExceptionMessage($exceptionMsg);
        } else {
            // PHPUnit 4.
            $this->setExpectedException($exception, $exceptionMsg);
        }

        $msgCollector = new MessageCollector();
        $msgCollector->add('Message', $type);

    }//end testAddingMessageWithUnsupportedMessageTypeResultsInException()


    /**
     * Data provider.
     *
     * @see testAddingMessageWithUnsupportedMessageTypeResultsInException()
     *
     * @return array<string, array<mixed>>
     */
    public static function dataAddingMessageWithUnsupportedMessageTypeResultsInException()
    {
        return [
            'null'                                                                        => [null],
            'boolean'                                                                     => [true],
            'string'                                                                      => ['DEPRECATED'],
            'integer which doesn\'t match any of the message type constants: -235'        => [-235],
            'integer which doesn\'t match any of the message type constants: 0'           => [0],
            'integer which doesn\'t match any of the message type constants: 3'           => [3],
            'integer which doesn\'t match any of the message type constants: 6'           => [6],
            'integer which doesn\'t match any of the message type constants: 123'         => [123],
            'integer which doesn\'t match any of the message type constants: PHP_INT_MAX' => [PHP_INT_MAX],
        ];

    }//end dataAddingMessageWithUnsupportedMessageTypeResultsInException()


    /**
     * Verify that the `containsBlockingErrors()` method correctly identifies whether the collected messages
     * include messages which are blocking (errors), or only include non-blocking (warnings, notices,
     * deprecations) messages.
     *
     * @param array<string, int> $messages The messages to display.
     *                                     Key is the message, value is the error level.
     * @param bool               $expected The expected function output.
     *
     * @dataProvider dataContainsBlockingErrors
     *
     * @return void
     */
    public function testContainsBlockingErrors($messages, $expected)
    {
        $msgCollector = new MessageCollector();
        $this->createErrorCache($msgCollector, $messages);

        $this->assertSame($expected, $msgCollector->containsBlockingErrors());

    }//end testContainsBlockingErrors()


    /**
     * Data provider.
     *
     * @see testContainsBlockingErrors()
     *
     * @return array<string, array<string, array<string, int>|bool>>
     */
    public static function dataContainsBlockingErrors()
    {
        return [
            'No messages'                               => [
                'messages' => [],
                'expected' => false,
            ],
            'Only non-blocking messages'                => [
                'messages' => [
                    'First message'  => MessageCollector::WARNING,
                    'Second message' => MessageCollector::NOTICE,
                    'Third message'  => MessageCollector::DEPRECATED,
                ],
                'expected' => false,
            ],
            'Only blocking messages'                    => [
                'messages' => [
                    'First message'  => MessageCollector::ERROR,
                    'Second message' => MessageCollector::ERROR,
                    'Third message'  => MessageCollector::ERROR,
                ],
                'expected' => true,
            ],
            'Mix of blocking and non-blocking messages' => [
                'messages' => [
                    'First message'  => MessageCollector::DEPRECATED,
                    'Second message' => MessageCollector::ERROR,
                    'Third message'  => MessageCollector::WARNING,
                ],
                'expected' => true,
            ],
        ];

    }//end dataContainsBlockingErrors()


    /**
     * Test displaying non-blocking messages only.
     *
     * Verifies that:
     * - Each message is prefixed with the appropriate prefix.
     * - The default message order is observed.
     * - The messages use the appropriate OS-specific EOL character.
     *
     * @param array<string, int> $messages The messages to display.
     *                                     Key is the message, value is the error level.
     * @param string             $expected The expected exception message.
     *
     * @dataProvider dataDisplayingNonBlockingMessages
     *
     * @return void
     */
    public function testDisplayingNonBlockingMessages($messages, $expected)
    {
        $msgCollector = new MessageCollector();
        $this->createErrorCache($msgCollector, $messages);

        $this->expectOutputString($expected);

        $msgCollector->display();

    }//end testDisplayingNonBlockingMessages()


    /**
     * Data provider.
     *
     * @see testDisplayingNonBlockingMessages()
     *
     * @return array<string, array<string, array<string, int>|string>>
     */
    public static function dataDisplayingNonBlockingMessages()
    {
        // phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound -- Test readability is more important.
        return [
            'No messages'           => [
                'messages' => [],
                'expected' => '',
            ],
            'One warning'           => [
                'messages' => [
                    'This is a warning' => MessageCollector::WARNING,
                ],
                'expected' => 'WARNING: This is a warning'.PHP_EOL.PHP_EOL,
            ],
            'One notice'            => [
                'messages' => [
                    'This is a notice' => MessageCollector::NOTICE,
                ],
                'expected' => 'NOTICE: This is a notice'.PHP_EOL.PHP_EOL,
            ],
            'One deprecation'       => [
                'messages' => [
                    'This is a deprecation' => MessageCollector::DEPRECATED,
                ],
                'expected' => 'DEPRECATED: This is a deprecation'.PHP_EOL.PHP_EOL,
            ],
            'Multiple warnings'     => [
                'messages' => [
                    'First warning'  => MessageCollector::WARNING,
                    'Second warning' => MessageCollector::WARNING,
                ],
                'expected' => 'WARNING: First warning'.PHP_EOL
                    .'WARNING: Second warning'.PHP_EOL.PHP_EOL,
            ],
            'Multiple notices'      => [
                'messages' => [
                    'First notice'  => MessageCollector::NOTICE,
                    'Second notice' => MessageCollector::NOTICE,
                    'Third notice'  => MessageCollector::NOTICE,
                ],
                'expected' => 'NOTICE: First notice'.PHP_EOL
                    .'NOTICE: Second notice'.PHP_EOL
                    .'NOTICE: Third notice'.PHP_EOL.PHP_EOL,
            ],
            'Multiple deprecations' => [
                'messages' => [
                    'First deprecation'  => MessageCollector::DEPRECATED,
                    'Second deprecation' => MessageCollector::DEPRECATED,
                ],
                'expected' => 'DEPRECATED: First deprecation'.PHP_EOL
                    .'DEPRECATED: Second deprecation'.PHP_EOL.PHP_EOL,
            ],
            'All together now'      => [
                'messages' => [
                    'First deprecation' => MessageCollector::DEPRECATED,
                    'Second warning'    => MessageCollector::WARNING,
                    'Third notice'      => MessageCollector::NOTICE,
                    'Fourth warning'    => MessageCollector::WARNING,
                ],
                'expected' => 'WARNING: Second warning'.PHP_EOL
                    .'WARNING: Fourth warning'.PHP_EOL
                    .'NOTICE: Third notice'.PHP_EOL
                    .'DEPRECATED: First deprecation'.PHP_EOL.PHP_EOL,
            ],
        ];
        // phpcs:enable

    }//end dataDisplayingNonBlockingMessages()


    /**
     * Test displaying message collections containing blocking messages.
     *
     * Verifies that:
     * - Each message is prefixed with the appropriate prefix.
     * - The default message order is observed.
     * - The messages use the appropriate OS-specific EOL character.
     *
     * @param array<string, int> $messages The messages to display.
     *                                     Key is the message, value is the error level.
     * @param string             $expected The expected exception message.
     *
     * @dataProvider dataDisplayingBlockingErrors
     *
     * @return void
     */
    public function testDisplayingBlockingErrors($messages, $expected)
    {
        $exception = 'PHP_CodeSniffer\Exceptions\RuntimeException';
        if (method_exists($this, 'expectException') === true) {
            // PHPUnit 5+.
            $this->expectException($exception);
            $this->expectExceptionMessage($expected);
        } else {
            // PHPUnit 4.
            $this->setExpectedException($exception, $expected);
        }

        $msgCollector = new MessageCollector();
        $this->createErrorCache($msgCollector, $messages);
        $msgCollector->display();

    }//end testDisplayingBlockingErrors()


    /**
     * Data provider.
     *
     * @see testDisplayingBlockingErrors()
     *
     * @return array<string, array<string, array<string, int>|string>>
     */
    public static function dataDisplayingBlockingErrors()
    {
        // phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound -- Test readability is more important.
        return [
            'Single error'                            => [
                'messages' => [
                    'This is an error' => MessageCollector::ERROR,
                ],
                'expected' => 'ERROR: This is an error'.PHP_EOL.PHP_EOL,
            ],
            'Multiple errors'                         => [
                'messages' => [
                    'First error'  => MessageCollector::ERROR,
                    'Second error' => MessageCollector::ERROR,
                ],
                'expected' => 'ERROR: First error'.PHP_EOL
                    .'ERROR: Second error'.PHP_EOL.PHP_EOL,
            ],
            'Errors mixed with non-blocking messages' => [
                'messages' => [
                    'First deprecation'   => MessageCollector::DEPRECATED,
                    'Second warning'      => MessageCollector::WARNING,
                    'Third notice'        => MessageCollector::NOTICE,
                    'Fourth error'        => MessageCollector::ERROR,
                    'Fifth warning'       => MessageCollector::WARNING,
                    'Sixth error'         => MessageCollector::ERROR,
                    'Seventh deprecation' => MessageCollector::DEPRECATED,
                ],
                'expected' => 'ERROR: Fourth error'.PHP_EOL
                    .'ERROR: Sixth error'.PHP_EOL
                    .'WARNING: Second warning'.PHP_EOL
                    .'WARNING: Fifth warning'.PHP_EOL
                    .'NOTICE: Third notice'.PHP_EOL
                    .'DEPRECATED: First deprecation'.PHP_EOL
                    .'DEPRECATED: Seventh deprecation'.PHP_EOL.PHP_EOL,
            ],
        ];
        // phpcs:enable

    }//end dataDisplayingBlockingErrors()


    /**
     * Verify and safeguard that adding the same message twice is accepted when messages have different error levels.
     *
     * Note: have multiple messages with the exact same text is not great for conveying information
     * to the end-user, but that's not the concern of the MessageCollector class.
     *
     * @return void
     */
    public function testNonUniqueMessagesWithDifferentErrorLevelAreAccepted()
    {
        $message      = 'Trying to add the same message twice';
        $msgCollector = new MessageCollector();
        $msgCollector->add($message, MessageCollector::NOTICE);
        $msgCollector->add($message, MessageCollector::WARNING);

        $expected  = 'WARNING: Trying to add the same message twice'.PHP_EOL;
        $expected .= 'NOTICE: Trying to add the same message twice'.PHP_EOL.PHP_EOL;
        $this->expectOutputString($expected);

        $msgCollector->display();

    }//end testNonUniqueMessagesWithDifferentErrorLevelAreAccepted()


    /**
     * Verify and safeguard that adding the same message twice is accepted when messages have the same error level.
     *
     * Note: have multiple messages with the exact same text is not great for conveying information
     * to the end-user, but that's not the concern of the MessageCollector class.
     *
     * @return void
     */
    public function testNonUniqueMessagesWithSameErrorLevelAreAccepted()
    {
        $message      = 'Trying to add the same message twice';
        $msgCollector = new MessageCollector();
        $msgCollector->add($message, MessageCollector::NOTICE);
        $msgCollector->add($message, MessageCollector::NOTICE);

        $expected  = 'NOTICE: Trying to add the same message twice'.PHP_EOL;
        $expected .= 'NOTICE: Trying to add the same message twice'.PHP_EOL.PHP_EOL;
        $this->expectOutputString($expected);

        $msgCollector->display();

    }//end testNonUniqueMessagesWithSameErrorLevelAreAccepted()


    /**
     * Ensure that the message cache is cleared when the collected messages are displayed.
     *
     * @return void
     */
    public function testCallingDisplayTwiceWillNotShowMessagesTwice()
    {
        $messages = [
            'First notice'       => MessageCollector::NOTICE,
            'Second deprecation' => MessageCollector::DEPRECATED,
            'Third notice'       => MessageCollector::NOTICE,
            'Fourth warning'     => MessageCollector::WARNING,
        ];

        $msgCollector = new MessageCollector();
        $this->createErrorCache($msgCollector, $messages);

        $expected  = 'WARNING: Fourth warning'.PHP_EOL;
        $expected .= 'NOTICE: First notice'.PHP_EOL;
        $expected .= 'NOTICE: Third notice'.PHP_EOL;
        $expected .= 'DEPRECATED: Second deprecation'.PHP_EOL.PHP_EOL;
        $this->expectOutputString($expected);

        $msgCollector->display();
        $msgCollector->display();

    }//end testCallingDisplayTwiceWillNotShowMessagesTwice()


    /**
     * Test that messages are ordered correctly.
     *
     * @param string $order    The display order.
     * @param string $expected The expected output.
     *
     * @dataProvider dataDisplayOrderHandling
     *
     * @return void
     */
    public function testDisplayOrderHandling($order, $expected)
    {
        $messages = [
            'First notice'       => MessageCollector::NOTICE,
            'Second deprecation' => MessageCollector::DEPRECATED,
            'Third notice'       => MessageCollector::NOTICE,
            'Fourth warning'     => MessageCollector::WARNING,
        ];

        $msgCollector = new MessageCollector();
        $this->createErrorCache($msgCollector, $messages);

        $this->expectOutputString($expected);

        $msgCollector->display($order);

    }//end testDisplayOrderHandling()


    /**
     * Data provider.
     *
     * @see testDisplayOrderHandling()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataDisplayOrderHandling()
    {
        $expectedForSeverity  = 'WARNING: Fourth warning'.PHP_EOL;
        $expectedForSeverity .= 'NOTICE: First notice'.PHP_EOL;
        $expectedForSeverity .= 'NOTICE: Third notice'.PHP_EOL;
        $expectedForSeverity .= 'DEPRECATED: Second deprecation'.PHP_EOL.PHP_EOL;

        $expectedForReceived  = 'NOTICE: First notice'.PHP_EOL;
        $expectedForReceived .= 'DEPRECATED: Second deprecation'.PHP_EOL;
        $expectedForReceived .= 'NOTICE: Third notice'.PHP_EOL;
        $expectedForReceived .= 'WARNING: Fourth warning'.PHP_EOL.PHP_EOL;

        return [
            'Order by severity'                  => [
                'order'    => MessageCollector::ORDERBY_SEVERITY,
                'expected' => $expectedForSeverity,
            ],
            'Order by received'                  => [
                'order'    => MessageCollector::ORDERBY_RECEIVED,
                'expected' => $expectedForReceived,
            ],
            'Invalid order defaults to severity' => [
                'order'    => 'unknown order',
                'expected' => $expectedForSeverity,
            ],
        ];

    }//end dataDisplayOrderHandling()


    /**
     * Test helper.
     *
     * @param \PHP_CodeSniffer\Util\MessageCollector $collector The error cache object.
     * @param array<string, int>                     $messages  The error messages to add to the cache.
     *                                                          Key is the message, value is the error level.
     *
     * @return void
     */
    private function createErrorCache(MessageCollector $collector, $messages)
    {
        foreach ($messages as $msg => $type) {
            $collector->add($msg, $type);
        }

    }//end createErrorCache()


}//end class
