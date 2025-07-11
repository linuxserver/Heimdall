<?php

namespace Spatie\Html\Elements;

use Spatie\Html\BaseElement;

class Img extends BaseElement
{
    protected $tag = 'img';

    /**
     * @param string|null $alt
     *
     * @return static
     */
    public function alt($alt)
    {
        return $this->attribute('alt', $alt);
    }

    /**
     * @param string|null $src
     *
     * @return static
     */
    public function src($src)
    {
        return $this->attribute('src', $src);
    }

    /**
     * @param string|null $srcset
     *
     * @return static
     */
    public function srcset($srcset)
    {
        return $this->attribute('srcset', $srcset);
    }

    /**
     * @param string|null $loading
     *
     * @return static
     */
    public function loading($loading)
    {
        return $this->attribute('loading', $loading);
    }

    /**
     * @param string|null $crossorigin
     *
     * @return static
     */
    public function crossorigin($crossorigin)
    {
        return $this->attribute('crossorigin', $crossorigin);
    }
}
