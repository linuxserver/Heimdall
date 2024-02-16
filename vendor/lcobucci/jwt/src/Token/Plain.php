<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Token;

use DateTimeInterface;
use Lcobucci\JWT\UnencryptedToken;

use function in_array;

final class Plain implements UnencryptedToken
{
    public function __construct(
        private readonly DataSet $headers,
        private readonly DataSet $claims,
        private readonly Signature $signature,
    ) {
    }

    public function headers(): DataSet
    {
        return $this->headers;
    }

    public function claims(): DataSet
    {
        return $this->claims;
    }

    public function signature(): Signature
    {
        return $this->signature;
    }

    public function payload(): string
    {
        return $this->headers->toString() . '.' . $this->claims->toString();
    }

    public function isPermittedFor(string $audience): bool
    {
        return in_array($audience, $this->claims->get(RegisteredClaims::AUDIENCE, []), true);
    }

    public function isIdentifiedBy(string $id): bool
    {
        return $this->claims->get(RegisteredClaims::ID) === $id;
    }

    public function isRelatedTo(string $subject): bool
    {
        return $this->claims->get(RegisteredClaims::SUBJECT) === $subject;
    }

    public function hasBeenIssuedBy(string ...$issuers): bool
    {
        return in_array($this->claims->get(RegisteredClaims::ISSUER), $issuers, true);
    }

    public function hasBeenIssuedBefore(DateTimeInterface $now): bool
    {
        return $now >= $this->claims->get(RegisteredClaims::ISSUED_AT);
    }

    public function isMinimumTimeBefore(DateTimeInterface $now): bool
    {
        return $now >= $this->claims->get(RegisteredClaims::NOT_BEFORE);
    }

    public function isExpired(DateTimeInterface $now): bool
    {
        if (! $this->claims->has(RegisteredClaims::EXPIRATION_TIME)) {
            return false;
        }

        return $now >= $this->claims->get(RegisteredClaims::EXPIRATION_TIME);
    }

    public function toString(): string
    {
        return $this->headers->toString() . '.'
             . $this->claims->toString() . '.'
             . $this->signature->toString();
    }
}
