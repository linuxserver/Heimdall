<?php

namespace Phrity\Util\Transformer;

class ReversedReadableConverter implements TransformerInterface
{
    public function canTransform(mixed $subject, string|null $type = null): bool
    {
        $subjectType = gettype($subject);

        if ($subjectType != Type::STRING || !in_array($type, [Type::BOOLEAN, Type::NULL, null])) {
            return false; // Must be string
        }
        return (in_array(strtolower($subject), ['true', 'false', '1', '0', 'null', '']));
    }

    public function transform(mixed $subject, string|null $type = null): mixed
    {
        $subjectType = gettype($subject);
        if (!$this->canTransform($subject, $type)) {
            throw new TransformerException("Creating reversed readable for '{$subjectType}' is not supported.");
        }

        /** @var "true"|"false"|"null"|"1"|"0"|"" $subject */
        $subject = strtolower($subject);
        return match ($subject) {
            'true', '1' => true,
            'false', '0' => false,
            'null' => null,
            '' => $type == Type::NULL ? null : false,
        };
    }
}
