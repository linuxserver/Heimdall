<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Schema;

use function count;


/**
 * Translates the type-expression language of Expect::type() ('int|string', '?Foo', 'int[]', 'string:1..5',
 * 'pattern:\d+') into a plain description.
 * @internal
 */
final class TypeExpression
{
	/**
	 * The names Validators::is() knows and what kind of value they are; 'scalar' is a union handled apart,
	 * any other name is a class.
	 */
	private const Names = [
		'mixed' => Kind::Any,
		'bool' => Kind::Bool, 'boolean' => Kind::Bool,
		'int' => Kind::Int, 'integer' => Kind::Int,
		'float' => Kind::Float, 'number' => Kind::Number,
		'string' => Kind::String,
		'unicode' => Kind::String, 'email' => Kind::String, 'uri' => Kind::String, 'url' => Kind::String,
		'identifier' => Kind::String, 'class' => Kind::String, 'interface' => Kind::String,
		'file' => Kind::String, 'directory' => Kind::String, 'alnum' => Kind::String, 'alpha' => Kind::String,
		'digit' => Kind::String, 'lower' => Kind::String, 'upper' => Kind::String, 'space' => Kind::String,
		'xdigit' => Kind::String, 'pattern' => Kind::String,
		'array' => Kind::Array, 'list' => Kind::List, 'iterable' => Kind::Iterable,
		'object' => Kind::Object, 'callable' => Kind::Callable,
		'resource' => Kind::Other, 'none' => Kind::Other, 'numeric' => Kind::Other, 'numericint' => Kind::Other,
	];

	/** @var array<string, array<string, mixed>> */
	private static array $cache = [];


	/** @return array<string, mixed> */
	public static function parse(string $expression): array
	{
		return self::$cache[$expression] ??= self::doParse($expression);
	}


	/** @return array<string, mixed> */
	private static function doParse(string $expression): array
	{
		$variants = [];
		$nullable = $dynamic = false;
		foreach (explode('|', $expression) as $part) {
			if (str_starts_with($part, '?')) {
				$nullable = true;
				$part = substr($part, 1);
			}

			if ($part === 'null') {
				$nullable = true;
			} elseif ($part === DynamicParameter::class) {
				$dynamic = true;
			} else {
				array_push($variants, ...self::parseAlternative($part));
			}
		}

		$item = match (count($variants)) {
			0 => ['kind' => $nullable ? Kind::Null : Kind::Any],
			1 => $variants[0],
			default => ['kind' => Kind::Union, 'variants' => $variants],
		};
		return $item + ['nullable' => $nullable && count($variants) > 0, 'dynamic' => $dynamic];
	}


	/**
	 * One alternative of the expression; 'scalar' expands to several.
	 * @return list<array<string, mixed>>
	 */
	private static function parseAlternative(string $part): array
	{
		if (str_ends_with($part, '[]')) {
			$items = self::parseAlternative(substr($part, 0, -2));
			return [[
				'kind' => Kind::Iterable,
				'items' => count($items) === 1 ? $items[0] : ['kind' => Kind::Union, 'variants' => $items],
				'keys' => null,
				'min' => null,
				'max' => null,
			]];
		}

		if (str_contains($part, '&')) {
			return [['kind' => Kind::Other, 'type' => $part]]; // an intersection type cannot be checked
		}

		$parts = explode(':', $part, 2);
		$name = $parts[0];
		$arg = $parts[1] ?? null;
		[$min, $max] = $arg === null || $name === 'pattern' ? [null, null] : self::parseRange($arg);
		$number = fn(Kind $kind) => ['kind' => $kind, 'min' => $min, 'max' => $max];
		$string = fn(?string $format = null, ?string $pattern = null) => [
			'kind' => Kind::String,
			'min' => $min,
			'max' => $max,
			'pattern' => $pattern,
			'format' => $format,
		];
		$collection = fn(Kind $kind) => ['kind' => $kind, 'items' => null, 'keys' => null, 'min' => $min, 'max' => $max];

		$kind = self::Names[$name] ?? Kind::Instance;
		return match (true) {
			$name === 'scalar' => [['kind' => Kind::Bool], $number(Kind::Number), $string()],
			$name === 'pattern' => [$string(pattern: $arg)],
			$kind === Kind::String => [$string(format: $name === 'string' ? null : $name)],
			$kind === Kind::Int, $kind === Kind::Float, $kind === Kind::Number => [$number($kind)],
			$kind === Kind::Array, $kind === Kind::List, $kind === Kind::Iterable => [$collection($kind)],
			$kind === Kind::Instance => [['kind' => $kind, 'type' => $name]],
			$kind === Kind::Other => [['kind' => $kind, 'type' => $part]],
			default => [['kind' => $kind]],
		};
	}


	/**
	 * Bounds written as 'min..max', '..max', 'min..' or an exact 'value'.
	 * @return array{int|float|null, int|float|null}
	 */
	private static function parseRange(string $range): array
	{
		$bounds = explode('..', $range) + [1 => $range];
		return [
			$bounds[0] === '' ? null : self::parseBound($bounds[0]),
			$bounds[1] === '' ? null : self::parseBound($bounds[1]),
		];
	}


	/**
	 * An integer bound stays an integer, a float would lose the precision of large ones.
	 */
	private static function parseBound(string $bound): int|float
	{
		return is_numeric($bound) ? $bound + 0 : (float) $bound;
	}
}
