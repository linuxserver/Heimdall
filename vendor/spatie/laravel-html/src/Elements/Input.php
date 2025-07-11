<?php

namespace Spatie\Html\Elements;

use Spatie\Html\BaseElement;
use Spatie\Html\Elements\Attributes\Autocomplete;
use Spatie\Html\Elements\Attributes\Autofocus;
use Spatie\Html\Elements\Attributes\Disabled;
use Spatie\Html\Elements\Attributes\MinMaxLength;
use Spatie\Html\Elements\Attributes\Name;
use Spatie\Html\Elements\Attributes\Placeholder;
use Spatie\Html\Elements\Attributes\ReadonlyTrait;
use Spatie\Html\Elements\Attributes\Required;
use Spatie\Html\Elements\Attributes\Type;
use Spatie\Html\Elements\Attributes\Value;

class Input extends BaseElement
{
    use Autofocus;
    use Autocomplete;
    use Disabled;
    use MinMaxLength;
    use Name;
    use Placeholder;
    use ReadonlyTrait;
    use Required;
    use Type;
    use Value;

    protected $tag = 'input';

    /**
     * @return static
     */
    public function unchecked()
    {
        return $this->checked(false);
    }

    /**
     * @param bool $checked
     *
     * @return static
     */
    public function checked($checked = true)
    {
        return $checked
            ? $this->attribute('checked', 'checked')
            : $this->forgetAttribute('checked');
    }

    /**
     * @param string|null $size
     *
     * @return static
     */
    public function size($size)
    {
        return $this->attribute('size', $size);
    }
}
