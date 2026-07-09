<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Polyfill\Php86;

/**
 * @author kylekatarnls <kylekatarnls@gmail.com>
 *
 * @internal
 */
final class Php86
{
    /**
     * @template Value
     * @template Minimum
     * @template Maximum
     *
     * @param Value   $value
     * @param Minimum $min
     * @param Maximum $max
     *
     * @return Value|Minimum|Maximum
     */
    public static function clamp($value, $min, $max)
    {
        if (\is_float($min) && is_nan($min)) {
            self::throwValueErrorIfAvailable('clamp(): Argument #2 ($min) must not be NAN');
        }

        if (\is_float($max) && is_nan($max)) {
            self::throwValueErrorIfAvailable('clamp(): Argument #3 ($max) must not be NAN');
        }

        if ($max < $min) {
            self::throwValueErrorIfAvailable('clamp(): Argument #2 ($min) must be smaller than or equal to argument #3 ($max)');
        }

        if ($value > $max) {
            return $max;
        }

        if ($value < $min) {
            return $min;
        }

        return $value;
    }

    private static function throwValueErrorIfAvailable(string $message): void
    {
        if (!class_exists(\ValueError::class)) {
            throw new \InvalidArgumentException($message);
        }

        throw new \ValueError($message);
    }

    public static function grapheme_strrev(string $string)
    {
        if (!preg_match('//u', $string)) {
            return false;
        }

        if (false === $units = grapheme_str_split($string)) {
            return false;
        }

        return implode('', array_reverse($units));
    }
}
