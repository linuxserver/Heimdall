<?php

declare(strict_types=1);

namespace Brick\Math\Exception;

use RuntimeException;

/**
 * Exception thrown when attempting to compute a modular inverse that does not exist.
 */
final class NoInverseException extends RuntimeException implements MathException
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
    public static function noModularInverse(): self
    {
        return new self('This number has no multiplicative inverse modulo the given modulus (they are not coprime).');
    }
}
