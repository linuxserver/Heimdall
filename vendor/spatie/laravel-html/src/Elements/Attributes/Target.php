<?php

namespace Spatie\Html\Elements\Attributes;

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
