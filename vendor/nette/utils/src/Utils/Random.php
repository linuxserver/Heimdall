<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace Nette\Utils;

use Nette;


/**
 * Secure random string generator.
 */
final class Random
{
	use Nette\StaticClass;

	/**
	 * Generates a random string of given length from characters specified in second argument.
	 * Supports intervals, such as `0-9` or `A-Z`.
	 */
	public static function generate(int $length = 10, string $charlist = '0-9a-z'): string
	{
		$charlist = count_chars(preg_replace_callback('#.-.#', function (array $m): string {
			return implode('', range($m[0][0], $m[0][2]));
		}, $charlist), 3);
		$chLen = strlen($charlist);

		if ($length < 1) {
			throw new Nette\InvalidArgumentException('Length must be greater than zero.');
		} elseif ($chLen < 2) {
			throw new Nette\InvalidArgumentException('Character list must contain at least two chars.');
		}

		$res = '';
		for ($i = 0; $i < $length; $i++) {
			$res .= $charlist[random_int(0, $chLen - 1)];
		}

		return $res;
	}
}
