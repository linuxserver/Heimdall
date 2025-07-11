<?php
/**
 * Timing functions for the run.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Util;

class Timing
{

    /**
     * Number of milliseconds in a minute.
     *
     * @var int
     */
    const MINUTE_IN_MS = 60000;

    /**
     * Number of milliseconds in a second.
     *
     * @var int
     */
    const SECOND_IN_MS = 1000;

    /**
     * The start time of the run in microseconds.
     *
     * @var float
     */
    private static $startTime;

    /**
     * Used to make sure we only print the run time once per run.
     *
     * @var boolean
     */
    private static $printed = false;


    /**
     * Start recording time for the run.
     *
     * @return void
     */
    public static function startTiming()
    {

        self::$startTime = microtime(true);

    }//end startTiming()


    /**
     * Get the duration of the run up to "now".
     *
     * @return float Duration in milliseconds.
     */
    public static function getDuration()
    {
        if (self::$startTime === null) {
            // Timing was never started.
            return 0;
        }

        return ((microtime(true) - self::$startTime) * 1000);

    }//end getDuration()


    /**
     * Convert a duration in milliseconds to a human readable duration string.
     *
     * @param float $duration Duration in milliseconds.
     *
     * @return string
     */
    public static function getHumanReadableDuration($duration)
    {
        $timeString = '';
        if ($duration >= self::MINUTE_IN_MS) {
            $mins       = floor($duration / self::MINUTE_IN_MS);
            $secs       = round((fmod($duration, self::MINUTE_IN_MS) / self::SECOND_IN_MS), 2);
            $timeString = $mins.' mins';
            if ($secs >= 0.01) {
                $timeString .= ", $secs secs";
            }
        } else if ($duration >= self::SECOND_IN_MS) {
            $timeString = round(($duration / self::SECOND_IN_MS), 2).' secs';
        } else {
            $timeString = round($duration).'ms';
        }

        return $timeString;

    }//end getHumanReadableDuration()


    /**
     * Print information about the run.
     *
     * @param boolean $force If TRUE, prints the output even if it has
     *                       already been printed during the run.
     *
     * @return void
     */
    public static function printRunTime($force=false)
    {
        if ($force === false && self::$printed === true) {
            // A double call.
            return;
        }

        if (self::$startTime === null) {
            // Timing was never started.
            return;
        }

        $duration = self::getDuration();
        $duration = self::getHumanReadableDuration($duration);

        $mem = round((memory_get_peak_usage(true) / (1024 * 1024)), 2).'MB';
        echo "Time: $duration; Memory: $mem".PHP_EOL.PHP_EOL;

        self::$printed = true;

    }//end printRunTime()


}//end class
