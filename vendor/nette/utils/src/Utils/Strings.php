<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace Nette\Utils;

use Nette;
use function is_array, is_object, strlen;


/**
 * String tools library.
 */
class Strings
{
	use Nette\StaticClass;

	public const TRIM_CHARACTERS = " \t\n\r\0\x0B\u{A0}";


	/**
	 * Checks if the string is valid in UTF-8 encoding.
	 */
	public static function checkEncoding(string $s): bool
	{
		return $s === self::fixEncoding($s);
	}


	/**
	 * Removes all invalid UTF-8 characters from a string.
	 */
	public static function fixEncoding(string $s): string
	{
		// removes xD800-xDFFF, x110000 and higher
		return htmlspecialchars_decode(htmlspecialchars($s, ENT_NOQUOTES | ENT_IGNORE, 'UTF-8'), ENT_NOQUOTES);
	}


	/**
	 * Returns a specific character in UTF-8 from code point (number in range 0x0000..D7FF or 0xE000..10FFFF).
	 * @throws Nette\InvalidArgumentException if code point is not in valid range
	 */
	public static function chr(int $code): string
	{
		if ($code < 0 || ($code >= 0xD800 && $code <= 0xDFFF) || $code > 0x10FFFF) {
			throw new Nette\InvalidArgumentException('Code point must be in range 0x0 to 0xD7FF or 0xE000 to 0x10FFFF.');
		} elseif (!extension_loaded('iconv')) {
			throw new Nette\NotSupportedException(__METHOD__ . '() requires ICONV extension that is not loaded.');
		}

		return iconv('UTF-32BE', 'UTF-8//IGNORE', pack('N', $code));
	}


	/**
	 * Starts the $haystack string with the prefix $needle?
	 */
	public static function startsWith(string $haystack, string $needle): bool
	{
		return strncmp($haystack, $needle, strlen($needle)) === 0;
	}


	/**
	 * Ends the $haystack string with the suffix $needle?
	 */
	public static function endsWith(string $haystack, string $needle): bool
	{
		return $needle === '' || substr($haystack, -strlen($needle)) === $needle;
	}


	/**
	 * Does $haystack contain $needle?
	 */
	public static function contains(string $haystack, string $needle): bool
	{
		return strpos($haystack, $needle) !== false;
	}


	/**
	 * Returns a part of UTF-8 string specified by starting position and length. If start is negative,
	 * the returned string will start at the start'th character from the end of string.
	 */
	public static function substring(string $s, int $start, ?int $length = null): string
	{
		if (function_exists('mb_substr')) {
			return mb_substr($s, $start, $length, 'UTF-8'); // MB is much faster
		} elseif (!extension_loaded('iconv')) {
			throw new Nette\NotSupportedException(__METHOD__ . '() requires extension ICONV or MBSTRING, neither is loaded.');
		} elseif ($length === null) {
			$length = self::length($s);
		} elseif ($start < 0 && $length < 0) {
			$start += self::length($s); // unifies iconv_substr behavior with mb_substr
		}

		return iconv_substr($s, $start, $length, 'UTF-8');
	}


	/**
	 * Removes control characters, normalizes line breaks to `\n`, removes leading and trailing blank lines,
	 * trims end spaces on lines, normalizes UTF-8 to the normal form of NFC.
	 */
	public static function normalize(string $s): string
	{
		// convert to compressed normal form (NFC)
		if (class_exists('Normalizer', false) && ($n = \Normalizer::normalize($s, \Normalizer::FORM_C)) !== false) {
			$s = $n;
		}

		$s = self::normalizeNewLines($s);

		// remove control characters; leave \t + \n
		$s = self::pcre('preg_replace', ['#[\x00-\x08\x0B-\x1F\x7F-\x9F]+#u', '', $s]);

		// right trim
		$s = self::pcre('preg_replace', ['#[\t ]+$#m', '', $s]);

		// leading and trailing blank lines
		$s = trim($s, "\n");

		return $s;
	}


	/**
	 * Standardize line endings to unix-like.
	 */
	public static function normalizeNewLines(string $s): string
	{
		return str_replace(["\r\n", "\r"], "\n", $s);
	}


