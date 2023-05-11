<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Polyfill\Php73;

/**
 * @author Gabriel Caruso <carusogabriel34@gmail.com>
 * @author Ion Bazan <ion.bazan@gmail.com>
 *
 * @internal
 */
final class Php73
{
    public static $startAt = 1533462603;

    /**
     * @param bool $asNum
     *
     * @return array|float|int
     */
    public static function hrtime($asNum = false)
    {
        $ns = microtime(false);
        $s = substr($ns, 11) - self::$startAt;
        $ns = 1E9 * (float) $ns;

        if ($asNum) {
            $ns += $s * 1E9;

            return \PHP_INT_SIZE === 4 ? $ns : (int) $ns;
        }

        return [$s, (int) $ns];
    }
}
