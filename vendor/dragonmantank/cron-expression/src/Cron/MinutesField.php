<?php

namespace Cron;

use DateTimeInterface;

/**
 * Minutes field.  Allows: * , / -
 */
class MinutesField extends AbstractField
{
    /**
     * @inheritDoc
     */
    protected $rangeStart = 0;

    /**
     * @inheritDoc
     */
    protected $rangeEnd = 59;

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(DateTimeInterface $date, $value)
    {
        if ($value == '?') {
            return true;
        }

        return $this->isSatisfied($date->format('i'), $value);
    }

    /**
     * {@inheritDoc}
     *
     * @param \DateTime|\DateTimeImmutable &$date
     * @param string|null                  $parts
     */
    public function increment(DateTimeInterface &$date, $invert = false, $parts = null)
    {
        if (is_null($parts)) {
            $date = $date->modify(($invert ? '-' : '+') . '1 minute');
            return $this;
        }

        $parts = strpos($parts, ',') !== false ? explode(',', $parts) : array($parts);
        $minutes = array();
        foreach ($parts as $part) {
            $minutes = array_merge($minutes, $this->getRangeForExpression($part, 59));
        }

        $current_minute = $date->format('i');
        $position = $invert ? count($minutes) - 1 : 0;
        if (count($minutes) > 1) {
            for ($i = 0; $i < count($minutes) - 1; $i++) {
                if ((!$invert && $current_minute >= $minutes[$i] && $current_minute < $minutes[$i + 1]) ||
                    ($invert && $current_minute > $minutes[$i] && $current_minute <= $minutes[$i + 1])) {
                    $position = $invert ? $i : $i + 1;
                    break;
                }
            }
        }

        if ((!$invert && $current_minute >= $minutes[$position]) || ($invert && $current_minute <= $minutes[$position])) {
            $date = $date->modify(($invert ? '-' : '+') . '1 hour');
            $date = $date->setTime($date->format('H'), $invert ? 59 : 0);
        }
        else {
            $date = $date->setTime($date->format('H'), $minutes[$position]);
        }

        return $this;
    }
}
