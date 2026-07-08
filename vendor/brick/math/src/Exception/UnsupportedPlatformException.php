<?php

declare(strict_types=1);

namespace Brick\Math\Exception;

use RuntimeException;

/**
 * Exception thrown when the current PHP platform does not support a required feature.
 */
final class UnsupportedPlatformException extends RuntimeException implements MathException
{
    /**
     * @internal
     *
     * @pure
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function unsupportedFloatFormat(): self
    {
        return new self('Unsupported float format: expected IEEE-754 double.');
    }
}
