<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Signer;

use InvalidArgumentException;
use Lcobucci\JWT\Exception;

final class InvalidKeyProvided extends InvalidArgumentException implements Exception
{
    public static function cannotBeParsed(string $details): self
    {
        return new self('It was not possible to parse your key, reason:' . $details);
    }

    /**
     * @param non-empty-string $expectedType
     * @param non-empty-string $actualType
     */
    public static function incompatibleKeyType(string $expectedType, string $actualType): self
    {
        return new self(
            'The type of the provided key is not "' . $expectedType
            . '", "' . $actualType . '" provided',
        );
    }

    /** @param positive-int $expectedLength */
    public static function incompatibleKeyLength(int $expectedLength, int $actualLength): self
    {
        return new self(
            'The length of the provided key is different than ' . $expectedLength . ' bits, '
            . $actualLength . ' bits provided',
        );
    }

    public static function cannotBeEmpty(): self
    {
        return new self('Key cannot be empty');
    }

    public static function tooShort(int $expectedLength, int $actualLength): self
    {
        return new self('Key provided is shorter than ' . $expectedLength . ' bits,'
            . ' only ' . $actualLength . ' bits provided');
    }
}
