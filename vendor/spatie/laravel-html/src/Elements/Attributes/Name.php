<?php

namespace Spatie\Html\Elements\Attributes;

/**
 * @method static nameIf(bool $condition, string|null $name)
 * @method static nameIfNotNull(bool $condition, string|null $name)
 * @method static nameUnless(bool $condition, string|null $name)
 */
trait Name
{
    /**
     * @param string|null $name
     *
     * @return static
     */
    public function name($name)
    {
        return $this->attribute('name', $name);
    }
}
