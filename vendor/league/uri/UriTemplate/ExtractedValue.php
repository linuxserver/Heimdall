<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri\UriTemplate;

use function array_chunk;
use function array_column;
use function array_key_exists;
use function array_map;
use function array_unique;
use function count;
use function explode;
use function in_array;
use function is_array;
use function is_string;

final class ExtractedValue
{
    public readonly bool $isPartial;

    /**
     * @throws VariableCanNotBeExtracted
     */
    private function __construct(
        public readonly array|string|null $value,
        private readonly int $maxLength = -1,
        public readonly array $asList = [],
    ) {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                is_string($item) || throw VariableCanNotBeExtracted::dueTo('The extracted value contains a list with an invalid value at key "'.$key.'".', ExtractionErrorReason::TypeMismatch);
            }
        }
        - 1 === $maxLength || (is_string($value) && 0 < $maxLength) || throw VariableCanNotBeExtracted::dueTo('A prefix position can only be associated with a string value.', ExtractionErrorReason::UnsupportedOperation);

        $this->isPartial = -1 !== $maxLength;
    }

    public static function fromNull(): self
    {
        return new self(null);
    }

    public static function fromString(string $value, int $maxLength = -1): self
    {
        return new self($value, $maxLength);
    }

    public static function fromArray(array $value): self
    {
        return new self($value);
    }

    /**
     * @throws VariableCanNotBeExtracted
     */
    public static function fromList(string $value, Operator $operator, VarSpecifier $varSpecifier): self
    {
        return $operator->isNamed()
             ? self::fromNamedList($value, $operator, $varSpecifier)
             : self::fromUnnamedList($value, $operator);
    }

    /**
     * Extracts an exploded variable from a positional representation.
     *
     * Positional exploded values may contain either plain values or name/value
     * pairs, but not both representations at the same time.
     */
    public static function fromUnnamedList(string $value, Operator $operator): ExtractedValue
    {
        return match ($operator) {
            Operator::ReservedChars,
            Operator::Fragment => self::fromPositionalPairs($value, $operator),
            default => self::fromGenericList($value, $operator),
        };
    }

    /**
     * Creates an extracted value from a path parameter containing alternating
     * key/value pairs.
     *
     * The value is split into comma-separated key/value pairs before decoding,
     * so percent-encoded commas (`%2C`) remain part of the corresponding key or value.
     *
     * @param non-empty-string $value
     *
     * @throws VariableCanNotBeExtracted if the value is the empty string
     */
    public static function fromNamedValue(string $value, Operator $operator): self
    {
        '' !== $value || throw VariableCanNotBeExtracted::dueTo('The named value is invalid.', ExtractionErrorReason::MalformedValue);

        // Path parameters can encode an associative array as alternating
        // key/value pairs. Split before decoding so that encoded commas
        // (%2C) remain part of the value.
        $parts = explode(',', $value);
        $parts = 0 === count($parts) % 2 ? $parts : [...$parts, ''];
        $result = [];
        foreach (array_chunk($parts, 2) as [$k, $v]) {
            $key = $operator->decode($k);
            !array_key_exists($key, $result) || throw VariableCanNotBeExtracted::dueTo('The variable cannot be extracted because the input contains multiple values for "'.$key.'".', ExtractionErrorReason::ReconciliationFailed);

            $result[$key] = $operator->decode($v);
        }

        return ExtractedValue::fromArray($result);
    }

    /**
     * Extracts an exploded variable from a positional representation.
     *
     * Positional exploded values may contain either plain values or name/value
     * pairs, but not both representations at the same time.
     */
    private static function fromGenericList(string $value, Operator $operator): ExtractedValue
    {
        /** @var non-empty-string $separator */
        $separator = $operator->separator();
        $values = explode($separator, $value);
        $hasPairs = false;
        $result = [];

        foreach ($values as $pValue) {
            if (str_contains($pValue, '=')) {
                $hasPairs = true;
                [$key, $qValue] = explode('=', $pValue, 2);
                $result[$operator->decode($key)] = $operator->decode($qValue);
                continue;
            }

            !$hasPairs || throw VariableCanNotBeExtracted::dueTo('The value "'.$value.'" is malformed.', ExtractionErrorReason::MalformedValue);
            $result[] = $operator->decode($pValue);
        }

        if (!$hasPairs && 1 === count($result)) {
            $result = $result[0];
        }

        return new self($result);
    }

    private static function fromPositionalPairs(string $value, Operator $operator): self
    {
        /** @var non-empty-string $separator */
        $separator = $operator->separator();
        $parts = explode($separator, $value);
        $result = [];
        for ($i = 0, $count = count($parts); $i < $count;) {
            $key = $parts[$i++];
            $i < $count || throw VariableCanNotBeExtracted::dueTo('The value "'.$value.'" is malformed.', ExtractionErrorReason::MalformedValue);
            $part = $parts[$i++];
            if ('' === $part) {
                ($i < $count && '' === $parts[$i]) || throw VariableCanNotBeExtracted::dueTo('The value "'.$value.'" is malformed.', ExtractionErrorReason::MalformedValue);
                ++$i;
                $part = $separator;
            }

            $result[$operator->decode($key)] = $operator->decode($part);
        }

        return new self($result);
    }

    /**
     * Extracts an exploded variable from a named representation.
     *
     * The value may consist of repeated occurrences of the variable name or of
     * name/value pairs. When all pairs use the variable's name, the values are
     * returned as a list. Otherwise, the complete name/value mapping is returned,
     * provided the variable's name is not mixed with other names.
     */
    public static function fromNamedList(string $value, Operator $operator, VarSpecifier $varSpecifier): self
    {
        /** @var non-empty-string $separator */
        $separator = $operator->separator();
        $items = explode($separator, $value);
        $pairs = [];
        foreach ($items as $item) {
            $pairs[] = str_contains($item, '=') ? explode('=', $item, 2) : [$varSpecifier->name, $item];
        }

        $names = array_unique(array_column($pairs, 0));
        if (1 === count($names) && $varSpecifier->name === $names[0]) {
            return new self(array_map(fn (array $pair): string => $operator->decode($pair[1]), $pairs));
        }

        !in_array($varSpecifier->name, $names, true) || throw VariableCanNotBeExtracted::dueTo('The value "'.$value.'" is malformed.', ExtractionErrorReason::MalformedValue);

        $result = [];
        foreach ($pairs as [$pName, $pValue]) {
            $key = $operator->decode($pName);
            !array_key_exists($key, $result) || throw VariableCanNotBeExtracted::dueTo('The variable cannot be extracted because the input contains multiple values for "'.$key.'".', ExtractionErrorReason::ReconciliationFailed);
            $result[$key] = $operator->decode($pValue);
        }

        return new self($result);
    }

    public static function fromValue(string $value, Operator $operator, VarSpecifier $varSpecifier): self
    {
        return new self(
            value: $operator->decode($value),
            maxLength: 0 === $varSpecifier->position ? -1 : $varSpecifier->position,
            asList: array_map(
                static fn (string $value): string => $operator->decode($value),
                '' !== $value && $operator->supportsListValue() ? explode(',', $value) : []
            ),
        );
    }

    public function equals(mixed $value): bool
    {
        return $value instanceof self
            && $value->maxLength === $this->maxLength
            && $value->value === $this->value
            && $value->asList === $this->asList;
    }

    public function hasKey(string|int $name): bool
    {
        return is_array($this->value) && array_key_exists($name, $this->value);
    }

    /**
     * @throws VariableCanNotBeExtracted
     */
    public function reconcile(self $other): self
    {
        if ($this->equals($other)) {
            return 1 < count($this->asList) ? new self($this->asList) : $this;
        }

        $thisValue = $this->value;
        $otherValue = $other->value;
        if ([] !== $this->asList && is_array($otherValue) && $this->asList === $otherValue) {
            return $other;
        }

        if ([] !== $other->asList && is_array($thisValue) && $other->asList === $thisValue) {
            return $this;
        }

        (!is_array($thisValue) || !is_array($otherValue)) || throw VariableCanNotBeExtracted::dueTo('The extracted lists contain different data.', ExtractionErrorReason::ListMismatch);
        (is_string($thisValue) && is_string($otherValue)) || throw VariableCanNotBeExtracted::dueTo('The extracted values have different types.', ExtractionErrorReason::TypeMismatch);
        if ($this->maxLength === $other->maxLength) {
            ($thisValue === $otherValue) || throw VariableCanNotBeExtracted::dueTo('The extracted values are different.', ExtractionErrorReason::StringMismatch);

            $value = [] === $this->asList ? $other : $this;

            return 1 < count($value->asList) ? new self($value->asList) : $value;
        }

        $result = $other;
        $resultValue = $otherValue;
        $prefixValue = $thisValue;
        if (-1 === $this->maxLength || (-1 !== $other->maxLength && $this->maxLength > $other->maxLength)) {
            $result = $this;
            $resultValue = $thisValue;
            $prefixValue = $otherValue;
        }

        return str_starts_with($resultValue, $prefixValue)
            ? $result
            : throw VariableCanNotBeExtracted::dueTo('The extracted value does not start with the other extracted value.', ExtractionErrorReason::StringMismatch);
    }
}
