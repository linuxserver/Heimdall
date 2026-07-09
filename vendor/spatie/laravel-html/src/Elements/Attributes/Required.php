<?php

namespace Spatie\Html\Elements\Attributes;

/**
 * @method static requiredIf(bool $condition, bool $required)
 * @method static requiredIfNotNull(bool $condition, bool $required)
 * @method static requiredUnless(bool $condition, bool $required)
 */
trait Required
{
    /**
     * @param bool $required
     *
     * @return static
     */
    public function required($required = true)
    {
        return $required
            ? $this->attribute('required')
            : $this->forgetAttribute('required');
    }
}
