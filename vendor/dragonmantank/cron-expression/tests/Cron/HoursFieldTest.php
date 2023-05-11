<?php

namespace Cron\Tests;

use Cron\HoursField;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @author Michael Dowling <mtdowling@gmail.com>
 */
class HoursFieldTest extends TestCase
{
    /**
     * @covers \Cron\HoursField::validate
     */
    public function testValidatesField()
    {
        $f = new HoursField();
        $this->assertTrue($f->validate('1'));
        $this->assertTrue($f->validate('00'));
        $this->assertTrue($f->validate('01'));
        $this->assertTrue($f->validate('*'));
        $this->assertFalse($f->validate('*/3,1,1-12'));
    }

    /**
     * @covers \Cron\HoursField::isSatisfiedBy
     */
    public function testChecksIfSatisfied()
    {
        $f = new HoursField();
        $this->assertTrue($f->isSatisfiedBy(new DateTime(), '?'));
        $this->assertTrue($f->isSatisfiedBy(new DateTimeImmutable(), '?'));
    }

    /**
     * @covers \Cron\HoursField::increment
     */
    public function testIncrementsDate()
    {
        $d = new DateTime('2011-03-15 11:15:00');
        $f = new HoursField();
        $f->increment($d);
        $this->assertSame('2011-03-15 12:00:00', $d->format('Y-m-d H:i:s'));

        $d->setTime(11, 15, 0);
        $f->increment($d, true);
        $this->assertSame('2011-03-15 10:59:00', $d->format('Y-m-d H:i:s'));
    }

    /**
     * @covers \Cron\HoursField::increment
     */
    public function testIncrementsDateTimeImmutable()
    {
        $d = new DateTimeImmutable('2011-03-15 11:15:00');
        $f = new HoursField();
        $f->increment($d);
        $this->assertSame('2011-03-15 12:00:00', $d->format('Y-m-d H:i:s'));
    }

    /**
     * @covers \Cron\HoursField::increment
     */
    public function testIncrementsDateWithThirtyMinuteOffsetTimezone()
    {
        $tz = date_default_timezone_get();
        date_default_timezone_set('America/St_Johns');
        $d = new DateTime('2011-03-15 11:15:00');
        $f = new HoursField();
        $f->increment($d);
        $this->assertSame('2011-03-15 12:00:00', $d->format('Y-m-d H:i:s'));

        $d->setTime(11, 15, 0);
        $f->increment($d, true);
        $this->assertSame('2011-03-15 10:59:00', $d->format('Y-m-d H:i:s'));
        date_default_timezone_set($tz);
    }

    /**
     * @covers \Cron\HoursField::increment
     */
    public function testIncrementDateWithFifteenMinuteOffsetTimezone()
    {
        $tz = date_default_timezone_get();
        date_default_timezone_set('Asia/Kathmandu');
        $d = new DateTime('2011-03-15 11:15:00');
        $f = new HoursField();
        $f->increment($d);
        $this->assertSame('2011-03-15 12:00:00', $d->format('Y-m-d H:i:s'));

        $d->setTime(11, 15, 0);
        $f->increment($d, true);
        $this->assertSame('2011-03-15 10:59:00', $d->format('Y-m-d H:i:s'));
        date_default_timezone_set($tz);
    }
}
