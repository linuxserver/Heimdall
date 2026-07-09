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
    function clamp(mixed $value, mixed $min, mixed $max): mixed { return p\Php86::clamp($value, $min, $max); }
}
