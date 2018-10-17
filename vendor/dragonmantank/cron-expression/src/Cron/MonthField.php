<?php

namespace Cron;

use DateTime;

/**
 * Month field.  Allows: * , / -
 */
class MonthField extends AbstractField
{
    protected $rangeStart = 1;
    protected $rangeEnd = 12;
    protected $literals = [1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR', 5 => 'MAY', 6 => 'JUN', 7 => 'JUL',
        8 => 'AUG', 9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DEC'];

    public function isSatisfiedBy(DateTime $date, $value)
    {
        $value = $this->convertLiterals($value);

        return $this->isSatisfied($date->format('m'), $value);
    }

    public function increment(DateTime $date, $invert = false)
    {
        if ($invert) {
            $date->modify('last day of previous month');
            $date->setTime(23, 59);
        } else {
            $date->modify('first day of next month');
            $date->setTime(0, 0);
        }

        return $this;
    }


}
