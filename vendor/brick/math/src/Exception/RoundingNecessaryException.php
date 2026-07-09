<?php

declare(strict_types=1);

namespace Brick\Math\Exception;

use RuntimeException;

/**
 * Exception thrown when a number cannot be represented at the requested scale without rounding.
 */
final class RoundingNecessaryException extends RuntimeException implements MathException
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
    public static function decimalScaleTooSmall(): self
    {
        return new self('This decimal number cannot be represented at the requested scale without rounding.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function rationalScaleTooSmall(): self
    {
        return new self('This rational number cannot be represented at the requested scale without rounding.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function integerDivisionNotExact(): self
    {
        return new self('The division has a non-zero remainder and cannot be represented as an integer without rounding.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function decimalDivisionNotExact(): self
    {
        return new self('The division yields a non-terminating decimal expansion and cannot be represented as a decimal without rounding.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function decimalDivisionScaleTooSmall(): self
    {
        return new self('The division result is exact but cannot be represented at the requested scale without rounding.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function integerSquareRootNotExact(): self
    {
        return new self('The square root is not exact and cannot be represented as an integer without rounding.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function decimalSquareRootNotExact(): self
    {
        return new self('The square root is not exact and cannot be represented as a decimal without rounding.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function decimalSquareRootScaleTooSmall(): self
    {
        return new self('The square root is exact but cannot be represented at the requested scale without rounding.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function integerNthRootNotExact(): self
    {
        return new self('The nth root is not exact and cannot be represented as an integer without rounding.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function decimalNthRootNotExact(): self
    {
        return new self('The nth root is not exact and cannot be represented as a decimal without rounding.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function decimalNthRootScaleTooSmall(): self
    {
        return new self('The nth root is exact but cannot be represented at the requested scale without rounding.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function decimalNotConvertibleToInteger(): self
    {
        return new self('This decimal number cannot be represented as an integer without rounding.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function rationalNotConvertibleToInteger(): self
    {
        return new self('This rational number cannot be represented as an integer without rounding.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function rationalNotConvertibleToDecimal(): self
    {
        return new self('This rational number has a non-terminating decimal expansion and cannot be represented as a decimal without rounding.');
    }
}