	/**
	 * Converts UTF-8 string to ASCII, ie removes diacritics etc.
	 */
	public static function toAscii(string $s): string
	{
		$iconv = defined('ICONV_IMPL') ? trim(ICONV_IMPL, '"\'') : null;
		static $transliterator = null;
		if ($transliterator === null) {
			if (class_exists('Transliterator', false)) {
				$transliterator = \Transliterator::create('Any-Latin; Latin-ASCII');
			} else {
				trigger_error(__METHOD__ . "(): it is recommended to enable PHP extensions 'intl'.", E_USER_NOTICE);
				$transliterator = false;
			}
		}

		// remove control characters and check UTF-8 validity
		$s = self::pcre('preg_replace', ['#[^\x09\x0A\x0D\x20-\x7E\xA0-\x{2FF}\x{370}-\x{10FFFF}]#u', '', $s]);

		// transliteration (by Transliterator and iconv) is not optimal, replace some characters directly
		$s = strtr($s, ["\u{201E}" => '"', "\u{201C}" => '"', "\u{201D}" => '"', "\u{201A}" => "'", "\u{2018}" => "'", "\u{2019}" => "'", "\u{B0}" => '^', "\u{42F}" => 'Ya', "\u{44F}" => 'ya', "\u{42E}" => 'Yu', "\u{44E}" => 'yu', "\u{c4}" => 'Ae', "\u{d6}" => 'Oe', "\u{dc}" => 'Ue', "\u{1e9e}" => 'Ss', "\u{e4}" => 'ae', "\u{f6}" => 'oe', "\u{fc}" => 'ue', "\u{df}" => 'ss']); // „ “ ” ‚ ‘ ’ ° Я я Ю ю Ä Ö Ü ẞ ä ö ü ß
		if ($iconv !== 'libiconv') {
			$s = strtr($s, ["\u{AE}" => '(R)', "\u{A9}" => '(c)', "\u{2026}" => '...', "\u{AB}" => '<<', "\u{BB}" => '>>', "\u{A3}" => 'lb', "\u{A5}" => 'yen', "\u{B2}" => '^2', "\u{B3}" => '^3', "\u{B5}" => 'u', "\u{B9}" => '^1', "\u{BA}" => 'o', "\u{BF}" => '?', "\u{2CA}" => "'", "\u{2CD}" => '_', "\u{2DD}" => '"', "\u{1FEF}" => '', "\u{20AC}" => 'EUR', "\u{2122}" => 'TM', "\u{212E}" => 'e', "\u{2190}" => '<-', "\u{2191}" => '^', "\u{2192}" => '->', "\u{2193}" => 'V', "\u{2194}" => '<->']); // ® © … « » £ ¥ ² ³ µ ¹ º ¿ ˊ ˍ ˝ ` € ™ ℮ ← ↑ → ↓ ↔
		}

		if ($transliterator) {
			$s = $transliterator->transliterate($s);
			// use iconv because The transliterator leaves some characters out of ASCII, eg → ʾ
			if ($iconv === 'glibc') {
				$s = strtr($s, '?', "\x01"); // temporarily hide ? to distinguish them from the garbage that iconv creates
				$s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
				$s = str_replace(['?', "\x01"], ['', '?'], $s); // remove garbage and restore ? characters
			} elseif ($iconv === 'libiconv') {
				$s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
			} else { // null or 'unknown' (#216)
				$s = self::pcre('preg_replace', ['#[^\x00-\x7F]++#', '', $s]); // remove non-ascii chars
			}
		} elseif ($iconv === 'glibc' || $iconv === 'libiconv') {
			// temporarily hide these characters to distinguish them from the garbage that iconv creates
			$s = strtr($s, '`\'"^~?', "\x01\x02\x03\x04\x05\x06");
			if ($iconv === 'glibc') {
				// glibc implementation is very limited. transliterate into Windows-1250 and then into ASCII, so most Eastern European characters are preserved
				$s = iconv('UTF-8', 'WINDOWS-1250//TRANSLIT//IGNORE', $s);
				$s = strtr(
					$s,
					"\xa5\xa3\xbc\x8c\xa7\x8a\xaa\x8d\x8f\x8e\xaf\xb9\xb3\xbe\x9c\x9a\xba\x9d\x9f\x9e\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf8\xf9\xfa\xfb\xfc\xfd\xfe\x96\xa0\x8b\x97\x9b\xa6\xad\xb7",
					'ALLSSSSTZZZallssstzzzRAAAALCCCEEEEIIDDNNOOOOxRUUUUYTsraaaalccceeeeiiddnnooooruuuuyt- <->|-.'
				);
				$s = self::pcre('preg_replace', ['#[^\x00-\x7F]++#', '', $s]);
			} else {
				$s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
			}

			// remove garbage that iconv creates during transliteration (eg Ý -> Y')
			$s = str_replace(['`', "'", '"', '^', '~', '?'], '', $s);
			// restore temporarily hidden characters
			$s = strtr($s, "\x01\x02\x03\x04\x05\x06", '`\'"^~?');
		} else {
			$s = self::pcre('preg_replace', ['#[^\x00-\x7F]++#', '', $s]); // remove non-ascii chars
		}

		return $s;
	}


