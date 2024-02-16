<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Token;

final class Signature
{
    /**
     * @param non-empty-string $hash
     * @param non-empty-string $encoded
     */
    public function __construct(private readonly string $hash, private readonly string $encoded)
    {
    }

    /** @return non-empty-string */
    public function hash(): string
    {
        return $this->hash;
    }

    /**
     * Returns the encoded version of the signature
     *
     * @return non-empty-string
     */
    public function toString(): string
    {
        return $this->encoded;
    }
}
