<?php

namespace Spatie\Html\Elements;

use Spatie\Html\BaseElement;
use Spatie\Html\Elements\Attributes\Disabled;

/**
 * @method static legendIf(bool $condition, \Spatie\Html\HtmlElement|string $contents)
 * @method static legendIfNotNull(bool $condition, \Spatie\Html\HtmlElement|string $contents)
 * @method static legendUnless(bool $condition, \Spatie\Html\HtmlElement|string $contents)
 */
class Fieldset extends BaseElement
{
    use Disabled;

    protected $tag = 'fieldset';

    /**
     * @param \Spatie\Html\HtmlElement|string $contents
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
