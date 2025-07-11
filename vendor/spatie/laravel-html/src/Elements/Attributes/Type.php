<?php

namespace Spatie\Html\Elements\Attributes;

trait Type
{
    /**
     * @param string|null $type
     *
     * @return static
     */
    public function type($type)
    {
        return $this->attribute('type', $type);
    }
}
