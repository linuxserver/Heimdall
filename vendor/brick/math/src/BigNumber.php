<?php

declare(strict_types=1);

namespace Brick\Math;

use Brick\Math\Exception\IntegerOverflowException;
use Brick\Math\Exception\InvalidArgumentException;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\PlatformException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\Internal\Safe;
use JsonSerializable;
use Override;
use Stringable;

use function array_is_list;
use function assert;
use function filter_var;
use function in_array;
use function is_int;
use function ltrim;
use function max;
use function preg_match;
use function str_contains;
use function str_repeat;
use function strlen;

use const FILTER_VALIDATE_INT;
use const PHP_INT_MAX;
use const PREG_UNMATCHED_AS_NULL;

/**
 * Base class for arbitrary-precision numbers.
 *
 * This class is sealed: it is part of the public API but should not be subclassed in userland.
 * Protected methods may change in any version.
 *
 * @phpstan-sealed BigInteger|BigDecimal|BigRational
 */
abstract readonly class BigNumber implements JsonSerializable, Stringable
{
    /**
     * The regular expression used to parse integer or decimal numbers.
     *
     * The end anchor must be \z, not $: the latter would also match before a trailing newline.
     * The digit quantifiers must be possessive (++): backtracking on malformed input could exhaust
     * pcre.backtrack_limit, surfacing as PlatformException instead of NumberFormatException.
     */
    private const PARSE_REGEXP_NUMERICAL =
        '/^' .
        '(?<sign>[\-\+])?' .
        '(?<integral>[0-9]++)?' .
        '(?<point>\.)?' .
        '(?<fractional>[0-9]++)?' .
        '(?:[eE](?<exponent>[\-\+]?[0-9]++))?' .
        '\z/';

    /**
     * The regular expression used to parse rational numbers.
     *
     * The end anchor must be \z, not $: the latter would also match before a trailing newline.
     * The digit quantifiers must be possessive (++): backtracking on malformed input could exhaust
     * pcre.backtrack_limit, surfacing as PlatformException instead of NumberFormatException.
     */
    private const PARSE_REGEXP_RATIONAL =
        '/^' .
        '(?<sign>[\-\+])?' .
        '(?<numerator>[0-9]++)' .
        '\/' .
        '(?<denominator>[0-9]++)' .
        '\z/';

    /**
     * Creates a BigNumber of the given value.
     *
     * When of() is called on BigNumber, the concrete return type is dependent on the given value, with the following
     * rules:
     *
     * - BigNumber instances are returned as is
     * - integer numbers are returned as BigInteger
     * - strings containing a `/` character are returned as BigRational
     * - strings containing a `.` character or using an exponential notation are returned as BigDecimal
     * - strings containing only digits with an optional leading `+` or `-` sign are returned as BigInteger
     *
     * When of() is called on BigInteger, BigDecimal, or BigRational, the resulting number is converted to an instance
     * of the subclass when possible; otherwise a RoundingNecessaryException is thrown.
     *
     * When parsing untrusted input, use {@see parse()} instead.
     *
     * @throws NumberFormatException      If the input is a string, and the format of the number is not valid.
     * @throws RoundingNecessaryException If the method is called on a subclass of BigNumber, and the value cannot be
     *                                    converted to an instance of the subclass without rounding.
     *
     * @pure
     */
    final public static function of(BigNumber|int|string $value): static
    {
        $value = self::_of($value);

        if ($value instanceof static) {
            // No conversion needed.
            return $value;
        }

        return static::from($value);
    }

    /**
     * Creates a BigNumber of the given value, or returns null if the input is null.
     *
     * Behaves like {@see of()} for non-null values.
     *
     * When parsing untrusted input, use {@see parseNullable()} instead.
     *
     * @throws NumberFormatException      If the input is a string, and the format of the number is not valid.
     * @throws RoundingNecessaryException If the method is called on a subclass of BigNumber, and the value cannot be
     *                                    converted to an instance of the subclass without rounding.
     *
     * @pure
     */
    final public static function ofNullable(BigNumber|int|string|null $value): ?static
    {
        if ($value === null) {
            return null;
        }

        return static::of($value);
    }

    /**
     * Creates a BigNumber of the given string, limiting the allowed syntax and the number of digits.
     *
     * This method is designed to safely parse untrusted input: huge strings, and exponential notation that allows a
     * short string such as `1e1000000000` to expand to gigabytes of memory.
     *
     * The $allowedSyntax parameter restricts the accepted notations: plain integers such as `123` are always accepted,
     * then each NumberSyntax case allows one additional feature: DecimalPoint, Exponent, Fraction. A value is accepted
     * only if every feature it uses is allowed. The NumberSyntax enum also provides constants for the most common
     * combinations, from NumberSyntax::INTEGER to NumberSyntax::ALL.
     *
     * The $maxDigits parameter limits the number of digits, counted in each of these forms:
     *
     * - as written, where every digit of the input counts, including leading zeros and exponent digits: `005` counts
     *   3 digits, `01e-3` counts 3, and `010/012` counts 6;
     * - in its expanded form, with exponents materialized and leading zeros trimmed: `01e-3` counts 4 (`0.001`);
     * - in its converted form, when called on a subclass, as written by toString(): `BigDecimal::parse('1/8', ...)`
     *   counts 4 digits (`0.125`), although `1/8` counts only 2.
     *
     * When parse() is called on BigNumber, the concrete return type is determined by the format of the string,
     * following the same rules as {@see of()}. When called on a subclass, the value is converted to an instance of
     * that subclass when possible.
     *
     * @param string             $value         The untrusted value to parse.
     * @param list<NumberSyntax> $allowedSyntax The allowed syntax features; plain integers are always accepted.
     * @param positive-int       $maxDigits     The maximum number of digits, as written and in the resulting number.
     *
     * @throws NumberFormatException      If the format of $value is invalid, if it uses a syntax that is not allowed
     *                                    by $allowedSyntax, or if it has more than $maxDigits digits.
     * @throws RoundingNecessaryException If the method is called on a subclass of BigNumber, and the value cannot be
     *                                    converted to an instance of the subclass without rounding.
     * @throws InvalidArgumentException   If $allowedSyntax is not a list of NumberSyntax cases, or if $maxDigits is
     *                                    less than 1.
     *
     * @pure
     */
    final public static function parse(
        string $value,
        array $allowedSyntax,
        int $maxDigits,
    ): static {
        if (! array_is_list($allowedSyntax)) { // @phpstan-ignore function.alreadyNarrowedType
            throw InvalidArgumentException::invalidAllowedSyntax();
        }

        foreach ($allowedSyntax as $syntax) {
            if (! $syntax instanceof NumberSyntax) { // @phpstan-ignore instanceof.alwaysTrue
                throw InvalidArgumentException::invalidAllowedSyntax();
            }
        }

        if ($maxDigits < 1) { // @phpstan-ignore smaller.alwaysFalse
            throw InvalidArgumentException::nonPositiveMaxDigits();
        }

        $value = self::_parse($value, $allowedSyntax, $maxDigits);

        if ($value instanceof static) {
            // No conversion needed.
            return $value;
        }

        $result = static::from($value);

        // The conversion may expand the number (1/8 -> 0.125): the limit applies to the result as well.
        if ($result->digitCount() > $maxDigits) {
            throw NumberFormatException::tooManyDigits($maxDigits);
        }

        return $result;
    }

    /**
     * Creates a BigNumber of the given string, limiting the allowed syntax and the number of digits, or returns null
     * if the input is null.
     *
     * Behaves like {@see parse()} for non-null values.
     *
     * @param string|null        $value         The untrusted value to parse, or null.
     * @param list<NumberSyntax> $allowedSyntax The allowed syntax features; plain integers are always accepted.
     * @param positive-int       $maxDigits     The maximum number of digits, as written and in the resulting number.
     *
     * @throws NumberFormatException      If the format of $value is invalid, if it uses a syntax that is not allowed
     *                                    by $allowedSyntax, or if it has more than $maxDigits digits.
     * @throws RoundingNecessaryException If the method is called on a subclass of BigNumber, and the value cannot be
     *                                    converted to an instance of the subclass without rounding.
     * @throws InvalidArgumentException   If $allowedSyntax is not a list of NumberSyntax cases, or if $maxDigits is
     *                                    less than 1.
     *
     * @pure
     */
    final public static function parseNullable(
        ?string $value,
        array $allowedSyntax,
        int $maxDigits,
    ): ?static {
        if ($value === null) {
            if (! array_is_list($allowedSyntax)) { // @phpstan-ignore function.alreadyNarrowedType
                throw InvalidArgumentException::invalidAllowedSyntax();
            }

            foreach ($allowedSyntax as $syntax) {
                if (! $syntax instanceof NumberSyntax) { // @phpstan-ignore instanceof.alwaysTrue
                    throw InvalidArgumentException::invalidAllowedSyntax();
                }
            }

            if ($maxDigits < 1) { // @phpstan-ignore smaller.alwaysFalse
                throw InvalidArgumentException::nonPositiveMaxDigits();
            }

            return null;
        }

        return static::parse($value, $allowedSyntax, $maxDigits);
    }

    /**
     * Returns the minimum of the given values.
     *
     * If several values are equal and minimal, the first one is returned.
     * This can affect the concrete return type when calling this method on BigNumber.
     *
     * @param BigNumber|int|string $a    The first number. Must be convertible to an instance of the class this method
     *                                   is called on.
     * @param BigNumber|int|string ...$n The additional numbers. Each number must be convertible to an instance of the
     *                                   class this method is called on.
     *
     * @throws MathException If a number is not valid, or is not convertible to an instance of the class this method is
     *                       called on.
     *
     * @pure
     */
    final public static function min(BigNumber|int|string $a, BigNumber|int|string ...$n): static
    {
        $min = static::of($a);

        foreach ($n as $value) {
            $value = static::of($value);

            if ($value->isLessThan($min)) {
                $min = $value;
            }
        }

        return $min;
    }

    /**
     * Returns the maximum of the given values.
     *
     * If several values are equal and maximal, the first one is returned.
     * This can affect the concrete return type when calling this method on BigNumber.
     *
     * @param BigNumber|int|string $a    The first number. Must be convertible to an instance of the class this method
     *                                   is called on.
     * @param BigNumber|int|string ...$n The additional numbers. Each number must be convertible to an instance of the
     *                                   class this method is called on.
     *
     * @throws MathException If a number is not valid, or is not convertible to an instance of the class this method is
     *                       called on.
     *
     * @pure
     */
    final public static function max(BigNumber|int|string $a, BigNumber|int|string ...$n): static
    {
        $max = static::of($a);

        foreach ($n as $value) {
            $value = static::of($value);

            if ($value->isGreaterThan($max)) {
                $max = $value;
            }
        }

        return $max;
    }

    /**
     * Returns the sum of the given values.
     *
     * When called on BigNumber, sum() accepts any supported type and returns a result whose type is the widest among
     * the given values (BigInteger < BigDecimal < BigRational).
     *
     * When called on BigInteger, BigDecimal, or BigRational, sum() requires that all values can be converted to that
     * specific subclass, and returns a result of the same type.
     *
     * @param BigNumber|int|string $a    The first number. Must be convertible to an instance of the class this method
     *                                   is called on.
     * @param BigNumber|int|string ...$n The additional numbers. Each number must be convertible to an instance of the
     *                                   class this method is called on.
     *
     * @throws MathException If a number is not valid, or is not convertible to an instance of the class this method is
     *                       called on.
     *
     * @pure
     */
    final public static function sum(BigNumber|int|string $a, BigNumber|int|string ...$n): static
    {
        $sum = static::of($a);

        foreach ($n as $value) {
            $sum = self::add($sum, static::of($value));
        }

        assert($sum instanceof static);

        return $sum;
    }

    /**
     * Checks if this number is equal to the given one.
     *
     * @throws MathException If the given number is not valid.
     *
     * @pure
     */
    final public function isEqualTo(BigNumber|int|string $that): bool
    {
        return $this->compareTo($that) === 0;
    }

    /**
     * Checks if this number is strictly less than the given one.
     *
     * @throws MathException If the given number is not valid.
     *
     * @pure
     */
    final public function isLessThan(BigNumber|int|string $that): bool
    {
        return $this->compareTo($that) < 0;
    }

    /**
     * Checks if this number is less than or equal to the given one.
     *
     * @throws MathException If the given number is not valid.
     *
     * @pure
     */
    final public function isLessThanOrEqualTo(BigNumber|int|string $that): bool
    {
        return $this->compareTo($that) <= 0;
    }

    /**
     * Checks if this number is strictly greater than the given one.
     *
     * @throws MathException If the given number is not valid.
     *
     * @pure
     */
    final public function isGreaterThan(BigNumber|int|string $that): bool
    {
        return $this->compareTo($that) > 0;
    }

    /**
     * Checks if this number is greater than or equal to the given one.
     *
     * @throws MathException If the given number is not valid.
     *
     * @pure
     */
    final public function isGreaterThanOrEqualTo(BigNumber|int|string $that): bool
    {
        return $this->compareTo($that) >= 0;
    }

    /**
     * Checks if this number equals zero.
     *
     * @pure
     */
    final public function isZero(): bool
    {
        return $this->getSign() === 0;
    }

    /**
     * Checks if this number is strictly negative.
     *
     * @pure
     */
    final public function isNegative(): bool
    {
        return $this->getSign() < 0;
    }

    /**
     * Checks if this number is negative or zero.
     *
     * @pure
     */
    final public function isNegativeOrZero(): bool
    {
        return $this->getSign() <= 0;
    }

    /**
     * Checks if this number is strictly positive.
     *
     * @pure
     */
    final public function isPositive(): bool
    {
        return $this->getSign() > 0;
    }

    /**
     * Checks if this number is positive or zero.
     *
     * @pure
     */
    final public function isPositiveOrZero(): bool
    {
        return $this->getSign() >= 0;
    }

    /**
     * Returns the absolute value of this number.
     *
     * @pure
     */
    final public function abs(): static
    {
        return $this->isNegative() ? $this->negated() : $this;
    }

    /**
     * Returns the negated value of this number.
     *
     * @pure
     */
    abstract public function negated(): static;

    /**
     * Returns the sign of this number.
     *
     * Returns -1 if the number is negative, 0 if zero, 1 if positive.
     *
     * @return -1|0|1
     *
     * @pure
     */
    abstract public function getSign(): int;

    /**
     * Compares this number to the given one.
     *
     * Returns -1 if `$this` is lower than, 0 if equal to, 1 if greater than `$that`.
     *
     * @return -1|0|1
     *
     * @throws MathException If the number is not valid.
     *
     * @pure
     */
    abstract public function compareTo(BigNumber|int|string $that): int;

    /**
     * Limits (clamps) this number between the given minimum and maximum values.
     *
     * If the number is lower than $min, returns $min.
     * If the number is greater than $max, returns $max.
     * Otherwise, returns this number unchanged.
     *
     * @param BigNumber|int|string $min The minimum. Must be convertible to an instance of the class this method is called on.
     * @param BigNumber|int|string $max The maximum. Must be convertible to an instance of the class this method is called on.
     *
     * @throws MathException            If min/max are not convertible to an instance of the class this method is called on.
     * @throws InvalidArgumentException If min is greater than max.
     *
     * @pure
     */
    final public function clamp(BigNumber|int|string $min, BigNumber|int|string $max): static
    {
        $min = static::of($min);
        $max = static::of($max);

        if ($min->isGreaterThan($max)) {
            throw InvalidArgumentException::minGreaterThanMax();
        }

        if ($this->isLessThan($min)) {
            return $min;
        }

        if ($this->isGreaterThan($max)) {
            return $max;
        }

        return $this;
    }

    /**
     * Converts this number to a BigInteger.
     *
     * @throws RoundingNecessaryException If this number cannot be converted to a BigInteger without rounding.
     *
     * @pure
     */
    abstract public function toBigInteger(): BigInteger;

    /**
     * Converts this number to a BigDecimal.
     *
     * @throws RoundingNecessaryException If this number cannot be converted to a BigDecimal without rounding.
     *
     * @pure
     */
    abstract public function toBigDecimal(): BigDecimal;

    /**
     * Converts this number to a BigRational.
     *
     * @pure
     */
    abstract public function toBigRational(): BigRational;

    /**
     * Converts this number to a BigDecimal with the given scale, using rounding if necessary.
     *
     * @param non-negative-int $scale        The scale of the resulting `BigDecimal`. Must be non-negative.
     * @param RoundingMode     $roundingMode An optional rounding mode, defaults to Unnecessary.
     *
     * @throws InvalidArgumentException   If the scale is negative.
     * @throws RoundingNecessaryException If RoundingMode::Unnecessary is used, and this number cannot be converted to
     *                                    the given scale without rounding.
     *
     * @pure
     */
    abstract public function toScale(int $scale, RoundingMode $roundingMode = RoundingMode::Unnecessary): BigDecimal;

    /**
     * Returns the exact value of this number as a native integer.
     *
     * If this number cannot be converted to a native integer without losing precision, an exception is thrown.
     * Note that the acceptable range for an integer depends on the platform and differs for 32-bit and 64-bit.
     *
     * @throws RoundingNecessaryException If this number cannot be converted to an integer without rounding.
     * @throws IntegerOverflowException   If this number is too large to fit in a native integer.
     *
     * @pure
     */
    abstract public function toInt(): int;

    /**
     * Returns an approximation of this number as a floating-point value.
     *
     * Note that this method can discard information as the precision of a floating-point value
     * is inherently limited.
     *
     * If the number is greater than the largest representable floating point number, positive infinity is returned.
     * If the number is less than the smallest representable floating point number, negative infinity is returned.
     * This method never returns NaN.
     *
     * @pure
     */
    abstract public function toFloat(): float;

    /**
     * Returns a string representation of this number.
     *
     * The output of this method can be parsed by the `of()` factory method; this will yield an object equal to this
     * one, but possibly of a different type if instantiated through `BigNumber::of()`.
     *
     * @return non-empty-string
     *
     * @pure
     */
    abstract public function toString(): string;

    /**
     * @return non-empty-string
     */
    #[Override]
    final public function jsonSerialize(): string
    {
        return $this->toString();
    }

    /**
     * @return non-empty-string
     *
     * @pure
     */
    #[Override]
    final public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Overridden by subclasses to convert a BigNumber to an instance of the subclass.
     *
     * @throws RoundingNecessaryException If the value cannot be converted.
     *
     * @pure
     */
    abstract protected static function from(BigNumber $number): static;

    /**
     * Returns the number of digits in this number, as written by toString().
     *
     * @pure
     */
    abstract protected function digitCount(): int;

    /**
     * Proxy method to access BigInteger's protected constructor from sibling classes.
     *
     * @internal
     *
     * @pure
     */
    final protected function newBigInteger(string $value): BigInteger
    {
        return new BigInteger($value);
    }

    /**
     * Proxy method to access BigDecimal's protected constructor from sibling classes.
     *
     * @internal
     *
     * @param non-negative-int $scale
     *
     * @pure
     */
    final protected function newBigDecimal(string $value, int $scale = 0): BigDecimal
    {
        return new BigDecimal($value, $scale);
    }

    /**
     * Proxy method to access BigRational's protected constructor from sibling classes.
     *
     * @internal
     *
     * @pure
     */
    final protected function newBigRational(BigInteger $numerator, BigInteger $denominator, bool $checkDenominator, bool $simplify): BigRational
    {
        return new BigRational($numerator, $denominator, $checkDenominator, $simplify);
    }

    /**
     * @throws NumberFormatException If the format of the number is not valid.
     *
     * @pure
     */
    private static function _of(BigNumber|int|string $value): BigNumber
    {
        if ($value instanceof BigNumber) {
            return $value;
        }

        if (is_int($value)) {
            return new BigInteger((string) $value);
        }

        return self::_parse($value, null, null);
    }

    /**
     * Note: $allowedSyntax and $maxDigits are nullable as a performance optimization for of(): null behaves like
     * `NumberSyntax::ALL` and `PHP_INT_MAX`, but skips the checks, which could never fail against those values.
     *
     * @param list<NumberSyntax>|null $allowedSyntax The allowed syntax features; plain integers are always accepted. Null for all.
     * @param positive-int|null       $maxDigits     The maximum number of digits, as written and in the resulting number. Null for no limit.
     *
     * @throws NumberFormatException If the format of $value is invalid, if it uses a syntax that is not allowed
     *                               by $allowedSyntax, or if it has more than $maxDigits digits.
     *
     * @pure
     */
    private static function _parse(string $value, ?array $allowedSyntax, ?int $maxDigits): BigNumber
    {
        if ($value === '') {
            throw NumberFormatException::emptyNumber();
        }

        if (str_contains($value, '/')) {
            // Rational number
            $result = preg_match(self::PARSE_REGEXP_RATIONAL, $value, $matches, PREG_UNMATCHED_AS_NULL);

            if ($result === false) {
                throw PlatformException::pcreFailure();
            }

            if ($result === 0) {
                throw NumberFormatException::invalidFormat($value);
            }

            if ($allowedSyntax !== null && ! in_array(NumberSyntax::Fraction, $allowedSyntax, true)) {
                throw NumberFormatException::syntaxNotAllowed(NumberSyntax::Fraction);
            }

            $sign = $matches['sign'];
            $numerator = self::cleanUp($sign, $matches['numerator']);
            $denominator = self::cleanUp(null, $matches['denominator']);

            if ($denominator === '0') {
                throw NumberFormatException::zeroDenominator();
            }

            // The digit count is taken as written, before trimming zeros and before simplification:
            // the final count will always be less or equal.
            if ($maxDigits !== null && strlen($matches['numerator']) + strlen($matches['denominator']) > $maxDigits) {
                throw NumberFormatException::tooManyDigits($maxDigits);
            }

            return new BigRational(
                new BigInteger($numerator),
                new BigInteger($denominator),
                false,
                true,
            );
        }

        // Integer or decimal number
        $result = preg_match(self::PARSE_REGEXP_NUMERICAL, $value, $matches, PREG_UNMATCHED_AS_NULL);

        if ($result === false) {
            throw PlatformException::pcreFailure();
        }

        if ($result === 0) {
            throw NumberFormatException::invalidFormat($value);
        }

        $sign = $matches['sign'];
        $point = $matches['point'];
        $integral = $matches['integral'];
        $fractional = $matches['fractional'];
        $exponent = $matches['exponent'];

        if ($integral === null && $fractional === null) {
            throw NumberFormatException::invalidFormat($value);
        }

        if ($integral === null) {
            $integral = '0';
        }

        if ($point === null && $exponent === null) {
            // Integer number.
            if ($maxDigits !== null && strlen($integral) > $maxDigits) {
                throw NumberFormatException::tooManyDigits($maxDigits);
            }

            $integral = self::cleanUp($sign, $integral);

            return new BigInteger($integral);
        }

        // Decimal number.
        if ($allowedSyntax !== null) {
            if ($point !== null && ! in_array(NumberSyntax::DecimalPoint, $allowedSyntax, true)) {
                throw NumberFormatException::syntaxNotAllowed(NumberSyntax::DecimalPoint);
            }

            if ($exponent !== null && ! in_array(NumberSyntax::Exponent, $allowedSyntax, true)) {
                throw NumberFormatException::syntaxNotAllowed(NumberSyntax::Exponent);
            }
        }

        if ($exponent === null) {
            $exponent = 0;
        } else {
            $exponentSign = $exponent[0] === '-' ? '-' : '';
            $exponent = ltrim(ltrim($exponent, '+-'), '0');

            if ($exponent === '') {
                $exponent = 0;
            } else {
                $exponent = filter_var($exponentSign . $exponent, FILTER_VALIDATE_INT);

                if ($exponent === false) {
                    throw NumberFormatException::exponentTooLarge();
                }
            }
        }

        $fractional ??= '';

        $unscaledValue = self::cleanUp($sign, $integral . $fractional);
        $scale = strlen($fractional) - $exponent;

        // @phpstan-ignore function.alreadyNarrowedType (may overflow to float)
        if (! is_int($scale)) {
            throw NumberFormatException::exponentTooLarge();
        }

        $unscaledDigits = strlen($unscaledValue) - (int) ($unscaledValue[0] === '-');

        if ($scale < 0 && $unscaledValue !== '0') {
            // The unscaled value is padded with -$scale zeros below.
            $expandedDigits = $unscaledDigits - $scale;
        } else {
            // The fractional digits, plus at least a zero integer part.
            $expandedDigits = max($unscaledDigits, $scale + 1);
        }

        // @phpstan-ignore function.alreadyNarrowedType (may overflow to float)
        if (! is_int($expandedDigits)) {
            throw NumberFormatException::tooManyDigits($maxDigits ?? PHP_INT_MAX);
        }

        if ($maxDigits !== null) {
            // Digits as written: every digit of the input counts, including leading zeros and exponent digits.
            $writtenDigits = strlen($matches['integral'] ?? '') + strlen($matches['fractional'] ?? '');

            if ($matches['exponent'] !== null) {
                $writtenDigits += strlen($matches['exponent']) - (int) ($matches['exponent'][0] === '-' || $matches['exponent'][0] === '+');
            }

            if ($expandedDigits > $maxDigits || $writtenDigits > $maxDigits) {
                throw NumberFormatException::tooManyDigits($maxDigits);
            }
        }

        if ($scale < 0) {
            if ($unscaledValue !== '0') {
                $unscaledValue .= str_repeat('0', Safe::neg($scale));
            }
            $scale = 0;
        }

        return new BigDecimal($unscaledValue, $scale);
    }

    /**
     * Removes optional leading zeros and applies sign.
     *
     * @param '+'|'-'|null     $sign   The sign, optional. Null is allowed for convenience and treated as '+'.
     * @param non-empty-string $number The number, validated as a string of digits.
     *
     * @pure
     */
    private static function cleanUp(?string $sign, string $number): string
    {
        $number = ltrim($number, '0');

        if ($number === '') {
            return '0';
        }

        return $sign === '-' ? '-' . $number : $number;
    }

    /**
     * Adds two BigNumber instances in the correct order to avoid a RoundingNecessaryException.
     *
     * @pure
     */
    private static function add(BigNumber $a, BigNumber $b): BigNumber
    {
        if ($a instanceof BigRational) {
            return $a->plus($b);
        }

        if ($b instanceof BigRational) {
            return $b->plus($a);
        }

        if ($a instanceof BigDecimal) {
            return $a->plus($b);
        }

        if ($b instanceof BigDecimal) {
            return $b->plus($a);
        }

        return $a->plus($b);
    }
}
