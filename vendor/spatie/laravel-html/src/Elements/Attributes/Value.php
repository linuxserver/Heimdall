<?php

namespace Spatie\Html\Elements\Attributes;

trait Value
{
    /**
     * @param string|null $value
     *
     * @return static
     */
    public function value($value)
    {
        return $this->attribute('value', $value);
    }
}
