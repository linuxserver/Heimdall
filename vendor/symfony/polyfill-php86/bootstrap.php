<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Polyfill\Php86 as p;

if (\PHP_VERSION_ID >= 80600) {
    return;
}

if (!defined('ARRAY_FILTER_USE_VALUE')) {
    define('ARRAY_FILTER_USE_VALUE', 0);
}

if (\PHP_VERSION_ID >= 80000) {
    return require __DIR__.'/bootstrap80.php';
}

if (!function_exists('clamp')) {
    /**
     * @template V
     * @template L
     * @template H
     *
     * @param V $value
     * @param L $min
     * @param H $max
     *
     * @return V|L|H
     */
    function clamp($value, $min, $max) { return p\Php86::clamp($value, $min, $max); }
}

if (extension_loaded('intl') && !function_exists('grapheme_strrev')) {
    function grapheme_strrev(string $string) { return p\Php86::grapheme_strrev($string); }
}
