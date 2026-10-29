<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Schema\Elements;

use Nette\Schema\{Context, MergeMode, Schema};
use function is_array;


/**
 * An array, a list or an iterable, optionally with a schema for its items and keys.
 */
final class ArrayType extends Type
{
	public function __construct(
		string $type,
		/** a single value is accepted and wrapped into a one-item list */
		private readonly bool $wrap = false,
	) {
		parent::__construct($type);
	}


	public function doNormalize(mixed $value, Context $context): mixed
	{
		$value = parent::doNormalize($value, $context);
		return $this->wrap && !is_array($value) && $value !== null ? [$value] : $value;
	}


	/**
	 * Sets the schema of items and optionally of keys.
	 */
	public function items(string|Schema $valueType = 'mixed', string|Schema|null $keyType = null): static
	{
		return parent::items($valueType, $keyType);
	}


	/**
	 * Sets how the array is combined when merging multiple configuration layers.
	 */
	public function mergeMode(MergeMode $mode): static
	{
		return parent::mergeMode($mode);
	}


	/** @deprecated  an array has no pattern */
	public function pattern(?string $pattern): static
	{
		trigger_error(__METHOD__ . '() is deprecated, an array has no pattern.', E_USER_DEPRECATED);
		return parent::pattern($pattern);
	}


	public function describe(): array
	{
		return parent::describe() + ['wrap' => $this->wrap];
	}
}
