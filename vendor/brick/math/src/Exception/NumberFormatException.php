<?php

declare(strict_types=1);

namespace Brick\Math\Exception;

use RuntimeException;

use function dechex;
use function ord;
use function sprintf;
use function strtoupper;

/**
 * Exception thrown when attempting to create a number from a string with an invalid format.
 */
final class NumberFormatException extends RuntimeException implements MathException
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
    public static function invalidFormat(string $value): self
    {
        return new self(sprintf(
            'Value "%s" does not represent a valid number.',
            $value,
        ));
    }

    /**
     * @internal
     *
     * @param string $char The failing character.
     *
     * @pure
     */
    public static function charNotInAlphabet(string $char): self
    {
        return new self(sprintf(
            'Character %s is not valid in the given alphabet.',
            self::charToString($char),
        ));
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function charNotValidInBase(string $char, int $base): self
    {
        return new self(sprintf(
            'Character %s is not valid in base %d.',
            self::charToString($char),
            $base,
        ));
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function emptyNumber(): self
    {
        return new self('The number must not be empty.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function emptyByteString(): self
    {
        return new self('The byte string must not be empty.');
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function exponentTooLarge(): self
    {
        return new self('The exponent is too large to be represented as an integer.');
    }

    /**
     * @pure
     */
    private static function charToString(string $char): string
    {
        $ord = ord($char);

        if ($ord < 32 || $ord > 126) {
            $char = strtoupper(dechex($ord));

            if ($ord < 16) {
                $char = '0' . $char;
            }

            return '0x' . $char;
        }

        return '"' . $char . '"';
    }
}
