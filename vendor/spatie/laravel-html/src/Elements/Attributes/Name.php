<?php

namespace Spatie\Html\Elements\Attributes;

trait Name
{
    /**
     * @param string|null $name
     *
     * @return static
     */
    public function name($name)
    {
        return $this->attribute('name', $name);
    }
}
