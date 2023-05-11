<?php

namespace Cron;

use DateTimeInterface;

/**
 * Month field.  Allows: * , / -
 */
class MonthField extends AbstractField
{
    /**
     * @inheritDoc
     */
    protected $rangeStart = 1;

    /**
     * @inheritDoc
     */
    protected $rangeEnd = 12;

    /**
     * @inheritDoc
     */
    protected $literals = [1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR', 5 => 'MAY', 6 => 'JUN', 7 => 'JUL',
        8 => 'AUG', 9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DEC'];

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(DateTimeInterface $date, $value)
    {
        if ($value == '?') {
            return true;
        }

        $value = $this->convertLiterals($value);

        return $this->isSatisfied($date->format('m'), $value);
    }

    /**
     * @inheritDoc
     *
     * @param \DateTime|\DateTimeImmutable &$date
     */
    public function increment(DateTimeInterface &$date, $invert = false)
    {
        if ($invert) {
            $date = $date->modify('last day of previous month')->setTime(23, 59);
        } else {
            $date = $date->modify('first day of next month')->setTime(0, 0);
        }

        return $this;
    }


}
