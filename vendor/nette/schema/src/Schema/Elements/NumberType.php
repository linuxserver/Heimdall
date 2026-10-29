<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Schema\Elements;

use Nette\Schema\Schema;


/**
 * An int or a float.
 */
final class NumberType extends Type
{
	/** @deprecated  a number has no pattern */
	public function pattern(?string $pattern): static
	{
		trigger_error(__METHOD__ . '() is deprecated, a number has no pattern.', E_USER_DEPRECATED);
		return parent::pattern($pattern);
	}


	/** @deprecated  a number has no items */
	public function items(string|Schema $valueType = 'mixed', string|Schema|null $keyType = null): static
	{
		trigger_error(__METHOD__ . '() is deprecated, a number has no items.', E_USER_DEPRECATED);
		return parent::items($valueType, $keyType);
	}


	/** @deprecated  a number has no default merging */
	public function mergeDefaults(bool $state = true): static
	{
		trigger_error(__METHOD__ . '() is deprecated, a number has no default merging.', E_USER_DEPRECATED);
		return parent::mergeDefaults($state);
	}
}
