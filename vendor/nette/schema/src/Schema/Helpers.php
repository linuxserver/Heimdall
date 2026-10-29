<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Schema;

use Nette;
use Nette\Utils\Reflection;
use function count, is_array, is_float, is_int, is_object, is_scalar, is_string, strlen;


/**
 * @internal
 */
final class Helpers
{
	use Nette\StaticClass;

	public const PreventMerging = '_prevent_merging';


	/**
	 * Merges dataset. Left has higher priority than right one.
	 */
	public static function merge(mixed $value, mixed $base): mixed
	{
		if (is_array($value) && isset($value[self::PreventMerging])) {
			unset($value[self::PreventMerging]);
			return $value;
		}

		if (is_array($value) && is_array($base)) {
			$index = 0;
			foreach ($value as $key => $val) {
				if ($key === $index) {
					$base[] = $val;
					$index++;
				} else {
					$base[$key] = static::merge($val, $base[$key] ?? null);
				}
			}

			return $base;

		} elseif ($value === null && is_array($base)) {
			return $base;

		} else {
			return $value;
		}
	}


	/**
	 * Returns the type of a property or parameter as a string, or null if not determinable.
	 */
	public static function getPropertyType(\ReflectionProperty|\ReflectionParameter $prop): ?string
	{
		if ($type = Nette\Utils\Type::fromReflection($prop)) {
			return (string) $type;
		} elseif (
			($prop instanceof \ReflectionProperty)
			&& ($type = preg_replace('#\s.*#', '', (string) self::parseAnnotation($prop, 'var')))
		) {
			$class = Reflection::getPropertyDeclaringClass($prop);
			return preg_replace_callback('#[\w\\\]+#', fn($m) => Reflection::expandClassName($m[0], $class), $type);
		}

		return null;
	}


	/**
	 * Returns an annotation value.
	 * @param  \ReflectionClass<object>|\ReflectionProperty  $ref
	 */
	public static function parseAnnotation(\ReflectionClass|\ReflectionProperty $ref, string $name): ?string
	{
		if (!Reflection::areCommentsAvailable()) {
			throw new Nette\InvalidStateException('You have to enable phpDoc comments in opcode cache.');
		}

		$re = '#[\s*]@' . preg_quote($name, '#') . '(?=\s|$)(?:[ \t]+([^@\s]\S*))?#';
		if ($ref->getDocComment() && preg_match($re, trim($ref->getDocComment(), '/*'), $m)) {
			return $m[1] ?? '';
		}

		return null;
	}


	/**
	 * Formats a value for use in error messages (e.g., 'hello', true, object stdClass).
	 */
	public static function formatValue(mixed $value): string
	{
		if ($value instanceof DynamicParameter) {
			return 'dynamic';
		} elseif (is_object($value)) {
			return 'object ' . $value::class;
		} elseif (is_string($value)) {
			return "'" . Nette\Utils\Strings::truncate($value, 15, '...') . "'";
		} elseif (is_scalar($value)) {
			return var_export($value, return: true);
		} else {
			return get_debug_type($value);
		}
	}


	/**
	 * Adds a TypeMismatch error to the context if the value does not match the expected type.
	 */
	public static function validateType(mixed $value, string $expected, Context $context): void
	{
		if (!Nette\Utils\Validators::is($value, $expected)) {
			$expected = str_replace(DynamicParameter::class . '|', '', $expected);
			$expected = str_replace(['|', ':'], [' or ', ' in range '], $expected);
			$context->addError(
				'The %label% %path% expects to be %expected%, %value% given.',
				Message::TypeMismatch,
				['value' => $value, 'expected' => $expected],
			);
		}
	}


