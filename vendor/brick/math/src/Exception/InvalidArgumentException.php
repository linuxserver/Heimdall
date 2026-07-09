<?php

declare(strict_types=1);

namespace Brick\Math\Exception;

use function sprintf;

/**
 * Exception thrown when an invalid argument is provided.
 */
final class InvalidArgumentException extends \InvalidArgumentException implements MathException
{
    /**
     * @internal
     *
     * @pure
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function baseOutOfRange(int $base): self
    {
        return new self(sprintf('Base %d is out of range [2, 36].', $base));
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function negativeScale(): self
    {
        return new self('The scale must not be negative.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function negativeBitIndex(): self
    {
        return new self('The bit index must not be negative.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function negativeBitCount(): self
    {
        return new self('The bit count must not be negative.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function alphabetTooShort(): self
    {
        return new self('The alphabet must contain at least 2 characters.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function duplicateCharsInAlphabet(): self
    {
        return new self('The alphabet must not contain duplicate characters.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function minGreaterThanMax(): self
    {
        return new self('The minimum value must be less than or equal to the maximum value.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function cannotConvertFloat(string $type): self
    {
        return new self(sprintf('Cannot convert %s to a BigDecimal.', $type));
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function negativeExponent(): self
    {
        return new self('The exponent must not be negative.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function negativeModulus(): self
    {
        return new self('The modulus must not be negative.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function nonPositiveNthRootDegree(): self
    {
        return new self('The degree of an nth root must be a positive integer.');
    }
}
