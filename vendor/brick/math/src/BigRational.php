<?php

declare(strict_types=1);

namespace Brick\Math;

use Brick\Math\Exception\DivisionByZeroException;
use Brick\Math\Exception\InvalidArgumentException;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\Internal\DecimalHelper;
use Brick\Math\Internal\Safe;
use LogicException;
use Override;

use function max;
use function min;
use function strlen;
use function substr;

/**
 * An arbitrarily large rational number.
 *
 * This class is immutable.
 *
 * Fractions are automatically simplified to lowest terms. For example, `2/4` becomes `1/2`.
 * The denominator is always strictly positive; the sign is carried by the numerator.
 */
final readonly class BigRational extends BigNumber
{
    /**
     * The numerator.
     */
    private BigInteger $numerator;

    /**
     * The denominator. Always strictly positive.
     */
    private BigInteger $denominator;

    /**
     * Protected constructor. Use a factory method to obtain an instance.
     *
     * @param BigInteger $numerator        The numerator.
     * @param BigInteger $denominator      The denominator.
     * @param bool       $checkDenominator Whether to check the denominator for negative and zero.
     * @param bool       $simplify         Whether to simplify the fraction to lowest terms.
     *
     * @throws DivisionByZeroException If the denominator is zero.
     *
     * @pure
     */
    protected function __construct(BigInteger $numerator, BigInteger $denominator, bool $checkDenominator, bool $simplify)
    {
        if ($checkDenominator) {
            if ($denominator->isZero()) {
                throw DivisionByZeroException::zeroDenominator();
            }

            if ($denominator->isNegative()) {
                $numerator = $numerator->negated();
                $denominator = $denominator->negated();
            }
        }

        if ($simplify) {
            $gcd = $numerator->gcd($denominator);

            $numerator = $numerator->quotient($gcd);
            $denominator = $denominator->quotient($gcd);
        }

        $this->numerator = $numerator;
        $this->denominator = $denominator;
    }

    /**
     * Creates a BigRational out of a numerator and a denominator.
     *
     * If the denominator is negative, the signs of both the numerator and the denominator
     * will be inverted to ensure that the denominator is always positive.
     *
     * @param BigNumber|int|string $numerator   The numerator. Must be convertible to a BigInteger.
     * @param BigNumber|int|string $denominator The denominator. Must be convertible to a BigInteger.
     *
     * @throws MathException           If an argument is not valid, or is not convertible to a BigInteger.
     * @throws DivisionByZeroException If the denominator is zero.
     *
     * @pure
     */
    public static function ofFraction(
        BigNumber|int|string $numerator,
        BigNumber|int|string $denominator,
    ): BigRational {
        $numerator = BigInteger::of($numerator);
        $denominator = BigInteger::of($denominator);

        return new BigRational($numerator, $denominator, true, true);
    }

    /**
     * Returns a BigRational representing zero.
     *
     * @pure
     */
    public static function zero(): BigRational
    {
        /** @var BigRational|null $zero */
        static $zero;

        if ($zero === null) {
            $zero = new BigRational(BigInteger::zero(), BigInteger::one(), false, false);
        }

        return $zero;
    }

    /**
     * Returns a BigRational representing one.
     *
     * @pure
     */
    public static function one(): BigRational
    {
        /** @var BigRational|null $one */
        static $one;

        if ($one === null) {
            $one = new BigRational(BigInteger::one(), BigInteger::one(), false, false);
        }

        return $one;
    }

    /**
     * Returns a BigRational representing ten.
     *
     * @pure
     */
    public static function ten(): BigRational
    {
        /** @var BigRational|null $ten */
        static $ten;

        if ($ten === null) {
            $ten = new BigRational(BigInteger::ten(), BigInteger::one(), false, false);
        }

        return $ten;
    }

    /**
     * Returns the numerator of this rational number.
     *
     * @pure
     */
    public function getNumerator(): BigInteger
    {
        return $this->numerator;
    }

    /**
     * Returns the denominator of this rational number.
     *
     * The denominator is always strictly positive.
     *
     * @pure
     */
    public function getDenominator(): BigInteger
    {
        return $this->denominator;
    }

    /**
     * Returns the integral part of this rational number.
     *
     * Examples:
     *
     * - `7/3` returns `2` (since 7/3 = 2 + 1/3)
     * - `-7/3` returns `-2` (since -7/3 = -2 + (-1/3))
     *
     * The following identity holds: `$r->isEqualTo($r->getFractionalPart()->plus($r->getIntegralPart()))`. Note that in
     * this identity, the operand order is significant: the reversed form throws when the fractional part is non-zero.
     *
     * @pure
     */
    public function getIntegralPart(): BigInteger
    {
        return $this->numerator->quotient($this->denominator);
    }

    /**
     * Returns the fractional part of this rational number.
     *
     * Examples:
     *
     * - `7/3` returns `1/3` (since 7/3 = 2 + 1/3)
     * - `-7/3` returns `-1/3` (since -7/3 = -2 + (-1/3))
     *
     * The following identity holds: `$r->isEqualTo($r->getFractionalPart()->plus($r->getIntegralPart()))`. Note that in
     * this identity, the operand order is significant: the reversed form throws when the fractional part is non-zero.
     *
     * @pure
     */
    public function getFractionalPart(): BigRational
    {
        return new BigRational($this->numerator->remainder($this->denominator), $this->denominator, false, false);
    }

    /**
     * Returns the sum of this number and the given one.
     *
     * @param BigNumber|int|string $that The number to add.
     *
     * @throws MathException If the number is not valid.
     *
     * @pure
     */
    public function plus(BigNumber|int|string $that): BigRational
    {
        $that = BigRational::of($that);

        if ($that->isZero()) {
            return $this;
        }

        if ($this->isZero()) {
            return $that;
        }

        $numerator = $this->numerator->multipliedBy($that->denominator);
        $numerator = $numerator->plus($that->numerator->multipliedBy($this->denominator));
        $denominator = $this->denominator->multipliedBy($that->denominator);

        return new BigRational($numerator, $denominator, false, true);
    }

    /**
     * Returns the difference of this number and the given one.
     *
     * @param BigNumber|int|string $that The number to subtract.
     *
     * @throws MathException If the number is not valid.
     *
     * @pure
     */
    public function minus(BigNumber|int|string $that): BigRational
    {
        $that = BigRational::of($that);

        if ($that->isZero()) {
            return $this;
        }

        if ($this->isZero()) {
            return $that->negated();
        }

        $numerator = $this->numerator->multipliedBy($that->denominator);
        $numerator = $numerator->minus($that->numerator->multipliedBy($this->denominator));
        $denominator = $this->denominator->multipliedBy($that->denominator);

        return new BigRational($numerator, $denominator, false, true);
    }

    /**
     * Returns the product of this number and the given one.
     *
     * @param BigNumber|int|string $that The multiplier.
     *
     * @throws MathException If the multiplier is not valid.
     *
     * @pure
     */
    public function multipliedBy(BigNumber|int|string $that): BigRational
    {
        $that = BigRational::of($that);

        if ($that->isZero() || $this->isZero()) {
            return BigRational::zero();
        }

        $numerator = $this->numerator->multipliedBy($that->numerator);
        $denominator = $this->denominator->multipliedBy($that->denominator);

        return new BigRational($numerator, $denominator, false, true);
    }

    /**
     * Returns the result of the division of this number by the given one.
     *
     * @param BigNumber|int|string $that The divisor.
     *
     * @throws MathException           If the divisor is not valid.
     * @throws DivisionByZeroException If the divisor is zero.
     *
     * @pure
     */
    public function dividedBy(BigNumber|int|string $that): BigRational
    {
        $that = BigRational::of($that);

        if ($that->isZero()) {
            throw DivisionByZeroException::divisionByZero();
        }

        $numerator = $this->numerator->multipliedBy($that->denominator);
        $denominator = $this->denominator->multipliedBy($that->numerator);

        return new BigRational($numerator, $denominator, true, true);
    }

    /**
     * Returns this number exponentiated to the given value.
     *
     * Unlike BigInteger and BigDecimal, BigRational supports negative exponents:
     * the result is the reciprocal raised to the absolute value of the exponent.
     *
     * @throws DivisionByZeroException If the exponent is negative and this number is zero.
     *
     * @pure
     */
    public function power(int $exponent): BigRational
    {
        if ($exponent === 0) {
            return BigRational::one();
        }

        if ($exponent === 1) {
            return $this;
        }

        if ($exponent < 0) {
            if ($this->isZero()) {
                throw DivisionByZeroException::zeroToNegativePower();
            }

            return $this->reciprocal()->power(Safe::neg($exponent));
        }

        return new BigRational(
            $this->numerator->power($exponent),
            $this->denominator->power($exponent),
            false,
            false,
        );
    }

    /**
     * Returns the reciprocal of this BigRational.
     *
     * The reciprocal has the numerator and denominator swapped.
     *
     * @throws DivisionByZeroException If this number is zero.
     *
     * @pure
     */
    public function reciprocal(): BigRational
    {
        if ($this->isZero()) {
            throw DivisionByZeroException::reciprocalOfZero();
        }

        return new BigRational($this->denominator, $this->numerator, true, false);
    }

    #[Override]
    public function negated(): static
    {
        return new BigRational($this->numerator->negated(), $this->denominator, false, false);
    }

    #[Override]
    public function compareTo(BigNumber|int|string $that): int
    {
        $that = BigRational::of($that);

        if ($this->denominator->isEqualTo($that->denominator)) {
            return $this->numerator->compareTo($that->numerator);
        }

        return $this->numerator
            ->multipliedBy($that->denominator)
            ->compareTo($that->numerator->multipliedBy($this->denominator));
    }

    #[Override]
    public function getSign(): int
    {
        return $this->numerator->getSign();
    }

    #[Override]
    public function toBigInteger(): BigInteger
    {
        if ($this->denominator->isEqualTo(1)) {
            return $this->numerator;
        }

        throw RoundingNecessaryException::rationalNotConvertibleToInteger();
    }

    #[Override]
    public function toBigDecimal(): BigDecimal
    {
        $scale = DecimalHelper::computeScaleFromReducedFractionDenominator($this->denominator->toString());

        if ($scale === null) {
            throw RoundingNecessaryException::rationalNotConvertibleToDecimal();
        }

        return $this->numerator->toBigDecimal()->dividedBy($this->denominator, $scale)->strippedOfTrailingZeros();
    }

    #[Override]
    public function toBigRational(): BigRational
    {
        return $this;
    }

    #[Override]
    public function toScale(int $scale, RoundingMode $roundingMode = RoundingMode::Unnecessary): BigDecimal
    {
        if ($scale < 0) { // @phpstan-ignore smaller.alwaysFalse
            throw InvalidArgumentException::negativeScale();
        }

        if ($roundingMode === RoundingMode::Unnecessary) {
            $requiredScale = DecimalHelper::computeScaleFromReducedFractionDenominator($this->denominator->toString());

            if ($requiredScale === null) {
                throw RoundingNecessaryException::rationalNotConvertibleToDecimal();
            }

            if ($requiredScale > $scale) {
                throw RoundingNecessaryException::rationalScaleTooSmall();
            }
        }

        return $this->numerator->toBigDecimal()->dividedBy($this->denominator, $scale, $roundingMode);
    }

    #[Override]
    public function toInt(): int
    {
        return $this->toBigInteger()->toInt();
    }

    #[Override]
    public function toFloat(): float
    {
        if ($this->denominator->isEqualTo(1)) {
            return $this->numerator->toFloat();
        }

        // Avoid $this->numerator->toFloat() / $this->denominator->toFloat(): converting both operands to float first
        // adds an extra rounding step before the division and can change the final float. Instead, divide in decimal
        // first and convert the resulting decimal approximation to float once.

        // We need ~17 significant digits for double precision (we use 20 for some margin). Since $scale controls
        // decimal places (not significant digits), we subtract the estimated order of magnitude so that large results
        // use fewer decimal places and small results use more (to look past leading zeros). Clamped to [0, 350] as
        // doubles range from e-324 to e308 (350 ≈ 324 + 20 significant digits + margin).
        $magnitude = strlen($this->numerator->abs()->toString()) - strlen($this->denominator->toString());
        $scale = min(350, max(0, 20 - $magnitude));

        $result = $this->numerator
            ->toBigDecimal()
            ->dividedBy($this->denominator, $scale, RoundingMode::HalfEven)
            ->toFloat();

        // Preserve the sign when the decimal approximation underflows to zero.
        if ($result === 0.0 && $this->numerator->isNegative()) {
            return -0.0;
        }

        return $result;
    }

    #[Override]
    public function toString(): string
    {
        $numerator = $this->numerator->toString();
        $denominator = $this->denominator->toString();

        if ($denominator === '1') {
            return $numerator;
        }

        return $numerator . '/' . $denominator;
    }

    /**
     * Returns the decimal representation of this rational number, with repeating decimals in parentheses.
     *
     * WARNING: This method is unbounded.
     *          The length of the repeating decimal period can be as large as `denominator - 1`.
     *          For fractions with large denominators, this method can use excessive memory and CPU time.
     *          For example, `1/100019` has a repeating period of 100,018 digits.
     *
     * Examples:
     *
     * - `10/3` returns `3.(3)`
     * - `171/70` returns `2.4(428571)`
     * - `1/2` returns `0.5`
     *
     * @return non-empty-string
     *
     * @pure
     */
    public function toRepeatingDecimalString(): string
    {
        if ($this->isZero()) {
            return '0';
        }

        $sign = $this->numerator->isNegative() ? '-' : '';
        $numerator = $this->numerator->abs();
        $denominator = $this->denominator;

        $integral = $numerator->quotient($denominator);
        $remainder = $numerator->remainder($denominator);

        $integralString = $integral->toString();

        if ($remainder->isZero()) {
            return $sign . $integralString;
        }

        $digits = '';
        $remainderPositions = [];
        $index = 0;

        while (! $remainder->isZero()) {
            $remainderString = $remainder->toString();

            if (isset($remainderPositions[$remainderString])) {
                $repeatIndex = $remainderPositions[$remainderString];
                $nonRepeating = substr($digits, 0, $repeatIndex);
                $repeating = substr($digits, $repeatIndex);

                return $sign . $integralString . '.' . $nonRepeating . '(' . $repeating . ')';
            }

            $remainderPositions[$remainderString] = $index;
            $remainder = $remainder->multipliedBy(10);

            $digits .= $remainder->quotient($denominator)->toString();
            $remainder = $remainder->remainder($denominator);
            $index++;
        }

        return $sign . $integralString . '.' . $digits;
    }

    /**
     * This method is required for serializing the object and SHOULD NOT be accessed directly.
     *
     * @internal
     *
     * @return array{numerator: BigInteger, denominator: BigInteger}
     */
    public function __serialize(): array
    {
        return ['numerator' => $this->numerator, 'denominator' => $this->denominator];
    }

    /**
     * This method is only here to allow unserializing the object and cannot be accessed directly.
     *
     * @internal
     *
     * @param array{numerator: BigInteger, denominator: BigInteger} $data
     *
     * @throws LogicException
     */
    public function __unserialize(array $data): void
    {
        /** @phpstan-ignore isset.initializedProperty */
        if (isset($this->numerator)) {
            throw new LogicException('__unserialize() is an internal function, it must not be called directly.');
        }

        /** @phpstan-ignore deadCode.unreachable */
        $this->numerator = $data['numerator'];
        $this->denominator = $data['denominator'];
    }

    #[Override]
    protected static function from(BigNumber $number): static
    {
        return $number->toBigRational();
    }
}
