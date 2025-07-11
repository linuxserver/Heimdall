<?php

namespace Spatie\Html\Elements;

use Spatie\Html\BaseElement;
use Spatie\Html\Elements\Attributes\Disabled;
use Spatie\Html\Elements\Attributes\Value;
use Spatie\Html\Selectable;

class Option extends BaseElement implements Selectable
{
    use Disabled;
    use Value;

    /** @var string */
    protected $tag = 'option';

    /**
     * @return static
     */
    public function selected()
    {
        return $this->attribute('selected', 'selected');
    }

    /**
     * @param bool $condition
     *
     * @return static
     */
    public function selectedIf($condition)
    {
        return $condition ?
            $this->selected() :
            $this->unselected();
    }

    /**
     * @return static
     */
    public function unselected()
    {
        return $this->forgetAttribute('selected');
    }
}
