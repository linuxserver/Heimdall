<?php

namespace Phrity\Util;

use Phrity\Util\Transformer\{
    BasicTypeConverter,
    TransformerInterface,
    Type,
};

/**
 * Accessor utility trait.
 */
trait AccessorTrait
{
    private TransformerInterface|null $accessorTransformer = null;

    /**
     * Recursive worker function for get() operation.
     * @param mixed $data Data set to access
     * @param array<string> $path Path to access
     * @param mixed $default Default value
     * @param string|null $coerce Optional type coercion
     * @return mixed Specified content of data set
     */
    private function accessorGet(mixed $data, array $path, mixed $default, string|null $coerce = null): mixed
    {
        if (empty($path)) {
            if ($coerce && $this->accessorGetTransformer()->canTransform($data, $coerce)) {
                return $this->accessorGetTransformer()->transform($data, $coerce);
            }
            return $data; // Bottom case
        }
        $current = array_shift($path);
        if ($this->accessorGetTransformer()->canTransform($data)) {
            $data = $this->accessorGetTransformer()->transform($data);
        }
        if (is_array($data) && array_key_exists($current, $data)) {
            return $this->accessorGet($data[$current], $path, $default, $coerce);
        }
        if (is_object($data) && property_exists($data, $current)) {
            return $this->accessorGet($data->$current, $path, $default, $coerce);
        }
        return $default; // No match
    }

    /**
     * Recursive worker function for has() operation.
     * @param mixed $data Data set to access
     * @param array<string> $path Path to access
     * @return bool If speciefied content is present
     */
    private function accessorHas(mixed $data, array $path): bool
    {
        if (empty($path)) {
            return true; // Bottom case
        }
        $current = array_shift($path);
        if ($this->accessorGetTransformer()->canTransform($data)) {
            $data = $this->accessorGetTransformer()->transform($data);
        }
        if (is_array($data) && array_key_exists($current, $data)) {
            return $this->accessorHas($data[$current], $path);
        }
        if (is_object($data) && property_exists($data, $current)) {
            return $this->accessorHas($data->$current, $path);
        }
        return false; // No match
    }

    /**
     * Recursive worker function for set() operation.
     * @param mixed $data Data set to modify
     * @param array<string> $path Path to access
     * @param mixed $value Value to set
     * @return mixed Modified data set
     */
    private function accessorSet(mixed $data, array $path, mixed $value): mixed
    {
        if (empty($path)) {
            return $value; // Bottom case
        }
        $current = array_shift($path);
        if ($this->accessorGetTransformer()->canTransform($data)) {
            $data = $this->accessorGetTransformer()->transform($data);
        }
        if (is_object($data)) {
            $data->$current = $this->accessorSet($data->$current ?? null, $path, $value);
        }
        if (is_array($data)) {
            $data[$current] = $this->accessorSet($data[$current] ?? null, $path, $value);
        }
        if (is_null($data) || is_scalar($data)) {
            $data = [];
            $data[$current] = $this->accessorSet(null, $path, $value);
        }
        return $data;
    }

    /**
     * Parse string path into array segments.
     * @param string $path Path to parse
     * @param non-empty-string $separator Separator token
     * @return array<string> Path segments as array
     */
    private function accessorParsePath(string $path, string $separator): array
    {
        return array_filter(explode($separator, $path), function (string $item): bool {
            return $item !== '';
        });
    }

    /**
     * Get or set Transformer to use, BasicTypeConverter used as default.
     * @return TransformerInterface
     */
    private function accessorGetTransformer(): TransformerInterface
    {
        if (!$this->accessorTransformer) {
            $this->accessorTransformer = new BasicTypeConverter(); // Default type converter
        }
        return $this->accessorTransformer;
    }
}
