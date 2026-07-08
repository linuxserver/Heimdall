<?php

namespace Spatie\Html\Elements\Attributes;

/**
 * @method static valueIf(bool $condition, string|null $value)
 * @method static valueIfNotNull(bool $condition, string|null $value)
 * @method static valueUnless(bool $condition, string|null $value)
 */
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
