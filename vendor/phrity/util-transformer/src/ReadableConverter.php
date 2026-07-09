<?php

namespace Phrity\Util\Transformer;

class ReadableConverter implements TransformerInterface
{
    private string|null $default;

    public function __construct(bool $perDefault = false)
    {
        $this->default = $perDefault ? Type::STRING : null;
    }

    public function canTransform(mixed $subject, string|null $type = null): bool
    {
        $type ??= $this->default;
        return in_array(gettype($subject), [Type::BOOLEAN, Type::NULL]) && $type == Type::STRING;
    }

    public function transform(mixed $subject, string|null $type = null): mixed
    {
        $subjectType = gettype($subject);
        if (!$this->canTransform($subject, $type)) {
            throw new TransformerException("Creating readable for '{$subjectType}' is not supported.");
        }
        /** @var Type::BOOLEAN|Type::NULL $subjectType */
        return match ($subjectType) {
            Type::BOOLEAN => $subject ? 'true' : 'false',
            Type::NULL => 'null',
        };
    }
}
