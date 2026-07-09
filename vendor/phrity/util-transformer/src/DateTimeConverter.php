<?php

namespace Phrity\Util\Transformer;

use DateInterval;
use DatePeriod;
use DateTimeInterface;
use DateTimeZone;

class DateTimeConverter implements TransformerInterface
{
    public function __construct(
        private string $dateTimeFormat = 'c',
        private string $dateIntervalFormat = '%d',
    ) {
    }

    public function canTransform(mixed $subject, string|null $type = null): bool
    {
        if (!in_array($type, [Type::STRING, null])) {
            return false;
        }
        return $subject instanceof DateInterval
            || $subject instanceof DatePeriod
            || $subject instanceof DateTimeInterface
            || $subject instanceof DateTimeZone;
    }

    public function transform(mixed $subject, string|null $type = null): mixed
    {
        $subjectType = get_debug_type($subject);
        if (!$this->canTransform($subject, $type)) {
            throw new TransformerException("Converting '{$subjectType}' is not supported.");
        }
        if ($subject instanceof DateInterval) {
            return $subject->format($this->dateIntervalFormat);
        }
        if ($subject instanceof DatePeriod) {
            return sprintf(
                '%s (%s)',
                $subject->getStartDate()->format($this->dateTimeFormat),
                $subject->getDateInterval()->format($this->dateIntervalFormat)
            );
        }
        if ($subject instanceof DateTimeInterface) {
            return $subject->format($this->dateTimeFormat);
        }
        return $subject->getName();
    }
}
