<?php

namespace Spatie\Html\Elements\Attributes;

/**
 * @method static targetIf(bool $condition, string $target)
 * @method static targetIfNotNull(bool $condition, string $target)
 * @method static targetUnless(bool $condition, string $target)
 */
trait Target
{
    /**
     * @param string $target
     *
     * @return static
     */
    public function target($target)
    {
        return $this->attribute('target', $target);
    }
}
