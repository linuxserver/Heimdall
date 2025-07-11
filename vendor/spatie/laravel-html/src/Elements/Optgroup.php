<?php

namespace Spatie\Html\Elements;

use Spatie\Html\BaseElement;
use Spatie\Html\Elements\Attributes\Disabled;

class Optgroup extends BaseElement
{
    use Disabled;

    protected $tag = 'optgroup';

    /**
     * @param string|null $href
     *
     * @return static
     */
    public function label($label)
    {
        return $this->attribute('label', $label);
    }
}
