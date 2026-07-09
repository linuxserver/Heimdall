<?php

declare(strict_types=1);

namespace Brick\Math;

use Brick\Math\Exception\DivisionByZeroException;
use Brick\Math\Exception\InvalidArgumentException;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NegativeNumberException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\Exception\UnsupportedPlatformException;
use Brick\Math\Internal\CalculatorRegistry;
use Brick\Math\Internal\DecimalHelper;
use Brick\Math\Internal\Safe;
use LogicException;
use Override;

use function assert;
use function chr;
use function in_array;
use function ini_set;
use function intdiv;
use function is_infinite;
use function is_nan;
use function json_encode;
use function max;
use function pack;
use function rtrim;
use function str_repeat;
use function strlen;
use function substr;
use function unpack;

use const PHP_INT_SIZE;

/**
 * An arbitrarily large decimal number.
 *
 * This class is immutable.
 *
 * The scale of the number is the number of digits after the decimal point. It is always positive or zero.
 */
final readonly class BigDecimal extends BigNumber
{
    /**
     * The unscaled value of this decimal number.
     *
     * This is a string of digits with an optional leading minus sign.
     * No leading zero must be present.
     * No leading minus sign must be present if the value is 0.
     */
    private string $value;

    /**
     * The scale (number of digits after the decimal point) of this decimal number.
     *
     * This must be zero or more.
     *
     * @var non-negative-int
     */
    private int $scale;

    /**
     * Protected constructor. Use a factory method to obtain an instance.
     *
     * @param string           $value The unscaled value, validated.
     * @param non-negative-int $scale The scale, validated.
     *
     * @pure
     */
    protected function __construct(string $value, int $scale = 0)
    {
        $this->value = $value;
        $this->scale = $scale;
    }

    /**
     * Creates a BigDecimal from an unscaled value and a scale.
     *
     * Example: `(12345, 3)` will result in the BigDecimal `12.345`.
     *
     * A negative scale is normalized to zero by appending zeros to the unscaled value.
     *
     * Example: `(12345, -3)` will result in the BigDecimal `12345000`.
     *
     * @param BigNumber|int|string $value The unscaled value. Must be convertible to a BigInteger.
     * @param int                  $scale The scale of the number. If negative, the scale will be set to zero
     *                                    and the unscaled value will be adjusted accordingly.
     *
     * @throws MathException If the value is not valid, or is not convertible to a BigInteger.
     *
     * @pure
     */
    public static function ofUnscaledValue(BigNumber|int|string $value, int $scale = 0): BigDecimal
    {
        $value = BigInteger::of($value)->toString();

        if ($scale < 0) {
            if ($value !== '0') {
                $value .= str_repeat('0', Safe::neg($scale));
            }
            $scale = 0;
        }

        return new BigDecimal($value, $scale);
    }

    /**
     * Returns a BigDecimal representing zero, with a scale of zero.
     *
     * @pure
     */
    public static function zero(): BigDecimal
    {
        /** @var BigDecimal|null $zero */
        static $zero;

        if ($zero === null) {
            $zero = new BigDecimal('0');
        }

        return $zero;
    }

    /**
     * Returns a BigDecimal representing one, with a scale of zero.
     *
     * @pure
     */
    public static function one(): BigDecimal
    {
        /** @var BigDecimal|null $one */
        static $one;

        if ($one === null) {
            $one = new BigDecimal('1');
        }

        return $one;
    }

    /**
     * Returns a BigDecimal representing ten, with a scale of zero.
     *
     * @pure
     */
    public static function ten(): BigDecimal
    {
        /** @var BigDecimal|null $ten */
        static $ten;

        if ($ten === null) {
            $ten = new BigDecimal('10');
        }

        return $ten;
    }

    /**
     * Creates a BigDecimal from the exact IEEE-754 value of a float.
     *
     * Examples:
     *   - `fromFloatExact(0.1)` returns a BigDecimal with value '0.1000000000000000055511151231257827021181583404541015625'
     *   - `fromFloatExact(0.3)` returns a BigDecimal with value '0.299999999999999988897769753748434595763683319091796875'
     *   - `fromFloatExact(0.5)` returns a BigDecimal with value '0.5'
     *   - `fromFloatExact(1.0)` returns a BigDecimal with value '1'
     *
     * Note that BigDecimal has no concept of negative zero, so `-0.0` and `0.0` both convert to zero.
     *
     * @throws InvalidArgumentException     If the value is NaN or infinite.
     * @throws UnsupportedPlatformException If the platform uses a non-IEEE-754 double format.
     *
     * @pure
     */
    public static function fromFloatExact(float $value): BigDecimal
    {
        if (is_nan($value)) {
            throw InvalidArgumentException::cannotConvertFloat('NaN');
        }
        if (is_infinite($value)) {
            throw InvalidArgumentException::cannotConvertFloat($value > 0 ? 'INF' : '-INF');
        }

        if (pack('E', 1.0) !== "\x3f\xf0\x00\x00\x00\x00\x00\x00") {
            throw UnsupportedPlatformException::unsupportedFloatFormat();
        }

        if (PHP_INT_SIZE >= 8) {
            // 64-bit: extract the IEEE-754 bit pattern as a 64-bit integer.
            /** @var array{1: int} $unpacked */
            $unpacked = unpack('J', pack('E', $value));
            $bits = $unpacked[1];

            // Bits: [sign(1)|exp(11)|mantissa(52)]
            $signBit = ($bits >> 63) & 1;
            $expBits = ($bits >> 52) & 0x7FF;
            $mantissa = $bits & 0xFFFFFFFFFFFFF;

            // Zero (covers both 0.0 and -0.0).
            if ($expBits === 0 && $mantissa === 0) {
                return BigDecimal::zero();
            }

            if ($expBits === 0) {
                $significand = BigInteger::of($mantissa);
            } else {
                $significand = BigInteger::of(0x10000000000000 | $mantissa);
            }
        } else {
            // 32-bit: extract the IEEE-754 bit pattern as 8 bytes.
            $packed = pack('E', $value);

            // Get the first 16 bits as an integer.
            /** @var array{1: int} $unpacked */
            $unpacked = unpack('n', $packed);
            $high16 = $unpacked[1];

            // Bits: [sign(1)|exp(11)|mantissa(4)] in header (bytes 0-1) + 48 bits of mantissa in bytes 2-7
            $signBit = ($high16 >> 15) & 1;
            $expBits = ($high16 >> 4) & 0x7FF;
            $mantissaBytes = chr($high16 & 0x0F) . substr($packed, 2);

            // Zero (covers both 0.0 and -0.0).
            if ($expBits === 0 && $mantissaBytes === "\x00\x00\x00\x00\x00\x00\x00") {
                return BigDecimal::zero();
            }

            $mantissa = BigInteger::fromBytes($mantissaBytes, false);

            if ($expBits === 0) {
                $significand = $mantissa;
            } else {
                $significand = $mantissa->plus(BigInteger::of(1)->shiftedLeft(52));
            }
        }

        if ($expBits === 0) {
            // Subnormal: no implicit leading 1-bit; effective exponent = -1074.
            $baseExp = -1074;
        } else {
            // Normal: biased exp - 1023 (bias) - 52 (mantissa shift)
            $baseExp = $expBits - 1075;
        }

        if ($baseExp >= 0) {
            // Result is an integer: significand × 2^baseExp.
            $unscaled = $significand->multipliedBy(BigInteger::of(2)->power($baseExp));
            $scale = 0;
        } else {
            // Fraction: significand × 5^|baseExp| / 10^|baseExp|.
            // Multiplying by 5^n eliminates the 2-based denominator while keeping scale = n.
            $absExp = -$baseExp;
            $unscaled = $significand->multipliedBy(BigInteger::of(5)->power($absExp));
            $scale = $absExp;
        }

        if ($signBit === 1) {
            $unscaled = $unscaled->negated();
        }

        return BigDecimal::ofUnscaledValue($unscaled, $scale)->strippedOfTrailingZeros();
    }

    /**
     * Creates a BigDecimal from the shortest decimal representation of a float that round-trips back to the same value.
     *
     * The result is the shortest BigDecimal that passes `BigDecimal::fromFloatShortest($f)->toFloat() === $f`.
     *
     * Examples:
     *   - `fromFloatShortest(0.3)` returns a BigDecimal with value '0.3'
     *   - `fromFloatShortest(0.1 * 3.0)` returns a BigDecimal with value '0.30000000000000004' (`0.1 * 3.0 !== 0.3`)
     *   - `fromFloatShortest(1.0 / 3.0)` returns a BigDecimal with value '0.3333333333333333'
     *
     * Note that BigDecimal has no concept of negative zero, so `-0.0` and `0.0` both convert to zero.
     *
     * @throws InvalidArgumentException If the value is NaN or infinite.
     */
    public static function fromFloatShortest(float $value): BigDecimal
    {
        if (is_nan($value)) {
            throw InvalidArgumentException::cannotConvertFloat('NaN');
        }
        if (is_infinite($value)) {
            throw InvalidArgumentException::cannotConvertFloat($value > 0 ? 'INF' : '-INF');
        }

        // json_encode() uses serialize_precision; precision -1 uses the shortest round-trip algorithm
        $previousPrecision = ini_set('serialize_precision', '-1');

        try {
            $str = json_encode($value);
        } finally {
            if ($previousPrecision !== false) {
                ini_set('serialize_precision', $previousPrecision);
            }
        }

        assert($str !== false);

        return BigDecimal::of($str)->strippedOfTrailingZeros();
    }

    /**
     * Returns the sum of this number and the given one.
     *
     * The result has a scale of `max($this->scale, $that->scale)`.
     *
     * @param BigNumber|int|string $that The number to add. Must be convertible to a BigDecimal.
     *
     * @throws MathException If the number is not valid, or is not convertible to a BigDecimal.
     *
     * @pure
     */
    public function plus(BigNumber|int|string $that): BigDecimal
    {
        $that = BigDecimal::of($that);

        if ($that->isZero() && $that->scale <= $this->scale) {
            return $this;
        }

        if ($this->isZero() && $this->scale <= $that->scale) {
            return $that;
        }

        [$a, $b] = $this->scaleValues($this, $that);

        $value = CalculatorRegistry::get()->add($a, $b);
        $scale = max($this->scale, $that->scale);

        return new BigDecimal($value, $scale);
    }

    /**
     * Returns the difference of this number and the given one.
     *
     * The result has a scale of `max($this->scale, $that->scale)`.
     *
     * @param BigNumber|int|string $that The number to subtract. Must be convertible to a BigDecimal.
     *
     * @throws MathException If the number is not valid, or is not convertible to a BigDecimal.
     *
     * @pure
     */
    public function minus(BigNumber|int|string $that): BigDecimal
    {
        $that = BigDecimal::of($that);

        if ($that->isZero() && $that->scale <= $this->scale) {
            return $this;
        }

        if ($this->isZero() && $this->scale <= $that->scale) {
            return $that->negated();
        }

        [$a, $b] = $this->scaleValues($this, $that);

        $value = CalculatorRegistry::get()->sub($a, $b);
        $scale = max($this->scale, $that->scale);

        return new BigDecimal($value, $scale);
    }

    /**
     * Returns the product of this number and the given one.
     *
     * The result has a scale of `$this->scale + $that->scale`.
     *
     * @param BigNumber|int|string $that The multiplier. Must be convertible to a BigDecimal.
     *
     * @throws MathException If the multiplier is not valid, or is not convertible to a BigDecimal.
     *
     * @pure
     */
    public function multipliedBy(BigNumber|int|string $that): BigDecimal
    {
        $that = BigDecimal::of($that);

        if ($that->isOneScaleZero()) {
            return $this;
        }

        if ($this->isOneScaleZero()) {
            return $that;
        }

        /** @var non-negative-int $scale */
        $scale = Safe::add($this->scale, $that->scale);

        if ($this->isZero() || $that->isZero()) {
            return new BigDecimal('0', $scale);
        }

        $value = CalculatorRegistry::get()->mul($this->value, $that->value);

        return new BigDecimal($value, $scale);
    }

    /**
     * Returns the result of the division of this number by the given one, at the given scale.
     *
     * @param BigNumber|int|string $that         The divisor. Must be convertible to a BigDecimal.
     * @param non-negative-int     $scale        The desired scale. Must be non-negative.
     * @param RoundingMode         $roundingMode An optional rounding mode, defaults to Unnecessary.
     *
     * @throws MathException              If the divisor is not valid, or is not convertible to a BigDecimal.
     * @throws InvalidArgumentException   If the scale is negative.
     * @throws DivisionByZeroException    If the divisor is zero.
     * @throws RoundingNecessaryException If RoundingMode::Unnecessary is used and the result cannot be represented
     *                                    exactly at the given scale.
     *
     * @pure
     */
    public function dividedBy(BigNumber|int|string $that, int $scale, RoundingMode $roundingMode = RoundingMode::Unnecessary): BigDecimal
    {
        if ($scale < 0) { // @phpstan-ignore smaller.alwaysFalse
            throw InvalidArgumentException::negativeScale();
        }

        $that = BigDecimal::of($that);

        if ($that->isZero()) {
            throw DivisionByZeroException::divisionByZero();
        }

        if ($that->isOneScaleZero() && $scale === $this->scale) {
            return $this;
        }

        $p = $this->valueWithMinScale(Safe::add($that->scale, $scale));
        $q = $that->valueWithMinScale(Safe::sub($this->scale, $scale));

        $calculator = CalculatorRegistry::get();
        $result = $calculator->divRound($p, $q, $roundingMode);

        if ($result === null) {
            [$a, $b] = $this->scaleValues($this->abs(), $that->abs());

            $denominator = $calculator->divQ($b, $calculator->gcd($a, $b));
            $requiredScale = DecimalHelper::computeScaleFromReducedFractionDenominator($denominator);

            if ($requiredScale === null) {
                throw RoundingNecessaryException::decimalDivisionNotExact();
            }

            throw RoundingNecessaryException::decimalDivisionScaleTooSmall();
        }

        return new BigDecimal($result, $scale);
    }

    /**
     * Returns the exact result of the division of this number by the given one.
     *
     * The scale of the result is automatically calculated to fit all the fraction digits.
     *
     * @param BigNumber|int|string $that The divisor. Must be convertible to a BigDecimal.
     *
     * @throws MathException              If the divisor is not valid, or is not convertible to a BigDecimal.
     * @throws DivisionByZeroException    If the divisor is zero.
     * @throws RoundingNecessaryException If the result yields an infinite number of digits.
     *
     * @pure
     */
    public function dividedByExact(BigNumber|int|string $that): BigDecimal
    {
        $that = BigDecimal::of($that);

        if ($that->isZero()) {
            throw DivisionByZeroException::divisionByZero();
        }

        [$a, $b] = $this->scaleValues($this->abs(), $that->abs());

        $calculator = CalculatorRegistry::get();

        $denominator = $calculator->divQ($b, $calculator->gcd($a, $b));
        $scale = DecimalHelper::computeScaleFromReducedFractionDenominator($denominator);

        if ($scale === null) {
            throw RoundingNecessaryException::decimalDivisionNotExact();
        }

        return $this->dividedBy($that, $scale)->strippedOfTrailingZeros();
    }

    /**
     * Returns this number exponentiated to the given value.
     *
     * The result has a scale of `$this->scale * $exponent`.
     *
     * @param non-negative-int $exponent
     *
     * @throws InvalidArgumentException If the exponent is negative.
     *
     * @pure
     */
    public function power(int $exponent): BigDecimal
    {
        if ($exponent === 0) {
            return BigDecimal::one();
        }

        if ($exponent === 1) {
            return $this;
        }

        if ($exponent < 0) { // @phpstan-ignore smaller.alwaysFalse
            throw InvalidArgumentException::negativeExponent();
        }

        /** @var non-negative-int $scale */
        $scale = Safe::mul($this->scale, $exponent);

        return new BigDecimal(CalculatorRegistry::get()->pow($this->value, $exponent), $scale);
    }

    /**
     * Returns the quotient of the division of this number by the given one.
     *
     * The quotient has a scale of `0`.
     *
     * Examples:
     *
     * - `7.5` quotient `3` returns `2`
     * - `7.5` quotient `-3` returns `-2`
     * - `-7.5` quotient `3` returns `-2`
     * - `-7.5` quotient `-3` returns `2`
     *
     * @param BigNumber|int|string $that The divisor. Must be convertible to a BigDecimal.
     *
     * @throws MathException           If the divisor is not valid, or is not convertible to a BigDecimal.
     * @throws DivisionByZeroException If the divisor is zero.
     *
     * @pure
     */
    public function quotient(BigNumber|int|string $that): BigDecimal
    {
        $that = BigDecimal::of($that);

        if ($that->isZero()) {
            throw DivisionByZeroException::divisionByZero();
        }

        $p = $this->valueWithMinScale($that->scale);
        $q = $that->valueWithMinScale($this->scale);

        $quotient = CalculatorRegistry::get()->divQ($p, $q);

        return new BigDecimal($quotient, 0);
    }

    /**
     * Returns the remainder of the division of this number by the given one.
     *
     * The remainder has a scale of `max($this->scale, $that->scale)`.
     * The remainder, when non-zero, has the same sign as the dividend.
     *
     * Examples:
     *
     * - `7.5` remainder `3` returns `1.5`
     * - `7.5` remainder `-3` returns `1.5`
     * - `-7.5` remainder `3` returns `-1.5`
     * - `-7.5` remainder `-3` returns `-1.5`
     *
     * @param BigNumber|int|string $that The divisor. Must be convertible to a BigDecimal.
     *
     * @throws MathException           If the divisor is not valid, or is not convertible to a BigDecimal.
     * @throws DivisionByZeroException If the divisor is zero.
     *
     * @pure
     */
    public function remainder(BigNumber|int|string $that): BigDecimal
    {
        $that = BigDecimal::of($that);

        if ($that->isZero()) {
            throw DivisionByZeroException::divisionByZero();
        }

        $p = $this->valueWithMinScale($that->scale);
        $q = $that->valueWithMinScale($this->scale);

        $remainder = CalculatorRegistry::get()->divR($p, $q);

        $scale = max($this->scale, $that->scale);

        return new BigDecimal($remainder, $scale);
    }

    /**
     * Returns the quotient and remainder of the division of this number by the given one.
     *
     * The quotient has a scale of `0`, and the remainder has a scale of `max($this->scale, $that->scale)`.
     *
     * Examples:
     *
     * - `7.5` quotientAndRemainder `3` returns [`2`, `1.5`]
     * - `7.5` quotientAndRemainder `-3` returns [`-2`, `1.5`]
     * - `-7.5` quotientAndRemainder `3` returns [`-2`, `-1.5`]
     * - `-7.5` quotientAndRemainder `-3` returns [`2`, `-1.5`]
     *
     * @param BigNumber|int|string $that The divisor. Must be convertible to a BigDecimal.
     *
     * @return array{BigDecimal, BigDecimal} An array containing the quotient and the remainder.
     *
     * @throws MathException           If the divisor is not valid, or is not convertible to a BigDecimal.
     * @throws DivisionByZeroException If the divisor is zero.
     *
     * @pure
     */
    public function quotientAndRemainder(BigNumber|int|string $that): array
    {
        $that = BigDecimal::of($that);

        if ($that->isZero()) {
            throw DivisionByZeroException::divisionByZero();
        }

        $p = $this->valueWithMinScale($that->scale);
        $q = $that->valueWithMinScale($this->scale);

        [$quotient, $remainder] = CalculatorRegistry::get()->divQR($p, $q);

        $scale = max($this->scale, $that->scale);

        $quotient = new BigDecimal($quotient, 0);
        $remainder = new BigDecimal($remainder, $scale);

        return [$quotient, $remainder];
    }

    /**
     * Returns the square root of this number, rounded to the given scale according to the given rounding mode.
     *
     * @param non-negative-int $scale        The target scale. Must be non-negative.
     * @param RoundingMode     $roundingMode An optional rounding mode, defaults to Unnecessary.
     *
     * @throws InvalidArgumentException   If the scale is negative.
     * @throws NegativeNumberException    If this number is negative.
     * @throws RoundingNecessaryException If RoundingMode::Unnecessary is used and the result cannot be represented
     *                                    exactly at the given scale.
     *
     * @pure
     */
    public function sqrt(int $scale, RoundingMode $roundingMode = RoundingMode::Unnecessary): BigDecimal
    {
        if ($scale < 0) { // @phpstan-ignore smaller.alwaysFalse
            throw InvalidArgumentException::negativeScale();
        }

        if ($this->isZero()) {
            return new BigDecimal('0', $scale);
        }

        if ($this->isNegative()) {
            throw NegativeNumberException::squareRootOfNegativeNumber();
        }

        $value = $this->value;
        $inputScale = $this->scale;

        if ($inputScale % 2 !== 0) {
            $value .= '0';
            $inputScale = Safe::add($inputScale, 1);
        }

        $calculator = CalculatorRegistry::get();

        // Keep one extra digit for rounding.
        $intermediateScale = Safe::add(max($scale, intdiv($inputScale, 2)), 1);
        $value .= str_repeat('0', Safe::sub(Safe::mul(2, $intermediateScale), $inputScale));

        $sqrt = $calculator->sqrt($value);
        $isExact = $calculator->mul($sqrt, $sqrt) === $value;

        if (! $isExact) {
            if ($roundingMode === RoundingMode::Unnecessary) {
                throw RoundingNecessaryException::decimalSquareRootNotExact();
            }

            // Non-perfect-square sqrt is irrational, so the true value is strictly above this sqrt floor.
            // Add one at the intermediate scale to guarantee Up/Ceiling round up at the target scale.
            if (in_array($roundingMode, [RoundingMode::Up, RoundingMode::Ceiling], true)) {
                $sqrt = $calculator->add($sqrt, '1');
            }

            // Irrational sqrt cannot land exactly on a midpoint; treat tie-to-down modes as HalfUp.
            elseif (in_array($roundingMode, [RoundingMode::HalfDown, RoundingMode::HalfEven, RoundingMode::HalfFloor], true)) {
                $roundingMode = RoundingMode::HalfUp;
            }
        }

        $scaled = DecimalHelper::scale($sqrt, $intermediateScale, $scale, $roundingMode);

        if ($scaled === null) {
            throw RoundingNecessaryException::decimalSquareRootScaleTooSmall();
        }

        return new BigDecimal($scaled, $scale);
    }

    /**
     * Returns the nth root of this number, rounded to the given scale according to the given rounding mode.
     *
     * For odd $n, the operation is defined for negative inputs: the sign is preserved and the
     * magnitude of the root is |$this|^(1/$n).
     *
     * @param positive-int     $n            The root degree. Must be a strictly positive integer.
     * @param non-negative-int $scale        The target scale. Must be non-negative.
     * @param RoundingMode     $roundingMode An optional rounding mode, defaults to Unnecessary.
     *
     * @throws InvalidArgumentException   If $n is less than 1 or $scale is negative.
     * @throws NegativeNumberException    If this number is negative and $n is even.
     * @throws RoundingNecessaryException If RoundingMode::Unnecessary is used and the result cannot be represented
     *                                    exactly at the given scale.
     *
     * @pure
     */
    public function nthRoot(int $n, int $scale, RoundingMode $roundingMode = RoundingMode::Unnecessary): BigDecimal
    {
        if ($n < 1) { // @phpstan-ignore smaller.alwaysFalse
            throw InvalidArgumentException::nonPositiveNthRootDegree();
        }

        if ($scale < 0) { // @phpstan-ignore smaller.alwaysFalse
            throw InvalidArgumentException::negativeScale();
        }

        $isNegative = $this->isNegative();

        if ($isNegative && $n % 2 === 0) {
            throw NegativeNumberException::nthRootOfNegativeNumber();
        }

        if ($n === 1) {
            $scaled = DecimalHelper::scale($this->value, $this->scale, $scale, $roundingMode);

            if ($scaled === null) {
                throw RoundingNecessaryException::decimalNthRootScaleTooSmall();
            }

            return new BigDecimal($scaled, $scale);
        }

        if ($this->isZero()) {
            return new BigDecimal('0', $scale);
        }

        $value = $this->value;
        $inputScale = $this->scale;

        // Pad inputScale up to a multiple of $n so the shift by n*intermediateScale lands cleanly.
        $remainder = $inputScale % $n;

        if ($remainder !== 0) {
            $padding = $n - $remainder;
            $value .= str_repeat('0', $padding);
            $inputScale = Safe::add($inputScale, $padding);
        }

        $calculator = CalculatorRegistry::get();

        // Keep one extra digit beyond the target scale for rounding.
        $intermediateScale = Safe::add(max($scale, intdiv($inputScale, $n)), 1);
        $value .= str_repeat('0', Safe::sub(Safe::mul($n, $intermediateScale), $inputScale));

        $root = $calculator->nthRoot($value, $n);
        $isExact = $calculator->pow($root, $n) === $value;

        if (! $isExact) {
            if ($roundingMode === RoundingMode::Unnecessary) {
                throw RoundingNecessaryException::decimalNthRootNotExact();
            }

            $isPositive = ! $isNegative;

            // Non-perfect-nth-power root is irrational, so the true value has strictly greater
            // magnitude than this truncated root. For "round away from zero" modes, bump the
            // integer root one step further from zero so the subsequent rescale rounds up.
            if (
                $roundingMode === RoundingMode::Up
                || ($roundingMode === RoundingMode::Ceiling && $isPositive)
                || ($roundingMode === RoundingMode::Floor && ! $isPositive)
            ) {
                $root = $isPositive
                    ? $calculator->add($root, '1')
                    : $calculator->sub($root, '1');
            }

            // Irrational nth root cannot land on a midpoint. For any Half* mode, the "tie" case
            // never occurs, so rewrite them all to HalfUp (round half away from zero), which is
            // the mode whose away-from-zero direction matches the sign of the (strictly larger
            // in magnitude) true value for both positive and negative inputs.
            elseif (in_array($roundingMode, [
                RoundingMode::HalfDown,
                RoundingMode::HalfEven,
                RoundingMode::HalfFloor,
                RoundingMode::HalfCeiling,
            ], true)) {
                $roundingMode = RoundingMode::HalfUp;
            }
        }

        $scaled = DecimalHelper::scale($root, $intermediateScale, $scale, $roundingMode);

        if ($scaled === null) {
            throw RoundingNecessaryException::decimalNthRootScaleTooSmall();
        }

        return new BigDecimal($scaled, $scale);
    }

    /**
     * Returns a copy of this BigDecimal with the decimal point moved to the left by the given number of places.
     *
     * If $places is negative, the decimal point is moved to the right by the absolute value instead.
     *
     * @pure
     */
    public function withPointMovedLeft(int $places): BigDecimal
    {
        if ($places === 0) {
            return $this;
        }

        if ($places < 0) {
            return $this->withPointMovedRight(Safe::neg($places));
        }

        /** @var non-negative-int $scale */
        $scale = Safe::add($this->scale, $places);

        return new BigDecimal($this->value, $scale);
    }

    /**
     * Returns a copy of this BigDecimal with the decimal point moved to the right by the given number of places.
     *
     * If $places is negative, the decimal point is moved to the left by the absolute value instead.
     *
     * @pure
     */
    public function withPointMovedRight(int $places): BigDecimal
    {
        if ($places === 0) {
            return $this;
        }

        if ($places < 0) {
            return $this->withPointMovedLeft(Safe::neg($places));
        }

        $value = $this->value;
        $scale = Safe::sub($this->scale, $places);

        if ($scale < 0) {
            if ($value !== '0') {
                $value .= str_repeat('0', Safe::neg($scale));
            }
            $scale = 0;
        }

        return new BigDecimal($value, $scale);
    }

    /**
     * Returns a copy of this BigDecimal with any trailing zeros removed from the fractional part.
     *
     * Examples:
     *
     * - `1.200` returns `1.2`
     * - `1.000` returns `1`
     * - `100` returns `100`
     *
     * @pure
     */
    public function strippedOfTrailingZeros(): BigDecimal
    {
        if ($this->scale === 0) {
            return $this;
        }

        $trimmedValue = rtrim($this->value, '0');

        if ($trimmedValue === '') {
            return BigDecimal::zero();
        }

        $trimmableZeros = strlen($this->value) - strlen($trimmedValue);

        if ($trimmableZeros === 0) {
            return $this;
        }

        if ($trimmableZeros > $this->scale) {
            $trimmableZeros = $this->scale;
        }

        $value = substr($this->value, 0, -$trimmableZeros);

        /** @var non-negative-int $scale */
        $scale = $this->scale - $trimmableZeros;

        return new BigDecimal($value, $scale);
    }

    #[Override]
    public function negated(): static
    {
        return new BigDecimal(CalculatorRegistry::get()->neg($this->value), $this->scale);
    }

    #[Override]
    public function compareTo(BigNumber|int|string $that): int
    {
        $that = BigNumber::of($that);

        if ($that instanceof BigInteger) {
            $that = $that->toBigDecimal();
        }

        if ($that instanceof BigDecimal) {
            [$a, $b] = $this->scaleValues($this, $that);

            return CalculatorRegistry::get()->cmp($a, $b);
        }

        return -$that->compareTo($this);
    }

    #[Override]
    public function getSign(): int
    {
        return ($this->value === '0') ? 0 : (($this->value[0] === '-') ? -1 : 1);
    }

    /**
     * Returns the unscaled value of this decimal number.
     *
     * For example, the unscaled value of `123.456` is `123456`.
     *
     * @pure
     */
    public function getUnscaledValue(): BigInteger
    {
        return self::newBigInteger($this->value);
    }

    /**
     * Returns the scale of this decimal number.
     *
     * The scale is the number of digits after the decimal point. For example, the scale of `123.456` is `3`.
     *
     * @return non-negative-int
     *
     * @pure
     */
    public function getScale(): int
    {
        return $this->scale;
    }

    /**
     * Returns the number of significant digits in the number.
     *
     * This is the number of digits in the unscaled value of the number.
     * The sign has no impact on the result.
     *
     * Examples:
     *   0 => 1
     *   0.0 => 1
     *   123 => 3
     *   123.456 => 6
     *   0.00123 => 3
     *   0.0012300 => 5
     *
     * @return positive-int
     *
     * @pure
     */
    public function getPrecision(): int
    {
        $length = strlen($this->value);

        /** @var positive-int */
        return ($this->value[0] === '-') ? $length - 1 : $length;
    }

    /**
     * Returns the integral part of this decimal number.
     *
     * Examples:
     *
     * - `123.456` returns `123`
     * - `-123.456` returns `-123`
     * - `0.123` returns `0`
     * - `-0.123` returns `0`
     *
     * The following identity holds: `$d->isEqualTo($d->getFractionalPart()->plus($d->getIntegralPart()))`. Note that in
     * this identity, the operand order is significant: the reversed form throws when the fractional part is non-zero.
     *
     * @pure
     */
    public function getIntegralPart(): BigInteger
    {
        if ($this->scale === 0) {
            return self::newBigInteger($this->value);
        }

        $value = DecimalHelper::padUnscaledValue($this->value, $this->scale);
        $integerPart = substr($value, 0, -$this->scale);

        if ($integerPart === '-0') {
            $integerPart = '0';
        }

        return self::newBigInteger($integerPart);
    }

    /**
     * Returns the fractional part of this decimal number.
     *
     * Examples:
     *
     * - `123.456` returns `0.456`
     * - `-123.456` returns `-0.456`
     * - `123` returns `0`
     * - `-123` returns `0`
     * - `123.000` returns `0.000`
     *
     * The result always has the same scale as `$this`.
     *
     * The following identity holds: `$d->isEqualTo($d->getFractionalPart()->plus($d->getIntegralPart()))`. Note that in
     * this identity, the operand order is significant: the reversed form throws when the fractional part is non-zero.
     *
     * @pure
     */
    public function getFractionalPart(): BigDecimal
    {
        if ($this->scale === 0) {
            return BigDecimal::zero();
        }

        return $this->minus($this->getIntegralPart());
    }

    #[Override]
    public function toBigInteger(): BigInteger
    {
        $value = DecimalHelper::tryScaleExactly($this->value, $this->scale, 0);

        if ($value !== null) {
            return self::newBigInteger($value);
        }

        throw RoundingNecessaryException::decimalNotConvertibleToInteger();
    }

    #[Override]
    public function toBigDecimal(): BigDecimal
    {
        return $this;
    }

    #[Override]
    public function toBigRational(): BigRational
    {
        $numerator = self::newBigInteger($this->value);
        $denominator = self::newBigInteger('1' . str_repeat('0', $this->scale));

        return self::newBigRational($numerator, $denominator, false, true);
    }

    #[Override]
    public function toScale(int $scale, RoundingMode $roundingMode = RoundingMode::Unnecessary): BigDecimal
    {
        if ($scale < 0) { // @phpstan-ignore smaller.alwaysFalse
            throw InvalidArgumentException::negativeScale();
        }

        if ($scale === $this->scale) {
            return $this;
        }

        $value = DecimalHelper::scale($this->value, $this->scale, $scale, $roundingMode);

        if ($value === null) {
            throw RoundingNecessaryException::decimalScaleTooSmall();
        }

        return new BigDecimal($value, $scale);
    }

    #[Override]
    public function toInt(): int
    {
        return $this->toBigInteger()->toInt();
    }

    #[Override]
    public function toFloat(): float
    {
        return (float) $this->toString();
    }

    /**
     * @return numeric-string
     */
    #[Override]
    public function toString(): string
    {
        if ($this->scale === 0) {
            /** @var numeric-string */
            return $this->value;
        }

        $value = DecimalHelper::padUnscaledValue($this->value, $this->scale);

        /** @phpstan-ignore return.type */
        return substr($value, 0, -$this->scale) . '.' . substr($value, -$this->scale);
    }

    /**
     * This method is required for serializing the object and SHOULD NOT be accessed directly.
     *
     * @internal
     *
     * @return array{value: string, scale: non-negative-int}
     */
    public function __serialize(): array
    {
        return ['value' => $this->value, 'scale' => $this->scale];
    }

    /**
     * This method is only here to allow unserializing the object and cannot be accessed directly.
     *
     * @internal
     *
     * @param array{value: string, scale: non-negative-int} $data
     *
     * @throws LogicException
     */
    public function __unserialize(array $data): void
    {
        /** @phpstan-ignore isset.initializedProperty */
        if (isset($this->value)) {
            throw new LogicException('__unserialize() is an internal function, it must not be called directly.');
        }

        /** @phpstan-ignore deadCode.unreachable */
        $this->value = $data['value'];
        $this->scale = $data['scale'];
    }

    #[Override]
    protected static function from(BigNumber $number): static
    {
        return $number->toBigDecimal();
    }

    /**
     * Puts the internal values of the given decimal numbers on the same scale.
     *
     * @return array{string, string} The scaled integer values of $x and $y.
     *
     * @pure
     */
    private function scaleValues(BigDecimal $x, BigDecimal $y): array
    {
        $a = $x->value;
        $b = $y->value;

        if ($b !== '0' && $x->scale > $y->scale) {
            $b .= str_repeat('0', $x->scale - $y->scale);
        } elseif ($a !== '0' && $x->scale < $y->scale) {
            $a .= str_repeat('0', $y->scale - $x->scale);
        }

        return [$a, $b];
    }

    /**
     * @pure
     */
    private function valueWithMinScale(int $scale): string
    {
        $value = $this->value;

        if ($this->value !== '0' && $scale > $this->scale) {
            $value .= str_repeat('0', $scale - $this->scale);
        }

        return $value;
    }

    /**
     * @pure
     */
    private function isOneScaleZero(): bool
    {
        return $this->value === '1' && $this->scale === 0;
    }
}
