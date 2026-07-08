<?php

namespace Spatie\Html\Elements\Attributes;

/**
 * @method static isReadonlyIf(bool $condition, bool $readonly)
 * @method static isReadonlyIfNotNull(bool $condition, bool $readonly)
 * @method static isReadonlyUnless(bool $condition, bool $readonly)
 */
trait ReadonlyTrait
{
    /**
     * @param bool $readonly
     *
     * @return static
     */
    public function isReadonly($readonly = true)
    {
        return $readonly
            ? $this->attribute('readonly')
            : $this->forgetAttribute('readonly');
    }
}
