<?php

namespace Phrity\Util\Transformer;

use JsonSerializable;

class JsonSerializableConverter implements TransformerInterface
{
    public function canTransform(mixed $subject, string|null $type = null): bool
    {
        if (!$subject instanceof JsonSerializable) {
            return false;
        }
        return in_array($type, [null, gettype($subject->jsonSerialize())]);
    }

    public function transform(mixed $subject, string|null $type = null): mixed
    {
        $subjectType = get_debug_type($subject);
        if (!$this->canTransform($subject, $type)) {
            throw new TransformerException("JSON serialize for '{$subjectType}' is not supported.");
        }
        return $subject->jsonSerialize();
    }
}