	/**
	 * Modifies the UTF-8 string to the form used in the URL, ie removes diacritics and replaces all characters
	 * except letters of the English alphabet and numbers with a hyphens.
	 */
	public static function webalize(string $s, ?string $charlist = null, bool $lower = true): string
	{
		$s = self::toAscii($s);
		if ($lower) {
			$s = strtolower($s);
		}

		$s = self::pcre('preg_replace', ['#[^a-z0-9' . ($charlist !== null ? preg_quote($charlist, '#') : '') . ']+#i', '-', $s]);
		$s = trim($s, '-');
		return $s;
	}


	/**
	 * Truncates a UTF-8 string to given maximal length, while trying not to split whole words. Only if the string is truncated,
	 * an ellipsis (or something else set with third argument) is appended to the string.
	 */
	public static function truncate(string $s, int $maxLen, string $append = "\u{2026}"): string
	{
		if (self::length($s) > $maxLen) {
			$maxLen -= self::length($append);
			if ($maxLen < 1) {
				return $append;

			} elseif ($matches = self::match($s, '#^.{1,' . $maxLen . '}(?=[\s\x00-/:-@\[-`{-~])#us')) {
				return $matches[0] . $append;

			} else {
				return self::substring($s, 0, $maxLen) . $append;
			}
		}

		return $s;
	}


	/**
	 * Indents a multiline text from the left. Second argument sets how many indentation chars should be used,
	 * while the indent itself is the third argument (*tab* by default).
	 */
	public static function indent(string $s, int $level = 1, string $chars = "\t"): string
	{
		if ($level > 0) {
			$s = self::replace($s, '#(?:^|[\r\n]+)(?=[^\r\n])#', '$0' . str_repeat($chars, $level));
		}

		return $s;
	}


	/**
	 * Converts all characters of UTF-8 string to lower case.
	 */
	public static function lower(string $s): string
	{
		return mb_strtolower($s, 'UTF-8');
	}


	/**
	 * Converts the first character of a UTF-8 string to lower case and leaves the other characters unchanged.
	 */
	public static function firstLower(string $s): string
	{
		return self::lower(self::substring($s, 0, 1)) . self::substring($s, 1);
	}


	/**
	 * Converts all characters of a UTF-8 string to upper case.
	 */
	public static function upper(string $s): string
	{
		return mb_strtoupper($s, 'UTF-8');
	}


	/**
	 * Converts the first character of a UTF-8 string to upper case and leaves the other characters unchanged.
	 */
	public static function firstUpper(string $s): string
	{
		return self::upper(self::substring($s, 0, 1)) . self::substring($s, 1);
	}


	/**
	 * Converts the first character of every word of a UTF-8 string to upper case and the others to lower case.
	 */
	public static function capitalize(string $s): string
	{
		return mb_convert_case($s, MB_CASE_TITLE, 'UTF-8');
	}


