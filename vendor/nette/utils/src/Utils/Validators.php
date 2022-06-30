<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace Nette\Utils;

use Nette;


/**
 * Validation utilities.
 */
class Validators
{
	use Nette\StaticClass;

	/** @var array<string,?callable> */
	protected static $validators = [
		// PHP types
		'array' => 'is_array',
		'bool' => 'is_bool',
		'boolean' => 'is_bool',
		'float' => 'is_float',
		'int' => 'is_int',
		'integer' => 'is_int',
		'null' => 'is_null',
		'object' => 'is_object',
		'resource' => 'is_resource',
		'scalar' => 'is_scalar',
		'string' => 'is_string',

		// pseudo-types
		'callable' => [self::class, 'isCallable'],
		'iterable' => 'is_iterable',
		'list' => [Arrays::class, 'isList'],
		'mixed' => [self::class, 'isMixed'],
		'none' => [self::class, 'isNone'],
		'number' => [self::class, 'isNumber'],
		'numeric' => [self::class, 'isNumeric'],
		'numericint' => [self::class, 'isNumericInt'],

		// string patterns
		'alnum' => 'ctype_alnum',
		'alpha' => 'ctype_alpha',
		'digit' => 'ctype_digit',
		'lower' => 'ctype_lower',
		'pattern' => null,
		'space' => 'ctype_space',
		'unicode' => [self::class, 'isUnicode'],
		'upper' => 'ctype_upper',
		'xdigit' => 'ctype_xdigit',

		// syntax validation
		'email' => [self::class, 'isEmail'],
		'identifier' => [self::class, 'isPhpIdentifier'],
		'uri' => [self::class, 'isUri'],
		'url' => [self::class, 'isUrl'],

		// environment validation
		'class' => 'class_exists',
		'interface' => 'interface_exists',
		'directory' => 'is_dir',
		'file' => 'is_file',
		'type' => [self::class, 'isType'],
	];

	/** @var array<string,callable> */
	protected static $counters = [
		'string' => 'strlen',
		'unicode' => [Strings::class, 'length'],
		'array' => 'count',
		'list' => 'count',
		'alnum' => 'strlen',
		'alpha' => 'strlen',
		'digit' => 'strlen',
		'lower' => 'strlen',
		'space' => 'strlen',
		'upper' => 'strlen',
		'xdigit' => 'strlen',
	];


	/**
	 * Verifies that the value is of expected types separated by pipe.
	 * @param  mixed  $value
	 * @throws AssertionException
	 */
	public static function assert($value, string $expected, string $label = 'variable'): void
	{
		if (!static::is($value, $expected)) {
			$expected = str_replace(['|', ':'], [' or ', ' in range '], $expected);
			static $translate = ['boolean' => 'bool', 'integer' => 'int', 'double' => 'float', 'NULL' => 'null'];
			$type = $translate[gettype($value)] ?? gettype($value);
			if (is_int($value) || is_float($value) || (is_string($value) && strlen($value) < 40)) {
				$type .= ' ' . var_export($value, true);
			} elseif (is_object($value)) {
				$type .= ' ' . get_class($value);
			}

			throw new AssertionException("The $label expects to be $expected, $type given.");
		}
	}


	/**
	 * Verifies that element $key in array is of expected types separated by pipe.
	 * @param  mixed[]  $array
	 * @param  int|string  $key
	 * @throws AssertionException
	 */
	public static function assertField(
		array $array,
		$key,
		?string $expected = null,
		string $label = "item '%' in array"
	): void {
		if (!array_key_exists($key, $array)) {
			throw new AssertionException('Missing ' . str_replace('%', $key, $label) . '.');

		} elseif ($expected) {
			static::assert($array[$key], $expected, str_replace('%', $key, $label));
		}
	}


	/**
	 * Verifies that the value is of expected types separated by pipe.
	 * @param  mixed  $value
	 */
	public static function is($value, string $expected): bool
	{
		foreach (explode('|', $expected) as $item) {
			if (substr($item, -2) === '[]') {
				if (is_iterable($value) && self::everyIs($value, substr($item, 0, -2))) {
					return true;
				}

				continue;
			} elseif (substr($item, 0, 1) === '?') {
				$item = substr($item, 1);
				if ($value === null) {
					return true;
				}
			}

			[$type] = $item = explode(':', $item, 2);
			if (isset(static::$validators[$type])) {
				try {
					if (!static::$validators[$type]($value)) {
						continue;
					}
				} catch (\TypeError $e) {
					continue;
				}
			} elseif ($type === 'pattern') {
				if (Strings::match($value, '|^' . ($item[1] ?? '') . '$|D')) {
					return true;
				}

				continue;
			} elseif (!$value instanceof $type) {
				continue;
			}

			if (isset($item[1])) {
				$length = $value;
				if (isset(static::$counters[$type])) {
					$length = static::$counters[$type]($value);
				}

				$range = explode('..', $item[1]);
				if (!isset($range[1])) {
					$range[1] = $range[0];
				}

				if (($range[0] !== '' && $length < $range[0]) || ($range[1] !== '' && $length > $range[1])) {
					continue;
				}
			}

			return true;
		}

		return false;
	}


