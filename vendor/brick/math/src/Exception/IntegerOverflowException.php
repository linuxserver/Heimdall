<?php

declare(strict_types=1);

namespace Brick\Math\Exception;

use Brick\Math\BigInteger;
use RuntimeException;

use function sprintf;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

/**
 * Exception thrown when a native integer overflow occurs.
 */
final class IntegerOverflowException extends RuntimeException implements MathException
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
    public static function integerOutOfRange(BigInteger $value): self
    {
        $message = '%s is out of range [%d, %d] and cannot be represented as an integer.';

        return new self(sprintf($message, $value->toString(), PHP_INT_MIN, PHP_INT_MAX));
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function nativeIntegerOverflow(string $expression): self
    {
        return new self(sprintf(
            'Cannot compute %s because the result is outside the native integer range [%d, %d].',
            $expression,
            PHP_INT_MIN,
            PHP_INT_MAX,
        ));
    }
}
