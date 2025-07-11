<?php

namespace Spatie\Html\Elements\Attributes;

trait Autofocus
{
    /**
     * @param bool $autofocus
     *
     * @return static
     */
    public function autofocus($autofocus = true)
    {
        return $autofocus
            ? $this->attribute('autofocus')
            : $this->forgetAttribute('autofocus');
    }
}
