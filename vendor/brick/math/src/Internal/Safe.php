<?php

declare(strict_types=1);

namespace Brick\Math\Internal;

use Brick\Math\Exception\IntegerOverflowException;

use function is_int;
use function sprintf;

use const PHP_INT_MIN;

/**
 * Helpers for arithmetic operations that throw on native integer overflow.
 *
 * @internal
 */
final class Safe
{
    private function __construct()
    {
    }

    /**
     * @pure
     */
    public static function add(int $a, int $b): int
    {
        $result = $a + $b;

        if (is_int($result)) {
            return $result;
        }

        // @phpstan-ignore deadCode.unreachable
        throw IntegerOverflowException::nativeIntegerOverflow(sprintf('%d + %d', $a, $b));
    }

    /**
     * @pure
     */
    public static function sub(int $a, int $b): int
    {
        $result = $a - $b;

        if (is_int($result)) {
            return $result;
        }

        // @phpstan-ignore deadCode.unreachable
        throw IntegerOverflowException::nativeIntegerOverflow(sprintf('%d - %d', $a, $b));
    }

    /**
     * @pure
     */
    public static function mul(int $a, int $b): int
    {
        $result = $a * $b;

        if (is_int($result)) {
            return $result;
        }

        // @phpstan-ignore deadCode.unreachable
        throw IntegerOverflowException::nativeIntegerOverflow(sprintf('%d * %d', $a, $b));
    }

    /**
     * @pure
     */
    public static function neg(int $value): int
    {
        if ($value === PHP_INT_MIN) {
            throw IntegerOverflowException::nativeIntegerOverflow(sprintf('-(%d)', $value));
        }

        return -$value;
    }
}
