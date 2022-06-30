<?php

namespace Facade\FlareClient\Truncation;

class TrimContextItemsStrategy extends AbstractTruncationStrategy
{
    public static function thresholds()
    {
        return [100, 50, 25, 10];
    }

    public function execute(array $payload): array
    {
        foreach (static::thresholds() as $threshold) {
            if (! $this->reportTrimmer->needsToBeTrimmed($payload)) {
                break;
            }

            $payload['context'] = $this->iterateContextItems($payload['context'], $threshold);
        }

        return $payload;
    }

    protected function iterateContextItems(array $contextItems, int $threshold): array
    {
        array_walk($contextItems, [$this, 'trimContextItems'], $threshold);

        return $contextItems;
    }

    protected function trimContextItems(&$value, $key, int $threshold)
    {
        if (is_array($value)) {
            if (count($value) > $threshold) {
                $value = array_slice($value, $threshold * -1, $threshold);
            }

            array_walk($value, [$this, 'trimContextItems'], $threshold);
        }

        return $value;
    }
}
