<?php

declare(strict_types=1);

namespace Brick\Math\Exception;

use Brick\Math\NumberSyntax;
use RuntimeException;

use function ord;
use function sprintf;
use function strlen;
use function substr;

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
            self::truncateAndEscape($value),
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
            'Character "%s" is not valid in the given alphabet.',
            self::escapeChar($char),
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
            'Character "%s" is not valid in base %d.',
            self::escapeChar($char),
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
     * @internal
     *
     * @pure
     */
    public static function tooManyDigits(int $maxDigits): self
    {
        return new self(sprintf(
            'The number exceeds the maximum number of %d digits.',
            $maxDigits,
        ));
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function syntaxNotAllowed(NumberSyntax $syntax): self
    {
        return new self(sprintf('The %s syntax is not allowed.', match ($syntax) {
            NumberSyntax::DecimalPoint => 'decimal point',
            NumberSyntax::Exponent => 'exponent',
            NumberSyntax::Fraction => 'fraction',
        }));
    }

    /**
     * @internal
     *
     * @pure
     */
    public static function zeroDenominator(): self
    {
        return new self('The denominator of a rational number must not be zero.');
    }

    /**
     * @pure
     */
    private static function truncateAndEscape(string $value): string
    {
        if (strlen($value) > 40) {
            $value = substr($value, 0, 40) . '...';
        }

        $escaped = '';
        $length = strlen($value);

        for ($i = 0; $i < $length; $i++) {
            $escaped .= self::escapeChar($value[$i]);
        }

        return $escaped;
    }

    /**
     * @pure
     */
    private static function escapeChar(string $char): string
    {
        $ord = ord($char);

        return match (true) {
            $char === "\t" => '\t',
            $char === "\n" => '\n',
            $char === "\r" => '\r',
            $char === '\\' => '\\\\',
            $char === '"' => '\"',
            $ord < 32 || $ord > 126 => sprintf('\x%02X', $ord),
            default => $char,
        };
    }
}
