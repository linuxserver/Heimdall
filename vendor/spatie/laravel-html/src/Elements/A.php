<?php

namespace Spatie\Html\Elements;

use Spatie\Html\BaseElement;
use Spatie\Html\Elements\Attributes\Target;

class A extends BaseElement
{
    use Target;

    protected $tag = 'a';

    /**
     * @param string|null $href
     *
     * @return static
     */
    public function href($href)
    {
        return $this->attribute('href', $href);
    }

    /**
     * @param string|null $route
     * @param mixed $params
     *
     * @return static
     */
    public function route($route, ...$params)
    {
        return $this->href(route($route, ...$params));
    }
}
