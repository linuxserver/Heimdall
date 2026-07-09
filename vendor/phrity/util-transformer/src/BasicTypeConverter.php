<?php

namespace Phrity\Util\Transformer;

class BasicTypeConverter implements TransformerInterface
{
    /** @var array<string|null> $supportedTypes */
    private static array $supportedTypes = [
        null,
        Type::ARRAY,
        Type::BOOLEAN,
        Type::INTEGER,
        Type::NULL,
        Type::NUMBER,
        Type::OBJECT,
        Type::STRING,
    ];

    /** @var array<string, string> $typeMap */
    private array $typeMap;

    /** @param array<string, string> $typeMap */
    public function __construct(array $typeMap = [])
    {
        $this->typeMap = $typeMap;
    }

    public function canTransform(mixed $subject, string|null $type = null): bool
    {
        return in_array($type, self::$supportedTypes);
    }

    public function transform(mixed $subject, string|null $type = null): mixed
    {
        if (!in_array(gettype($subject), self::$supportedTypes)) {
            $subject = get_debug_type($subject); // Can only extract type name
        }
        $subjectType = gettype($subject);
        $targetType = $type ?? $this->typeMap[$subjectType] ?? $subjectType;

        if (!$this->canTransform($subject, $targetType)) {
            throw new TransformerException("Converting '{$subjectType}' to '{$targetType}' is not supported.");
        }
        /** @var Type::ARRAY|Type::BOOLEAN|Type::INTEGER|Type::NULL|Type::NUMBER|Type::OBJECT|Type::STRING $targetType */
        return match ($targetType) {
            Type::ARRAY => (array)match ($subjectType) {
                Type::OBJECT => get_object_vars($subject),
                default => $subject,
            },
            Type::BOOLEAN => (bool)$subject,
            Type::INTEGER => (int)match ($subjectType) {
                Type::OBJECT => (bool)$subject,
                default => $subject,
            },
            Type::NULL => null,
            Type::NUMBER => (float)match ($subjectType) {
                Type::OBJECT => (bool)$subject,
                default => $subject,
            },
            Type::OBJECT => (object)match ($subjectType) {
                Type::OBJECT => get_object_vars($subject),
                default => $subject,
            },
            Type::STRING => (string)match ($subjectType) {
                Type::ARRAY, Type::OBJECT => get_debug_type($subject),
                default => $subject,
            },
        };
    }
}
