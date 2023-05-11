<?php

namespace Cron;

use DateTimeInterface;

/**
 * CRON field interface
 */
interface FieldInterface
{
    /**
     * Check if the respective value of a DateTime field satisfies a CRON exp
     *
     * @param DateTimeInterface $date  DateTime object to check
     * @param string            $value CRON expression to test against
     *
     * @return bool Returns TRUE if satisfied, FALSE otherwise
     */
    public function isSatisfiedBy(DateTimeInterface $date, $value);

    /**
     * When a CRON expression is not satisfied, this method is used to increment
     * or decrement a DateTime object by the unit of the cron field
     *
     * @param DateTimeInterface &$date  DateTime object to change
     * @param bool              $invert (optional) Set to TRUE to decrement
     *
     * @return FieldInterface
     */
    public function increment(DateTimeInterface &$date, $invert = false);

    /**
     * Validates a CRON expression for a given field
     *
     * @param string $value CRON expression value to validate
     *
     * @return bool Returns TRUE if valid, FALSE otherwise
     */
    public function validate($value);
}
