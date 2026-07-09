<?php

namespace Phrity\Util\Transformer;

use InvalidArgumentException;
use Throwable;

class ThrowableConverter implements TransformerInterface
{
    /** @var array<string|null> $supportedTypes */
    private static array $supportedTypes = [
        Type::ARRAY,
        Type::OBJECT,
        Type::STRING,
    ];

    private string $default;
    /** @var array<string> $parts */
    private array $parts;

    /**
     * @param Type::ARRAY|Type::OBJECT|Type::STRING $default
     * @param array<string> $parts
     */
    public function __construct(string $default = Type::OBJECT, array $parts = ['type', 'message', 'code'])
    {
        if (!in_array($default, self::$supportedTypes)) {
            throw new InvalidArgumentException("Invalid '{$default}' provided");
        }
        $this->default = $default;
        $this->parts = $parts;
    }

    public function canTransform(mixed $subject, string|null $type = null): bool
    {
        $type ??= $this->default;
        return $subject instanceof Throwable && in_array($type, self::$supportedTypes);
    }

    public function transform(mixed $subject, string|null $type = null): mixed
    {
        $subjectType = get_debug_type($subject);
        $targetType = $type ?? $this->default;

        if (!$this->canTransform($subject, $targetType)) {
            throw new TransformerException("Throwable conversion for '{$subjectType}' is not supported.");
        }
        /** @var Type::ARRAY|Type::OBJECT|Type::STRING $targetType */
        return match ($targetType) {
            Type::ARRAY => $this->createData($subject),
            Type::OBJECT => (object)$this->createData($subject),
            Type::STRING => $subject->getMessage(),
        };
    }

    /**
     * @param Throwable $subject
     * @return array<string, mixed>
     */
    private function createData(Throwable $subject): array
    {
        $data = [];
        foreach ($this->parts as $part) {
            $data[$part] = match ($part) {
                'type' => $subject::class,
                'message' => $subject->getMessage(),
                'code' => $subject->getCode(),
                'file' => $subject->getFile(),
                'line' => $subject->getLine(),
                'trace' => $subject->getTrace(),
                'previous' => $subject->getPrevious(),
                default => null,
            };
        }
        return $data;
    }
}
