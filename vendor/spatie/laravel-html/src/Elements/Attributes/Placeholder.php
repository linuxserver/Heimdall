<?php

namespace Spatie\Html\Elements\Attributes;

trait Placeholder
{
    /**
     * @param string|null $placeholder
     *
     * @return static
     */
    public function placeholder($placeholder)
    {
        return $this->attribute('placeholder', $placeholder);
    }
}
