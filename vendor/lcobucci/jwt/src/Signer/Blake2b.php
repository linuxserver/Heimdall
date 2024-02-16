<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Signer;

use Lcobucci\JWT\Signer;

use function hash_equals;
use function sodium_crypto_generichash;
use function strlen;

final class Blake2b implements Signer
{
    private const MINIMUM_KEY_LENGTH_IN_BITS = 256;

    public function algorithmId(): string
    {
        return 'BLAKE2B';
    }

    public function sign(string $payload, Key $key): string
    {
        $actualKeyLength = 8 * strlen($key->contents());

        if ($actualKeyLength < self::MINIMUM_KEY_LENGTH_IN_BITS) {
            throw InvalidKeyProvided::tooShort(self::MINIMUM_KEY_LENGTH_IN_BITS, $actualKeyLength);
        }

        return sodium_crypto_generichash($payload, $key->contents());
    }

    public function verify(string $expected, string $payload, Key $key): bool
    {
        return hash_equals($expected, $this->sign($payload, $key));
    }
}
