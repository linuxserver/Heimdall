<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Schema;

use Nette;
use function count, is_bool, is_float, is_int, is_string;


/**
 * Exports a schema as JSON Schema.
 */
final class JsonSchema
{
	private const Formats = ['email' => 'email', 'url' => 'uri', 'uri' => 'uri'];


	/**
	 * @return array<string, mixed>|\stdClass
	 * @throws Nette\NotSupportedException  when the schema contains a class type or another PHP-only type
	 */
	public static function export(Schema $schema): array|\stdClass
	{
		return self::build($schema);
	}


	/**
	 * @param  Schema|array<string, mixed>  $schema  a schema, or a description as describe() returns it
	 * @return array<string, mixed>|\stdClass
	 */
	private static function build(Schema|array $schema): array|\stdClass
	{
		$item = self::describe($schema);
		$kind = $item['kind'];
		if (!$kind instanceof Kind) {
			throw new Nette\InvalidStateException('describe() must report a Kind.');
		}

		$res = match ($kind) {
			Kind::Any => [],
			Kind::Null => ['type' => 'null'],
			Kind::Bool => ['type' => 'boolean'],
			Kind::Int => ['type' => 'integer'] + self::range($item, 'minimum', 'maximum'),
			Kind::Float, Kind::Number => ['type' => 'number'] + self::range($item, 'minimum', 'maximum'),
			Kind::String => self::buildString($item),
			// an iterable of items is narrowed to a JSON array, which PHP accepts too
			Kind::List, Kind::Iterable => self::buildList($item),
			Kind::Array => self::buildArray($item),
			Kind::Structure => self::buildStructure($item),
			Kind::Tuple => self::buildTuple($item),
			Kind::Enum => self::buildEnum($item['values']),
			Kind::Union => self::buildUnion($item),
			Kind::Instance, Kind::Other, Kind::Object, Kind::Callable => throw new Nette\NotSupportedException(
				"Type '" . ($item['type'] ?? strtolower($kind->name)) . "' cannot be expressed in JSON Schema.",
			),
		};

		if ($item['nullable'] ?? false) {
			if (isset($res['enum'])) {
				$res['enum'][] = null;
			}
			if (isset($res['type']) && $res['type'] !== 'null') {
				$res['type'] = [$res['type'], 'null'];
			} elseif (isset($res['anyOf'])) {
				$res['anyOf'][] = ['type' => 'null'];
			}
		}

		if (($item['description'] ?? null) !== null) {
			$res['description'] = $item['description'];
		}

		return $res ?: new \stdClass;
	}


	/**
	 * @param  Schema|array<string, mixed>  $schema
	 * @return array<string, mixed>
	 */
	private static function describe(Schema|array $schema): array
	{
		if (!$schema instanceof Schema) {
			return $schema;
		} elseif (
			$schema instanceof Elements\Type
			|| $schema instanceof Elements\Structure
			|| $schema instanceof Elements\AnyOf
		) {
			return $schema->describe();
		}
		throw new Nette\NotSupportedException('Element ' . $schema::class . ' cannot be expressed in JSON Schema.');
	}


	/**
	 * @param  Schema|array<string, mixed>|null  $schema
	 * @return array<string, mixed>|\stdClass
	 */
	private static function buildOrAny(Schema|array|null $schema): array|\stdClass
	{
		return $schema === null ? new \stdClass : self::build($schema);
	}


	/**
	 * @param  array<string, mixed>  $item
	 * @return array<string, mixed>
	 */
	private static function buildString(array $item): array
	{
		$res = ['type' => 'string'] + self::countRange($item, 'minLength', 'maxLength');
		if ($item['pattern'] !== null) {
			$res['pattern'] = '^(?:' . $item['pattern'] . ')$';
		}
		// only the formats JSON Schema knows; the others are checked by PHP alone, like assert()
		if ($format = self::Formats[$item['format'] ?? ''] ?? null) {
			$res['format'] = $format;
		}
		return $res;
	}


	/**
	 * A list that wraps a single value accepts the item alone as well.
	 * @param  array<string, mixed>  $item
	 * @return array<string, mixed>
	 */
	private static function buildList(array $item): array
	{
		$res = ['type' => 'array', 'items' => self::buildOrAny($item['items'])] + self::countRange($item, 'minItems', 'maxItems');
		$single = ($item['wrap'] ?? false) && ($item['min'] ?? 0) <= 1 && ($item['max'] ?? 1) >= 1;
		return $single
			? ['anyOf' => [self::buildOrAny($item['items']), $res]]
			: $res;
	}


