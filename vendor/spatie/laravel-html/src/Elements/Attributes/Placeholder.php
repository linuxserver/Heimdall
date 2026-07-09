<?php

namespace Spatie\Html\Elements\Attributes;

/**
 * @method static placeholderIf(bool $condition, string|null $placeholder)
 * @method static placeholderIfNotNull(bool $condition, string|null $placeholder)
 * @method static placeholderUnless(bool $condition, string|null $placeholder)
 */
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
