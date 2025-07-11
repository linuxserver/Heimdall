<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Timing class.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Timing;

use PHP_CodeSniffer\Util\Timing;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHP_CodeSniffer\Util\Timing class.
 *
 * {@internal These tests need to run in separate processes as the Timing class uses static properties
 * to keep track of the start time and whether or not the runtime has been printed and these
 * can't be unset/reset once set.}
 *
 * @covers \PHP_CodeSniffer\Util\Timing
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState         disabled
 */
final class TimingTest extends TestCase
{


    /**
     * Verify that getDuration() returns 0 when the timer wasn't started.
     *
     * @return void
     */
    public function testGetDurationWithoutStartReturnsZero()
    {
        $this->assertSame(0, Timing::getDuration());

    }//end testGetDurationWithoutStartReturnsZero()


    /**
     * Verify that getDuration() returns the time in milliseconds.
     *
     * @return void
     */
    public function testGetDurationWithStartReturnsMilliseconds()
    {
        Timing::startTiming();
        usleep(1500);
        $duration = Timing::getDuration();

        $this->assertTrue(is_float($duration));

    }//end testGetDurationWithStartReturnsMilliseconds()


    /**
     * Verify that printRunTime() doesn't print anything if the timer wasn't started.
     *
     * @return void
     */
    public function testTimeIsNotPrintedIfTimerWasNeverStarted()
    {
        $this->expectOutputString('');
        Timing::printRunTime();

    }//end testTimeIsNotPrintedIfTimerWasNeverStarted()


    /**
     * Verify that printRunTime() doesn't print anything if the timer wasn't started.
     *
     * @return void
     */
    public function testTimeIsNotPrintedIfTimerWasNeverStartedEvenWhenForced()
    {
        $this->expectOutputString('');
        Timing::printRunTime(true);

    }//end testTimeIsNotPrintedIfTimerWasNeverStartedEvenWhenForced()


    /**
     * Verify that printRunTime() when called multiple times only prints the runtime information once.
     *
     * @return void
     */
    public function testTimeIsPrintedOnlyOnce()
    {
        $this->expectOutputRegex('`^Time: [0-9]+ms; Memory: [0-9\.]+MB'.PHP_EOL.PHP_EOL.'$`');

        Timing::startTiming();
        usleep(2000);
        Timing::printRunTime();
        Timing::printRunTime();
        Timing::printRunTime();

    }//end testTimeIsPrintedOnlyOnce()


    /**
     * Verify that printRunTime() when called multiple times prints the runtime information multiple times if forced.
     *
     * @return void
     */
    public function testTimeIsPrintedMultipleTimesOnlyIfForced()
    {
        $this->expectOutputRegex('`^(Time: [0-9]+ms; Memory: [0-9\.]+MB'.PHP_EOL.PHP_EOL.'){3}$`');

        Timing::startTiming();
        usleep(2000);
        Timing::printRunTime(true);
        Timing::printRunTime(true);
        Timing::printRunTime(true);

    }//end testTimeIsPrintedMultipleTimesOnlyIfForced()


}//end class