	/**
	 * Adds a range error to the context if the value (or its length for strings/arrays) is outside the given range.
	 * @param  array{int|float|null, int|float|null}  $range
	 */
	public static function validateRange(mixed $value, array $range, Context $context, string $types = ''): void
	{
		if (is_array($value) || is_string($value)) {
			[$length, $label] = is_array($value)
				? [count($value), 'items']
				: (in_array('unicode', explode('|', $types), true)
					? [Nette\Utils\Strings::length($value), 'characters']
					: [strlen($value), 'bytes']);

			if (!self::isInRange($length, $range)) {
				$context->addError(
					"The length of %label% %path% expects to be in range %expected%, %length% $label given.",
					Message::LengthOutOfRange,
					['value' => $value, 'length' => $length, 'expected' => implode('..', $range)],
				);
			}
		} elseif ((is_int($value) || is_float($value)) && !self::isInRange($value, $range)) {
			$context->addError(
				'The %label% %path% expects to be in range %expected%, %value% given.',
				Message::ValueOutOfRange,
				['value' => $value, 'expected' => implode('..', $range)],
			);
		}
	}


	/**
	 * Checks whether a value falls within the given [min, max] range (null means no bound).
	 * @param  array{int|float|null, int|float|null}  $range
	 */
	public static function isInRange(mixed $value, array $range): bool
	{
		return ($range[0] === null || $value >= $range[0])
			&& ($range[1] === null || $value <= $range[1]);
	}


	/**
	 * Adds a PatternMismatch error to the context if the value does not match the pattern.
	 */
	public static function validatePattern(string $value, string $pattern, Context $context): void
	{
		$res = @preg_match("\x01^(?:$pattern)$\x01Du", $value); // @ - an invalid pattern is thrown below
		if ($res === false && preg_last_error() === PREG_INTERNAL_ERROR) {
			throw new Nette\InvalidStateException("Invalid pattern '$pattern': " . Nette\Utils\Helpers::getLastError());
		} elseif (!$res) {
			$context->addError(
				"The %label% %path% expects to match pattern '%pattern%', %value% given.",
				Message::PatternMismatch,
				['value' => $value, 'pattern' => $pattern],
			);
		}
	}


	/**
	 * Returns a closure that casts a value to the given type (built-in, backed enum, class with constructor, or plain class).
	 * A null stays null for an enum or a class, and for a built-in type only when written as ?type.
	 * @return \Closure(mixed, Context): mixed
	 */
	public static function getCastStrategy(string $type): \Closure
	{
		if (str_starts_with($type, '?')) {
			$cast = self::getCastStrategy(substr($type, 1));
			return static fn($value, Context $context) => $value === null ? null : $cast($value, $context);

		} elseif (Nette\Utils\Validators::isBuiltinType($type)) {
			return static function ($value) use ($type) {
				settype($value, $type);
				return $value;
			};

		} elseif (is_subclass_of($type, \BackedEnum::class)) {
			return static function ($value, Context $context) use ($type) {
				if ($value === null) {
					return null;
				}

				try {
					return $type::from($value);
				} catch (\TypeError|\ValueError) {
					$context->addError(
						'The %label% %path% expects to be %expected%, %value% given.',
						Message::TypeMismatch,
						[
							'value' => $value,
							'expected' => implode('|', array_map(fn(\BackedEnum $case) => self::formatValue($case->value), $type::cases())),
						],
					);
					return null;
				}
			};

		} elseif (is_subclass_of($type, \UnitEnum::class)) {
			throw new Nette\InvalidStateException("Cannot cast value to pure enum $type.");
		}

		$factory = method_exists($type, '__construct')
			? static fn($value) => is_array($value) || $value instanceof \stdClass
				? new $type(...(array) $value)
				: new $type($value)
			: static fn($value) => Nette\Utils\Arrays::toObject((array) $value, new $type);

		return static function ($value) use ($factory, $type) {
			if ($value === null) {
				return null;
			}

			try {
				return $factory($value);
			} catch (\Error $e) {
				throw new Nette\InvalidStateException("Unable to cast value to $type: " . $e->getMessage(), 0, $e);
			}
		};
	}
}
