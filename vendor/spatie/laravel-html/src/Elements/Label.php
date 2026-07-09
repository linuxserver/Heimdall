<?php

namespace Spatie\Html\Elements;

use Spatie\Html\BaseElement;

/**
 * @method static forIf(bool $condition, string|null $for)
 * @method static forIfNotNull(bool $condition, string|null $for)
 * @method static forUnless(bool $condition, string|null $for)
 */
class Label extends BaseElement
{
    protected $tag = 'label';

    /**
     * @param string|null $for
     *
     * @return static
     */
    public function for($for)
    {
        return $this->attribute('for', $for);
    }
}
