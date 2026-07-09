<?php

namespace Phrity\Util\Transformer;

class RecursionResolver implements TransformerInterface
{
    private TransformerInterface $transformer;

    public function __construct(TransformerInterface $transformer)
    {
        $this->transformer = $transformer;
    }

    public function canTransform(mixed $subject, string|null $type = null): bool
    {
        return $this->transformer->canTransform($subject, $type);
    }

    public function transform(mixed $subject, string|null $type = null): mixed
    {
        $transformed = $this->transformer->transform($subject, $type);
        if (is_array($transformed)) {
            foreach ($transformed as $key => $content) {
                if (is_array($content) || is_object($content)) {
                    $transformed[$key] = $this->transform($content, $type);
                }
            }
        }
        if (is_object($transformed)) {
            foreach ((array)$transformed as $key => $content) {
                if (is_array($content) || is_object($content)) {
                    $transformed->$key = $this->transform($content, $type);
                }
            }
        }
        return $transformed;
    }
}
