# Brick\Math

<img src="https://raw.githubusercontent.com/brick/brick/master/logo.png" alt="" align="left" height="64">

A PHP library to work with arbitrary-precision numbers.

[![Build Status](https://github.com/brick/math/workflows/CI/badge.svg)](https://github.com/brick/math/actions)
[![Coverage Status](https://codecov.io/github/brick/math/graph/badge.svg)](https://codecov.io/github/brick/math)
[![Latest Stable Version](https://poser.pugx.org/brick/math/v/stable)](https://packagist.org/packages/brick/math)
[![Total Downloads](https://poser.pugx.org/brick/math/downloads)](https://packagist.org/packages/brick/math)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://opensource.org/license/MIT)

## Introduction

This library provides immutable classes to work with three types of numbers:

- `BigInteger` — an integer number such as `123`
- `BigDecimal` — a decimal number such as `1.23`
- `BigRational` — a fraction such as `2/3` — always reduced to lowest terms, e.g. `2/6` becomes `1/3`

It automatically uses GMP or BCMath when available, and falls back to a pure-PHP implementation otherwise.

All classes work with a virtually **unlimited number of digits**, and are only limited by available memory and CPU time.

### Installation

This library is installable via [Composer](https://getcomposer.org/):

```bash
composer require brick/math
```

### Requirements

This library requires PHP 8.2 or later.

Although the library can work seamlessly on any PHP installation, it is highly recommended that you install the
[GMP](https://php.net/manual/en/book.gmp.php) or [BCMath](https://php.net/manual/en/book.bc.php) extension
to speed up calculations. The fastest available calculator implementation will be automatically selected at runtime.

## Number classes

The three number classes all extend the same `BigNumber` class:

```
Brick\Math\BigNumber
           ├── BigInteger
           ├── BigDecimal
           └── BigRational
```

`BigNumber` is an abstract class that defines the common behaviour of all number classes:

- `of()` — to obtain an instance
- sign methods: `isZero()`, `isPositive()`, etc.
- comparison methods: `isEqualTo()`, `isGreaterThan()`, etc.
- `min()`, `max()`, `sum()`, `toString()`, etc.

## Instantiation

The constructors of the classes are not public, so you must use a factory method to obtain an instance.

All classes provide an `of()` factory method that accepts any of the following types:

- `BigNumber` instances
- `int` numbers
- `string` representations of integer, decimal and rational numbers

Example:

```php
BigInteger::of(123456);
BigInteger::of('9999999999999999999999999999999999999999999');

BigDecimal::of('9.99999999999999999999999999999999999999999999');
BigDecimal::of('1.23e1000');

BigRational::of('2/3');
```

The `of()` method of each class accepts all the representations above, *as long as the value can be safely converted to that class*:

```php
BigInteger::of('1e3'); // 1000
BigInteger::of('1.00'); // 1
BigInteger::of('1.01'); // RoundingNecessaryException

BigDecimal::of('1/8'); // 0.125
BigDecimal::of('1/3'); // RoundingNecessaryException

BigRational::of('1.1'); // 11/10
BigRational::of('1.15'); // 23/20
```

> [!NOTE]
> The `of()` factory method does not accept `float` values, because casting a float to string can be lossy.
> To convert a float to a `BigDecimal`, use one of the dedicated methods:
>
> ```php
> // Exact IEEE-754 representation — the value the float actually holds:
> BigDecimal::fromFloatExact(0.1); // 0.1000000000000000055511151231257827021181583404541015625
>
> // Shortest decimal that round-trips back to the same float:
> BigDecimal::fromFloatShortest(0.1); // 0.1
> ```

## Parsing untrusted input

`of()` places no hard limits on its input: a string with millions of digits is accepted as is, and a number in
exponential notation is expanded to its full length, so a string as short as `1e1000000000` yields a number with
a billion digits.

If your input comes from an untrusted source, such as an HTTP request, use `parse()` instead, which requires you
to specify the allowed syntax and a maximum number of digits:

```php
use Brick\Math\NumberSyntax;

BigDecimal::parse($input, allowedSyntax: NumberSyntax::DECIMAL, maxDigits: 20);
```

The `$allowedSyntax` parameter restricts the accepted notations. Plain integers such as `123` are always accepted,
and each `NumberSyntax` case (`DecimalPoint`, `Exponent`, `Fraction`) allows one additional feature. The enum also
provides constants for the most common combinations:

- `NumberSyntax::INTEGER` — integers only: `123`
- `NumberSyntax::DECIMAL` — integers and decimal numbers: `123`, `123.45`; typical for monetary input
- `NumberSyntax::SCIENTIFIC` — integers, decimal numbers and exponents: `123`, `123.45`, `1.5e-3`; accepts every JSON number
- `NumberSyntax::RATIONAL` — integers and fractions: `123`, `22/7`
- `NumberSyntax::ALL` — the full syntax accepted by `of()`: `123`, `123.45`, `1.5e-3`, `22/7`

The `$maxDigits` parameter limits the number of digits, counted both as written in the input and in the resulting number,
so that a value such as `1e1000000000` is rejected before it is ever expanded:

```php
BigDecimal::parse('123.45', allowedSyntax: NumberSyntax::DECIMAL, maxDigits: 20); // 123.45
BigDecimal::parse('1.2e3', allowedSyntax: NumberSyntax::DECIMAL, maxDigits: 20); // NumberFormatException (exponent not allowed)
BigDecimal::parse('1e1000000000', allowedSyntax: NumberSyntax::SCIENTIFIC, maxDigits: 20); // NumberFormatException (too many digits)
```

## Parameter types

All methods that accept a number (`plus()`, `minus()`, `multipliedBy()`, etc.) accept the same types as `of()`.
For example, given the following number:

```php
$integer = BigInteger::of(123);
```

The following lines are equivalent:

```php
$integer->multipliedBy(123);
$integer->multipliedBy('123');
$integer->multipliedBy($integer);
```

As with `of()`, other types of numbers are accepted, as long as they can be safely converted to the current type:

```php
echo BigInteger::of(2)->multipliedBy('2.0'); // 4
echo BigInteger::of(2)->multipliedBy('2.5'); // RoundingNecessaryException
echo BigDecimal::of('2.5')->multipliedBy(2); // 5.0
```

These parameters are converted with `of()`, so the same rules apply: for untrusted strings, use
[`parse()`](#parsing-untrusted-input) first, and pass the resulting number to the method.

## Immutability & chaining

The `BigInteger`, `BigDecimal` and `BigRational` classes are immutable: their value never changes,
so that they can be safely passed around. All methods that return a `BigInteger`, `BigDecimal` or `BigRational`
return a new object, leaving the original object unaffected:

```php
$ten = BigInteger::of(10);

echo $ten->plus(5); // 15
echo $ten->multipliedBy(3); // 30
```

The methods can be chained for better readability:

```php
echo BigInteger::of(10)->plus(5)->multipliedBy(3); // 45
```

## Rounding

Unless documented otherwise, all methods either return an exact result or throw an exception if the result is not exact.
Where applicable, this behaviour is configurable through an optional `RoundingMode` parameter:

| Rounding mode               | Description                                                   |
|-----------------------------|---------------------------------------------------------------|
| `RoundingMode::Unnecessary` | Requires an exact result; throws if rounding would be needed. |
| `RoundingMode::Up`          | Rounds away from zero.                                        |
| `RoundingMode::Down`        | Rounds toward zero.                                           |
| `RoundingMode::Ceiling`     | Rounds toward positive infinity.                              |
| `RoundingMode::Floor`       | Rounds toward negative infinity.                              |
| `RoundingMode::HalfUp`      | Rounds to nearest; ties away from zero.                       |
| `RoundingMode::HalfDown`    | Rounds to nearest; ties toward zero.                          |
| `RoundingMode::HalfCeiling` | Rounds to nearest; ties toward positive infinity.             |
| `RoundingMode::HalfFloor`   | Rounds to nearest; ties toward negative infinity.             |
| `RoundingMode::HalfEven`    | Rounds to nearest; ties to the even neighbor.                 |
| `RoundingMode::HalfOdd`     | Rounds to nearest; ties to the odd neighbor.                  |

See the [Division](#division) section for examples of `RoundingMode` in action.

> [!TIP]
> PHP 8.4 introduced a native [RoundingMode](https://www.php.net/manual/en/enum.roundingmode.php) enum, used by
> `round()`, `bcround()` and `BcMath\Number::round()`. If you already have a native rounding mode at hand, you can
> convert it to its `Brick\Math` equivalent:
>
> ```php
> $roundingMode = RoundingMode::fromNativeRoundingMode(\RoundingMode::HalfAwayFromZero);
> ```

## Arithmetic operations

### Addition, subtraction and multiplication

These operations are straightforward on all number classes:

```php
echo BigInteger::of(1)->plus(2)->multipliedBy(3); // 9
echo BigDecimal::of('1.2')->plus('3.4')->multipliedBy('5.6'); // 25.76
echo BigRational::of('2/3')->plus('5/6')->multipliedBy('5/4'); // 15/8
```

The scale of `BigDecimal` operation results is predictable:

- for addition and subtraction, it is the larger of the two operand scales;
- for multiplication, it is the sum of the operand scales.

`BigRational` results are automatically reduced to lowest terms.

### Division

Division uses a class-specific API because exactness and precision rules differ between integers, decimals, and rationals.

#### BigInteger

By default, dividing a `BigInteger` returns the exact result of the division, or throws an exception if the remainder
of the division is not zero:

```php
echo BigInteger::of(999)->dividedBy(3); // 333
echo BigInteger::of(1000)->dividedBy(3); // RoundingNecessaryException
```

You can pass an optional `RoundingMode` to round the result, if necessary:

```php
echo BigInteger::of(1000)->dividedBy(3, RoundingMode::Down); // 333
echo BigInteger::of(1000)->dividedBy(3, RoundingMode::Up); // 334
```

You can also compute quotients and remainders:

```php
echo BigInteger::of(1000)->quotient(3); // 333
echo BigInteger::of(1000)->remainder(3); // 1
```

You can also get both in one call:

```php
[$quotient, $remainder] = BigInteger::of(1000)->quotientAndRemainder(3);
```

#### BigDecimal

Dividing a `BigDecimal` always requires a scale to be specified. If the exact result of the division does not fit in
the given scale, a `RoundingMode` must be provided:

```php
echo BigDecimal::of(1)->dividedBy('8', 3); // 0.125
echo BigDecimal::of(1)->dividedBy('8', 2); // RoundingNecessaryException
echo BigDecimal::of(1)->dividedBy('8', 2, RoundingMode::HalfDown); // 0.12
echo BigDecimal::of(1)->dividedBy('8', 2, RoundingMode::HalfUp); // 0.13
```

If you know that the division yields a finite number of decimal places, you can use `dividedByExact()`, which will
automatically compute the required scale to fit the result, or throw an exception if the division yields an infinite
repeating decimal:

```php
echo BigDecimal::of(1)->dividedByExact(256); // 0.00390625
echo BigDecimal::of(1)->dividedByExact(11); // RoundingNecessaryException
```

#### BigRational

The result of the division of a `BigRational` can always be represented exactly:

```php
echo BigRational::of('13/99')->dividedBy('7'); // 13/693
echo BigRational::of('13/99')->dividedBy('9/8'); // 104/891
```

`BigRational` results are automatically reduced to lowest terms.

### Other arithmetic operations

In addition to `plus()`, `minus()`, `multipliedBy()`, and `dividedBy()`, the library provides:

- exponentiation with `power()` on all number classes
- square root with `sqrt()` on `BigInteger` and `BigDecimal`
- nth root with `nthRoot()` on `BigInteger` and `BigDecimal`
- greatest common divisor / least common multiple with `gcd()`, `lcm()`, `gcdAll()`, `lcmAll()` on `BigInteger`
- modular arithmetic with `mod()`, `modInverse()`, and `modPow()` on `BigInteger`
- reciprocal with `reciprocal()` on `BigRational`
- decimal-point shifts with `withPointMovedLeft()` and `withPointMovedRight()` on `BigDecimal`

## Sign and comparison

All number classes share the same sign and comparison methods through `BigNumber`.

### Sign methods

Use these methods to inspect the sign of a number:

- `getSign()` — returns `-1`, `0`, or `1` for values `< 0`, `= 0`, and `> 0`, respectively
- `isZero()`
- `isNegative()`
- `isNegativeOrZero()`
- `isPositive()`
- `isPositiveOrZero()`

For sign-related transformations, use:

- `abs()` — returns the absolute value
- `negated()` — returns the opposite value

### Comparison methods

Comparison works across all number classes (`BigInteger`, `BigDecimal`, `BigRational`):

- `compareTo()` — returns `-1`, `0`, or `1` if this number is `<`, `=`, or `>` the given number
- `isEqualTo()`
- `isLessThan()`
- `isLessThanOrEqualTo()`
- `isGreaterThan()`
- `isGreaterThanOrEqualTo()`

You can also use `min()`, `max()`, and `clamp()` to compare and bound values.

## Type conversion

### Conversion to other number classes

All classes provide the following methods:

- `toBigInteger()`
- `toBigDecimal()`
- `toBigRational()`

`toBigInteger()` and `toBigDecimal()` either return an exact result, or throw a `RoundingNecessaryException` if the conversion is not exact.
`toBigRational()` always returns an exact result.

You can also convert any number to a `BigDecimal` with a given scale, rounding the result if necessary:

```php
echo BigRational::of('2/3')->toScale(5, RoundingMode::Up); // 0.66667
```

To convert any number to a `BigInteger` with rounding, use:

```php
echo BigRational::of('10/3')->toScale(0, RoundingMode::Up)->toBigInteger(); // 4
```

### Conversion to native numbers

All classes provide the following methods:

- `toInt()` — converts exactly to an `int` if possible, or throws an exception otherwise
- `toFloat()` — returns an approximation of the number as a `float` (may be infinite)

> [!WARNING]
> `toFloat()` is the only method of the library that returns an approximation. Use it with caution.

### Conversion to string

All number classes can be converted to string using either the `toString()` method or the `(string)` cast. For example, the following lines are equivalent:

```php
echo BigInteger::of(123)->toString();
echo (string) BigInteger::of(123);
```

Different number classes produce different outputs. Note that a `BigDecimal` with a scale of zero and a `BigRational` with a denominator of one both print as plain digit strings:

```php
echo BigInteger::of(-123)->toString(); // -123

echo BigDecimal::of('1.0')->toString(); // 1.0
echo BigDecimal::of('1')->toString(); // 1

echo BigRational::of('2/3')->toString(); // 2/3
echo BigRational::of('1/1')->toString(); // 1
```

All string outputs are parseable by the `of()` factory method. The following is guaranteed to work:

```php
BigNumber::of($bigNumber->toString());
```

> [!IMPORTANT]
> Because `BigDecimal::toString()` and `BigRational::toString()` can return whole numbers, these numbers will be parsed
> as `BigInteger` when using `BigNumber::of()`. If you want to retain the original type when reparsing numbers, be sure
> to use `of()` on the specific class: `BigDecimal::of()` or `BigRational::of()`.

#### BigRational to decimal string

In addition to the standard rational representation such as `2/3`, rational numbers can be represented as decimal numbers
with a potentially repeating sequence of digits. You can use `toRepeatingDecimalString()` to get this representation:

```php
BigRational::of('1/2')->toRepeatingDecimalString(); // 0.5
BigRational::of('2/3')->toRepeatingDecimalString(); // 0.(6)
BigRational::of('171/70')->toRepeatingDecimalString(); // 2.4(428571)
```

The part in parentheses is the repeating period, if any.

> [!NOTE]
> The `of()` and `parse()` factory methods do not accept decimal strings with repeating periods.

> [!WARNING]
> `BigRational::toRepeatingDecimalString()` is unbounded.
> The repeating period can be as large as `denominator - 1`, so large denominators can require a lot of memory and CPU time.
> Example: `BigRational::of('1/100019')->toRepeatingDecimalString()` has a repeating period of 100,018 digits.

## Base conversion

`BigInteger` can parse and format numbers in different bases:

- `fromBase()` / `toBase()` for bases 2 to 36 (case-insensitive input, lowercase output above base 10)
- `fromArbitraryBase()` / `toArbitraryBase()` for custom single-byte alphabets

```php
echo BigInteger::fromBase('ff', 16); // 255
echo BigInteger::of(255)->toBase(16); // ff

echo BigInteger::fromArbitraryBase('bab', 'ab'); // 5
echo BigInteger::of(5)->toArbitraryBase('ab'); // bab
```

You can also convert to and from byte strings using `fromBytes()` and `toBytes()`.

## Bitwise operations

`BigInteger` supports bitwise operations:

- `and()`
- `or()`
- `xor()`
- `not()`

and bit shifting:

- `shiftedLeft()`
- `shiftedRight()`

Bit-level inspection helpers are also available:

- `getBitLength()`
- `getLowestSetBit()`
- `isBitSet()`

## Random number generation

`BigInteger` provides factory methods for random integers:

- `randomBits($bitCount)` returns a non-negative integer with up to the requested bit length.
- `randomRange($min, $max)` returns a value in the inclusive range `[$min, $max]`.

Both methods use a secure random source by default and throw `RandomSourceException` if randomness cannot be obtained.

## Exceptions

All exceptions thrown by this library implement the `MathException` interface.
This means that you can safely catch all exceptions thrown by this library using a single catch clause:

```php
use Brick\Math\BigInteger;
use Brick\Math\Exception\MathException;

try {
    $number = BigInteger::of(1)->dividedBy(3);
} catch (MathException $e) {
    // ...
}
```

If you need more granular control over the exceptions thrown, you can catch the specific exception classes documented in each method:

- `DivisionByZeroException`
- `IntegerOverflowException`
- `InvalidArgumentException`
- `NegativeNumberException`
- `NoInverseException`
- `NumberFormatException`
- `PlatformException`
- `RandomSourceException`
- `RoundingNecessaryException`

## Serialization

`BigInteger`, `BigDecimal` and `BigRational` can be safely serialized on a machine and unserialized on another,
even if these machines do not share the same set of PHP extensions.

For example, serializing on a machine with GMP support and unserializing on a machine that does not have this extension
installed will still work as expected.

### JSON

All number classes support serialization to JSON using the `json_encode()` function:

```php
echo json_encode(BigInteger::of(123)); // "123"
```

## Additional information

### Release process

This library follows [semantic versioning](https://semver.org/).

### PHPStan extension

A third-party [PHPStan extension](https://github.com/simPod/phpstan-brick-math) is available for this library. It provides more specific throw type narrowing for brick/math methods, so that PHPStan can infer the exact exception classes thrown. Note that this extension is not maintained by the author of brick/math.
