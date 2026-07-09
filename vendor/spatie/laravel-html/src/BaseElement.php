<?php

namespace Spatie\Html;

use BadMethodCallException;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Spatie\Html\Exceptions\InvalidChild;
use Spatie\Html\Exceptions\InvalidHtml;
use Spatie\Html\Exceptions\MissingTag;

/**
 * @method static attributeIf(bool $condition, string $attribute, string|null $value)
 * @method static attributeIfNotNull(bool $condition, string $attribute, string|null $value)
 * @method static attributeUnless(bool $condition, string $attribute, string|null $value)
 * @method static attributesIf(bool $condition, iterable<string> $attributes)
 * @method static attributesIfNotNull(bool $condition, iterable<string> $attributes)
 * @method static attributesUnless(bool $condition, iterable<string> $attributes)
 * @method static forgetAttributeIf(bool $condition, string $attribute)
 * @method static forgetAttributeIfNotNull(bool $condition, string $attribute)
 * @method static forgetAttributeUnless(bool $condition, string $attribute)
 * @method static classIf(bool $condition, string|iterable<string> $class)
 * @method static classIfNotNull(bool $condition, string|iterable<string> $class)
 * @method static classUnless(bool $condition, string|iterable<string> $class)
 * @method static addClassIf(bool $condition, string|iterable<string> $class)
 * @method static addClassIfNotNull(bool $condition, string|iterable<string> $class)
 * @method static addClassUnless(bool $condition, string|iterable<string> $class)
 * @method static idIf(bool $condition, string $id)
 * @method static idIfNotNull(bool $condition, string $id)
 * @method static idUnless(bool $condition, string $id)
 * @method static styleIf(bool $condition, array<string,mixed>|string|null $style)
 * @method static styleIfNotNull(bool $condition, array<string,mixed>|string|null $style)
 * @method static styleUnless(bool $condition, array<string,mixed>|string|null $style)
 * @method static dataIf(bool $condition, string $name, string|null $value)
 * @method static dataIfNotNull(bool $condition, string $name, string|null $value)
 * @method static dataUnless(bool $condition, string $name, string|null $value)
 * @method static ariaIf(bool $condition, string $name, string|null $value)
 * @method static ariaIfNotNull(bool $condition, string $name, string|null $value)
 * @method static ariaUnless(bool $condition, string $name, string|null $value)
 * @method static addChildrenIf(bool $condition, \Spatie\Html\HtmlElement|string|iterable|int|float|null $children, callable|null $mapper)
 * @method static addChildrenIfNotNull(bool $condition, \Spatie\Html\HtmlElement|string|iterable|int|float|null $children, callable|null $mapper)
 * @method static addChildrenUnless(bool $condition, \Spatie\Html\HtmlElement|string|iterable|int|float|null $children, callable|null $mapper)
 * @method static addChildIf(bool $condition, \Spatie\Html\HtmlElement|string|iterable|int|float|null $child, callable|null $mapper)
 * @method static addChildIfNotNull(bool $condition, \Spatie\Html\HtmlElement|string|iterable|int|float|null $child, callable|null $mapper)
 * @method static addChildUnless(bool $condition, \Spatie\Html\HtmlElement|string|iterable|int|float|null $child, callable|null $mapper)
 * @method static childIf(bool $condition, \Spatie\Html\HtmlElement|string|iterable|int|float|null $child, callable|null $mapper)
 * @method static childIfNotNull(bool $condition, \Spatie\Html\HtmlElement|string|iterable|int|float|null $child, callable|null $mapper)
 * @method static childUnless(bool $condition, \Spatie\Html\HtmlElement|string|iterable|int|float|null $child, callable|null $mapper)
 * @method static childrenIf(bool $condition, \Spatie\Html\HtmlElement|string|iterable|int|float|null $children, callable|null $mapper)
 * @method static childrenIfNotNull(bool $condition, \Spatie\Html\HtmlElement|string|iterable|int|float|null $children, callable|null $mapper)
 * @method static childrenUnless(bool $condition, \Spatie\Html\HtmlElement|string|iterable|int|float|null $children, callable|null $mapper)
 * @method static setChildrenIf(bool $condition, \Spatie\Html\HtmlElement[] $children, callable|null $mapper)
 * @method static setChildrenIfNotNull(bool $condition, \Spatie\Html\HtmlElement[] $children, callable|null $mapper)
 * @method static setChildrenUnless(bool $condition, \Spatie\Html\HtmlElement[] $children, callable|null $mapper)
 * @method static prependChildrenIf(bool $condition, \Spatie\Html\HtmlElement|string|iterable|int|float|null $children, callable|null $mapper)
 * @method static prependChildrenIfNotNull(bool $condition, \Spatie\Html\HtmlElement|string|iterable|int|float|null $children, callable|null $mapper)
 * @method static prependChildrenUnless(bool $condition, \Spatie\Html\HtmlElement|string|iterable|int|float|null $children, callable|null $mapper)
 * @method static prependChildIf(bool $condition, \Spatie\Html\HtmlElement|string|iterable|int|float|null $children, callable|null $mapper)
 * @method static prependChildIfNotNull(bool $condition, \Spatie\Html\HtmlElement|string|iterable|int|float|null $children, callable|null $mapper)
 * @method static prependChildUnless(bool $condition, \Spatie\Html\HtmlElement|string|iterable|int|float|null $children, callable|null $mapper)
 * @method static textIf(bool $condition, string|null $text)
 * @method static textIfNotNull(bool $condition, string|null $text)
 * @method static textUnless(bool $condition, string|null $text)
 * @method static htmlIf(bool $condition, string|null $html)
 * @method static htmlIfNotNull(bool $condition, string|null $html)
 * @method static htmlUnless(bool $condition, string|null $html)
 */
