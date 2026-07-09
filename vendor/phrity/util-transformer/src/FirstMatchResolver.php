<?php

namespace Phrity\Util\Transformer;

use InvalidArgumentException;

class FirstMatchResolver implements TransformerInterface
{
    /** @var array<TransformerInterface> $transformers */
    private array $transformers;
    private string|null $default;

    /**
     * @param array<TransformerInterface> $transformers
     * @param string|null $default
     */
    public function __construct(array $transformers, string|null $default = null)
    {
        foreach ($transformers as $transformer) {
            if (!$transformer instanceof TransformerInterface) {
                throw new InvalidArgumentException(sprintf(
                    "'%s' is not implementing %s",
                    get_debug_type($transformer),
                    TransformerInterface::class
                ));
            }
        }
        $this->transformers = $transformers;
        $this->default = $default;
    }

    public function canTransform(mixed $subject, string|null $type = null): bool
    {
        $type ??= $this->default;
        foreach ($this->transformers as $transformer) {
            if ($transformer->canTransform($subject, $type)) {
                return true;
            }
        }
        return false;
    }

    public function transform(mixed $subject, string|null $type = null): mixed
    {
        $type ??= $this->default;
        foreach ($this->transformers as $transformer) {
            if ($transformer->canTransform($subject, $type)) {
                return $transformer->transform($subject, $type);
            }
        }
        $subjectType = get_debug_type($subject);
        throw new TransformerException("Could not find transformer for '{$subjectType}'.");
    }
}
