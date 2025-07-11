<?php

namespace Spatie\Html\Elements\Attributes;

trait Autocomplete
{
    /**
     * @param bool|string $autocomplete
     *
     * @return static
     */
    public function autocomplete($autocomplete = true)
    {
        $value = 'on';
        if ($autocomplete === false) {
            $value = 'off';
        }
        if (is_string($autocomplete)) {
            $value = $autocomplete;
        }

        return $this->attribute('autocomplete', $value);
    }
}
