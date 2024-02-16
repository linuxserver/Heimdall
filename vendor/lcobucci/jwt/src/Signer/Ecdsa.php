<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Signer;

use Lcobucci\JWT\Signer\Ecdsa\MultibyteStringConverter;
use Lcobucci\JWT\Signer\Ecdsa\SignatureConverter;

use const OPENSSL_KEYTYPE_EC;

abstract class Ecdsa extends OpenSSL
{
    public function __construct(
        private readonly SignatureConverter $converter = new MultibyteStringConverter(),
    ) {
    }

    final public function sign(string $payload, Key $key): string
    {
        return $this->converter->fromAsn1(
            $this->createSignature($key->contents(), $key->passphrase(), $payload),
            $this->pointLength(),
        );
    }

    final public function verify(string $expected, string $payload, Key $key): bool
    {
        return $this->verifySignature(
            $this->converter->toAsn1($expected, $this->pointLength()),
            $payload,
            $key->contents(),
        );
    }

    /** {@inheritDoc} */
    final protected function guardAgainstIncompatibleKey(int $type, int $lengthInBits): void
    {
        if ($type !== OPENSSL_KEYTYPE_EC) {
            throw InvalidKeyProvided::incompatibleKeyType(
                self::KEY_TYPE_MAP[OPENSSL_KEYTYPE_EC],
                self::KEY_TYPE_MAP[$type],
            );
        }

        $expectedKeyLength = $this->expectedKeyLength();

        if ($lengthInBits !== $expectedKeyLength) {
            throw InvalidKeyProvided::incompatibleKeyLength($expectedKeyLength, $lengthInBits);
        }
    }

    /**
     * @internal
     *
     * @return positive-int
     */
    abstract public function expectedKeyLength(): int;

    /**
     * Returns the length of each point in the signature, so that we can calculate and verify R and S points properly
     *
     * @internal
     *
     * @return positive-int
     */
    abstract public function pointLength(): int;
}
