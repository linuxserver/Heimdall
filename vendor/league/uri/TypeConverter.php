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

namespace League\Uri;

use BackedEnum;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use ReflectionEnum;
use Stringable;
use UnitEnum;
use ValueError;

use function enum_exists;
use function filter_var;
use function is_bool;
use function is_float;
use function is_int;
use function is_iterable;
use function is_scalar;
use function is_string;
use function trim;

use const FILTER_NULL_ON_FAILURE;
use const FILTER_VALIDATE_BOOLEAN;
use const FILTER_VALIDATE_FLOAT;
use const FILTER_VALIDATE_INT;

final class TypeConverter
{
    private function __construct()
    {
    }

    public static function toString(mixed $value): ?string
    {
        if ($value instanceof BackedEnum) {
            $value = $value->value;
        }

        return match (true) {
            is_bool($value) => true === $value ? '1' : '0',
            is_scalar($value),
            $value instanceof Stringable => (string) $value,
            default => null,
        };
    }

    public static function toInteger(mixed $value): ?int
    {
        if (!is_int($value) && !is_float($value)) {
            $value = self::toString($value);
        }

        return match (true) {
            is_int($value) => $value,
            is_string($value) => false !== ($res = filter_var($value, FILTER_VALIDATE_INT)) ? $res : null,
            default => null,
        };
    }

    public static function toFloat(mixed $value): ?float
    {
        if (!is_int($value) && !is_float($value)) {
            $value = self::toString($value);
        }

        return match (true) {
            is_float($value) => $value,
            is_int($value) => (float) $value,
            is_string($value) => false !== ($res = filter_var($value, FILTER_VALIDATE_FLOAT)) ? $res : null,
            default => null,
        };
    }

    public static function toBoolean(mixed $value): ?bool
    {
        if (!is_bool($value)) {
            $value = self::toString($value);
        }

        return match (true) {
            is_bool($value) => $value,
            is_string($value) => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            default => null,
        };
    }

    /**
     * @return array<string>
     */
    public static function toStrings(mixed $values, ?string $default = null): array
    {
        if (!is_iterable($values)) {
            return [];
        }

        $arr = [];
        foreach ($values as $index => $value) {
            $item = self::toString($value) ?? $default;
            if (null === $item) {
                return [];
            }

            $arr[$index] = $item;
        }

        return $arr;
    }

    /**
     * @return array<int>
     */
    public static function toIntegers(mixed $values, ?int $default = null): array
    {
        if (!is_iterable($values)) {
            return [];
        }

        $arr = [];
        foreach ($values as $index => $value) {
            $item = self::toInteger($value) ?? $default;
            if (null === $item) {
                return [];
            }

            $arr[$index] = $item;
        }

        return $arr;
    }

    /**
     * @return array<float>
     */
    public static function toFloats(mixed $values, ?float $default = null): array
    {
        if (!is_iterable($values)) {
            return [];
        }

        $arr = [];
        foreach ($values as $index => $value) {
            $item = self::toFloat($value) ?? $default;
            if (null === $item) {
                return [];
            }

            $arr[$index] = $item;
        }

        return $arr;
    }

    /**
     * @return array<bool>
     */
    public static function toBooleans(mixed $values, ?bool $default = null): array
    {
        if (!is_iterable($values)) {
            return [];
        }

        $arr = [];
        foreach ($values as $index => $value) {
            $item = self::toBoolean($value) ?? $default;
            if (null === $item) {
                return [];
            }

            $arr[$index] = $item;
        }

        return $arr;
    }

    /**
     * @param class-string<UnitEnum> $enumClass
     */
    public static function toEnum(mixed $value, string $enumClass): ?UnitEnum
    {
        enum_exists($enumClass) || throw new ValueError($enumClass.' does not exist or could not be found.');
        /** @var array<UnitEnum> $cases */
        $cases = $enumClass::cases();

        return (new ReflectionEnum($enumClass))->isBacked()
            ? self::findBackedEnum($value, $enumClass, $cases) /* @phpstan-ignore-line */
            : self::findEnum($value, $enumClass, $cases);
    }

