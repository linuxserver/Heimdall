<?php

namespace Spatie\Html\Elements\Attributes;

/**
 * @method static minlengthIf(bool $condition, int $minlength)
 * @method static minlengthIfNotNull(bool $condition, int $minlength)
 * @method static minlengthUnless(bool $condition, int $minlength)
 * @method static maxlengthIf(bool $condition, int $maxlength)
 * @method static maxlengthIfNotNull(bool $condition, int $maxlength)
 * @method static maxlengthUnless(bool $condition, int $maxlength)
 */
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