	/**
	 * Finds whether all values are of expected types separated by pipe.
	 * @param  mixed[]  $values
	 */
	public static function everyIs(iterable $values, string $expected): bool
	{
		foreach ($values as $value) {
			if (!static::is($value, $expected)) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Checks if the value is an integer or a float.
	 * @param  mixed  $value
	 */
	public static function isNumber($value): bool
	{
		return is_int($value) || is_float($value);
	}


	/**
	 * Checks if the value is an integer or a integer written in a string.
	 * @param  mixed  $value
	 */
	public static function isNumericInt($value): bool
	{
		return is_int($value) || (is_string($value) && preg_match('#^[+-]?[0-9]+$#D', $value));
	}


	/**
	 * Checks if the value is a number or a number written in a string.
	 * @param  mixed  $value
	 */
	public static function isNumeric($value): bool
	{
		return is_float($value) || is_int($value) || (is_string($value) && preg_match('#^[+-]?([0-9]++\.?[0-9]*|\.[0-9]+)$#D', $value));
	}


	/**
	 * Checks if the value is a syntactically correct callback.
	 * @param  mixed  $value
	 */
	public static function isCallable($value): bool
	{
		return $value && is_callable($value, true);
	}


	/**
	 * Checks if the value is a valid UTF-8 string.
	 * @param  mixed  $value
	 */
	public static function isUnicode($value): bool
	{
		return is_string($value) && preg_match('##u', $value);
	}


	/**
	 * Checks if the value is 0, '', false or null.
	 * @param  mixed  $value
	 */
	public static function isNone($value): bool
	{
		return $value == null; // intentionally ==
	}


	/** @internal */
	public static function isMixed(): bool
	{
		return true;
	}


	/**
	 * Checks if a variable is a zero-based integer indexed array.
	 * @param  mixed  $value
	 * @deprecated  use Nette\Utils\Arrays::isList
	 */
	public static function isList($value): bool
	{
		return Arrays::isList($value);
	}


	/**
	 * Checks if the value is in the given range [min, max], where the upper or lower limit can be omitted (null).
	 * Numbers, strings and DateTime objects can be compared.
	 * @param  mixed  $value
	 */
	public static function isInRange($value, array $range): bool
	{
		if ($value === null || !(isset($range[0]) || isset($range[1]))) {
			return false;
		}

		$limit = $range[0] ?? $range[1];
		if (is_string($limit)) {
			$value = (string) $value;
		} elseif ($limit instanceof \DateTimeInterface) {
			if (!$value instanceof \DateTimeInterface) {
				return false;
			}
		} elseif (is_numeric($value)) {
			$value *= 1;
		} else {
			return false;
		}

		return (!isset($range[0]) || ($value >= $range[0])) && (!isset($range[1]) || ($value <= $range[1]));
	}


	/**
	 * Checks if the value is a valid email address. It does not verify that the domain actually exists, only the syntax is verified.
	 */
	public static function isEmail(string $value): bool
	{
		$atom = "[-a-z0-9!#$%&'*+/=?^_`{|}~]"; // RFC 5322 unquoted characters in local-part
		$alpha = "a-z\x80-\xFF"; // superset of IDN
		return (bool) preg_match(<<<XX
		(^
			("([ !#-[\\]-~]*|\\\\[ -~])+"|$atom+(\\.$atom+)*)  # quoted or unquoted
			@
			([0-9$alpha]([-0-9$alpha]{0,61}[0-9$alpha])?\\.)+  # domain - RFC 1034
			[$alpha]([-0-9$alpha]{0,17}[$alpha])?              # top domain
		$)Dix
XX
, $value);
	}


	/**
	 * Checks if the value is a valid URL address.
	 */
	public static function isUrl(string $value): bool
	{
		$alpha = "a-z\x80-\xFF";
		return (bool) preg_match(<<<XX
		(^
			https?://(
				(([-_0-9$alpha]+\\.)*                       # subdomain
					[0-9$alpha]([-0-9$alpha]{0,61}[0-9$alpha])?\\.)?  # domain
					[$alpha]([-0-9$alpha]{0,17}[$alpha])?   # top domain
				|\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}  # IPv4
				|\\[[0-9a-f:]{3,39}\\]                      # IPv6
			)(:\\d{1,5})?                                   # port
			(/\\S*)?                                        # path
			(\\?\\S*)?                                      # query
			(\\#\\S*)?                                      # fragment
		$)Dix
XX
, $value);
	}


	/**
	 * Checks if the value is a valid URI address, that is, actually a string beginning with a syntactically valid schema.
	 */
	public static function isUri(string $value): bool
	{
		return (bool) preg_match('#^[a-z\d+\.-]+:\S+$#Di', $value);
	}


	/**
	 * Checks whether the input is a class, interface or trait.
	 */
	public static function isType(string $type): bool
	{
		return class_exists($type) || interface_exists($type) || trait_exists($type);
	}


	/**
	 * Checks whether the input is a valid PHP identifier.
	 */
	public static function isPhpIdentifier(string $value): bool
	{
		return preg_match('#^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$#D', $value) === 1;
	}
}
