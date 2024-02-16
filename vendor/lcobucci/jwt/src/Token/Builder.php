<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Token;

use DateTimeImmutable;
use Lcobucci\JWT\Builder as BuilderInterface;
use Lcobucci\JWT\ClaimsFormatter;
use Lcobucci\JWT\Encoder;
use Lcobucci\JWT\Encoding\CannotEncodeContent;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\UnencryptedToken;

use function array_diff;
use function array_merge;
use function in_array;

/** @immutable */
final class Builder implements BuilderInterface
{
    /** @var array<non-empty-string, mixed> */
    private array $headers = ['typ' => 'JWT', 'alg' => null];

    /** @var array<non-empty-string, mixed> */
    private array $claims = [];

    public function __construct(private readonly Encoder $encoder, private readonly ClaimsFormatter $claimFormatter)
    {
    }

    public function permittedFor(string ...$audiences): BuilderInterface
    {
        $configured = $this->claims[RegisteredClaims::AUDIENCE] ?? [];
        $toAppend   = array_diff($audiences, $configured);

        return $this->setClaim(RegisteredClaims::AUDIENCE, array_merge($configured, $toAppend));
    }

    public function expiresAt(DateTimeImmutable $expiration): BuilderInterface
    {
        return $this->setClaim(RegisteredClaims::EXPIRATION_TIME, $expiration);
    }

    public function identifiedBy(string $id): BuilderInterface
    {
        return $this->setClaim(RegisteredClaims::ID, $id);
    }

    public function issuedAt(DateTimeImmutable $issuedAt): BuilderInterface
    {
        return $this->setClaim(RegisteredClaims::ISSUED_AT, $issuedAt);
    }

    public function issuedBy(string $issuer): BuilderInterface
    {
        return $this->setClaim(RegisteredClaims::ISSUER, $issuer);
    }

    public function canOnlyBeUsedAfter(DateTimeImmutable $notBefore): BuilderInterface
    {
        return $this->setClaim(RegisteredClaims::NOT_BEFORE, $notBefore);
    }

    public function relatedTo(string $subject): BuilderInterface
    {
        return $this->setClaim(RegisteredClaims::SUBJECT, $subject);
    }

    public function withHeader(string $name, mixed $value): BuilderInterface
    {
        $new                 = clone $this;
        $new->headers[$name] = $value;

        return $new;
    }

    public function withClaim(string $name, mixed $value): BuilderInterface
    {
        if (in_array($name, RegisteredClaims::ALL, true)) {
            throw RegisteredClaimGiven::forClaim($name);
        }

        return $this->setClaim($name, $value);
    }

    /** @param non-empty-string $name */
    private function setClaim(string $name, mixed $value): BuilderInterface
    {
        $new                = clone $this;
        $new->claims[$name] = $value;

        return $new;
    }

    /**
     * @param array<non-empty-string, mixed> $items
     *
     * @throws CannotEncodeContent When data cannot be converted to JSON.
     */
    private function encode(array $items): string
    {
        return $this->encoder->base64UrlEncode(
            $this->encoder->jsonEncode($items),
        );
    }

    public function getToken(Signer $signer, Key $key): UnencryptedToken
    {
        $headers        = $this->headers;
        $headers['alg'] = $signer->algorithmId();

        $encodedHeaders = $this->encode($headers);
        $encodedClaims  = $this->encode($this->claimFormatter->formatClaims($this->claims));

        $signature        = $signer->sign($encodedHeaders . '.' . $encodedClaims, $key);
        $encodedSignature = $this->encoder->base64UrlEncode($signature);

        return new Plain(
            new DataSet($headers, $encodedHeaders),
            new DataSet($this->claims, $encodedClaims),
            new Signature($signature, $encodedSignature),
        );
    }
}
