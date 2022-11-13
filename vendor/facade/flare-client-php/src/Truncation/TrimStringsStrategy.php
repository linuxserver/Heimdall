<?php

namespace Facade\FlareClient\Truncation;

class TrimStringsStrategy extends AbstractTruncationStrategy
{
    public static function thresholds()
    {
        return [1024, 512, 256];
    }

    public function execute(array $payload): array
    {
        foreach (static::thresholds() as $threshold) {
            if (! $this->reportTrimmer->needsToBeTrimmed($payload)) {
                break;
            }

            $payload = $this->trimPayloadString($payload, $threshold);
        }

        return $payload;
    }

    protected function trimPayloadString(array $payload, int $threshold): array
    {
        array_walk_recursive($payload, function (&$value) use ($threshold) {
            if (is_string($value) && strlen($value) > $threshold) {
                $value = substr($value, 0, $threshold);
            }
        });

        return $payload;
    }
}
