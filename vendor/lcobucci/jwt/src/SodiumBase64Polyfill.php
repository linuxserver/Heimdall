<?php
declare(strict_types=1);

namespace Lcobucci\JWT;

use Lcobucci\JWT\Encoding\CannotDecodeContent;
use SodiumException;

use function base64_decode;
use function base64_encode;
use function function_exists;
use function is_string;
use function rtrim;
use function sodium_base642bin;
use function sodium_bin2base64;
use function strtr;

/** @internal */
final class SodiumBase64Polyfill
{
    public const SODIUM_BASE64_VARIANT_ORIGINAL            = 1;
    public const SODIUM_BASE64_VARIANT_ORIGINAL_NO_PADDING = 3;
    public const SODIUM_BASE64_VARIANT_URLSAFE             = 5;
    public const SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING  = 7;

    /** @return ($decoded is non-empty-string ? non-empty-string : string) */
    public static function bin2base64(string $decoded, int $variant): string
    {
        if (! function_exists('sodium_bin2base64')) {
            return self::bin2base64Fallback($decoded, $variant); // @codeCoverageIgnore
        }

        return sodium_bin2base64($decoded, $variant);
    }

    /** @return ($decoded is non-empty-string ? non-empty-string : string) */
    public static function bin2base64Fallback(string $decoded, int $variant): string
    {
        $encoded = base64_encode($decoded);

        if (
            $variant === self::SODIUM_BASE64_VARIANT_URLSAFE
            || $variant === self::SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING
        ) {
            $encoded = strtr($encoded, '+/', '-_');
        }

        if (
            $variant === self::SODIUM_BASE64_VARIANT_ORIGINAL_NO_PADDING
            || $variant === self::SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING
        ) {
            $encoded = rtrim($encoded, '=');
        }

        return $encoded;
    }

    /**
     * @return ($encoded is non-empty-string ? non-empty-string : string)
     *
     * @throws CannotDecodeContent
     */
    public static function base642bin(string $encoded, int $variant): string
    {
        if (! function_exists('sodium_base642bin')) {
            return self::base642binFallback($encoded, $variant); // @codeCoverageIgnore
        }

        try {
            return sodium_base642bin($encoded, $variant, '');
        } catch (SodiumException) {
            throw CannotDecodeContent::invalidBase64String();
        }
    }

    /**
     * @return ($encoded is non-empty-string ? non-empty-string : string)
     *
     * @throws CannotDecodeContent
     */
    public static function base642binFallback(string $encoded, int $variant): string
    {
        if (
            $variant === self::SODIUM_BASE64_VARIANT_URLSAFE
            || $variant === self::SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING
        ) {
            $encoded = strtr($encoded, '-_', '+/');
        }

        $decoded = base64_decode($encoded, true);

        if (! is_string($decoded)) {
            throw CannotDecodeContent::invalidBase64String();
        }

        return $decoded;
    }
}
