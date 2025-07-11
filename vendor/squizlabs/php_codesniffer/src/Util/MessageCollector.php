<?php
/**
 * Collect messages for display at a later point in the process flow.
 *
 * If any message with type "error" is passed in, displaying the errors will result in halting the program
 * with a non-zero exit code.
 * If only messages with a lower severity are passed in, displaying the errors will be non-blocking
 * and will not affect the exit code.
 *
 * ---------------------------------------------------------------------------------------------
 * This class is intended for internal use only and is not part of the public API.
 * This also means that it has no promise of backward compatibility. Use at your own risk.
 * ---------------------------------------------------------------------------------------------
 *
 * @internal
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Util;

use InvalidArgumentException;
use PHP_CodeSniffer\Exceptions\RuntimeException;

final class MessageCollector
{

    /**
     * Indicator for a (blocking) error.
     *
     * @var int
     */
    const ERROR = 1;

    /**
     * Indicator for a warning.
     *
     * @var int
     */
    const WARNING = 2;

    /**
     * Indicator for a notice.
     *
     * @var int
     */
    const NOTICE = 4;

    /**
     * Indicator for a deprecation notice.
     *
     * @var int
     */
    const DEPRECATED = 8;

    /**
     * Indicator for ordering the messages based on severity first, order received second.
     *
     * @var string
     */
    const ORDERBY_SEVERITY = 'severity';

    /**
     * Indicator for ordering the messages based on the order in which they were received.
     *
     * @var string
     */
    const ORDERBY_RECEIVED = 'received';

    /**
     * Collected messages.
     *
     * @var array<array<string, string|int>> The value for each array entry is an associative array
     *                                       which holds two keys:
     *                                       - 'message' string The message text.
     *                                       - 'type'    int    The type of the message based on the
     *                                                          above declared error level constants.
     */
    private $cache = [];


    /**
     * Add a new message.
     *
     * @param string $message The message text.
     * @param int    $type    The type of message. Should be one of the following constants:
     *                        MessageCollector::ERROR, MessageCollector::WARNING, MessageCollector::NOTICE
     *                        or MessageCollector::DEPRECATED.
     *                        Defaults to MessageCollector::NOTICE.
     *
     * @return void
     *
     * @throws \InvalidArgumentException If the message text is not a string.
     * @throws \InvalidArgumentException If the message type is not one of the accepted types.
     */
    public function add($message, $type=self::NOTICE)
    {
        if (is_string($message) === false) {
            throw new InvalidArgumentException('The $message should be of type string. Received: '.gettype($message).'.');
        }

        if ($type !== self::ERROR
            && $type !== self::WARNING
            && $type !== self::NOTICE
            && $type !== self::DEPRECATED
        ) {
            throw new InvalidArgumentException('The message $type should be one of the predefined MessageCollector constants. Received: '.$type.'.');
        }

        $this->cache[] = [
            'message' => $message,
            'type'    => $type,
        ];

    }//end add()


    /**
     * Determine whether or not the currently cached errors include blocking errors.
     *
     * @return bool
     */
    public function containsBlockingErrors()
    {
        $seenTypes     = $this->arrayColumn($this->cache, 'type');
        $typeFrequency = array_count_values($seenTypes);
        return isset($typeFrequency[self::ERROR]);

    }//end containsBlockingErrors()


    /**
     * Display the cached messages.
     *
     * Displaying the messages will also clear the message cache.
     *
     * @param string $order Optional. The order in which to display the messages.
     *                      Should be one of the following constants: MessageCollector::ORDERBY_SEVERITY,
     *                      MessageCollector::ORDERBY_RECEIVED.
     *                      Defaults to MessageCollector::ORDERBY_SEVERITY.
     *
     * @return void
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException When there are blocking errors.
     */
    public function display($order=self::ORDERBY_SEVERITY)
    {
        if ($this->cache === []) {
            return;
        }

        $blocking    = $this->containsBlockingErrors();
        $messageInfo = $this->prefixAll($this->cache);
        $this->clearCache();

        if ($order === self::ORDERBY_RECEIVED) {
            $messages = $this->arrayColumn($messageInfo, 'message');
        } else {
            $messages = $this->sortBySeverity($messageInfo);
        }

        $allMessages = implode(PHP_EOL, $messages).PHP_EOL.PHP_EOL;

        if ($blocking === true) {
            throw new RuntimeException($allMessages);
        } else {
            echo $allMessages;
        }

    }//end display()


    /**
     * Label all messages based on their type.
     *
     * @param array<array<string, string|int>> $messages A multi-dimensional array of messages with their severity.
     *
     * @return array<array<string, string|int>>
     */
    private function prefixAll(array $messages)
    {
        foreach ($messages as $i => $details) {
            $messages[$i]['message'] = $this->prefix($details['message'], $details['type']);
        }

        return $messages;

    }//end prefixAll()


    /**
     * Add a message type prefix to a message.
     *
     * @param string $message The message text.
     * @param int    $type    The type of message.
     *
     * @return string
     */
    private function prefix($message, $type)
    {
        switch ($type) {
        case self::ERROR:
            $message = 'ERROR: '.$message;
            break;

        case self::WARNING:
            $message = 'WARNING: '.$message;
            break;

        case self::DEPRECATED:
            $message = 'DEPRECATED: '.$message;
            break;

        default:
            $message = 'NOTICE: '.$message;
            break;
        }

        return $message;

    }//end prefix()


    /**
     * Sort an array of messages by severity.
     *
     * @param array<array<string, string|int>> $messages A multi-dimensional array of messages with their severity.
     *
     * @return array<string> A single dimensional array of only messages, sorted by severity.
     */
    private function sortBySeverity(array $messages)
    {
        if (count($messages) === 1) {
            return [$messages[0]['message']];
        }

        $errors       = [];
        $warnings     = [];
        $notices      = [];
        $deprecations = [];

        foreach ($messages as $details) {
            switch ($details['type']) {
            case self::ERROR:
                $errors[] = $details['message'];
                break;

            case self::WARNING:
                $warnings[] = $details['message'];
                break;

            case self::DEPRECATED:
                $deprecations[] = $details['message'];
                break;

            default:
                $notices[] = $details['message'];
                break;
            }
        }

        return array_merge($errors, $warnings, $notices, $deprecations);

    }//end sortBySeverity()


    /**
     * Clear the message cache.
     *
     * @return void
     */
    private function clearCache()
    {
        $this->cache = [];

    }//end clearCache()


    /**
     * Return the values from a single column in the input array.
     *
     * Polyfill for the PHP 5.5+ native array_column() function (for the functionality needed here).
     *
     * @param array<array<string, string|int>> $input     A multi-dimensional array from which to pull a column of values.
     * @param string                           $columnKey The name of the column of values to return.
     *
     * @link https://www.php.net/function.array-column
     *
     * @return array<string|int>
     */
    private function arrayColumn(array $input, $columnKey)
    {
        if (function_exists('array_column') === true) {
            // PHP 5.5+.
            return array_column($input, $columnKey);
        }

        // PHP 5.4.
        $callback = function ($row) use ($columnKey) {
            return $row[$columnKey];
        };

        return array_map($callback, $input);

    }//end arrayColumn()


}//end class
