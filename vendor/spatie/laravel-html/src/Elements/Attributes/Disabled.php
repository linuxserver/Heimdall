<?php

namespace Spatie\Html\Elements\Attributes;

/**
 * @method static disabledIf(bool $condition, bool $disabled)
 * @method static disabledIfNotNull(bool $condition, bool $disabled)
 * @method static disabledUnless(bool $condition, bool $disabled)
 */
trait Disabled
{
    /**
     * @param bool $disabled
     *
     * @return static
     */
    public function disabled($disabled = true)
    {
        return $disabled
            ? $this->attribute('disabled', 'disabled')
            : $this->forgetAttribute('disabled');
    }
}
