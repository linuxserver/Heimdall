<?php

namespace Spatie\Html\Elements\Attributes;

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
