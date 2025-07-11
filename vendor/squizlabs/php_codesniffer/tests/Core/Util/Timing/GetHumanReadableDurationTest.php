<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Timing::getHumanReadableDuration() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Timing;

use PHP_CodeSniffer\Util\Timing;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHP_CodeSniffer\Util\Timing::getHumanReadableDuration() method.
 *
 * @covers \PHP_CodeSniffer\Util\Timing::getHumanReadableDuration
 */
final class GetHumanReadableDurationTest extends TestCase
{


    /**
     * Test the method.
     *
     * @param int|float $duration A duration in milliseconds.
     * @param string    $expected The expected human readable string.
     *
     * @dataProvider dataGetHumanReadableDuration
     *
     * @return void
     */
    public function testGetHumanReadableDuration($duration, $expected)
    {
        $this->assertSame($expected, Timing::getHumanReadableDuration($duration));

    }//end testGetHumanReadableDuration()


    /**
     * Data provider.
     *
     * @return array<string, array<string, int|float|string>>
     */
    public static function dataGetHumanReadableDuration()
    {
        return [
            'Duration: 0'                                  => [
                'duration' => 0,
                'expected' => '0ms',
            ],
            'Duration: 13 milliseconds'                    => [
                'duration' => 13.232101565,
                'expected' => '13ms',
            ],
            'Duration: 14 milliseconds'                    => [
                'duration' => 13.916015625,
                'expected' => '14ms',
            ],
            'Duration: 999 milliseconds'                   => [
                'duration' => 999.2236,
                'expected' => '999ms',
            ],
            'Duration: 999+ milliseconds'                  => [
                'duration' => 999.89236,
                'expected' => '1000ms',
            ],
            'Duration: 1 second'                           => [
                'duration' => 1000,
                'expected' => '1 secs',
            ],
            'Duration: slightly more than 1 second'        => [
                'duration' => 1001.178215,
                'expected' => '1 secs',
            ],
            'Duration: just under a 1 minute'              => [
                'duration' => 59999.3851,
                'expected' => '60 secs',
            ],
            'Duration: exactly 1 minute'                   => [
                'duration' => 60000,
                'expected' => '1 mins',
            ],
            'Duration: slightly more than 1 minute'        => [
                'duration' => 60001.7581235,
                'expected' => '1 mins',
            ],
            'Duration: 1 minute, just under half a second' => [
                'duration' => 60499.83639,
                'expected' => '1 mins, 0.5 secs',
            ],
            'Duration: 1 minute, just over half a second'  => [
                'duration' => 60501.961238,
                'expected' => '1 mins, 0.5 secs',
            ],
            'Duration: 1 minute, 1 second'                 => [
                'duration' => 61000,
                'expected' => '1 mins, 1 secs',
            ],
            'Duration: exactly 1 hour'                     => [
                'duration' => 3600000,
                'expected' => '60 mins',
            ],
            'Duration: 89.4 mins'                          => [
                'duration' => 5364000,
                'expected' => '89 mins, 24 secs',
            ],
        ];

    }//end dataGetHumanReadableDuration()


}//end class
