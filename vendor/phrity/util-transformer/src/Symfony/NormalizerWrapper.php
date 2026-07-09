<?php

namespace Phrity\Util\Transformer\Symfony;

use Phrity\Util\Transformer\{
    TransformerException,
    TransformerInterface,
    Type,
};
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class NormalizerWrapper implements TransformerInterface
{
    public function __construct(
        private NormalizerInterface $normalizer,
    ) {
    }

    public function canTransform(mixed $subject, string|null $type = null): bool
    {
        if (!$this->normalizer->supportsNormalization($subject)) {
            return false;
        }
        return in_array($type, [null, gettype($this->normalizer->normalize($subject))]);
    }

    public function transform(mixed $subject, string|null $type = null): mixed
    {
        if (!$this->canTransform($subject, $type)) {
            $subjectType = get_debug_type($subject);
            $class = get_debug_type($this->normalizer);
            throw new TransformerException("Normalizer {$class} to not support '{$subjectType}'.");
        }
        return $this->normalizer->normalize($subject);
    }
}
