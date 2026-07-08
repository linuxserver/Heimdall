<?php

declare(strict_types=1);

namespace Brick\Math\Exception;

use RuntimeException;

/**
 * Exception thrown when attempting to perform an unsupported operation, such as a square root, on a negative number.
 */
final class NegativeNumberException extends RuntimeException implements MathException
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
    public static function squareRootOfNegativeNumber(): self
    {
        return new self('Cannot calculate the square root of a negative number.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function nthRootOfNegativeNumber(): self
    {
        return new self('Cannot take an even nth root of a negative number.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function toArbitraryBaseOfNegativeNumber(): self
    {
        return new self('Cannot convert a negative number to an arbitrary base.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function unsignedBytesOfNegativeNumber(): self
    {
        return new self('Cannot convert a negative number to a byte string in unsigned mode.');
    }
}
