<?php

declare(strict_types=1);

namespace Brick\Math\Internal;

use Brick\Math\RoundingMode;

use function ltrim;
use function rtrim;
use function str_pad;
use function str_repeat;
use function strlen;
use function substr;

use const STR_PAD_LEFT;

/**
 * Shared helper for decimal operations.
 *
 * @internal
 */
final class DecimalHelper
{
    private function __construct()
    {
    }

    /**
     * Computes the scale needed to represent the exact decimal result of a reduced fraction.
     *
     * Returns null if the denominator has prime factors other than 2 or 5.
     *
     * @param string $denominator The denominator of the reduced fraction. Must be strictly positive.
     *
     * @return non-negative-int|null
     *
     * @pure
     */
    public static function computeScaleFromReducedFractionDenominator(string $denominator): ?int
    {
        $calculator = CalculatorRegistry::get();

        $d = rtrim($denominator, '0');

        /** @var non-negative-int $scale rtrim can only shorten a string */
        $scale = strlen($denominator) - strlen($d);

        foreach ([5, 2] as $prime) {
            for (; ;) {
                $lastDigit = (int) $d[-1];

                if ($lastDigit % $prime !== 0) {
                    break;
                }

                $d = $calculator->divQ($d, (string) $prime);
                $scale++;
            }
        }

        return $d === '1' ? $scale : null;
    }

    /**
     * Scales an unscaled decimal value to the requested scale.
     *
     * Returns null when rounding is necessary and the rounding mode is Unnecessary.
     *
     * @param string       $value        The unscaled value.
     * @param int          $currentScale The current scale.
     * @param int          $targetScale  The target scale.
     * @param RoundingMode $roundingMode The rounding mode.
     *
     * @return string|null The unscaled value at the target scale, or null if RoundingMode::Unnecessary is used and rounding is necessary.
     *
     * @pure
     */
    public static function scale(string $value, int $currentScale, int $targetScale, RoundingMode $roundingMode): ?string
    {
        $scaled = self::tryScaleExactly($value, $currentScale, $targetScale);

        if ($scaled !== null) {
            return $scaled;
        }

        if ($roundingMode === RoundingMode::Unnecessary) {
            return null;
        }

        $divisor = '1' . str_repeat('0', $currentScale - $targetScale);

        return CalculatorRegistry::get()->divRound($value, $divisor, $roundingMode);
    }

    /**
     * Adds leading zeros if necessary to represent the full decimal number.
     *
     * @param string $value The unscaled value.
     * @param int    $scale The current scale.
     *
     * @pure
     */
    public static function padUnscaledValue(string $value, int $scale): string
    {
        $targetLength = $scale + 1;
        $negative = ($value[0] === '-');
        $length = strlen($value);

        if ($negative) {
            $length--;
        }

        if ($length >= $targetLength) {
            return $value;
        }

        if ($negative) {
            $value = substr($value, 1);
        }

        $value = str_pad($value, $targetLength, '0', STR_PAD_LEFT);

        if ($negative) {
            $value = '-' . $value;
        }

        return $value;
    }

    /**
     * Tries to scale exactly without rounding, returning null when rounding would be required.
     *
     * @param string $value        The unscaled value.
     * @param int    $currentScale The current scale.
     * @param int    $targetScale  The target scale.
     *
     * @return string|null The unscaled value at the target scale, or null if rounding would be required.
     *
     * @pure
     */
    public static function tryScaleExactly(string $value, int $currentScale, int $targetScale): ?string
    {
        if ($value === '0' || $targetScale === $currentScale) {
            return $value;
        }

        if ($targetScale > $currentScale) {
            return $value . str_repeat('0', $targetScale - $currentScale);
        }

        $negative = ($value[0] === '-');
        if ($negative) {
            $value = substr($value, 1);
        }

        $value = self::padUnscaledValue($value, $currentScale);
        $discardedDigits = $currentScale - $targetScale;

        if (substr($value, -$discardedDigits) !== str_repeat('0', $discardedDigits)) {
            return null;
        }

        $value = substr($value, 0, -$discardedDigits);
        $value = ltrim($value, '0');

        if ($value === '') {
            return '0';
        }

        if ($negative) {
            $value = '-' . $value;
        }

        return $value;
    }
}
