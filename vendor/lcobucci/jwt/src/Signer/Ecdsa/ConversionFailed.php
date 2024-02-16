<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Signer\Ecdsa;

use InvalidArgumentException;
use Lcobucci\JWT\Exception;

final class ConversionFailed extends InvalidArgumentException implements Exception
{
    public static function invalidLength(): self
    {
        return new self('Invalid signature length.');
    }

    public static function incorrectStartSequence(): self
    {
        return new self('Invalid data. Should start with a sequence.');
    }

    public static function integerExpected(): self
    {
        return new self('Invalid data. Should contain an integer.');
    }
}
