<?php

namespace Phrity\Util;

use Phrity\Util\Transformer\TransformerInterface;

/**
 * PathAccessor utility class.
 */
class PathAccessor
{
    use AccessorTrait;

    /**
     * @var string $path Path
     */
    protected string $path;

    /**
     * @var non-empty-string $separator Separator
     */
    protected string $separator;

    /**
     * Constructor for this class.
     * @param string $path Path
     * @param non-empty-string $separator Separator
     * @param TransformerInterface|null $transformer Transformer
     */
    public function __construct(string $path, string $separator = '/', TransformerInterface|null $transformer = null)
    {
        $this->path = $path;
        $this->separator = $separator;
        $this->accessorTransformer = $transformer;
    }

    /**
     * Get specified content from data set.
     * @param mixed $data Data set to access
     * @param mixed $default Default value
     * @return mixed Specified content of data set
     */
    public function get(mixed $data, mixed $default = null, string|null $coerce = null): mixed
    {
        return $this->accessorGet($data, $this->accessorParsePath($this->path, $this->separator), $default, $coerce);
    }

    /**
     * Check specified content in data set.
     * @param mixed $data Data set to access
     * @return bool If speciefied content is present
     */
    public function has(mixed $data): bool
    {
        return $this->accessorHas($data, $this->accessorParsePath($this->path, $this->separator));
    }

    /**
     * Set specified content on data set.
     * @param mixed $data Data set to modify
     * @param mixed $value Value to set
     * @return mixed Modified data set
     */
    public function set(mixed $data, mixed $value): mixed
    {
        return $this->accessorSet($data, $this->accessorParsePath($this->path, $this->separator), $value);
    }
}
