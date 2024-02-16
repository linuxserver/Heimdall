<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Encoding;

use JsonException;
use Lcobucci\JWT\Decoder;
use Lcobucci\JWT\Encoder;
use Lcobucci\JWT\SodiumBase64Polyfill;

use function json_decode;
use function json_encode;

use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * A utilitarian class that encodes and decodes data according to JOSE specifications
 */
final class JoseEncoder implements Encoder, Decoder
{
    public function jsonEncode(mixed $data): string
    {
        try {
            return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw CannotEncodeContent::jsonIssues($exception);
        }
    }

    public function jsonDecode(string $json): mixed
    {
        try {
            return json_decode(json: $json, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw CannotDecodeContent::jsonIssues($exception);
        }
    }

    public function base64UrlEncode(string $data): string
    {
        return SodiumBase64Polyfill::bin2base64(
            $data,
            SodiumBase64Polyfill::SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING,
        );
    }

    public function base64UrlDecode(string $data): string
    {
        return SodiumBase64Polyfill::base642bin(
            $data,
            SodiumBase64Polyfill::SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING,
        );
    }
}
