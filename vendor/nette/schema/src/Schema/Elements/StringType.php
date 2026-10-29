<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Schema\Elements;

use Nette\Schema\Schema;


/**
 * A string, plain or in a specified format.
 */
final class StringType extends Type
{
	/** @deprecated  a string has no items */
	public function items(string|Schema $valueType = 'mixed', string|Schema|null $keyType = null): static
	{
		trigger_error(__METHOD__ . '() is deprecated, a string has no items.', E_USER_DEPRECATED);
		return parent::items($valueType, $keyType);
	}


	/** @deprecated  a string has no default merging */
	public function mergeDefaults(bool $state = true): static
	{
		trigger_error(__METHOD__ . '() is deprecated, a string has no default merging.', E_USER_DEPRECATED);
		return parent::mergeDefaults($state);
	}
}
