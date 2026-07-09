<?php

namespace Spatie\Html\Elements;

use Spatie\Html\BaseElement;

/**
 * @method static altIf(bool $condition, string|null $alt)
 * @method static altIfNotNull(bool $condition, string|null $alt)
 * @method static altUnless(bool $condition, string|null $alt)
 * @method static srcIf(bool $condition, string|null $src)
 * @method static srcIfNotNull(bool $condition, string|null $src)
 * @method static srcUnless(bool $condition, string|null $src)
 * @method static srcsetIf(bool $condition, string|null $srcset)
 * @method static srcsetIfNotNull(bool $condition, string|null $srcset)
 * @method static srcsetUnless(bool $condition, string|null $srcset)
 * @method static loadingIf(bool $condition, string|null $loading)
 * @method static loadingIfNotNull(bool $condition, string|null $loading)
 * @method static loadingUnless(bool $condition, string|null $loading)
 * @method static crossoriginIf(bool $condition, string|null $crossorigin)
 * @method static crossoriginIfNotNull(bool $condition, string|null $crossorigin)
 * @method static crossoriginUnless(bool $condition, string|null $crossorigin)
 */
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
