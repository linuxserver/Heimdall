<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Schema\Elements;

use Nette;
use Nette\Schema\{Context, Helpers, Kind, Schema};
use function is_array;


/**
 * Schema that accepts any of a fixed set of values or sub-schemas (union type / enumeration).
 */
final class AnyOf implements Schema
{
	use Base;

	/** @var mixed[] */
	private array $set;
	private bool $dynamic = false;


	public function __construct(mixed ...$set)
	{
		if (!$set) {
			throw new Nette\InvalidStateException('The enumeration must not be empty.');
		}

		$this->set = $set;
	}


	/**
	 * Sets the first variant as the default value (instead of null).
	 */
	public function firstIsDefault(): self
	{
		$this->default = $this->set[0];
		return $this;
	}


	/**
	 * Allows null as an accepted value in addition to the defined variants.
	 */
	public function nullable(): self
	{
		$this->set[] = null;
		return $this;
	}


	/**
	 * Allows the value to be a DynamicParameter as an accepted variant.
	 */
	public function dynamic(): self
	{
		$this->dynamic = true;
		return $this;
	}


	/********************* inspection ****************d*g**/


	/**
	 * Scalar variants are reported as 'values', schema variants as 'variants'; scalars alone make an Enum,
	 * anything with a schema variant a Union.
	 * @return array<string, mixed>
	 * @internal
	 */
	public function describe(): array
	{
		$values = $variants = [];
		$nullable = false;
		foreach ($this->set as $item) {
			if ($item === null) {
				$nullable = true;
			} elseif (!$item instanceof Schema) {
				$values[] = $item;
			} else {
				$variants[] = $item;
			}
		}

		return [
			'kind' => match (true) {
				(bool) $variants => Kind::Union,
				(bool) $values => Kind::Enum,
				default => $nullable ? Kind::Null : Kind::Any,
			},
			'values' => $values,
			'variants' => $variants,
			'nullable' => $nullable,
			'dynamic' => $this->dynamic,
		] + $this->describeBase();
	}


	/********************* processing ****************d*g**/


	public function normalize(mixed $value, Context $context): mixed
	{
		return $this->doNormalize($value, $context);
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

		return Helpers::merge($value, $base);
	}


	public function complete(mixed $value, Context $context): mixed
	{
		$this->doDeprecation($context);
		$isOk = $context->createChecker();
		$value = $this->findAlternative($value, $context);
		$isOk() && $value = $this->doTransform($value, $context);
		return $isOk() ? $value : null;
	}


	private function findAlternative(mixed $value, Context $context): mixed
	{
		$keepNullAt = $value === null && in_array(null, $this->set, true)
			? $context->path
			: $context->keepNullAt;

		$expecteds = $innerErrors = [];
		foreach ($this->set as $item) {
			if ($item instanceof Schema) {
				$dolly = new Context;
				$dolly->skipDefaults = $context->skipDefaults;
				$dolly->isKey = $context->isKey;
				$dolly->path = $context->path;
				$dolly->keepNullAt = $keepNullAt;
				$res = $item->complete($item->normalize($value, $dolly), $dolly);
				if (!$dolly->errors) {
					$context->warnings = [...$context->warnings, ...$dolly->warnings];
					$context->dynamics = [...$context->dynamics, ...$dolly->dynamics];
					return $res;
				}

				foreach ($dolly->errors as $error) {
					if ($error->path !== $context->path || empty($error->variables['expected'])) {
						$innerErrors[] = $error;
					} else {
						$expecteds[] = $error->variables['expected'];
					}
				}
			} else {
				if ($item === $value) {
					return $value;
				}

				$expecteds[] = Nette\Schema\Helpers::formatValue($item);
			}
		}

		if ($this->dynamic && $value instanceof Nette\Schema\DynamicParameter) {
			return $value;
		}

		if ($innerErrors) {
			$context->errors = [...$context->errors, ...$innerErrors];
		} else {
			$context->addError(
				'The %label% %path% expects to be %expected%, %value% given.',
				Nette\Schema\Message::TypeMismatch,
				[
					'value' => $value,
					'expected' => implode('|', array_unique($expecteds)),
				],
			);
		}

		return null;
	}


	public function completeDefault(Context $context): mixed
	{
		if ($this->required) {
			$context->addError(
				'The mandatory item %path% is missing.',
				Nette\Schema\Message::MissingItem,
			);
			return null;
		}

		if ($this->default instanceof Schema) {
			return $this->default->completeDefault($context);
		}

		return $this->default;
	}
}
