<?php

namespace Spatie\Html\Elements\Attributes;

trait MinMaxLength
{
    /**
     * @param int $minlength
     *
     * @return static
     */
    public function minlength(int $minlength)
    {
        return $this->attribute('minlength', $minlength);
    }

    /**
     * @param int $maxlength
     *
     * @return static
     */
    public function maxlength(int $maxlength)
    {
        return $this->attribute('maxlength', $maxlength);
    }
}
