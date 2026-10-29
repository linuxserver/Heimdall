<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Schema\Elements;

use Nette;
use Nette\Schema\{Context, Helpers};
use function count, is_string;


/**
 * @internal
 */
trait Base
{
	private bool $required = false;
	private mixed $default = null;

	/** @var ?\Closure(mixed): mixed */
	private ?\Closure $before = null;

	/** @var list<\Closure(mixed, Context): mixed> */
	private array $transforms = [];
	private ?string $deprecated = null;
	private ?string $description = null;

	/** @var ?\Closure(mixed, mixed): mixed */
	private ?\Closure $mergeWith = null;


	public function default(mixed $value): static
	{
		$this->default = $value;
		return $this;
	}


	public function required(bool $state = true): static
	{
		$this->required = $state;
		return $this;
	}


	/**
	 * Sets a pre-normalization callback applied to the raw input value before any validation.
	 * @param  callable(mixed): mixed  $handler
	 */
	public function before(callable $handler): static
	{
		if ($this->before) {
			trigger_error(
				'Calling before() repeatedly is deprecated, it replaces the previous handler while in 2.0 the handlers will be chained; merge them into one.',
				E_USER_DEPRECATED,
			);
		}

		$this->before = $handler(...);
		return $this;
	}


	/**
	 * Sets a custom strategy combining two layers. Must be a pure combiner; canonicalize layer shape in before() instead.
	 * Either side may be null, a layer can legally be null.
	 * @param  callable(mixed, mixed): mixed  $fn
	 */
	public function mergeWith(callable $fn): static
	{
		$this->mergeWith = $fn(...);
		return $this;
	}


	/**
	 * Casts the validated value to a built-in type or instantiates the given class; a null stays null for a class or ?type.
	 */
	public function castTo(string $type): static
	{
		return $this->transform(Helpers::getCastStrategy($type));
	}


	/**
	 * Adds a post-validation transformation callback. The handler may also report errors via Context.
	 * @param  callable(mixed, Context): mixed  $handler
	 */
	public function transform(callable $handler): static
	{
		$this->transforms[] = $handler(...);
		return $this;
	}


	/**
	 * Adds a custom validation assertion; optionally describe it for error messages.
	 * @param  callable(mixed): bool  $handler
	 */
	public function assert(callable $handler, ?string $description = null): static
	{
		$expected = $description ?? (is_string($handler) ? "$handler()" : '#' . count($this->transforms));
		return $this->transform(function ($value, Context $context) use ($handler, $description, $expected) {
			if ($handler($value)) {
				return $value;
			}
			$context->addError(
				'Failed assertion ' . ($description ? "'%assertion%'" : '%assertion%') . ' for %label% %path% with value %value%.',
				Nette\Schema\Message::FailedAssertion,
				['value' => $value, 'assertion' => $expected],
			);
			return null;
		});
	}


	/**
	 * Marks the item as deprecated; emits a warning with the given message when the item is used.
	 */
	public function deprecated(string $message = 'The item %path% is deprecated.'): static
	{
		$this->deprecated = $message;
		return $this;
	}


	/**
	 * Sets a human-readable description of the item; it does not affect validation.
	 */
	public function description(string $description): static
	{
		$this->description = $description;
		return $this;
	}


	/**
	 * The metadata every element reports from describe(); the element adds its own keys.
	 * @return array{required: bool, description: ?string}
	 */
	protected function describeBase(): array
	{
		return ['required' => $this->required, 'description' => $this->description];
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

		return $this->default;
	}


	public function doNormalize(mixed $value, Context $context): mixed
	{
		if ($this->before) {
			$value = ($this->before)($value);
		}

		return $value;
	}


	private function doDeprecation(Context $context): void
	{
		if ($this->deprecated !== null) {
			$context->addWarning(
				$this->deprecated,
				Nette\Schema\Message::Deprecated,
			);
		}
	}


	private function doTransform(mixed $value, Context $context): mixed
	{
		$isOk = $context->createChecker();
		foreach ($this->transforms as $handler) {
			$value = $handler($value, $context);
			if (!$isOk()) {
				return null;
			}
		}
		return $value;
	}


	/** @deprecated use Nette\Schema\Helpers::validateType() */
	private function doValidate(mixed $value, string $expected, Context $context): bool
	{
		$isOk = $context->createChecker();
		Helpers::validateType($value, $expected, $context);
		return $isOk();
	}


	/**
	 * @deprecated use Nette\Schema\Helpers::validateRange()
	 * @param  array{int|float|null, int|float|null}  $range
	 */
	private static function doValidateRange(mixed $value, array $range, Context $context, string $types = ''): bool
	{
		$isOk = $context->createChecker();
		Helpers::validateRange($value, $range, $context, $types);
		return $isOk();
	}


	/** @deprecated use doTransform() */
	private function doFinalize(mixed $value, Context $context): mixed
	{
		return $this->doTransform($value, $context);
	}
}
