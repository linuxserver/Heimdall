<?php

namespace Phrity\Util\Transformer;

use Stringable;

class StringableConverter implements TransformerInterface
{
    private string|null $default;

    public function __construct(bool $perDefault = false)
    {
        $this->default = $perDefault ? Type::STRING : null;
    }

    public function canTransform(mixed $subject, string|null $type = null): bool
    {
        $type ??= $this->default;
        return $subject instanceof Stringable && $type == Type::STRING;
    }

    public function transform(mixed $subject, string|null $type = null): mixed
    {
        $subjectType = get_debug_type($subject);
        if (!$this->canTransform($subject, $type)) {
            throw new TransformerException("Creating stringable for '{$subjectType}' is not supported.");
        }
        return $subject->__toString();
    }
}
