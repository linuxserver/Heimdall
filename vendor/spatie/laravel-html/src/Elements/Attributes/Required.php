<?php

namespace Spatie\Html\Elements\Attributes;

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
