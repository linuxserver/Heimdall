<?php

namespace Facade\FlareClient\Truncation;

class ReportTrimmer
{
    protected static $maxPayloadSize = 524288;

    protected $strategies = [
        TrimStringsStrategy::class,
        TrimContextItemsStrategy::class,
    ];

    public function trim(array $payload): array
    {
        foreach ($this->strategies as $strategy) {
            if (! $this->needsToBeTrimmed($payload)) {
                break;
            }

            $payload = (new $strategy($this))->execute($payload);
        }

        return $payload;
    }

    public function needsToBeTrimmed(array $payload): bool
    {
        return strlen(json_encode($payload)) > self::getMaxPayloadSize();
    }

    public static function getMaxPayloadSize(): int
    {
        return self::$maxPayloadSize;
    }

    public static function setMaxPayloadSize(int $maxPayloadSize): void
    {
        self::$maxPayloadSize = $maxPayloadSize;
    }
}
