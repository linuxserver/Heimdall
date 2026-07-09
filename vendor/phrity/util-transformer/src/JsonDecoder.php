<?php

namespace Phrity\Util\Transformer;

use JsonException;

class JsonDecoder implements TransformerInterface
{
    public function __construct(
        private bool $associative = false,
    ) {
    }

    public function canTransform(mixed $subject, string|null $type = null): bool
    {
        if (gettype($subject) != Type::STRING) {
            return false;
        }
        try {
            return in_array($type, [null, gettype($this->decode($subject))]);
        } catch (JsonException $e) {
            return false;
        }
    }

    /**
     * @throws TransformerException
     */
    public function transform(mixed $subject, string|null $type = null): mixed
    {
        if (!$this->canTransform($subject, $type)) {
            throw new TransformerException("JSON decoding for '{$subject}' is not supported.");
        }
        return $this->decode($subject);
    }

    /**
     * @throws JsonException
     */
    private function decode(string $subject): mixed
    {
        return json_decode($subject, associative: $this->associative, flags: JSON_THROW_ON_ERROR);
    }
}
