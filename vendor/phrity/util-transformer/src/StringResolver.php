<?php

namespace Phrity\Util\Transformer;

class StringResolver implements TransformerInterface
{
    private BasicTypeConverter $converter;
    private TransformerInterface $transformer;

    public function __construct(TransformerInterface|null $transformer = null)
    {
        $this->converter = new BasicTypeConverter();
        $this->transformer = $transformer ?? $this->converter;
    }

    public function canTransform(mixed $subject, string|null $type = null): bool
    {
        return true;
    }

    public function transform(mixed $subject, string|null $type = null): mixed
    {
        $subjectType = get_debug_type($subject);
        if ($this->transformer->canTransform($subject, $type)) {
            // Convert using configured transformes and type
            $subject = $this->transformer->transform($subject, $type);
        }
        if (is_string($subject)) {
            // If we get string, done here
            return $subjectType == Type::STRING ? $this->string($subject) : $subject;
        }
        if (is_object($subject)) {
            // Force object result to array using basic type converter
            $subject = $this->converter->transform($subject, Type::ARRAY);
        }
        if (is_array($subject)) {
            // Descend into recursive worker
            return $this->wrap($subject, $type, array_is_list($subject));
        }
        // Force non-string scalars to string
        return $this->converter->transform($subject, Type::STRING);
    }

    /**
     * @param array<array-key, mixed> $items
     */
    private function wrap(array $items, string|null $type, bool $isList): string
    {
        return sprintf(
            '%s%s%s',
            $isList ? '[' : '{',
            implode(', ', $this->map($items, $type, $isList)),
            $isList ? ']' : '}'
        );
    }

    /**
     * @param array<array-key, mixed> $items
     * @return array<array-key, mixed>
     */
    private function map(array $items, string|null $type, bool $isList): array
    {
        return array_map(function (mixed $value, mixed $key) use ($type, $isList): mixed {
            return $this->index($this->transform($value, $type), $isList ? null : $key);
        }, $items, array_keys($items));
    }

    private function index(mixed $value, string|int|null $key = null): string
    {
        return is_null($key) ? $value : "{$key}: {$value}";
    }

    private function string(mixed $value): string
    {
        return is_string($value) ? "\"{$value}\"" : $value;
    }
}
