<?php

namespace Spatie\Html;

use Spatie\Html\Helpers\Arr;

class Attributes
{
    /** @var array */
    protected $attributes = [];

    /** @var array */
    protected $classes = [];

    /**
     * @param iterable $attributes
     *
     * @return $this
     */
    public function setAttributes($attributes)
    {
        foreach ($attributes as $attribute => $value) {
            if ($attribute === 'class') {
                $this->addClass($value);

                continue;
            }

            if (is_int($attribute)) {
                $attribute = $value;
                $value = '';
            }

            $this->setAttribute($attribute, (string) $value);
        }

        return $this;
    }

    /**
     * @param string $attribute
     * @param string|null $value
     *
     * @return $this
     */
    public function setAttribute($attribute, $value = null)
    {
        if ($attribute === 'class') {
            $this->addClass($value);

            return $this;
        }

        $this->attributes[$attribute] = $value;

        return $this;
    }

    /**
     * @param string $attribute
     *
     * @return $this
     */
    public function forgetAttribute($attribute)
    {
        if ($attribute === 'class') {
            $this->classes = [];

            return $this;
        }

        if (isset($this->attributes[$attribute])) {
            unset($this->attributes[$attribute]);
        }

        return $this;
    }

    /**
     * @param string $attribute
     * @param mixed $fallback
     *
     * @return mixed
     */
    public function getAttribute($attribute, $fallback = null)
    {
        if ($attribute === 'class') {
            return implode(' ', $this->classes);
        }

        return $this->attributes[$attribute] ?? $fallback;
    }

    public function hasAttribute(string $attribute): bool
    {
        return array_key_exists($attribute, $this->attributes);
    }

    /**
     * @param string|iterable $class
     */
    public function addClass($class)
    {
        if (is_string($class)) {
            $class = explode(' ', $class);
        }

        $class = Arr::getToggledValues($class);

        $this->classes = array_unique(
            array_merge($this->classes, $class)
        );
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->attributes) && empty($this->classes);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        if (empty($this->classes)) {
            return $this->attributes;
        }

        return array_merge(['class' => implode(' ', $this->classes)], $this->attributes);
    }

    /**
     * @return string
     */
    public function render()
    {
        if ($this->isEmpty()) {
            return '';
        }

        $attributeStrings = [];

        foreach ($this->toArray() as $attribute => $value) {
            if ($value === '') {
                $attributeStrings[] = $attribute;

                continue;
            }

            $value = htmlentities($value, ENT_QUOTES, 'UTF-8', false);

            $attributeStrings[] = "{$attribute}=\"{$value}\"";
        }

        return implode(' ', $attributeStrings);
    }
}