abstract class BaseElement implements Htmlable, HtmlElement
{
    use Conditionable {
        unless as trait_unless;
    }

    use Macroable {
        __call as __macro_call;
    }

    /** @var string */
    protected $tag;

    /** @var \Spatie\Html\Attributes */
    protected $attributes;

    /** @var \Illuminate\Support\Collection */
    protected $children;

    public function __construct()
    {
        if (empty($this->tag)) {
            throw MissingTag::onClass(static::class);
        }

        $this->attributes = new Attributes();
        $this->children = new Collection();
    }

    public static function create()
    {
        return new static();
    }

    /**
     * @param string $attribute
     * @param string|null $value
     *
     * @return static
     */
    public function attribute($attribute, $value = null)
    {
        $element = clone $this;

        $element->attributes->setAttribute($attribute, (string) $value);

        return $element;
    }

    /**
     * @param iterable $attributes
     *
     * @return static
     */
    public function attributes($attributes)
    {
        $element = clone $this;

        $element->attributes->setAttributes($attributes);

        return $element;
    }

    /**
     * @param string $attribute
     *
     * @return static
     */
    public function forgetAttribute($attribute)
    {
        $element = clone $this;

        $element->attributes->forgetAttribute($attribute);

        return $element;
    }

    /**
     * @param string $attribute
     * @param mixed $fallback
     *
     * @return mixed
     */
    public function getAttribute($attribute, $fallback = null)
    {
        return $this->attributes->getAttribute($attribute, $fallback);
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function hasAttribute($attribute)
    {
        return $this->attributes->hasAttribute($attribute);
    }

    /**
     * @param iterable|string $class
     *
     * @return static
     */
    public function class($class)
    {
        return $this->addClass($class);
    }

    /**
     * Alias for `class`.
     *
     * @param iterable|string $class
     *
     * @return static
     */
    public function addClass($class)
    {
        $element = clone $this;

        $element->attributes->addClass($class);

        return $element;
    }

    /**
     * @param string $id
     *
     * @return static
     */
    public function id($id)
    {
        return $this->attribute('id', $id);
    }

    /**
     * @param array|string|null $style
     *
     * @return static
     */
    public function style($style)
    {
        if (is_array($style)) {
            $style = implode('; ', array_map(function ($value, $attribute) {
                return "{$attribute}: {$value}";
            }, $style, array_keys($style)));
        }

        return $this->attribute('style', $style);
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return static
     */
    public function data($name, $value = null)
    {
        return $this->attribute("data-{$name}", $value);
    }

    /**
     * @param string $attribute
     * @param string|null $value
     *
     * @return static
     */
    public function aria($attribute, $value = null)
    {
        return $this->attribute("aria-{$attribute}", $value);
    }

    /**
     * @param \Spatie\Html\HtmlElement|string|iterable|int|float|null $children
     * @param callable|null $mapper
     *
     * @return static
     */
    public function addChildren($children, $mapper = null)
    {
        if (is_null($children)) {
            return $this;
        }

        $children = $this->parseChildren($children, $mapper);

        $element = clone $this;

        $element->children = $element->children->merge($children);

        return $element;
    }

    /**
     * Alias for `addChildren`.
     *
     * @param \Spatie\Html\HtmlElement|string|iterable|int|float|null $child
     * @param callable|null $mapper
     *
     * @return static
     */
    public function addChild($child, $mapper = null)
    {
        return $this->addChildren($child, $mapper);
    }

    /**
     * Alias for `addChildren`.
     *
     * @param \Spatie\Html\HtmlElement|string|iterable|int|float|null $child
     * @param callable|null $mapper
     *
     * @return static
     */
    public function child($child, $mapper = null)
    {
        return $this->addChildren($child, $mapper);
    }

    /**
     * Alias for `addChildren`.
     *
     * @param \Spatie\Html\HtmlElement|string|iterable|int|float|null $children
     * @param callable|null $mapper
     *
     * @return static
     */
    public function children($children, $mapper = null)
    {
        return $this->addChildren($children, $mapper);
    }

    /**
     * Replace all children with an array of elements.
     *
     * @param \Spatie\Html\HtmlElement[] $children
     * @param callable|null $mapper
     *
     * @return static
     */
    public function setChildren($children, $mapper = null)
    {
        $element = clone $this;

        $element->children = new Collection();

        return $element->addChildren($children, $mapper);
    }

    /**
     * @param \Spatie\Html\HtmlElement|string|iterable|int|float|null $children
     * @param callable|null $mapper
     *
     * @return static
     */
    public function prependChildren($children, $mapper = null)
    {
        $children = $this->parseChildren($children, $mapper);

        $element = clone $this;

        $element->children = $children->merge($element->children);

        return $element;
    }

    /**
     * Alias for `prependChildren`.
     *
     * @param \Spatie\Html\HtmlElement|string|iterable|int|float|null $children
     * @param callable|null $mapper
     *
     * @return static
     */
    public function prependChild($children, $mapper = null)
    {
        return $this->prependChildren($children, $mapper);
    }

    /**
     * @param string|null $text
     *
     * @return static
     */
    public function text($text)
    {
        return $this->html(htmlentities($text ?? '', ENT_QUOTES, 'UTF-8', false));
    }

    /**
     * @param string|null $html
     *
     * @return static
     */
    public function html($html)
    {
        if ($this->isVoidElement()) {
            throw new InvalidHtml("Can't set inner contents on `{$this->tag}` because it's a void element");
        }

        return $this->setChildren($html);
    }

    /**
     * Conditionally transform the element. Note that since elements are
     * immutable, you'll need to return a new instance from the callback.
     *
     * @param bool $condition
     * @param \Closure $callback
     *
     * @return mixed
     */
    public function if(bool $condition, \Closure $callback)
    {
        return $condition ? $callback($this) : $this;
    }

    /**
     * Conditionally transform the element. Note that since elements are
     * immutable, you'll need to return a new instance from the callback.
     *
     * @param mixed $condition
     * @param callable $callback
     * @param callable|null $default
     *
     * @return mixed
     */
    public function unless($condition, callable $callback, callable|null $default = null)
    {
        if (! is_bool($condition) || ! $callback instanceof \Closure || ! is_null($default)) {
            return $this->trait_unless($condition, $callback, $default);
        }

        return $this->if(! $condition, $callback);
    }

    /**
     * Conditionally transform the element. Note that since elements are
     * immutable, you'll need to return a new instance from the callback.
     *
     * @param mixed $value
     * @param \Closure $callback
     *
     * @return mixed
     */
    public function ifNotNull($value, \Closure $callback)
    {
        return ! is_null($value) ? $callback($this) : $this;
    }

    /**
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function open()
    {
        $tag = $this->attributes->isEmpty()
            ? '<' . $this->tag . '>'
            : "<{$this->tag} {$this->attributes->render()}>";

        $children = $this->children->map(function ($child): string {
            if ($child instanceof HtmlElement) {
                return $child->render();
            }

            if (is_null($child)) {
                return '';
            }

            if (is_string($child) || is_numeric($child)) {
                return $child;
            }

            throw InvalidChild::childMustBeAnHtmlElementOrAString();
        })->implode('');

        return new HtmlString($tag . $children);
    }

    /**
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function close()
    {
        return new HtmlString(
            $this->isVoidElement()
                ? ''
                : "</{$this->tag}>"
        );
    }

    /**
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function render()
    {
        return new HtmlString(
            $this->open() . $this->close()
        );
    }

    public function isVoidElement(): bool
    {
        return in_array($this->tag, [
            'area', 'base', 'br', 'col', 'embed', 'hr',
            'img', 'input', 'keygen', 'link', 'menuitem',
            'meta', 'param', 'source', 'track', 'wbr',
        ]);
    }

    /**
     * Dynamically handle calls to the class.
     * Check for methods finishing by If or fallback to Macroable.
     *
     * @param  string  $name
     * @param  array   $arguments
     * @return mixed
     *
     * @throws BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        if (Str::endsWith($name, $conditions = ['If', 'Unless', 'IfNotNull'])) {
            foreach ($conditions as $condition) {
                if (! method_exists($this, $method = str_replace($condition, '', $name))) {
                    continue;
                }

                return $this->callConditionalMethod($condition, $method, $arguments);
            }
        }

        return $this->__macro_call($name, $arguments);
    }

    protected function callConditionalMethod($type, $method, array $arguments)
    {
        $value = array_shift($arguments);
        $callback = function () use ($method, $arguments) {
            return $this->{$method}(...$arguments);
        };

        switch ($type) {
            case 'If':
                return $this->if((bool) $value, $callback);
            case 'Unless':
                return $this->unless((bool) $value, $callback);
            case 'IfNotNull':
                return $this->ifNotNull($value, $callback);
            default:
                return $this;
        }
    }

    public function __clone()
    {
        $this->attributes = clone $this->attributes;
        $this->children = clone $this->children;
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public function toHtml(): string
    {
        return $this->render();
    }

    protected function parseChildren($children, $mapper = null): Collection
    {
        if ($children instanceof HtmlElement) {
            $children = [$children];
        } elseif ($children instanceof Htmlable) {
            $children = $children->toHtml();
        }

        $children = Collection::make($children);

        if ($mapper) {
            $children = $children->map($mapper);
        }

        $this->guardAgainstInvalidChildren($children);

        return $children;
    }

    protected function guardAgainstInvalidChildren(Collection $children)
    {
        foreach ($children as $child) {
            if ($child instanceof HtmlElement || is_null($child) || is_string($child) || is_numeric($child)) {
                continue;
            }

            throw InvalidChild::childMustBeAnHtmlElementOrAString();
        }
    }
}
