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

class Textarea extends BaseElement
{
    use Autofocus;
    use Autocomplete;
    use Placeholder;
    use Name;
    use Required;
    use Disabled;
    use ReadonlyTrait;
    use MinMaxLength;

    protected $tag = 'textarea';

    /**
     * @param string|null $value
     *
     * @return static
     * @throws \Spatie\Html\Exceptions\InvalidHtml
     */
    public function value($value)
    {
        return $this->html($value);
    }

    /**
     * @param int $rows
     *
     * @return static
     */
    public function rows(int $rows)
    {
        return $this->attribute('rows', $rows);
    }

    /**
     * @param int $cols
     *
     * @return static
     */
    public function cols(int $cols)
    {
        return $this->attribute('cols', $cols);
    }
}
