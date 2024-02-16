<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Signer;

use Lcobucci\JWT\Signer;

use function hash_equals;
use function hash_hmac;
use function strlen;

abstract class Hmac implements Signer
{
    final public function sign(string $payload, Key $key): string
    {
        $actualKeyLength   = 8 * strlen($key->contents());
        $expectedKeyLength = $this->minimumBitsLengthForKey();

        if ($actualKeyLength < $expectedKeyLength) {
            throw InvalidKeyProvided::tooShort($expectedKeyLength, $actualKeyLength);
        }

        return hash_hmac($this->algorithm(), $payload, $key->contents(), true);
    }

    final public function verify(string $expected, string $payload, Key $key): bool
    {
        return hash_equals($expected, $this->sign($payload, $key));
    }

    /**
     * @internal
     *
     * @return non-empty-string
     */
    abstract public function algorithm(): string;

    /**
     * @internal
     *
     * @return positive-int
     */
    abstract public function minimumBitsLengthForKey(): int;
}
