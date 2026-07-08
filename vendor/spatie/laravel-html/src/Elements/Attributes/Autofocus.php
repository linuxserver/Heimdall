<?php

namespace Spatie\Html\Elements\Attributes;

/**
 * @method static autofocusIf(bool $condition, bool $autofocus)
 * @method static autofocusIfNotNull(bool $condition, bool $autofocus)
 * @method static autofocusUnless(bool $condition, bool $autofocus)
 */
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