    /**
     * @template T of UnitEnum
     *
     * @param class-string<T> $enumClass
     * @param ?T $default
    */
    public static function toEnums(mixed $values, string $enumClass, ?UnitEnum $default = null): array
    {
        if (!is_iterable($values)) {
            return [];
        }

        enum_exists($enumClass) || throw new ValueError($enumClass.' does not exist or could not be found.');
        null === $default || $default instanceof $enumClass || throw new ValueError('The default value must be an instance of '.$enumClass.'; '.get_debug_type($default).' given.');

        $arr = [];
        $cases = $enumClass::cases();
        /** @var ReflectionEnum<UnitEnum> $reflection */
        $reflection = new ReflectionEnum($enumClass);

        $createItem = $reflection->isBacked()
             ? fn ($item) => self::findBackedEnum($item, $enumClass, $cases) /* @phpstan-ignore-line */
             : fn ($item) => self::findEnum($item, $enumClass, $cases);

        foreach ($values as $index => $value) {
            $item = $createItem($value) ?? $default;
            if (null === $item) {
                return [];
            }

            $arr[$index] = $item;
        }

        return $arr;
    }

    /**
     * @template T of BackedEnum
     *
     * @param class-string<T> $enumClass
     * @param array<T> $cases
     *
     * @return ?T
     */
    private static function findBackedEnum(mixed $value, string $enumClass, array $cases): ?UnitEnum
    {
        if ($value instanceof $enumClass) {
            return $value;
        }

        if ($value instanceof UnitEnum || is_float($value) || is_bool($value)) {
            return null;
        }

        if (!is_string($value) && !is_int($value)) {
            $value = self::toString($value);
        }

        if (null === $value) {
            return null;
        }

        $intValue = is_int($value) ? $value : self::toInteger($value);
        $value = (string) $value;
        foreach ($cases as $case) {
            if ($intValue === $case->value || $value === $case->value) {
                return $case;
            }
        }

        return null;
    }

    /**
     * @template T of UnitEnum
     *
     * @param class-string<T> $enumClass
     * @param array<T> $cases
     *
     * @return ?T
     */
    private static function findEnum(mixed $value, string $enumClass, array $cases): ?UnitEnum
    {
        if ($value instanceof $enumClass) {
            return $value;
        }

        if ($value instanceof UnitEnum || (is_scalar($value) && !is_string($value))) {
            return null;
        }

        $value = self::toString($value);
        if (null === $value) {
            return null;
        }

        foreach ($cases as $case) {
            if ($case->name === $value) {
                return $case;
            }
        }

        return null;
    }

    /**
     * @param non-empty-string $format
     *
     * @throws Exception if the provided timezone is invalid
     */
    public static function toDateTimeImmutable(mixed $value, string $format, DateTimeZone|string $timezone = 'UTC'): ?DateTimeImmutable
    {
        $format = trim($format);
        '' !== $format || throw new ValueError('The date format must be a non-empty string.');
        $timezone = !$timezone instanceof DateTimeZone ? new DateTimeZone($timezone) : $timezone;

        return self::createDateTimeImmutable($value, $format, $timezone);
    }

    /**
     * @param non-empty-string $format
     *
     * @throws Exception
     *
     * @return array<DateTimeImmutable>
     */
    public static function toDateTimeImmutables(
        mixed $values,
        string $format,
        DateTimeZone|string $timezone = 'UTC',
        DateTimeInterface|null $default = null,
    ): array {
        if (!is_iterable($values)) {
            return [];
        }

        $format = trim($format);
        '' !== $format || throw new ValueError('The date format must be a non-empty string.');
        $timezone = !$timezone instanceof DateTimeZone ? new DateTimeZone($timezone) : $timezone;

        if (null !== $default && !$default instanceof DateTimeImmutable) {
            $default = DateTimeImmutable::createFromInterface($default);
        }

        $arr = [];
        foreach ($values as $index => $value) {
            $item = self::createDateTimeImmutable($value, $format, $timezone) ?? $default;
            if (null === $item) {
                return [];
            }

            $arr[$index] = $item;
        }

        return $arr;
    }

    /**
     * @param non-empty-string $format
     */
    private static function createDateTimeImmutable(mixed $value, string $format, DateTimeZone $timezone): ?DateTimeImmutable
    {
        if ($value instanceof DateTimeImmutable) {
            return $value;
        }

        if ($value instanceof DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($value);
        }

        if (is_scalar($value) && !is_string($value)) {
            return null;
        }

        $value = self::toString($value);
        if (!is_string($value) || str_contains($value, "\0")) {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat($format, $value, $timezone);
        $errors = DateTimeImmutable::getLastErrors();

        return false !== $date
            && (false === $errors || (0 === $errors['error_count'] && 0 === $errors['warning_count'])) ? $date : null;
    }
}