	/**
	 * Compares two UTF-8 strings or their parts, without taking character case into account. If length is null, whole strings are compared,
	 * if it is negative, the corresponding number of characters from the end of the strings is compared,
	 * otherwise the appropriate number of characters from the beginning is compared.
	 */
	public static function compare(string $left, string $right, ?int $length = null): bool
	{
		if (class_exists('Normalizer', false)) {
			$left = \Normalizer::normalize($left, \Normalizer::FORM_D); // form NFD is faster
			$right = \Normalizer::normalize($right, \Normalizer::FORM_D); // form NFD is faster
		}

		if ($length < 0) {
			$left = self::substring($left, $length, -$length);
			$right = self::substring($right, $length, -$length);
		} elseif ($length !== null) {
			$left = self::substring($left, 0, $length);
			$right = self::substring($right, 0, $length);
		}

		return self::lower($left) === self::lower($right);
	}


	/**
	 * Finds the common prefix of strings or returns empty string if the prefix was not found.
	 * @param  string[]  $strings
	 */
	public static function findPrefix(array $strings): string
	{
		$first = array_shift($strings);
		for ($i = 0; $i < strlen($first); $i++) {
			foreach ($strings as $s) {
				if (!isset($s[$i]) || $first[$i] !== $s[$i]) {
					while ($i && $first[$i - 1] >= "\x80" && $first[$i] >= "\x80" && $first[$i] < "\xC0") {
						$i--;
					}

					return substr($first, 0, $i);
				}
			}
		}

		return $first;
	}


	/**
	 * Returns number of characters (not bytes) in UTF-8 string.
	 * That is the number of Unicode code points which may differ from the number of graphemes.
	 */
	public static function length(string $s): int
	{
		return function_exists('mb_strlen')
			? mb_strlen($s, 'UTF-8')
			: strlen(utf8_decode($s));
	}


	/**
	 * Removes all left and right side spaces (or the characters passed as second argument) from a UTF-8 encoded string.
	 */
	public static function trim(string $s, string $charlist = self::TRIM_CHARACTERS): string
	{
		$charlist = preg_quote($charlist, '#');
		return self::replace($s, '#^[' . $charlist . ']+|[' . $charlist . ']+$#Du', '');
	}


	/**
	 * Pads a UTF-8 string to given length by prepending the $pad string to the beginning.
	 */
	public static function padLeft(string $s, int $length, string $pad = ' '): string
	{
		$length = max(0, $length - self::length($s));
		$padLen = self::length($pad);
		return str_repeat($pad, (int) ($length / $padLen)) . self::substring($pad, 0, $length % $padLen) . $s;
	}


	/**
	 * Pads UTF-8 string to given length by appending the $pad string to the end.
	 */
	public static function padRight(string $s, int $length, string $pad = ' '): string
	{
		$length = max(0, $length - self::length($s));
		$padLen = self::length($pad);
		return $s . str_repeat($pad, (int) ($length / $padLen)) . self::substring($pad, 0, $length % $padLen);
	}


	/**
	 * Reverses UTF-8 string.
	 */
	public static function reverse(string $s): string
	{
		if (!extension_loaded('iconv')) {
			throw new Nette\NotSupportedException(__METHOD__ . '() requires ICONV extension that is not loaded.');
		}

		return iconv('UTF-32LE', 'UTF-8', strrev(iconv('UTF-8', 'UTF-32BE', $s)));
	}


	/**
	 * Returns part of $haystack before $nth occurence of $needle or returns null if the needle was not found.
	 * Negative value means searching from the end.
	 */
	public static function before(string $haystack, string $needle, int $nth = 1): ?string
	{
		$pos = self::pos($haystack, $needle, $nth);
		return $pos === null
			? null
			: substr($haystack, 0, $pos);
	}


	/**
	 * Returns part of $haystack after $nth occurence of $needle or returns null if the needle was not found.
	 * Negative value means searching from the end.
	 */
	public static function after(string $haystack, string $needle, int $nth = 1): ?string
	{
		$pos = self::pos($haystack, $needle, $nth);
		return $pos === null
			? null
			: substr($haystack, $pos + strlen($needle));
	}


	/**
	 * Returns position in characters of $nth occurence of $needle in $haystack or null if the $needle was not found.
	 * Negative value of `$nth` means searching from the end.
	 */
	public static function indexOf(string $haystack, string $needle, int $nth = 1): ?int
	{
		$pos = self::pos($haystack, $needle, $nth);
		return $pos === null
			? null
			: self::length(substr($haystack, 0, $pos));
	}


