<?php

namespace Spatie\Html\Elements;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Html\BaseElement;
use Spatie\Html\Elements\Attributes\Autocomplete;
use Spatie\Html\Elements\Attributes\Autofocus;
use Spatie\Html\Elements\Attributes\Disabled;
use Spatie\Html\Elements\Attributes\Name;
use Spatie\Html\Elements\Attributes\ReadonlyTrait;
use Spatie\Html\Elements\Attributes\Required;
use Spatie\Html\Selectable;

class Select extends BaseElement
{
    use Autofocus;
    use Autocomplete;
    use Disabled;
    use Name;
    use Required;
    use ReadonlyTrait;

    /** @var string */
    protected $tag = 'select';

    /** @var array */
    protected $options = [];

    /** @var string|iterable */
    protected $value = '';

    /**
     * @return static
     */
    public function multiple()
    {
        $element = clone $this;

        $element = $element->attribute('multiple');

        $name = $element->getAttribute('name');

        if ($name && ! Str::endsWith($name, '[]')) {
            $element = $element->name($name.'[]');
        }

        $element->applyValueToOptions();

        return $element;
    }

    /**
     * @param iterable $options
     *
     * @return static
     */
    public function options($options)
    {
        return $this->addChildren($options, function ($text, $value) {
            if (is_array($text) || $text instanceof Collection) {
                return $this->optgroup($value, $text);
            }

            return Option::create()
                ->value($value)
                ->text($text)
                ->selectedIf($value === $this->value);
        });
    }

    /**
     * @param string $label
     * @param iterable $options
     *
     * @return static
     */
    public function optgroup($label, $options)
    {
        return Optgroup::create()
            ->label($label)
            ->addChildren($options, function ($text, $value) {
                return Option::create()
                    ->value($value)
                    ->text($text)
                    ->selectedIf($value === $this->value);
            });
    }

    /**
     * @param string|null $text
     *
     * @return static
     */
    public function placeholder($text)
    {
        return $this->prependChild(
            Option::create()
                ->value(null)
                ->text($text)
                ->selectedIf(! $this->hasSelection())
        );
    }

    /**
     * @param string|iterable $value
     *
     * @return static
     */
    public function value($value = null)
    {
        $element = clone $this;

        $element->value = $value;

        $element->applyValueToOptions();

        return $element;
    }

    protected function hasSelection()
    {
        return $this->children->contains->hasAttribute('selected');
    }

    protected function applyValueToOptions()
    {
        $value = $this->value instanceof \Illuminate\Support\Collection
            ? $this->value
            : Collection::make($this->value);

        if (! $this->hasAttribute('multiple')) {
            $value = $value->take(1);
        }

        $this->children = $this->applyValueToElements($value, $this->children);
    }

    protected function applyValueToElements($value, Collection $children)
    {
        return $children->map(function ($child) use ($value) {
            if ($child instanceof Optgroup) {
                return $child->setChildren($this->applyValueToElements($value, $child->children));
            }

            if ($child instanceof Selectable) {
                return $child->selectedIf($value->contains($child->getAttribute('value')));
            }

            return $child;
        });
    }
}
