## Brick\Math

<img src="https://raw.githubusercontent.com/brick/brick/master/logo.png" alt="" align="left" height="64">

A PHP library to work with arbitrary precision numbers.

[![Build Status](https://github.com/brick/math/workflows/CI/badge.svg)](https://github.com/brick/math/actions)
[![Coverage Status](https://codecov.io/github/brick/math/graph/badge.svg)](https://codecov.io/github/brick/math)
[![Latest Stable Version](https://poser.pugx.org/brick/math/v/stable)](https://packagist.org/packages/brick/math)
[![Total Downloads](https://poser.pugx.org/brick/math/downloads)](https://packagist.org/packages/brick/math)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](http://opensource.org/licenses/MIT)

### Installation

This library is installable via [Composer](https://getcomposer.org/):

```bash
composer require brick/math
```

### Requirements

This library requires PHP 8.2 or later.

For PHP 8.1 compatibility, you can use version `0.13`. For PHP 8.0, you can use version `0.11`. For PHP 7.4, you can use version `0.10`. For PHP 7.1, 7.2 & 7.3, you can use version `0.9`. Note that [PHP versions < 8.1 are EOL](http://php.net/supported-versions.php) and not supported anymore. If you're still using one of these PHP versions, you should consider upgrading as soon as possible.

Although the library can work seamlessly on any PHP installation, it is highly recommended that you install the
[GMP](http://php.net/manual/en/book.gmp.php) or [BCMath](http://php.net/manual/en/book.bc.php) extension
to speed up calculations. The fastest available calculator implementation will be automatically selected at runtime.

### Project status & release process

While this library is still under development, it is well tested and considered stable enough to use in production
environments.

The current releases are numbered `0.x.y`. When a non-breaking change is introduced (adding new methods, optimizing
existing code, etc.), `y` is incremented.

**When a breaking change is introduced, a new `0.x` version cycle is always started.**

It is therefore safe to lock your project to a given release cycle, such as `^0.17`.

If you need to upgrade to a newer release cycle, check the [release history](https://github.com/brick/math/releases)
for a list of changes introduced by each further `0.x.0` version.

### Package contents

This library provides the following public classes in the `Brick\Math` namespace:

- [BigNumber](https://github.com/brick/math/blob/0.17.2/src/BigNumber.php): base class for `BigInteger`, `BigDecimal` and `BigRational`
- [BigInteger](https://github.com/brick/math/blob/0.17.2/src/BigInteger.php): represents an arbitrary-precision integer number.
- [BigDecimal](https://github.com/brick/math/blob/0.17.2/src/BigDecimal.php): represents an arbitrary-precision decimal number.
- [BigRational](https://github.com/brick/math/blob/0.17.2/src/BigRational.php): represents an arbitrary-precision rational number (fraction), always reduced to lowest terms.
- [RoundingMode](https://github.com/brick/math/blob/0.17.2/src/RoundingMode.php): enum representing all available rounding modes.

And [exceptions](#exceptions) in the `Brick\Math\Exception` namespace.

### Overview

#### Instantiation

The constructors of the classes are not public, you must use a factory method to obtain an instance.

All classes provide an `of()` factory method that accepts any of the following types:

- `BigNumber` instances
- `int` numbers
- `string` representations of integer, decimal and rational numbers

Example:

```php
BigInteger::of(123546);
BigInteger::of('9999999999999999999999999999999999999999999');

BigDecimal::of('9.99999999999999999999999999999999999999999999');

BigRational::of('2/3');
```

Note that all `of()` methods accept all the representations above, *as long as it can be safely converted to
the current type*:

```php
BigInteger::of('1.00'); // 1
BigInteger::of('1.01'); // RoundingNecessaryException

BigDecimal::of('1/8'); // 0.125
BigDecimal::of('1/3'); // RoundingNecessaryException

BigRational::of('1.1'); // 11/10
BigRational::of('1.15'); // 23/20 (reduced to lowest terms)
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

#### Immutability & chaining

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

#### Parameter types

All methods that accept a number: `plus()`, `minus()`, `multipliedBy()`, etc. accept the same types as `of()`.
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

Just like `of()`, other types of `BigNumber` are acceptable, as long as they can be safely converted to the current type:

```php
echo BigInteger::of(2)->multipliedBy(BigDecimal::of('2.0')); // 4
echo BigInteger::of(2)->multipliedBy(BigDecimal::of('2.5')); // RoundingNecessaryException
echo BigDecimal::of(2.5)->multipliedBy(BigInteger::of(2)); // 5.0
```

#### Division & rounding

##### BigInteger

By default, dividing a `BigInteger` returns the exact result of the division, or throws an exception if the remainder
of the division is not zero:

```php
echo BigInteger::of(999)->dividedBy(3); // 333
echo BigInteger::of(1000)->dividedBy(3); // RoundingNecessaryException
```

You can pass an optional [rounding mode](https://github.com/brick/math/blob/0.17.2/src/RoundingMode.php) to round the result, if necessary:

```php
echo BigInteger::of(1000)->dividedBy(3, RoundingMode::Down); // 333
echo BigInteger::of(1000)->dividedBy(3, RoundingMode::Up); // 334
```

If you're into quotients and remainders, there are methods for this, too:

```php
echo BigInteger::of(1000)->quotient(3); // 333
echo BigInteger::of(1000)->remainder(3); // 1
```

You can even get both at the same time:

```php
[$quotient, $remainder] = BigInteger::of(1000)->quotientAndRemainder(3);
```

##### BigDecimal

Dividing a `BigDecimal` always requires a scale to be specified. If the exact result of the division does not fit in
the given scale, a [rounding mode](https://github.com/brick/math/blob/0.17.2/src/RoundingMode.php) must be provided.

```php
echo BigDecimal::of(1)->dividedBy('8', 3); // 0.125
echo BigDecimal::of(1)->dividedBy('8', 2); // RoundingNecessaryException
echo BigDecimal::of(1)->dividedBy('8', 2, RoundingMode::HalfDown); // 0.12
echo BigDecimal::of(1)->dividedBy('8', 2, RoundingMode::HalfUp); // 0.13
```

If you know that the division yields a finite number of decimals places, you can use `dividedByExact()`, which will
automatically compute the required scale to fit the result, or throw an exception if the division yields an infinite
repeating decimal:

```php
echo BigDecimal::of(1)->dividedByExact(256); // 0.00390625
echo BigDecimal::of(1)->dividedByExact(11); // RoundingNecessaryException
```

##### BigRational

The result of the division of a `BigRational` can always be represented exactly:

```php
echo BigRational::of('13/99')->dividedBy('7'); // 13/693
echo BigRational::of('13/99')->dividedBy('9/8'); // 104/891
```

#### Bitwise operations

`BigInteger` supports bitwise operations:

- `and()`
- `or()`
- `xor()`
- `not()`

and bit shifting:

- `shiftedLeft()`
- `shiftedRight()`

#### Exceptions

All exceptions thrown by this library implement the `MathException` interface.
This means that you can safely catch all exceptions thrown by this library using a single catch clause:

```php
use Brick\Math\BigDecimal;
use Brick\Math\Exception\MathException;

try {
    $number = BigInteger::of(1)->dividedBy(3);
} catch (MathException $e) {
    // ...
}
```

If you need more granular control over the exceptions thrown, you can catch the specific exception classes:

- `DivisionByZeroException`
- `IntegerOverflowException`
- `InvalidArgumentException`
- `NegativeNumberException`
- `NoInverseException`
- `NumberFormatException`
- `RandomSourceException`
- `RoundingNecessaryException`
- `UnsupportedPlatformException`

#### Serialization

`BigInteger`, `BigDecimal` and `BigRational` can be safely serialized on a machine and unserialized on another,
even if these machines do not share the same set of PHP extensions.

For example, serializing on a machine with GMP support and unserializing on a machine that does not have this extension
installed will still work as expected.

#### PHPStan extension

A third-party [PHPStan extension](https://github.com/simPod/phpstan-brick-math) is available for this library. It provides more specific throw type narrowing for brick/math methods, so that PHPStan can infer the exact exception classes thrown. Note that this extension is not maintained by the author of brick/math.
