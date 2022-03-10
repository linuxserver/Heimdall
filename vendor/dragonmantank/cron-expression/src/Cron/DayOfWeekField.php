<?php

namespace Cron;

use DateTime;
use DateTimeInterface;
use InvalidArgumentException;

/**
 * Day of week field.  Allows: * / , - ? L #
 *
 * Days of the week can be represented as a number 0-7 (0|7 = Sunday)
 * or as a three letter string: SUN, MON, TUE, WED, THU, FRI, SAT.
 *
 * 'L' stands for "last". It allows you to specify constructs such as
 * "the last Friday" of a given month.
 *
 * '#' is allowed for the day-of-week field, and must be followed by a
 * number between one and five. It allows you to specify constructs such as
 * "the second Friday" of a given month.
 */
class DayOfWeekField extends AbstractField
{
    /**
     * @inheritDoc
     */
    protected $rangeStart = 0;

    /**
     * @inheritDoc
     */
    protected $rangeEnd = 7;

    /**
     * @var array Weekday range
     */
    protected $nthRange;

    /**
     * @inheritDoc
     */
    protected $literals = [1 => 'MON', 2 => 'TUE', 3 => 'WED', 4 => 'THU', 5 => 'FRI', 6 => 'SAT', 7 => 'SUN'];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->nthRange = range(1, 5);
        parent::__construct();
    }

    /**
     * @inheritDoc
     *
     * @param \DateTime|\DateTimeImmutable $date
     */
    public function isSatisfiedBy(DateTimeInterface $date, $value)
    {
        if ($value == '?') {
            return true;
        }

        // Convert text day of the week values to integers
        $value = $this->convertLiterals($value);

        $currentYear = $date->format('Y');
        $currentMonth = $date->format('m');
        $lastDayOfMonth = $date->format('t');

        // Find out if this is the last specific weekday of the month
        if (strpos($value, 'L')) {
            $weekday = (int) $this->convertLiterals(substr($value, 0, strpos($value, 'L')));
            $weekday %= 7;

            $tdate = clone $date;
            $tdate = $tdate->setDate($currentYear, $currentMonth, $lastDayOfMonth);
            while ($tdate->format('w') != $weekday) {
                $tdateClone = new DateTime();
                $tdate = $tdateClone
                    ->setTimezone($tdate->getTimezone())
                    ->setDate($currentYear, $currentMonth, --$lastDayOfMonth);
            }

            return $date->format('j') == $lastDayOfMonth;
        }

        // Handle # hash tokens
        if (strpos($value, '#')) {
            list($weekday, $nth) = explode('#', $value);

            if (!is_numeric($nth)) {
                throw new InvalidArgumentException("Hashed weekdays must be numeric, {$nth} given");
            } else {
                $nth = (int) $nth;
            }

            // 0 and 7 are both Sunday, however 7 matches date('N') format ISO-8601
            if ($weekday === '0') {
                $weekday = 7;
            }

            $weekday = $this->convertLiterals($weekday);

            // Validate the hash fields
            if ($weekday < 0 || $weekday > 7) {
                throw new InvalidArgumentException("Weekday must be a value between 0 and 7. {$weekday} given");
            }

            if (!in_array($nth, $this->nthRange)) {
                throw new InvalidArgumentException("There are never more than 5 or less than 1 of a given weekday in a month, {$nth} given");
            }

            // The current weekday must match the targeted weekday to proceed
            if ($date->format('N') != $weekday) {
                return false;
            }

            $tdate = clone $date;
            $tdate = $tdate->setDate($currentYear, $currentMonth, 1);
            $dayCount = 0;
            $currentDay = 1;
            while ($currentDay < $lastDayOfMonth + 1) {
                if ($tdate->format('N') == $weekday) {
                    if (++$dayCount >= $nth) {
                        break;
                    }
                }
                $tdate = $tdate->setDate($currentYear, $currentMonth, ++$currentDay);
            }

            return $date->format('j') == $currentDay;
        }

        // Handle day of the week values
        if (strpos($value, '-')) {
            $parts = explode('-', $value);
            if ($parts[0] == '7') {
                $parts[0] = '0';
            } elseif ($parts[1] == '0') {
                $parts[1] = '7';
            }
            $value = implode('-', $parts);
        }

        // Test to see which Sunday to use -- 0 == 7 == Sunday
        $format = in_array(7, str_split($value)) ? 'N' : 'w';
        $fieldValue = $date->format($format);

        return $this->isSatisfied($fieldValue, $value);
    }

    /**
     * @inheritDoc
     *
     * @param \DateTime|\DateTimeImmutable &$date
     */
    public function increment(DateTimeInterface &$date, $invert = false)
    {
        if ($invert) {
            $date = $date->modify('-1 day')->setTime(23, 59, 0);
        } else {
            $date = $date->modify('+1 day')->setTime(0, 0, 0);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function validate($value)
    {
        $basicChecks = parent::validate($value);

        if (!$basicChecks) {
            // Handle the # value
            if (strpos($value, '#') !== false) {
                $chunks = explode('#', $value);
                $chunks[0] = $this->convertLiterals($chunks[0]);

                if (parent::validate($chunks[0]) && is_numeric($chunks[1]) && in_array($chunks[1], $this->nthRange)) {
                    return true;
                }
            }

            if (preg_match('/^(.*)L$/', $value, $matches)) {
                return $this->validate($matches[1]);
            }

            return false;
        }

        return $basicChecks;
    }
}
