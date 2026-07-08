<?php

declare(strict_types=1);

namespace Brick\Math\Exception;

use RuntimeException;
use Throwable;

use function get_debug_type;
use function sprintf;

/**
 * Exception thrown when random byte generation fails.
 */
final class RandomSourceException extends RuntimeException implements MathException
{
    /**
     * @internal
     *
     * @pure
     */
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function randomSourceFailure(Throwable $previous): self
    {
        return new self('Random byte generation failed.', $previous);
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function invalidRandomBytesType(mixed $value): self
    {
        return new self(sprintf(
            'The random bytes generator must return a string, got %s.',
            get_debug_type($value),
        ));
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function invalidRandomBytesLength(int $expectedLength, int $actualLength): self
    {
        return new self(sprintf(
            'The random bytes generator returned %d byte(s), expected %d.',
            $actualLength,
            $expectedLength,
        ));
    }
}
