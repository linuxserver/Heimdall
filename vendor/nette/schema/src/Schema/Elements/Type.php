<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Schema\Elements;

use Nette;
use Nette\Schema\{Context, DynamicParameter, Helpers, Kind, MergeMode, Message, Schema, TypeExpression};
use Nette\Utils\Validators;
use function array_key_exists, is_array, is_int, is_string;
use const INF;


class Type implements Schema
{
	use Base;

	private ?Schema $itemsValue = null;
	private ?Schema $itemsKey = null;

	/** @var array{int|float|null, int|float|null} */
	private array $range = [null, null];
	private ?string $pattern = null;
	private bool $merge = true;
	private ?MergeMode $mergeMode = null;


	public function __construct(
		private string $type,
	) {
		$defaults = ['list' => [], 'array' => []];
		$this->default = strpos($type, '[]') ? [] : $defaults[$type] ?? null;
	}


	/**
	 * Allows the value to be null in addition to the declared type.
	 */
	public function nullable(): static
	{
		$this->type = 'null|' . $this->type;
		return $this;
	}


	/**
	 * Controls whether the default value is merged with the input array (enabled by default).
	 */
	public function mergeDefaults(bool $state = true): static
	{
		$this->unionDeprecated('mergeDefaults');
		$this->merge = $state;
		return $this;
	}


	/** @internal */
	protected function mergeMode(MergeMode $mode): static
	{
		$this->mergeMode = $mode;
		return $this;
	}


	/**
	 * Allows the value to be a DynamicParameter, which is recorded for deferred validation.
	 */
	public function dynamic(): static
	{
		$this->type = DynamicParameter::class . '|' . $this->type;
		return $this;
	}


	public function min(int|float|null $min): static
	{
		$this->unionDeprecated('min');
		$this->range[0] = $min;
		return $this;
	}


	public function max(int|float|null $max): static
	{
		$this->unionDeprecated('max');
		$this->range[1] = $max;
		return $this;
	}


	/**
	 * @internal  use arrayOf() or listOf()
	 */
	public function items(string|Schema $valueType = 'mixed', string|Schema|null $keyType = null): static
	{
		$this->unionDeprecated('items');
		$this->itemsValue = $valueType instanceof Schema
			? $valueType
			: Nette\Schema\Expect::type($valueType);
		$this->itemsKey = $keyType instanceof Schema || $keyType === null
			? $keyType
			: Nette\Schema\Expect::type($keyType);
		return $this;
	}


	/**
	 * Sets a regex pattern the string value must match entirely (anchored to start and end).
	 */
	public function pattern(?string $pattern): static
	{
		$this->unionDeprecated('pattern');
		$this->pattern = $pattern;
		return $this;
	}


	/**
	 * A union of kinds ('int|string') becomes anyOf() in the next major and takes no options of its own;
	 * they belong to the variants: anyOf(Expect::int()->min(1), Expect::string()->min(1)).
	 */
	private function unionDeprecated(string $option): void
	{
		if (static::class === self::class && $this->getParsed()['kind'] === Kind::Union) {
			trigger_error(
				"$option() on the union '$this->type' is deprecated, give it to the variants of anyOf() instead.",
				E_USER_DEPRECATED,
			);
		}
	}


	/********************* inspection ****************d*g**/


	/**
	 * Reports what the type accepts as a plain array (see TypeExpression::parse()); min(), max(), pattern()
	 * and items() narrow every variant they apply to, child schemas are reported as they are.
	 * @return array<string, mixed>
	 * @internal
	 */
	public function describe(): array
	{
		return $this->narrow($this->getParsed()) + $this->describeBase();
	}


	/**
	 * The parsed expression; 'kind', 'nullable', 'dynamic' and the keys of the kind.
	 * @return array<string, mixed>
	 */
	protected function getParsed(): array
	{
		return TypeExpression::parse($this->type);
	}


	/**
	 * Puts min(), max(), pattern() and items() into every variant of the parsed expression that has the key.
	 * @param  array<string, mixed>  $item
	 * @return array<string, mixed>
	 */
	private function narrow(array $item): array
	{
		if ($item['kind'] === Kind::Union) {
			$item['variants'] = array_map($this->narrow(...), $item['variants']);
		}
		if (array_key_exists('min', $item)) { // both bounds are checked, so the tighter one holds
			$item['min'] = $this->range[0] === null ? $item['min'] : max($this->range[0], $item['min'] ?? -INF);
			$item['max'] = $this->range[1] === null ? $item['max'] : min($this->range[1], $item['max'] ?? INF);
		}
		if (array_key_exists('pattern', $item)) {
			$item['pattern'] = $this->pattern ?? $item['pattern'];
		}
		if (array_key_exists('items', $item)) {
			$item['items'] = $this->itemsValue ?? $item['items'];
			$item['keys'] = $this->itemsKey ?? $item['keys'];
		}
		return $item;
	}