	/**
	 * A PHP array is a JSON array when its keys are integers and an object otherwise.
	 * @param  array<string, mixed>  $item
	 * @return array<string, mixed>
	 */
	private static function buildArray(array $item): array
	{
		$keys = $item['keys'] === null ? null : self::describe($item['keys']);
		if ($keys && $keys['kind'] === Kind::Int) {
			return ['type' => 'array', 'items' => self::buildOrAny($item['items'])]
				+ self::countRange($item, 'minItems', 'maxItems');
		} elseif ($item['items'] === null && $keys === null) {
			throw new Nette\NotSupportedException('An array without item type cannot be expressed in JSON Schema; use listOf(), arrayOf() or structure().');
		}

		$res = ['type' => 'object', 'additionalProperties' => self::buildOrAny($item['items'])]
			+ self::countRange($item, 'minProperties', 'maxProperties');
		// a JSON property name is a string, so only a key schema of strings says something about it
		if (
			$keys
			&& ($keys['kind'] === Kind::String
				|| ($keys['kind'] === Kind::Enum && array_filter($keys['values'], is_string(...)) === $keys['values']))
		) {
			$names = self::build($keys);
			if ($names !== ['type' => 'string']) {
				$res['propertyNames'] = $names;
			}
		}
		return $res;
	}


	/**
	 * @param  array<string, mixed>  $item
	 * @return array<string, mixed>
	 */
	private static function buildStructure(array $item): array
	{
		$properties = $required = [];
		foreach ($item['shape'] as $key => $property) {
			$properties[$key] = self::build($property);
			if (self::isRequired($property)) {
				$required[] = (string) $key;
			}
		}

		return [
			'type' => 'object',
			'properties' => $properties && !array_is_list($properties) ? $properties : (object) $properties, // integer keys from 0 would encode as a JSON array
			'required' => $required,
			'additionalProperties' => $item['otherItems'] ? self::build($item['otherItems']) : false,
		] + self::countRange($item, 'minProperties', 'maxProperties');
	}


	/**
	 * A required position makes all positions before it present too.
	 * @param  array<string, mixed>  $item
	 * @return array<string, mixed>
	 */
	private static function buildTuple(array $item): array
	{
		$res = ['type' => 'array'];
		if ($item['shape']) { // prefixItems must not be empty
			$res['prefixItems'] = array_map(self::build(...), $item['shape']);
		}
		$res['items'] = $item['otherItems'] ? self::build($item['otherItems']) : false;

		$range = self::countRange($item, 'minItems', 'maxItems');
		$positions = array_keys(array_filter($item['shape'], self::isRequired(...)));
		if ($positions) {
			$range['minItems'] = max($range['minItems'] ?? 0, max($positions) + 1);
		}
		return $res + $range;
	}


	/**
	 * Whether a missing item is an error. A structure is created from its defaults instead,
	 * so it fails only when something inside it is required or its size has a minimum.
	 * @param  Schema|array<string, mixed>  $schema
	 */
	private static function isRequired(Schema|array $schema): bool
	{
		$item = self::describe($schema);
		return $item['required'] && match ($item['kind']) {
			Kind::Structure => ($item['min'] ?? 0) > 0 || array_filter($item['shape'], self::isRequired(...)),
			default => true,
		};
	}


	/**
	 * The type is stated only when every value shares it.
	 * @param  list<mixed>  $values
	 * @return array<string, mixed>
	 */
	private static function buildEnum(array $values): array
	{
		$types = array_unique(array_map(fn($value) => match (true) {
			is_string($value) => 'string',
			is_int($value) => 'integer',
			is_float($value) => 'number',
			is_bool($value) => 'boolean',
			default => null,
		}, $values));
		return (count($types) === 1 && reset($types) !== null ? ['type' => reset($types)] : []) + ['enum' => $values];
	}


	/**
	 * Scalar values of an anyOf() come first as one enum, then the schema variants.
	 * @param  array<string, mixed>  $item
	 * @return array<string, mixed>
	 */
	private static function buildUnion(array $item): array
	{
		$variants = array_map(self::build(...), $item['variants']);
		if ($item['values'] ?? []) {
			array_unshift($variants, self::buildEnum($item['values']));
		}
		return ['anyOf' => $variants];
	}


	/**
	 * A count of characters, items or properties is a non-negative integer.
	 * @param  array<string, mixed>  $item
	 * @return array<string, int>
	 */
	private static function countRange(array $item, string $minKey, string $maxKey): array
	{
		return array_filter([
			$minKey => $item['min'] === null ? null : max(0, (int) ceil($item['min'])),
			$maxKey => $item['max'] === null ? null : max(0, (int) floor($item['max'])),
		], fn($v) => $v !== null);
	}


	/**
	 * @param  array<string, mixed>  $item
	 * @return array<string, int|float>
	 */
	private static function range(array $item, string $minKey, string $maxKey): array
	{
		$number = fn(int|float|null $value) => $value === null ? null : ($value == (int) $value ? (int) $value : $value); // intentionally ==, a whole float is exported as int
		return array_filter([$minKey => $number($item['min']), $maxKey => $number($item['max'])], fn($v) => $v !== null);
	}
}
