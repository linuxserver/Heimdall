<?php

namespace Spatie\Html\Elements;

use Spatie\Html\BaseElement;
use Spatie\Html\Elements\Attributes\Disabled;

class Fieldset extends BaseElement
{
    use Disabled;

    protected $tag = 'fieldset';

    /**
     * @param \Spatie\Html\HtmlElement|string $text
     *
     * @return static
     */
    public function legend($contents)
    {
        return $this->prependChild(
            Legend::create()->text($contents)
        );
    }
}
