<?php

declare(strict_types=1);

namespace Brick\Math;

/**
 * A syntax feature that {@see BigNumber::parse()} can accept.
 *
 * Plain signed integers such as `123` and `-7` are the base language: they are always accepted.
 * Each case allows one additional feature:
 *
 * - DecimalPoint: `.`
 * - Exponent: `e` or `E`
 * - Fraction: `/`
 *
 * In addition to its cases, this enum provides list constants for the most common combinations, from INTEGER to ALL.
 */
enum NumberSyntax
{
    /**
     * Allows the decimal point: `1.5`, `.5`, `1.`.
     */
    case DecimalPoint;

    /**
     * Allows the exponent: `5e3`, `15E-2`.
     */
    case Exponent;

    /**
     * Allows the fraction form: `2/4`. The numerator and denominator are unsigned integers; an optional sign
     * precedes the whole fraction.
     */
    case Fraction;

    /**
     * Integers only: `123`.
     * The base language, with no additional notation.
     */
    public const INTEGER = [];

    /**
     * Integers and decimal numbers: `123`, `123.45`.
     * Typical for monetary input.
     */
    public const DECIMAL = [
        self::DecimalPoint,
    ];

    /**
     * Integers and decimal numbers, with exponents: `123`, `123.45`, `1.5e-3`.
     * Accepts every JSON number.
     */
    public const SCIENTIFIC = [
        self::DecimalPoint,
        self::Exponent,
    ];

    /**
     * Integers and fractions: `123`, `22/7`.
     */
    public const RATIONAL = [
        self::Fraction,
    ];

    /**
     * The full syntax accepted by {@see BigNumber::of()}: `123`, `123.45`, `1.5e-3`, `22/7`.
     */
    public const ALL = [
        self::DecimalPoint,
        self::Exponent,
        self::Fraction,
    ];
}
