<?php

declare(strict_types=1);

namespace Brick\Math;

/**
 * Specifies rounding behavior by defining how discarded digits affect the returned result when an exact value cannot
 * be represented at the requested scale.
 */
enum RoundingMode
{
    /**
     * Asserts that the requested operation has an exact result, hence no rounding is necessary.
     *
     * If this rounding mode is specified on an operation that yields a result that
     * cannot be represented at the requested scale, a RoundingNecessaryException is thrown.
     *
     * Equivalent native PHP rounding mode: none.
     */
    case Unnecessary;

    /**
     * Rounds away from zero.
     *
     * Always increments the digit prior to a nonzero discarded fraction.
     * Note that this rounding mode never decreases the magnitude of the calculated value.
     *
     * Equivalent native PHP rounding mode: {@see \RoundingMode::AwayFromZero}.
     */
    case Up;

    /**
     * Rounds towards zero.
     *
     * Never increments the digit prior to a discarded fraction (i.e., truncates).
     * Note that this rounding mode never increases the magnitude of the calculated value.
     *
     * Equivalent native PHP rounding mode: {@see \RoundingMode::TowardsZero}.
     */
    case Down;

    /**
     * Rounds towards positive infinity.
     *
     * If the result is positive, behaves as for Up; if negative, behaves as for Down.
     * Note that this rounding mode never decreases the calculated value.
     *
     * Equivalent native PHP rounding mode: {@see \RoundingMode::PositiveInfinity}.
     */
    case Ceiling;

    /**
     * Rounds towards negative infinity.
     *
     * If the result is positive, behaves as for Down; if negative, behaves as for Up.
     * Note that this rounding mode never increases the calculated value.
     *
     * Equivalent native PHP rounding mode: {@see \RoundingMode::NegativeInfinity}.
     */
    case Floor;

    /**
     * Rounds towards "nearest neighbor" unless both neighbors are equidistant, in which case round up.
     *
     * Behaves as for Up if the discarded fraction is >= 0.5; otherwise, behaves as for Down.
     * Note that this is the rounding mode commonly taught at school.
     *
     * Equivalent native PHP rounding mode: {@see \RoundingMode::HalfAwayFromZero}.
     */
    case HalfUp;

    /**
     * Rounds towards "nearest neighbor" unless both neighbors are equidistant, in which case round down.
     *
     * Behaves as for Up if the discarded fraction is > 0.5; otherwise, behaves as for Down.
     *
     * Equivalent native PHP rounding mode: {@see \RoundingMode::HalfTowardsZero}.
     */
    case HalfDown;

    /**
     * Rounds towards "nearest neighbor" unless both neighbors are equidistant, in which case round towards positive infinity.
     *
     * If the result is positive, behaves as for HalfUp; if negative, behaves as for HalfDown.
     *
     * Equivalent native PHP rounding mode: none.
     */
    case HalfCeiling;

    /**
     * Rounds towards "nearest neighbor" unless both neighbors are equidistant, in which case round towards negative infinity.
     *
     * If the result is positive, behaves as for HalfDown; if negative, behaves as for HalfUp.
     *
     * Equivalent native PHP rounding mode: none.
     */
    case HalfFloor;

    /**
     * Rounds towards the "nearest neighbor" unless both neighbors are equidistant, in which case rounds towards the even neighbor.
     *
     * Behaves as for HalfUp if the digit to the left of the discarded fraction is odd;
     * behaves as for HalfDown if it's even.
     *
     * Note that this is the rounding mode that statistically minimizes
     * cumulative error when applied repeatedly over a sequence of calculations.
     * It is sometimes known as "Banker's rounding", and is the default rounding mode in IEEE 754.
     *
     * Equivalent native PHP rounding mode: {@see \RoundingMode::HalfEven}.
     */
    case HalfEven;

    /**
     * Rounds towards the "nearest neighbor" unless both neighbors are equidistant, in which case rounds towards the odd neighbor.
     *
     * Behaves as for HalfUp if the digit to the left of the discarded fraction is even;
     * behaves as for HalfDown if it's odd.
     *
     * Like HalfEven, this rounding mode is free of bias towards or away from zero on ties;
     * unlike HalfEven, ties never round to an even digit — in particular, never to zero or to a multiple of ten.
     *
     * Equivalent native PHP rounding mode: {@see \RoundingMode::HalfOdd}.
     */
    case HalfOdd;

    /**
     * Returns the equivalent of the given native PHP rounding mode.
     *
     * Note that PHP's RoundingMode enum is only available on PHP 8.4 and later.
     *
     * @pure
     */
    public static function fromNativeRoundingMode(\RoundingMode $roundingMode): self
    {
        return match ($roundingMode) {
            \RoundingMode::AwayFromZero => self::Up,
            \RoundingMode::TowardsZero => self::Down,
            \RoundingMode::PositiveInfinity => self::Ceiling,
            \RoundingMode::NegativeInfinity => self::Floor,
            \RoundingMode::HalfAwayFromZero => self::HalfUp,
            \RoundingMode::HalfTowardsZero => self::HalfDown,
            \RoundingMode::HalfEven => self::HalfEven,
            \RoundingMode::HalfOdd => self::HalfOdd,
        };
    }
}