	/**
	 * Returns position in characters of $nth occurence of $needle in $haystack or null if the needle was not found.
	 */
	private static function pos(string $haystack, string $needle, int $nth = 1): ?int
	{
		if (!$nth) {
			return null;
		} elseif ($nth > 0) {
			if ($needle === '') {
				return 0;
			}

			$pos = 0;
			while (($pos = strpos($haystack, $needle, $pos)) !== false && --$nth) {
				$pos++;
			}
		} else {
			$len = strlen($haystack);
			if ($needle === '') {
				return $len;
			} elseif ($len === 0) {
				return null;
			}

			$pos = $len - 1;
			while (($pos = strrpos($haystack, $needle, $pos - $len)) !== false && ++$nth) {
				$pos--;
			}
		}

		return Helpers::falseToNull($pos);
	}


	/**
	 * Splits a string into array by the regular expression. Parenthesized expression in the delimiter are captured.
	 * Parameter $flags can be any combination of PREG_SPLIT_NO_EMPTY and PREG_OFFSET_CAPTURE flags.
	 */
	public static function split(string $subject, string $pattern, int $flags = 0): array
	{
		return self::pcre('preg_split', [$pattern, $subject, -1, $flags | PREG_SPLIT_DELIM_CAPTURE]);
	}


	/**
	 * Checks if given string matches a regular expression pattern and returns an array with first found match and each subpattern.
	 * Parameter $flags can be any combination of PREG_OFFSET_CAPTURE and PREG_UNMATCHED_AS_NULL flags.
	 */
	public static function match(string $subject, string $pattern, int $flags = 0, int $offset = 0): ?array
	{
		if ($offset > strlen($subject)) {
			return null;
		}

		return self::pcre('preg_match', [$pattern, $subject, &$m, $flags, $offset])
			? $m
			: null;
	}


	/**
	 * Finds all occurrences matching regular expression pattern and returns a two-dimensional array. Result is array of matches (ie uses by default PREG_SET_ORDER).
	 * Parameter $flags can be any combination of PREG_OFFSET_CAPTURE, PREG_UNMATCHED_AS_NULL and PREG_PATTERN_ORDER flags.
	 */
	public static function matchAll(string $subject, string $pattern, int $flags = 0, int $offset = 0): array
	{
		if ($offset > strlen($subject)) {
			return [];
		}

		self::pcre('preg_match_all', [
			$pattern, $subject, &$m,
			($flags & PREG_PATTERN_ORDER) ? $flags : ($flags | PREG_SET_ORDER),
			$offset,
		]);
		return $m;
	}


	/**
	 * Replaces all occurrences matching regular expression $pattern which can be string or array in the form `pattern => replacement`.
	 * @param  string|array  $pattern
	 * @param  string|callable  $replacement
	 */
	public static function replace(string $subject, $pattern, $replacement = '', int $limit = -1): string
	{
		if (is_object($replacement) || is_array($replacement)) {
			if (!is_callable($replacement, false, $textual)) {
				throw new Nette\InvalidStateException("Callback '$textual' is not callable.");
			}

			return self::pcre('preg_replace_callback', [$pattern, $replacement, $subject, $limit]);

		} elseif (is_array($pattern) && is_string(key($pattern))) {
			$replacement = array_values($pattern);
			$pattern = array_keys($pattern);
		}

		return self::pcre('preg_replace', [$pattern, $replacement, $subject, $limit]);
	}


	/** @internal */
	public static function pcre(string $func, array $args)
	{
		$res = Callback::invokeSafe($func, $args, function (string $message) use ($args): void {
			// compile-time error, not detectable by preg_last_error
			throw new RegexpException($message . ' in pattern: ' . implode(' or ', (array) $args[0]));
		});

		if (($code = preg_last_error()) // run-time error, but preg_last_error & return code are liars
			&& ($res === null || !in_array($func, ['preg_filter', 'preg_replace_callback', 'preg_replace'], true))
		) {
			throw new RegexpException((RegexpException::MESSAGES[$code] ?? 'Unknown error')
				. ' (pattern: ' . implode(' or ', (array) $args[0]) . ')', $code);
		}

		return $res;
	}
}
