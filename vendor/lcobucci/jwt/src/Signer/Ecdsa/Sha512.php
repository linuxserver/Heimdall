<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Signer\Ecdsa;

use Lcobucci\JWT\Signer\Ecdsa;

use const OPENSSL_ALGO_SHA512;

final class Sha512 extends Ecdsa
{
    public function algorithmId(): string
    {
        return 'ES512';
    }

    public function algorithm(): int
    {
        return OPENSSL_ALGO_SHA512;
    }

    public function pointLength(): int
    {
        return 132;
    }

    public function expectedKeyLength(): int
    {
        // ES512 means ECDSA using P-521 and SHA-512.
        // The key size is indeed 521 bits.
        return 521;
    }
}
