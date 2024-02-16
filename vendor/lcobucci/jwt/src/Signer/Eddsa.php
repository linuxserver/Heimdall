<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Signer;

use Lcobucci\JWT\Signer;
use SodiumException;

use function sodium_crypto_sign_detached;
use function sodium_crypto_sign_verify_detached;

final class Eddsa implements Signer
{
    public function algorithmId(): string
    {
        return 'EdDSA';
    }

    public function sign(string $payload, Key $key): string
    {
        try {
            return sodium_crypto_sign_detached($payload, $key->contents());
        } catch (SodiumException $sodiumException) {
            throw new InvalidKeyProvided($sodiumException->getMessage(), 0, $sodiumException);
        }
    }

    public function verify(string $expected, string $payload, Key $key): bool
    {
        try {
            return sodium_crypto_sign_verify_detached($expected, $payload, $key->contents());
        } catch (SodiumException $sodiumException) {
            throw new InvalidKeyProvided($sodiumException->getMessage(), 0, $sodiumException);
        }
    }
}
