<?php
declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2018 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 *
 * @link https://github.com/web-token/jwt-framework/blob/v1.2/src/Component/Core/Util/ECSignature.php
 */

namespace Lcobucci\JWT\Signer\Ecdsa;

use function assert;
use function bin2hex;
use function dechex;
use function hex2bin;
use function hexdec;
use function is_string;
use function str_pad;
use function strlen;
use function substr;

use const STR_PAD_LEFT;

/**
 * ECDSA signature converter using ext-mbstring
 *
 * @internal
 */
final class MultibyteStringConverter implements SignatureConverter
{
    private const ASN1_SEQUENCE          = '30';
    private const ASN1_INTEGER           = '02';
    private const ASN1_MAX_SINGLE_BYTE   = 128;
    private const ASN1_LENGTH_2BYTES     = '81';
    private const ASN1_BIG_INTEGER_LIMIT = '7f';
    private const ASN1_NEGATIVE_INTEGER  = '00';
    private const BYTE_SIZE              = 2;

    public function toAsn1(string $points, int $length): string
    {
        $points = bin2hex($points);

        if (self::octetLength($points) !== $length) {
            throw ConversionFailed::invalidLength();
        }

        $pointR = self::preparePositiveInteger(substr($points, 0, $length));
        $pointS = self::preparePositiveInteger(substr($points, $length, null));

        $lengthR = self::octetLength($pointR);
        $lengthS = self::octetLength($pointS);

        $totalLength  = $lengthR + $lengthS + self::BYTE_SIZE + self::BYTE_SIZE;
        $lengthPrefix = $totalLength > self::ASN1_MAX_SINGLE_BYTE ? self::ASN1_LENGTH_2BYTES : '';

        $asn1 = hex2bin(
            self::ASN1_SEQUENCE
            . $lengthPrefix . dechex($totalLength)
            . self::ASN1_INTEGER . dechex($lengthR) . $pointR
            . self::ASN1_INTEGER . dechex($lengthS) . $pointS,
        );
        assert(is_string($asn1));
        assert($asn1 !== '');

        return $asn1;
    }

    private static function octetLength(string $data): int
    {
        return (int) (strlen($data) / self::BYTE_SIZE);
    }

    private static function preparePositiveInteger(string $data): string
    {
        if (substr($data, 0, self::BYTE_SIZE) > self::ASN1_BIG_INTEGER_LIMIT) {
            return self::ASN1_NEGATIVE_INTEGER . $data;
        }

        while (
            substr($data, 0, self::BYTE_SIZE) === self::ASN1_NEGATIVE_INTEGER
            && substr($data, 2, self::BYTE_SIZE) <= self::ASN1_BIG_INTEGER_LIMIT
        ) {
            $data = substr($data, 2, null);
        }

        return $data;
    }

    public function fromAsn1(string $signature, int $length): string
    {
        $message  = bin2hex($signature);
        $position = 0;

        if (self::readAsn1Content($message, $position, self::BYTE_SIZE) !== self::ASN1_SEQUENCE) {
            throw ConversionFailed::incorrectStartSequence();
        }

        // @phpstan-ignore-next-line
        if (self::readAsn1Content($message, $position, self::BYTE_SIZE) === self::ASN1_LENGTH_2BYTES) {
            $position += self::BYTE_SIZE;
        }

        $pointR = self::retrievePositiveInteger(self::readAsn1Integer($message, $position));
        $pointS = self::retrievePositiveInteger(self::readAsn1Integer($message, $position));

        $points = hex2bin(str_pad($pointR, $length, '0', STR_PAD_LEFT) . str_pad($pointS, $length, '0', STR_PAD_LEFT));
        assert(is_string($points));
        assert($points !== '');

        return $points;
    }

    private static function readAsn1Content(string $message, int &$position, int $length): string
    {
        $content   = substr($message, $position, $length);
        $position += $length;

        return $content;
    }

    private static function readAsn1Integer(string $message, int &$position): string
    {
        if (self::readAsn1Content($message, $position, self::BYTE_SIZE) !== self::ASN1_INTEGER) {
            throw ConversionFailed::integerExpected();
        }

        $length = (int) hexdec(self::readAsn1Content($message, $position, self::BYTE_SIZE));

        return self::readAsn1Content($message, $position, $length * self::BYTE_SIZE);
    }

    private static function retrievePositiveInteger(string $data): string
    {
        while (
            substr($data, 0, self::BYTE_SIZE) === self::ASN1_NEGATIVE_INTEGER
            && substr($data, 2, self::BYTE_SIZE) > self::ASN1_BIG_INTEGER_LIMIT
        ) {
            $data = substr($data, 2, null);
        }

        return $data;
    }
}
