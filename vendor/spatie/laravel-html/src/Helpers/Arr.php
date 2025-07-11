<?php

namespace Spatie\Html\Helpers;

use Illuminate\Support\Collection;

class Arr
{
    /**
     * Return an array of enabled values. Enabled values are either:
     *     - Keys that have a truthy value;
     *     - Values that don't have keys.
     *
     * Example:
     *
     *     Arr::getToggledValues(['foo' => true, 'bar' => false, 'baz'])
     *     // => ['foo', 'baz']
     *
     * @param mixed $map
     *
     * @return array
     */
    public static function getToggledValues($map)
    {
        return Collection::make($map)->map(function ($condition, $value) {
            if (is_numeric($value)) {
                return $condition;
            }

            return $condition ? $value : null;
        })->filter()->toArray();
    }
}
