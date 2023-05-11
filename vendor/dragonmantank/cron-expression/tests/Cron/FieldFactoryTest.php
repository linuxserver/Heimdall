<?php

namespace Cron\Tests;

use Cron\FieldFactory;
use PHPUnit\Framework\TestCase;

/**
 * @author Michael Dowling <mtdowling@gmail.com>
 */
class FieldFactoryTest extends TestCase
{
    /**
     * @covers \Cron\FieldFactory::getField
     */
    public function testRetrievesFieldInstances()
    {
        $mappings = array(
            0 => 'Cron\MinutesField',
            1 => 'Cron\HoursField',
            2 => 'Cron\DayOfMonthField',
            3 => 'Cron\MonthField',
            4 => 'Cron\DayOfWeekField',
        );

        $f = new FieldFactory();

        foreach ($mappings as $position => $class) {
            $this->assertSame($class, get_class($f->getField($position)));
        }
    }

    /**
     * @covers \Cron\FieldFactory::getField
     */
    public function testValidatesFieldPosition()
    {
        $this->expectException(\InvalidArgumentException::class);

        $f = new FieldFactory();
        $f->getField(-1);
    }
}
