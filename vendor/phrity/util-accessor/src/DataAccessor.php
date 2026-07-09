<?php

namespace Phrity\Util;

use JsonSerializable;
use Phrity\Util\Transformer\TransformerInterface;

/**
 * DataAccessor utility class.
 */
class DataAccessor implements JsonSerializable
{
    use AccessorTrait;

    /**
     * @var mixed $data Data set
     */
    protected mixed $data;

    /**
     * @var non-empty-string $separator Separator
     */
    protected string $separator;

    /**
     * Constructor for this class.
     * @param mixed $data Data set to access
     * @param non-empty-string $separator Separator
     * @param TransformerInterface|null $transformer Transformer
     */
    public function __construct(mixed $data, string $separator = '/', TransformerInterface|null $transformer = null)
    {
        $this->data = $data;
        $this->separator = $separator;
        $this->accessorTransformer = $transformer;
    }

    /**
     * Get specified content from data set.
     * @param string $path Path to access
     * @param mixed $default Default value
     * @param string|null $coerce Optional type coercion
     * @return mixed Specified content of data set
     */
    public function get(string $path, mixed $default = null, string|null $coerce = null): mixed
    {
        return $this->accessorGet($this->data, $this->accessorParsePath($path, $this->separator), $default, $coerce);
    }

    /**
     * Check specified content in data set.
     * @param string $path Path to access
     * @return bool If speciefied content is present
     */
    public function has(string $path): bool
    {
        return $this->accessorHas($this->data, $this->accessorParsePath($path, $this->separator));
    }

    /**
     * Set specified content on data set.
     * @param string $path Path to access
     * @param mixed $value Value to set
     * @return mixed Modified data set
     */
    public function set(string $path, mixed $value): mixed
    {
        $this->data = $this->accessorSet($this->data, $this->accessorParsePath($path, $this->separator), $value);
        return $this->data;
    }

    public function jsonSerialize(): mixed
    {
        return $this->data;
    }
}
