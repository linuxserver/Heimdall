<?php

namespace Spatie\Html\Elements;

use Spatie\Html\BaseElement;
use Spatie\Html\Elements\Attributes\Disabled;

/**
 * @method static labelIf(bool $condition, string|null $label)
 * @method static labelIfNotNull(bool $condition, string|null $label)
 * @method static labelUnless(bool $condition, string|null $label)
 */
class Optgroup extends BaseElement
{
    use Disabled;

    protected $tag = 'optgroup';

    /**
     * @param string|null $label
     *
     * @return static
     */
    public function label($label)
    {
        return $this->attribute('label', $label);
    }
}