	/********************* processing ****************d*g**/


	public function normalize(mixed $value, Context $context): mixed
	{
		if ($prevent = (is_array($value) && isset($value[Helpers::PreventMerging]))) {
			unset($value[Helpers::PreventMerging]);
		}

		$value = $this->doNormalize($value, $context);
		if (is_array($value)) {
			$value = $this->normalizeItems($value, $context);
		}

		if ($prevent && is_array($value)) {
			$value[Helpers::PreventMerging] = true;
		}

		return $value;
	}


	/**
	 * @param  array<mixed>  $value
	 * @return array<mixed>
	 * @internal
	 */
	protected function normalizeItems(array $value, Context $context): array
	{
		if (!$this->itemsValue) {
			return $value;
		}

		$res = [];
		foreach ($value as $key => $val) {
			$context->path[] = $key;
			$context->isKey = true;
			$key = $this->itemsKey
				? $this->itemsKey->normalize($key, $context)
				: $key;
			if (!is_int($key) && !is_string($key)) {
				$context->addError(
					'The %label% %path% expects to be int|string, %value% given.',
					Message::TypeMismatch,
					['value' => $key, 'expected' => 'int|string'],
				);
				$context->isKey = false;
				array_pop($context->path);
				continue;
			}

			$context->isKey = false;
			$res[$key] = $this->itemsValue->normalize($val, $context);
			array_pop($context->path);
		}

		return $res;
	}


	public function merge(mixed $value, mixed $base): mixed
	{
		if (is_array($value) && isset($value[Helpers::PreventMerging])) {
			unset($value[Helpers::PreventMerging]);
			return $value;
		}

		if ($this->mergeWith) {
			return ($this->mergeWith)($value, $base);
		}

		return $this->mergeValues($value, $base);
	}


	/**
	 * Without a mode, an array without described items merges deeply.
	 * @internal
	 */
	protected function mergeValues(mixed $value, mixed $base): mixed
	{
		if ($this->mergeMode === MergeMode::Replace) {
			return $value;
		}

		if (is_array($value) && is_array($base) && ($this->itemsValue || $this->mergeMode)) {
			$index = $this->mergeMode === MergeMode::OverwriteKeys ? null : 0;
			foreach ($value as $key => $val) {
				if ($key === $index) {
					$base[] = $val;
					$index++;
				} else {
					$base[$key] = array_key_exists($key, $base) && $this->itemsValue
						? $this->itemsValue->merge($val, $base[$key])
						: $val;
				}
			}

			return $base;
		}

		return Helpers::merge($value, $base);
	}


	public function complete(mixed $value, Context $context): mixed
	{
		$merge = $this->merge;
		if (is_array($value) && isset($value[Helpers::PreventMerging])) {
			unset($value[Helpers::PreventMerging]);
			$merge = false;
		}

		if ($value === null && is_array($this->default) && !Validators::is(null, $this->type) && $context->keepNullAt !== $context->path) {
			$value = []; // is unable to distinguish null from array in NEON
		}

		$this->doDeprecation($context);

		$isOk = $context->createChecker();
		Helpers::validateType($value, $this->type, $context);
		$isOk() && Helpers::validateRange($value, $this->range, $context, $this->type);
		$isOk() && $value !== null && $this->pattern !== null && Helpers::validatePattern($value, $this->pattern, $context);
		$isOk() && is_array($value) && $this->validateItems($value, $context);
		$isOk() && $merge && $value !== null && $value = Helpers::merge($value, $this->default);
		$isOk() && $value = $this->doTransform($value, $context);
		if (!$isOk()) {
			return null;
		}

		if ($value instanceof DynamicParameter && $this->type !== DynamicParameter::class) {
			$expected = $this->type . ($this->range === [null, null] ? '' : ':' . implode('..', $this->range));
			$context->dynamics[] = [$value, str_replace(DynamicParameter::class . '|', '', $expected), $context->path];
		}
		return $value;
	}


	/**
	 * @param  array<mixed>  $value
	 * @internal
	 */
	protected function validateItems(array &$value, Context $context): void
	{
		if (!($itemsValue = $this->itemsValue)) {
			return;
		}

		$res = [];
		foreach ($value as $key => $val) {
			$context->path[] = $key;
			$context->isKey = true;
			$isKeyOk = $context->createChecker();
			$key = $this->itemsKey ? $this->itemsKey->complete($key, $context) : $key;
			if ($isKeyOk() && !is_int($key) && !is_string($key)) {
				$context->addError(
					'The %label% %path% expects to be int|string, %value% given.',
					Message::TypeMismatch,
					['value' => $key, 'expected' => 'int|string'],
				);
			}

			$context->isKey = false;
			$keyOk = $isKeyOk();
			$val = $itemsValue->complete($val, $context);
			if ($keyOk) {
				$res[$key] = $val;
			}

			array_pop($context->path);
		}
		$value = $res;
	}
}
